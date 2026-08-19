# ImpMartínez - Sistema de Gestión de Servicio Técnico

Sistema web para gestión de cadena de servicio técnico de dispositivos electrónicos (celulares, laptops, PC, TVs, radios).

## Stack Tecnológico
- **Backend:** PHP (XAMPP)
- **Base de datos:** MySQL
- **Frontend:** HTML, CSS, JavaScript nativo
- **Colores:** Rojo, blanco y negro

## Roles del Sistema
1. **Recepcionista** - Registro de clientes, equipos y diagnóstico inicial con fotos
2. **Técnico** - Gestión de trabajos asignados (máx. 4), reporte de inicio y notas técnicas
3. **Administrador de Sucursal** - Asignación de equipos entre sucursales, gestión de usuarios
4. **Jefe Técnico** - Distribución de trabajos a técnicos de su sucursal
5. **Almacenista** - Inventario de repuestos, movimientos y pedidos
6. **Gerente** - Vista global de todas las sucursales y procesos
7. **Recursos Humanos** - Asistencia, inspecciones de limpieza/uniforme y productividad

## Instalación
1. Copiar la carpeta `impMartines` en `C:\xampp\htdocs\`
2. Iniciar Apache y MySQL desde XAMPP
3. Importar `database.sql` en phpMyAdmin o ejecutar con MySQL CLI
4. Acceder a `http://localhost/impMartines/`

## Usuarios de Prueba (Apellido / Carnet)
| Rol | Apellido | Carnet |
|-----|----------|--------|
| Gerente | Admin | 0001 |
| RRHH | Admin | 0002 |
| Admin Sucursal 1 | Sucursal1 | 1001 |
| Admin Sucursal 2 | Sucursal2 | 1002 |
| Admin Sucursal 3 | Sucursal3 | 1003 |

## Flujo de Trabajo
1. Recepcionista registra cliente y equipo → pasa a estado "pendiente_asignacion"
2. Administrador de sucursal decide a qué sucursal asignar el equipo
3. Jefe Técnico de la sucursal destino distribuye trabajos a técnicos (máx. 4 por técnico)
4. Técnico reporta recepción, inicio de reparación y notas técnicas
5. Al completar, el equipo queda disponible para entrega al cliente
