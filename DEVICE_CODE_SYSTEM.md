# Sistema de Generación Automática de Códigos de Equipos

## Descripción General

Este documento explica cómo funciona el sistema de generación automática de códigos para equipos con el prefijo `EQ-` en la aplicación de mantenimiento.

## Arquitectura

### Backend (Laravel)

**Archivo:** `app/Http/Controllers/DeviceController.php`

El controlador `DeviceController` maneja la creación de dispositivos con el siguiente flujo:

1. **Validación de datos** (sin requerir el campo `code`):
   - `type` (requerido)
   - `brand` (requerido)
   - `model` (requerido)
   - `status` (requerido)
   - `assigned_to` (opcional)

2. **Creación del registro**:
   ```php
   $device = Device::create($validated);
   ```

3. **Generación automática del código**:
   ```php
   $code = "EQ-" . $device->id;
   $device->update(['code' => $code]);
   ```

4. **Retorno de la respuesta** con el código generado

### Modelo (Laravel)

**Archivo:** `app/Models/Device.php`

El modelo incluye un sistema de respaldo con el evento `boot()`:

```php
protected static function boot()
{
    parent::boot();
    
    static::created(function ($device) {
        if (!$device->code) {
            $device->update(['code' => "EQ-" . $device->id]);
        }
    });
}
```

Esto asegura que si por alguna razón el código no se genera en el controlador, se genera automáticamente en el evento `created` del modelo.

### Migraciones (Laravel)

**Archivo:** `database/migrations/0001_01_01_000003_create_devices_table.php`

El campo `code` es:
- `nullable` (inicialmente puede ser null para permitir la generación después del insert)
- `unique` (asegura que no hay códigos duplicados)

```php
$table->string('code')->nullable()->unique();
```

### Frontend (Angular)

**Servicio:** `src/app/core/services/device.service.ts`

El servicio expone métodos HTTP para CRUD:

```typescript
create(data: Omit<DeviceResponse, 'id' | 'code' | 'created_at' | 'updated_at'>): Observable<DeviceResponse>
```

**Nota importante**: El método `create()` NO incluye el campo `code`, ya que es generado por el backend.

### Componente

**Archivo:** `src/app/pages/devices/devices.component.ts`

El componente:

1. **Cargar dispositivos** en `ngOnInit()`:
   ```typescript
   ngOnInit() {
     this.loadDevices();
   }
   ```

2. **Crear dispositivo** sin incluir código:
   ```typescript
   const deviceData = {
     type: this.newEquipment.type,
     brand: this.newEquipment.brand,
     model: this.newEquipment.model,
     status: this.newEquipment.status,
     assigned_to: this.newEquipment.assigned_to || null,
   };
   
   this.deviceService.create(deviceData).subscribe({
     next: (device) => {
       // El dispositivo retornado ya tiene el código generado
       this.equipments.push(device);
     }
   });
   ```

### Formulario HTML

**Archivo:** `src/app/pages/devices/devices.component.html`

El formulario:

1. **No muestra campo de entrada** para el código
2. **Muestra mensaje informativo**:
   - En creación: "El código será generado automáticamente al guardar"
   - En edición: Muestra el código generado de forma read-only

## Flujo Completo

```
Usuario abre modal "Nuevo Equipo"
                ↓
Ingresa: tipo, marca, modelo, estado
                ↓
Click en "Guardar"
                ↓
Frontend valida datos (sin código)
                ↓
Llamada HTTP POST /devices
                ↓
Backend recibe datos sin código
                ↓
Backend crea registro en BD
                ↓
Backend obtiene ID insertado
                ↓
Backend genera código: "EQ-{ID}"
                ↓
Backend actualiza código en BD
                ↓
Backend retorna dispositivo con código
                ↓
Frontend recibe código "EQ-1"
                ↓
Tabla se actualiza automáticamente
                ↓
Código visible en tabla y detalles
```

## Ventajas del Sistema

1. **Código único y predecible**: Basado en el ID de la base de datos
2. **No requiere lógica del cliente**: El backend genera el código
3. **Evita colisiones**: La unicidad se valida en la BD
4. **User-friendly**: El usuario no ve campos confusos
5. **Escalable**: Funciona con cualquier número de dispositivos
6. **Respaldo**: El modelo Laravel tiene un evento de respaldo

## Validación

El campo `code` tiene validación en el backend:

```php
$table->string('code')->nullable()->unique();
```

Esto asegura:
- Cada código es único
- No puede haber duplicados
- La BD rechaza intentos de insertar códigos duplicados

## API Response

Cuando se crea un dispositivo, el backend retorna:

```json
{
  "id": 1,
  "code": "EQ-1",
  "type": "PC",
  "brand": "Dell",
  "model": "Optiplex 7050",
  "status": "Sin asignación",
  "assigned_to": null,
  "created_at": "2026-05-15T10:30:00.000000Z",
  "updated_at": "2026-05-15T10:30:00.000000Z"
}
```

## Ejemplos de Códigos Generados

- Dispositivo con ID 1 → Código: `EQ-1`
- Dispositivo con ID 25 → Código: `EQ-25`
- Dispositivo con ID 150 → Código: `EQ-150`
- Dispositivo con ID 1000 → Código: `EQ-1000`

## Futuras Mejoras

1. **Prefijo configurable**: Permitir cambiar "EQ-" por otro prefijo
2. **Formato personalizado**: Por ejemplo, `EQ-2026-0001` (con año y secuencia)
3. **Generación de código antes de insertar**: Usar un evento `creating` para generar el código antes de insertar
4. **UI mejorada**: Mostrar vista previa del código que se generará

## Troubleshooting

### El código no aparece después de crear

**Solución**: Verificar que:
1. El backend está ejecutando correctamente
2. No hay errores en los logs de Laravel
3. La tabla `devices` existe con la columna `code`
4. El evento `created` del modelo se ejecuta correctamente

### Código duplicado

**Solución**: Esto no debería suceder porque:
1. La BD tiene restricción UNIQUE
2. El modelo genera código basado en el ID (que es único)
3. Si esto ocurre, revisar los logs de error

### Frontend no actualiza después de crear

**Solución**: Verificar:
1. La llamada HTTP al backend es correcta
2. El backend retorna el código en la respuesta
3. El frontend procesa correctamente la respuesta
4. No hay errores en la consola del navegador
