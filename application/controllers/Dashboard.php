<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Verificar autenticación
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Debe iniciar sesión para acceder a esta sección');
            redirect('login');
        }
    }
    
    public function index() {
        // Obtener el rol del usuario
        $rol = $this->session->userdata('rol');
        
        // Mapeo de roles a rutas
        $routes = [
            'administrador' => 'admin/dashboard',
            'jefe' => 'jefe',
            'operario' => 'operario/dashboard'
        ];
        
        // Redirigir según el rol
        if (isset($routes[$rol])) {
            redirect($routes[$rol]);
        } else {
            // Si no tiene un rol válido, cerrar sesión
            $this->session->set_flashdata('error', 'Rol de usuario no válido');
            redirect('logout');
        }
    }
}
