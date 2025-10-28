<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Models\User;

class CorreoController extends Controller
{
    protected $host = '192.168.43.146';

    public function getEnviar()
    {
        return view("pizzeria.correo.enviar");
    }

    public function getEnviados()
    {
        return view("pizzeria.correo.enviados");
    }

    public function getRecibidos()
    {
        return view("pizzeria.correo.recibidos");
    }

    #servidor de correo
    public function postEnviarMensaje(Request $request)
    {

        $mail = new PHPMailer(true);
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];


        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        $userId = $user->id;
        
        $emailRemitente = $user->email;
        $password = $user->password_zentyal;

        $nombreRemitente = $request->get('nombre');
        $emailReceptor = $request->get('email_receptor');
        $asunto = $request->get('asunto');
        $mensaje = $request->get('mensaje');

        try {
            // Configuración del servidor SMTP
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $emailRemitente;
            $mail->Password = $password;
            // $mail->SMTPSecure = 'tls';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($emailRemitente, $emailReceptor);
            $mail->addAddress($emailReceptor);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            // Enviar el correo
            $mail->send();

            return "Correo enviado correctamente de $nombreRemitente ($emailRemitente) a $emailReceptor";
        } catch (Exception $e) {
            return "Error al enviar el correo: {$e->getMessage()}";
        }
    }

    public function ejecutarComandoRemoto1()
    {
        $usuario = 'fran';
        $servidor = '192.168.43.146';
        $contrasena = '123'; // Reemplaza 'tu_contraseña' con la contraseña real
        $comando = 'sudo -S perl /var/www/html/create_zentyal_user.pl'; // Agregado '-S' para leer la contraseña desde stdin

        $process = new Process([
            'sshpass',
            '-p', $contrasena,
            'ssh',
            '-T',
            '-o', 'PasswordAuthentication=yes',
            '-o', 'StrictHostKeyChecking=no',
            "$usuario@$servidor",
            $comando,
        ]);

        try {
            // Establecer la entrada estándar con la contraseña
            $process->setInput($contrasena . "\n");
            $process->mustRun();
            return "Comando remoto ejecutado con éxito: " . $process->getOutput();
        } catch (ProcessFailedException $exception) {
            return "Error al ejecutar el comando remoto. Código: {$exception}, Salida: " . $exception->getProcess()->getOutput();
        }
    }


    public function ejecutarComandoRemoto2()
    {
        $usuario = 'fran';
        $servidor = '192.168.43.146';
        $contrasena = '123';
        $rutaScriptLocal = asset('mail/create_zentyal_user.pl'); // Reemplaza con la ruta correcta en tu servidor local
        $rutaScriptRemoto = '/var/www/html/create_zentyal_user.pl'; // Reemplaza con la ruta correcta en el servidor remoto

        // Leer el contenido del script Perl local
        $contenidoScriptLocal = file_get_contents($rutaScriptLocal);

        // Escapar comillas y caracteres especiales para evitar problemas de formato
        $contenidoScriptLocal = addslashes($contenidoScriptLocal);

        // Comando para enviar el script Perl y ejecutarlo remotamente
        $comando = 'echo \"$contenidoScriptLocal\" | sudo -S perl -';

        $process = new Process([
            'sshpass',
            '-p', $contrasena,
            'ssh',
            '-o', 'PasswordAuthentication=yes',
            '-o', 'StrictHostKeyChecking=no',
            "$usuario@$servidor",
            $comando,
        ]);

        try {
            // Establecer la entrada estándar con la contraseña
            $process->setInput($contrasena . "\n");
            $process->mustRun();
            return "Comando remoto ejecutado con éxito: " . $process->getOutput();
        } catch (ProcessFailedException $exception) {
            return "Error al ejecutar el comando remoto. Código: {$exception}, Salida: " . $exception->getProcess()->getOutput();
        }
    }



    public function ejecutarComandoRemoto3()
    {
        $usuario = 'fran';
        $servidor = '192.168.43.146';
        $contrasena = '123';
        $rutaScriptLocal = asset('mail/create_zentyal_user.pl');
        $rutaScriptRemoto = '/var/www/html/create_zentyal_user.pl';
    
        // Copiar el script Perl al servidor remoto
        $comandoCp = [
            'sshpass',
            '-p', $contrasena,
            'scp',
            '-o', 'StrictHostKeyChecking=no',
            $rutaScriptLocal,
            "$usuario@$servidor:$rutaScriptRemoto",
        ];
        $processCp = new Process($comandoCp);

        try {
            $processCp->mustRun();
        } catch (ProcessFailedException $exception) {
            return "Error al copiar el script al servidor remoto. Código: {$exception}, Salida: " . $exception->getProcess()->getOutput();
        }
    
        // Ejecutar el script Perl en el servidor remoto
        $comandoEjecutar = [
            'sshpass',
            '-p', $contrasena,
            'ssh',
            '-o', 'PasswordAuthentication=yes',
            '-o', 'StrictHostKeyChecking=no',
            "$usuario@$servidor",
            "sudo perl $rutaScriptRemoto",
        ];
        $processEjecutar = new Process($comandoEjecutar);
    
        try {
            $process->setInput($contrasena . "\n");
            $processEjecutar->mustRun();
            return "Comando remoto ejecutado con éxito: " . $processEjecutar->getOutput();
        } catch (ProcessFailedException $exception) {
            return "Error al ejecutar el comando remoto. Código: {$exception}, Salida: " . $exception->getProcess()->getOutput();
        }
    }


    public function ejecutarComandoRemoto4()
    {
        $usuario = 'fran';
        $servidor = '192.168.43.146';
        $contrasena = '123';

        // Leer el contenido del script Perl local
        $rutaScriptLocal = asset('mail/create_zentyal_user.pl');
        $contenidoScriptLocal = file_get_contents($rutaScriptLocal);

        // Eliminar caracteres de retorno de carro
        $contenidoScriptLocal = str_replace("\r", "", $contenidoScriptLocal);

        // Comando para enviar el script Perl y ejecutarlo remotamente
        $comando = [
            "echo \"$contenidoScriptLocal\" | dos2unix | sudo -S perl - > salida_script.txt 2>&1",
        ];

        $process = new Process([
            'sshpass',
            '-p', $contrasena,
            'ssh',
            '-T',
            '-o', 'PasswordAuthentication=yes',
            '-o', 'StrictHostKeyChecking=no',
            "$usuario@$servidor",
            implode(' ', $comando),
        ]);

        try {
            // Establecer la entrada estándar con la contraseña
            $process->setInput($contrasena . "\n");
            $process->mustRun();

            // Leer el contenido de salida_script.txt
            $salidaScript = file_get_contents('salida_script.txt');

            // Verificar si el usuario se creó correctamente
            if (strpos($salidaScript, 'Usuario creado exitosamente') !== false) {
                return "Comando remoto ejecutado con éxito: " . $process->getOutput();
            } else {
                return "Error al crear el usuario. Verifica el archivo salida_script.txt para obtener más detalles.";
            }
        } catch (ProcessFailedException $exception) {
            return "Error al ejecutar el comando remoto. Código: {$exception}, Salida: " . $exception->getProcess()->getOutput();
        }
    }

}
