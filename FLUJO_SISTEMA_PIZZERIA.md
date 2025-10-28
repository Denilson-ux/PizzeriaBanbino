# 🍕 FLUJO COMPLETO DEL SISTEMA DE PIZZERÍA

## 📋 Descripción General

Este sistema maneja el flujo completo de una pizzería desde la compra de ingredientes hasta la venta al cliente, con control automático de inventario.

## 🔄 FLUJO PRINCIPAL

### 1. **COMPRAS → ALMACÉN**
```
Proveedor → Compra → Ingredientes → SUMA al Stock
```

**Proceso:**
- Se registra una compra con ingredientes específicos
- Al guardar la compra: **AUTOMÁTICAMENTE SUMA** al stock del almacén
- Se actualiza el costo promedio ponderado
- Se registra movimiento de ingreso

**Tablas involucradas:**
- `compras` - Registro de la compra
- `detalle_compras` - Ingredientes comprados
- `almacen` - Stock actualizado automáticamente
- `movimientos_almacen` - Historial del ingreso

### 2. **RECETAS (ITEM MENÚ)**
```
Pizza Pepperoni = Masa (200g) + Salsa (50ml) + Queso (100g) + Pepperoni (80g)
```

**Proceso:**
- Se crean recetas definiendo qué ingredientes lleva cada pizza
- Se especifica la cantidad exacta de cada ingrediente
- Cada pizza tiene su "lista de ingredientes" con cantidades

**Tablas involucradas:**
- `item_menus` - Las pizzas/productos
- `ingredientes` - Los ingredientes disponibles
- `item_menu_ingredientes` - Relación: qué ingredientes lleva cada pizza y en qué cantidad

### 3. **CATÁLOGO MENÚ**
```
Recetas Creadas → Seleccionar → Activar en Menú
```

**Proceso:**
- Se seleccionan qué recetas estarán disponibles para venta
- Solo activa/desactiva productos del menú
- **NO descuenta ingredientes** (solo muestra disponibilidad)

**Tablas involucradas:**
- `menus` - Menús del día/temporada
- `menu_item_menus` - Qué pizzas están activas en cada menú

### 4. **VENTAS (PROCESO CRÍTICO)**
```
Cliente pide: 2 Pizza Pepperoni
↓
Sistema busca receta de Pizza Pepperoni
↓
Calcula: 2 × (200g masa + 50ml salsa + 100g queso + 80g pepperoni)
↓
Valida stock suficiente en almacén
↓
Al confirmar venta: RESTA ingredientes automáticamente
```

**Proceso detallado:**
1. Cliente hace pedido
2. Sistema busca la receta de cada producto
3. Calcula total de ingredientes necesarios
4. **VALIDA** que hay stock suficiente
5. Si hay stock: crea la venta
6. **AUTOMÁTICAMENTE DESCUENTA** ingredientes del almacén
7. Registra movimientos de egreso

**Tablas involucradas:**
- `nota_ventas` - Registro de la venta
- `detalle_ventas` - Productos vendidos
- `almacen` - Stock descontado automáticamente
- `movimientos_almacen` - Historial del egreso

### 5. **INVENTARIO**
```
Muestra stock actual → Alertas → Histórico de movimientos
```

**Funciones:**
- Stock actual de cada ingrediente
- Alertas de stock bajo
- Histórico completo de movimientos (ingresos/egresos)
- Productos próximos a vencer
- Valorización de inventario

---

## 🏗️ ESTRUCTURA DE BASE DE DATOS

### Tabla: `ingredientes`
```sql
- id_ingrediente (PK)
- nombre
- unidad_medida (gramos, litros, etc.)
- categoria
- es_perecedero
- estado
```

### Tabla: `almacen`
```sql
- id_almacen (PK)
- id_ingrediente (FK → ingredientes)
- stock_actual (DECIMAL)
- stock_minimo
- stock_maximo
- costo_unitario_promedio
- fecha_vencimiento
```

### Tabla: `item_menu_ingredientes` (RECETAS)
```sql
- id_item_menu (FK → item_menus)
- id_ingrediente (FK → ingredientes)
- cantidad_necesaria (DECIMAL)
- unidad_medida
```

### Tabla: `movimientos_almacen`
```sql
- id_ingrediente
- tipo_movimiento (ingreso/egreso/ajuste)
- cantidad
- stock_anterior
- stock_posterior
- referencia_tipo (compra/venta)
- referencia_id
- usuario_id
- fecha_movimiento
```

---

## 🔧 SERVICIOS IMPLEMENTADOS

### **VentaService**
- `procesarVenta()` - Procesa venta completa con descuento automático
- `validarDisponibilidadIngredientes()` - Verifica stock antes de vender
- `verificarDisponibilidadProducto()` - Chequea si un producto está disponible
- `obtenerCantidadMaximaDisponible()` - Máximas unidades que se pueden vender

