<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/PedidoRepuesto.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/SolicitudComponente.php';

class NotificacionController extends Controller {
    
    public function verificar() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->json(['notificaciones' => []]);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $rol = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'] ?? '';
        $sucursal_id = $_SESSION['sucursal_id'] ?? null;
        
        $notificaciones = [];
        
        $pedidoModel = new PedidoRepuesto();
        $equipoModel = new Equipo();
        $solicitudModel = new SolicitudComponente();
        
        if ($rol === 'almacenista') {
            $cantidad_pedidos = $pedidoModel->contarPendientesAlmacen();
            if ($cantidad_pedidos > 0) {
                $notificaciones[] = [
                    'tipo' => 'pedidos',
                    'cantidad' => $cantidad_pedidos,
                    'mensaje' => "$cantidad_pedidos pedido(s) pendiente(s) de respuesta",
                    'url' => APP_URL . '/public/pedidos/almacen',
                    'icono' => '📦'
                ];
            }
            
            $solicitudes_pendientes = $solicitudModel->contarPendientesAlmacen();
            if ($solicitudes_pendientes > 0) {
                $notificaciones[] = [
                    'tipo' => 'solicitudes',
                    'cantidad' => $solicitudes_pendientes,
                    'mensaje' => "$solicitudes_pendientes solicitud(es) de componente(s) pendiente(s) de entregar",
                    'url' => APP_URL . '/public/pedidos/almacen',
                    'icono' => '🔧'
                ];
            }
        } else {
            $cantidad_pedidos = $pedidoModel->contarPendientesConfirmacion($usuario_id);
            if ($cantidad_pedidos > 0) {
                $notificaciones[] = [
                    'tipo' => 'pedidos',
                    'cantidad' => $cantidad_pedidos,
                    'mensaje' => "$cantidad_pedidos respuesta(s) de almacen pendiente(s) de confirmacion",
                    'url' => APP_URL . '/public/pedidos',
                    'icono' => '📦'
                ];
            }
        }
        
        if ($rol === 'tecnico') {
            $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaTecnico($usuario_id);
            if ($trabajos_nuevos > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_nuevos,
                    'mensaje' => "$trabajos_nuevos trabajo(s) nuevo(s) asignado(s)",
                    'url' => APP_URL . '/public/tecnico/mis-trabajos',
                    'icono' => '🔧'
                ];
            }
            
            $solicitudes_enviadas = $solicitudModel->contarEnviadasTecnico($usuario_id);
            if ($solicitudes_enviadas > 0) {
                $notificaciones[] = [
                    'tipo' => 'componentes',
                    'cantidad' => $solicitudes_enviadas,
                    'mensaje' => "$solicitudes_enviadas componente(s) enviado(s) por almacen - Confirma recepción",
                    'url' => APP_URL . '/public/tecnico/mis-trabajos',
                    'icono' => '📦'
                ];
            }
        } elseif ($rol === 'jefe_tecnico') {
            $trabajos_pendientes = $equipoModel->contarTrabajosPendientesAsignarJefe($sucursal_id);
            if ($trabajos_pendientes > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_pendientes,
                    'mensaje' => "$trabajos_pendientes trabajo(s) pendiente(s) de asignar a tecnico",
                    'url' => APP_URL . '/public/jefe-tecnico/asignar-tecnicos',
                    'icono' => '👷'
                ];
            }
        } elseif ($rol === 'recepcionista') {
            $trabajos_completados = $equipoModel->contarTrabajosCompletadosParaRecepcion($sucursal_id);
            if ($trabajos_completados > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_completados,
                    'mensaje' => "$trabajos_completados trabajo(s) completado(s) listo(s) para entrega",
                    'url' => APP_URL . '/public/recepcion/equipos-listos',
                    'icono' => '✅'
                ];
            }
        } elseif ($rol === 'admin_sucursal') {
            $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaAdmin($sucursal_id);
            if ($trabajos_nuevos > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_nuevos,
                    'mensaje' => "$trabajos_nuevos equipo(s) nuevo(s) pendiente(s) de asignar sucursal",
                    'url' => APP_URL . '/public/admin-sucursal/pendientes',
                    'icono' => '📥'
                ];
            }
        }
        
        $this->json(['notificaciones' => $notificaciones]);
    }
}
