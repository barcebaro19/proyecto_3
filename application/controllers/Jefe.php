<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jefe extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Producto_model', 'Categoria_model', 'Movimiento_model']);
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        
        // Verificar autenticación
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Debe iniciar sesión para acceder a esta sección');
            redirect('login');
        }
        
        // Verificar rol
        if ($this->session->userdata('rol') !== 'jefe') {
            $this->session->set_flashdata('error', 'No tiene permisos para acceder a esta sección');
            redirect($this->session->userdata('rol') . '/dashboard');
        }
    }

    public function index() {
        try {
            log_message('error', 'Jefe Controller: Index iniciado');
            
            // Verificar si el modelo está cargado
            if (!isset($this->Producto_model)) {
                throw new Exception("Producto_model no está cargado");
            }

            $data = [
                'title' => 'Panel del Jefe',
                'total_productos' => 0,
                'productos_bajo_stock' => [],
                'ultimos_movimientos' => []
            ];

            // Intentar cargar datos uno por uno para identificar cuál falla
            try {
                $data['total_productos'] = $this->Producto_model->contar_productos();
            } catch (Exception $e) {
                log_message('error', 'Error contando productos: ' . $e->getMessage());
            }

            try {
                $data['productos_bajo_stock'] = $this->Producto_model->obtener_productos_bajo_stock();
            } catch (Exception $e) {
                log_message('error', 'Error obteniendo stock bajo: ' . $e->getMessage());
            }

            try {
                $data['ultimos_movimientos'] = $this->Movimiento_model->get_ultimos_movimientos(5);
            } catch (Exception $e) {
                log_message('error', 'Error obteniendo movimientos: ' . $e->getMessage());
            }
            
            $this->load->view('jefe/header', $data);
            $this->load->view('jefe/dashboard', $data);
            $this->load->view('jefe/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error fatal en Jefe/index: ' . $e->getMessage());
            echo "<h1>Error del Sistema</h1>";
            echo "<p>Ocurrió un error al cargar el dashboard: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }

    // Alias para que /jefe/dashboard funcione igual que /jefe
    public function dashboard() {
        $this->index();
    }

    // Gestión de Productos (solo visualización)
    public function productos() {
        $data['productos'] = $this->Producto_model->obtener_todos();
        $data['title'] = 'Productos';
        
        $this->load->view('jefe/header', $data);
        $this->load->view('jefe/productos/index', $data);
        $this->load->view('jefe/footer');
    }

    public function ver_producto($id) {
        $data['producto'] = $this->Producto_model->obtener_por_id($id);
        $data['movimientos'] = $this->Producto_model->obtener_movimientos_producto($id);
        $data['title'] = 'Detalles del Producto';
        
        $this->load->view('jefe/header', $data);
        $this->load->view('jefe/productos/ver', $data);
        $this->load->view('jefe/footer');
    }

    // Reportes
    public function reportes() {
        $data['title'] = 'Reportes';
        $data['productos_mas_vendidos'] = $this->Producto_model->obtener_mas_vendidos();
        $data['productos_bajo_stock'] = $this->Producto_model->obtener_productos_bajo_stock();
        
        $this->load->view('jefe/header', $data);
        $this->load->view('jefe/reportes/index', $data);
        $this->load->view('jefe/footer');
    }

    // Perfil de usuario
    public function perfil() {
        $this->load->model('Usuario_model');
        
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('correo', 'Correo', 'required|valid_email');
        
        if ($this->form_validation->run() === FALSE) {
            $data['usuario'] = $this->Usuario_model->obtener_por_id($this->session->userdata('user_id'));
            $data['title'] = 'Mi Perfil';
            
            $this->load->view('jefe/header', $data);
            $this->load->view('jefe/perfil', $data);
            $this->load->view('jefe/footer');
        } else {
            $usuario_data = [
                'nombre' => $this->input->post('nombre'),
                'apellido' => $this->input->post('apellido'),
                'telefono' => $this->input->post('telefono'),
                'correo' => $this->input->post('correo')
            ];
            
            // Actualizar contraseña solo si se proporciona una nueva
            if ($this->input->post('nueva_contrasena')) {
                $usuario_data['contrasena'] = password_hash($this->input->post('nueva_contrasena'), PASSWORD_BCRYPT);
            }
            
            $this->Usuario_model->actualizar($this->session->userdata('user_id'), $usuario_data);
            $this->session->set_flashdata('success', 'Perfil actualizado exitosamente');
            redirect('jefe/perfil');
        }
    }
}