### **CompraService**
- `procesarCompra()` - Procesa compra completa con aumento automático de stock
- `validarIngredientes()` - Verifica que los ingredientes existan
- `obtenerIngredientesParaReposicion()` - Lista ingredientes con stock bajo

---

## 🎯 EJEMPLOS PRÁCTICOS

### **Ejemplo 1: Crear Pizza Margherita**
```php
// 1. Crear la pizza
$pizza = ItemMenu::create([
    'nombre' => 'Pizza Margherita',
    'precio_venta' => 25.00
]);

// 2. Definir la receta (ingredientes)
$pizza->ingredientes()->attach([
    1 => ['cantidad_necesaria' => 200, 'unidad_medida' => 'gramos'], // Masa
    2 => ['cantidad_necesaria' => 80, 'unidad_medida' => 'mililitros'], // Salsa
    3 => ['cantidad_necesaria' => 150, 'unidad_medida' => 'gramos'], // Queso
    4 => ['cantidad_necesaria' => 50, 'unidad_medida' => 'gramos'] // Albahaca
]);
```

### **Ejemplo 2: Procesar una Venta**
```php
$ventaService = new VentaService();

$datosVenta = [
    'id_cliente' => 1,
    'descuento' => 0
];

$productos = [
    [
        'id_item_menu' => 1, // Pizza Margherita
        'cantidad' => 2,
        'precio_unitario' => 25.00
    ]
];

// Esto automáticamente:
// 1. Valida que hay stock suficiente
// 2. Crea la venta
// 3. Descuenta ingredientes del almacén
// 4. Registra movimientos
$venta = $ventaService->procesarVenta($datosVenta, $productos);
```

### **Ejemplo 3: Verificar Disponibilidad**
```php
$disponibilidad = $ventaService->verificarDisponibilidadProducto(1, 5);

if ($disponibilidad['disponible']) {
    echo "Se pueden hacer 5 pizzas";
} else {
    echo $disponibilidad['mensaje']; // "Stock insuficiente: Queso (disponible: 200g, requerido: 750g)"
}
```

---

## ⚠️ VALIDACIONES AUTOMÁTICAS

### **En Ventas:**
- ✅ Verifica stock suficiente antes de confirmar
- ✅ Descuenta automáticamente los ingredientes
- ✅ Registra movimientos de almacén
- ✅ Actualiza fechas de último egreso
- ❌ No permite vender si no hay stock

### **En Compras:**
- ✅ Aumenta automáticamente el stock
- ✅ Calcula costo promedio ponderado
- ✅ Registra movimientos de almacén
- ✅ Actualiza fechas de último ingreso

### **En Inventario:**
- ✅ Alertas de stock bajo
- ✅ Control de productos vencidos
- ✅ Trazabilidad completa de movimientos
- ✅ Valorización automática

---

## 🚀 VENTAJAS DEL SISTEMA

1. **Automatización Total**: No hay que descontar manualmente los ingredientes
2. **Control en Tiempo Real**: Stock siempre actualizado
3. **Prevención de Sobreventa**: No se puede vender sin stock
4. **Trazabilidad Completa**: Historial de todos los movimientos
5. **Alertas Inteligentes**: Notifica cuando hay que reponer
6. **Cálculo Automático**: Costos promedio ponderados
7. **Escalabilidad**: Maneja múltiples productos y recetas complejas

---

## 📊 REPORTES DISPONIBLES

- **Inventario Actual**: Stock de todos los ingredientes
- **Stock Bajo**: Ingredientes que necesitan reposición
- **Productos Vencidos**: Control de perecederos
- **Movimientos de Almacén**: Historial completo
- **Valorización**: Valor total del inventario
- **Estadísticas de Ventas**: Productos más vendidos
- **Estadísticas de Compras**: Análisis de proveedores

---

## 🔄 FLUJO DE DATOS RESUMIDO

```
COMPRA → [+] ALMACÉN ← [-] VENTA
         ↑            ↑
    INGREDIENTES → RECETAS → MENÚ → CLIENTE
```

**El sistema garantiza que:**
- Solo se puede vender lo que hay en stock
- El stock se actualiza automáticamente
- Hay trazabilidad completa de todos los movimientos
- Se mantiene control de costos y valorización

---

## 🛠️ PRÓXIMOS PASOS RECOMENDADOS

1. **Probar el formulario de ingredientes** que ya tienes
2. **Crear algunas recetas de pizzas** con sus ingredientes
3. **Registrar compras** para tener stock inicial
4. **Probar ventas** para ver el descuento automático
5. **Revisar reportes** de inventario

¡El sistema está listo para usar! 🎉