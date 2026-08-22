<?php
class Controller {
    protected function model($model) {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
