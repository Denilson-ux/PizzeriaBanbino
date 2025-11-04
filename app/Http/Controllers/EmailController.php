<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ReportePedidos;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmailController extends Controller
{
    /**
     * Mostrar el formulario para enviar correos
     */
    public function mostrarFormulario()
    {
        // Obtener lista de clientes para el selector
        $clientes = Cliente::with('persona')->get()->map(function($cliente) {
            return [
                'id' => $cliente->id_cliente,
                'nombre' => $cliente->persona ? $cliente->persona->nombres . ' ' . $cliente->persona->apellidos : 'Sin nombre'
            ];
        });

        return view('emails.formulario-envio', compact('clientes'));
    }

    /**
     * Enviar reporte de pedidos por correo
     */
    public function enviarReportePedidos(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'destinatarios' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:cliente,id_cliente',
            'asunto' => 'nullable|string|max:255',
        ], [
            'destinatarios.required' => 'Debe especificar al menos un destinatario',
            'fecha_inicio.required' => 'La fecha de inicio es requerida',
            'fecha_fin.required' => 'La fecha de fin es requerida',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
            'cliente_id.exists' => 'El cliente seleccionado no es válido'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Procesar destinatarios (separados por coma)
            $destinatarios = array_map('trim', explode(',', $request->destinatarios));
            
            // Validar formato de emails
            foreach ($destinatarios as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return back()->withErrors(['destinatarios' => "El email '{$email}' no tiene un formato válido"])->withInput();
                }
            }

            // Construir consulta de pedidos
            $query = Pedido::with(['cliente.persona', 'repartidor.persona', 'tipoPago', 'ubicacion'])
                          ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
                          ->where('estado', 1); // Solo pedidos activos

            // Filtrar por cliente si se especifica
            if ($request->cliente_id) {
                $query->where('id_cliente', $request->cliente_id);
            }

            $pedidos = $query->orderBy('fecha', 'desc')->get();

            // Obtener nombre del cliente si se filtró
            $clienteNombre = null;
            if ($request->cliente_id) {
                $cliente = Cliente::with('persona')->find($request->cliente_id);
                $clienteNombre = $cliente && $cliente->persona ? 
                    $cliente->persona->nombres . ' ' . $cliente->persona->apellidos : 'Cliente desconocido';
            }

            // Crear el mailable
            $reporte = new ReportePedidos(
                $pedidos,
                $request->fecha_inicio,
                $request->fecha_fin,
                $clienteNombre
            );

            // Enviar email a cada destinatario
            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario)->send($reporte);
            }

            // Mensaje de éxito
            $mensaje = "Reporte enviado exitosamente a " . count($destinatarios) . " destinatario(s).";
            $mensaje .= " Se encontraron {$pedidos->count()} pedidos en el período especificado.";
            
            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error al enviar reporte de pedidos: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Ocurrió un error al enviar el correo. Por favor, verifique la configuración del servidor de correo.'])->withInput();
        }
    }

    /**
     * Preview del email (para testing)
     */
    public function previewReporte(Request $request)
    {
        // Validar parámetros básicos
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:cliente,id_cliente',
        ]);

        // Obtener pedidos con los mismos criterios
        $query = Pedido::with(['cliente.persona', 'repartidor.persona', 'tipoPago', 'ubicacion'])
                      ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
                      ->where('estado', 1);

        if ($request->cliente_id) {
            $query->where('id_cliente', $request->cliente_id);
        }

        $pedidos = $query->orderBy('fecha', 'desc')->get();

        // Obtener nombre del cliente si se filtró
        $clienteNombre = null;
        if ($request->cliente_id) {
            $cliente = Cliente::with('persona')->find($request->cliente_id);
            $clienteNombre = $cliente && $cliente->persona ? 
                $cliente->persona->nombres . ' ' . $cliente->persona->apellidos : 'Cliente desconocido';
        }

        // Crear el mailable y retornar la vista directamente
        $reporte = new ReportePedidos(
            $pedidos,
            $request->fecha_inicio,
            $request->fecha_fin,
            $clienteNombre
        );

        // Retornar la vista del email directamente para preview
        return $reporte->render();
    }

    /**
     * Test de conexión de correo
     */
    public function testConexion()
    {
        try {
            // Obtener configuración de correo
            $config = config('mail.mailers.smtp');
            
            return response()->json([
                'success' => true,
                'message' => 'Configuración de correo cargada correctamente',
                'config' => [
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'encryption' => $config['encryption'],
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la configuración de correo: ' . $e->getMessage()
            ], 500);
        }
    }
}