<?php
class Controller {
    protected function model($model) {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($view, $data = []) {
        // Extraer las variables para que estén disponibles en la vista
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/public/' . ltrim($url, '/'));
        exit;
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function verificarRol($roles_requeridos) {
        // Usar el rol activo seleccionado por el usuario
        $rol_activo = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'] ?? null;
        
        if ($rol_activo && in_array($rol_activo, $roles_requeridos)) {
            return true;
        }
        
        $this->redirect('');
        return false;
    }
}
