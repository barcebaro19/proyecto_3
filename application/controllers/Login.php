<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {
    
    // Métodos que no requieren autenticación
    protected $metodos_sin_auth = ['index', 'authenticate', 'logout'];
    
    // No requerir autenticación para este controlador
    protected $sin_auth = true;

    // Credenciales de administrador por defecto
    private $admin_email = 'admin@bodega.com';
    private $admin_password = 'admin123';
    private $admin_data = [
        'user_id' => 1,
        'nombre' => 'Administrador',
        'email' => 'admin@bodega.com',
        'rol' => 'administrador',
        'logged_in' => TRUE
    ];

    public function __construct() {
        parent::__construct();
        
        // Configurar reglas de validación
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');
        
        // Cargar modelo de usuario
        $this->load->model('Usuario_model', 'usuario');
    }

    public function index() {
        // Verificar si ya hay una sesión activa
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
            return;
        }
        
        // Verificar si se envió el formulario
        if ($this->input->post() && $this->form_validation->run() === TRUE) {
            $this->authenticate();
            return;
        }
        
        $data = [
            'error' => $this->session->flashdata('error'),
            'email' => $this->session->flashdata('email')
        ];
        
        $this->load->view('login', $data);
    }

    public function authenticate() {
        try {
            // Validar formulario
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                throw new Exception(implode(' ', $errors));
            }
            
            // Obtener y limpiar credenciales
            $email = $this->input->post('email', TRUE);
            $password = $this->input->post('password', TRUE);
            
            // 1. Verificar si son las credenciales del administrador (HARDCODED)
            if ($email === $this->admin_email && $password === $this->admin_password) {
                $this->_set_session($this->admin_data);
                $this->_redirect_by_role('administrador');
                return;
            }
            
            // 2. Intentar autenticar con la base de datos
            $usuario = $this->usuario->verificar_credenciales($email, $password);
            
            if ($usuario) {
                // Normalizar el rol para evitar problemas de mayúsculas/espacios
                $nombre_rol = strtolower($usuario->rol);
                $rol_slug = $nombre_rol;
                
                if (strpos($nombre_rol, 'jefe') !== false) {
                    $rol_slug = 'jefe';
                } elseif (strpos($nombre_rol, 'admin') !== false) {
                    $rol_slug = 'administrador';
                } elseif (strpos($nombre_rol, 'operario') !== false) {
                    $rol_slug = 'operario';
                }

                $user_data = [
                    'user_id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
                    'email' => $usuario->correo,
                    'rol' => $rol_slug, // Usamos el rol normalizado
                    'rol_original' => $usuario->rol, // Guardamos el original por si acaso
                    'logged_in' => TRUE
                ];
                
                $this->_set_session($user_data);
                $this->_redirect_by_role($rol_slug);
                return;
            }
            
            // 3. Si llegamos aquí, las credenciales son incorrectas
            throw new Exception('Correo o contraseña incorrectos');
            
        } catch (Exception $e) {
            $this->session->set_flashdata('email', $this->input->post('email'));
            $this->session->set_flashdata('error', $e->getMessage());
            
            if ($this->es_ajax()) {
                $this->json_error($e->getMessage());
            } else {
                redirect('login');
            }
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    protected function _set_session($data) {
        $this->session->set_userdata($data);
    }

    protected function _redirect_by_role($rol = null) {
        $rol = $rol ?: $this->session->userdata('rol');
        
        // Mapeo de roles a rutas
        $routes = [
            'administrador' => 'admin/dashboard',
            'jefe' => 'jefe',
            'operario' => 'operario/dashboard'
        ];
        
        $redirect = isset($routes[$rol]) ? $routes[$rol] : 'dashboard';
        
        if ($this->es_ajax()) {
            $this->json_exito('Autenticación exitosa', ['redirect' => $redirect]);
        } else {
            redirect($redirect);
        }
    }
}
