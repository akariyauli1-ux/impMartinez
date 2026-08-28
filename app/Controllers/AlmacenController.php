<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Repuesto.php';
require_once __DIR__ . '/../Models/MovimientoInventario.php';
require_once __DIR__ . '/../Models/PedidoRepuesto.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Sucursal.php';
require_once __DIR__ . '/../Models/AlmacenAuditoria.php';

class AlmacenController extends Controller {
    private $repuestoModel;
    private $movimientoModel;
    private $pedidoModel;
    private $usuarioModel;
    private $sucursalModel;
    private $auditoriaModel;
    
    public function __construct() {
        $this->repuestoModel = new Repuesto();
        $this->movimientoModel = new MovimientoInventario();
        $this->pedidoModel = new PedidoRepuesto();
        $this->usuarioModel = new Usuario();
        $this->sucursalModel = new Sucursal();
        $this->auditoriaModel = new AlmacenAuditoria();
        $this->verificarSesion();
        $this->verificarRol(['almacenista']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function dashboard() {
        $sucursal_id = $_SESSION['sucursal_id'];
        
        $total_repuestos = count($this->repuestoModel->obtenerPorSucursal($sucursal_id));
        $stock_bajo = $this->repuestoModel->obtenerStockBajo($sucursal_id);
        $mas_solicitados = $this->repuestoModel->obtenerMasSolicitados(5);
        $pedidos_sucursal = $this->repuestoModel->obtenerPedidosPorSucursal();
        
        $this->view('almacen/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'total_repuestos' => $total_repuestos,
            'stock_bajo' => $stock_bajo,
            'mas_solicitados' => $mas_solicitados,
            'pedidos_sucursal' => $pedidos_sucursal
        ]);
    }
    
    public function inventario() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        $categorias = $this->repuestoModel->obtenerCategorias();
        $marcas = $this->repuestoModel->obtenerMarcas();
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $categoriasPredefinidas = ['Camara', 'Pantallas', 'Audio', 'Boton Flex', 'Baterias', 'Cargadores', 'Carcazas', 'Conectores'];
        $categoriasExistentes = array_column($categorias ?? [], 'categoria');
        $todasCategorias = array_unique(array_merge($categoriasPredefinidas, $categoriasExistentes));
        sort($todasCategorias);
        
        $this->view('almacen/inventario', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'sucursales' => $sucursales,
            'todasCategorias' => $todasCategorias
        ]);
    }
    
    public function guardarRepuesto() {
        $categoria = $_POST['categoria'];
        if ($categoria === '__nueva__' && !empty($_POST['nueva_categoria'])) {
            $categoria = $_POST['nueva_categoria'];
        }
        
        $data = [
            'codigo' => $_POST['codigo'],
            'nombre' => $_POST['nombre'],
            'marca' => $_POST['marca'] ?? '',
            'clave_producto' => $_POST['clave_producto'] ?? '',
            'categoria' => $categoria,
            'stock' => $_POST['stock'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 5,
            'precio_unitario' => $_POST['precio_unitario'] ?? 0,
            'unidades_disponibles' => $_POST['stock'] ?? 0,
            'sucursal_id' => $_SESSION['sucursal_id']
        ];
        
        $nuevo_id = $this->repuestoModel->crear($data);
        
        // Registrar en auditoría
        $this->auditoriaModel->registrar(
            $_SESSION['usuario_id'],
            'crear',
            'repuestos',
            $nuevo_id,
            'Creó nuevo repuesto: ' . $_POST['nombre'] . ' (Código: ' . $_POST['codigo'] . ')',
            null,
            $data
        );
        
        $this->redirect('almacen/inventario');
    }
    
    public function actualizarRepuesto() {
        $id = $_POST['id'];
        $categoria = $_POST['categoria'] ?? '';
        if ($categoria === '__nueva__' && !empty($_POST['nueva_categoria'])) {
            $categoria = $_POST['nueva_categoria'];
        }
        
        // Obtener datos antiguos para auditoría
        $datos_antiguos = $this->repuestoModel->obtenerPorId($id);
        
        $data = [
            'codigo' => $_POST['codigo'],
            'nombre' => $_POST['nombre'],
            'marca' => $_POST['marca'] ?? '',
            'clave_producto' => $_POST['clave_producto'] ?? '',
            'categoria' => $categoria,
            'stock' => $_POST['stock'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 5,
            'precio_unitario' => $_POST['precio_unitario'] ?? 0,
            'unidades_disponibles' => $_POST['stock'] ?? 0,
            'descontinuado' => isset($_POST['descontinuado']) ? 1 : 0
        ];
        
        $this->repuestoModel->actualizar($id, $data);
        
        // Registrar en auditoría
        $this->auditoriaModel->registrar(
            $_SESSION['usuario_id'],
            'editar',
            'repuestos',
            $id,
            'Editó repuesto: ' . $_POST['nombre'] . ' (ID: ' . $id . ')',
            $datos_antiguos,
            $data
        );
        
        $this->redirect('almacen/inventario');
    }
    
    public function pedidos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        $pedidos = $this->pedidoModel->obtenerPorSucursal($sucursal_id);
        $tecnicos = $this->usuarioModel->obtenerTecnicosPorSucursal($sucursal_id);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('almacen/pedidos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos,
            'pedidos' => $pedidos,
            'tecnicos' => $tecnicos,
            'sucursales' => $sucursales
        ]);
    }
    
    public function guardarPedido() {
        $repuesto_id = $_POST['repuesto_id'];
        $cantidad = $_POST['cantidad'];
        $tecnico_id = $_POST['tecnico_id'];
        
        $repuesto = $this->repuestoModel->obtenerPorId($repuesto_id);
        $precio_unitario = $repuesto['precio_unitario'];
        $total = $precio_unitario * $cantidad;
        
        $pedido_id = $this->pedidoModel->crear([
            'sucursal_id' => $_SESSION['sucursal_id'],
            'repuesto_id' => $repuesto_id,
            'tecnico_id' => $tecnico_id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total,
            'solicitado_por' => $_SESSION['usuario_id'],
            'estado' => 'solicitado'
        ]);
        
        $this->repuestoModel->incrementarSolicitudes($repuesto_id, $cantidad);
        $this->repuestoModel->actualizarStock($repuesto_id, $cantidad, 'resta');
        
        // Registrar en auditoría
        $this->auditoriaModel->registrar(
            $_SESSION['usuario_id'],
            'enviar_pedido',
            'pedidos_repuestos',
            $pedido_id,
            'Envió pedido de ' . $cantidad . ' unidad(es) de ' . $repuesto['nombre'] . ' al técnico ID: ' . $tecnico_id,
            null,
            ['repuesto_id' => $repuesto_id, 'cantidad' => $cantidad, 'tecnico_id' => $tecnico_id, 'total' => $total]
        );
        
        $this->redirect('almacen/pedidos');
    }
    
    public function toggleDescontinuado() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $repuesto = $this->repuestoModel->obtenerPorId($id);
            $nuevo_estado = $repuesto['descontinuado'] ? 0 : 1;
            $this->repuestoModel->actualizar($id, ['descontinuado' => $nuevo_estado]);
            
            // Registrar en auditoría
            $this->auditoriaModel->registrar(
                $_SESSION['usuario_id'],
                'editar',
                'repuestos',
                $id,
                ($nuevo_estado ? 'Habilitó' : 'Descontinuó') . ' repuesto: ' . $repuesto['nombre'],
                ['descontinuado' => !$nuevo_estado],
                ['descontinuado' => $nuevo_estado]
            );
        }
        $this->redirect('almacen/inventario');
    }
    
    public function editarCategoria() {
        $categoria_actual = $_POST['categoria_actual'] ?? '';
        $categoria_nueva = $_POST['categoria_nueva'] ?? '';
        $sucursal_id = $_SESSION['sucursal_id'];
        
        if ($categoria_actual && $categoria_nueva) {
            $this->repuestoModel->editarCategoria($categoria_actual, $categoria_nueva, $sucursal_id);
            
            // Registrar en auditoría
            $this->auditoriaModel->registrar(
                $_SESSION['usuario_id'],
                'editar',
                'categorias',
                null,
                'Editó categoría: "' . $categoria_actual . '" → "' . $categoria_nueva . '"'
            );
        }
        $this->redirect('almacen/inventario');
    }
    
    public function eliminarCategoria() {
        $categoria = $_POST['categoria'] ?? '';
        $sucursal_id = $_SESSION['sucursal_id'];
        
        if ($categoria) {
            $this->repuestoModel->eliminarCategoria($categoria, $sucursal_id);
            
            // Registrar en auditoría
            $this->auditoriaModel->registrar(
                $_SESSION['usuario_id'],
                'eliminar',
                'categorias',
                null,
                'Eliminó categoría: "' . $categoria . '"'
            );
        }
        $this->redirect('almacen/inventario');
    }
    
    public function historial() {
        $historial = $this->auditoriaModel->obtenerHistorial(200);
        
        $this->view('almacen/historial', [
            'usuario' => $this->obtenerUsuarioActual(),
            'historial' => $historial
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
