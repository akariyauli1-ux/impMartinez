<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Sucursal.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/EquipoFoto.php';

class ImagenController extends Controller {
    public function logo() {
        $sucursalModel = new Sucursal();
        $logo = $sucursalModel->obtenerLogoEmpresa();
        
        if ($logo && !empty($logo['logo_empresa_data'])) {
            header('Content-Type: ' . $logo['logo_empresa_tipo']);
            echo $logo['logo_empresa_data'];
        } else {
            http_response_code(404);
        }
        exit;
    }
    
    public function fotoUsuario() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            exit;
        }
        
        $usuarioModel = new Usuario();
        $foto = $usuarioModel->obtenerFoto($id);
        
        if ($foto && !empty($foto['foto_data'])) {
            header('Content-Type: ' . $foto['foto_tipo']);
            echo $foto['foto_data'];
        } else {
            http_response_code(404);
        }
        exit;
    }
    
    public function fotoEquipo() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            exit;
        }
        
        $equipoFotoModel = new EquipoFoto();
        $foto = $equipoFotoModel->obtenerFoto($id);
        
        if ($foto && !empty($foto['foto_data'])) {
            header('Content-Type: ' . $foto['foto_tipo']);
            echo $foto['foto_data'];
        } else {
            http_response_code(404);
        }
        exit;
    }
    
    public function fotosEquipo() {
        $equipo_id = $_GET['equipo_id'] ?? null;
        if (!$equipo_id) {
            http_response_code(404);
            exit;
        }
        
        $equipoFotoModel = new EquipoFoto();
        $fotos = $equipoFotoModel->obtenerPorEquipo($equipo_id);
        
        header('Content-Type: application/json');
        echo json_encode($fotos);
        exit;
    }
}
