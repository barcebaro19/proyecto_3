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
        // Habilitar reporte de errores
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
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
            
            // Cargar el helper de URL si no está cargado
            if (!function_exists('site_url')) {
                $this->load->helper('url');
            }
            
            // Mostrar la vista de login
            $data = [
                'title' => 'Iniciar Sesión',
                'email' => set_value('email')
            ];
            
            $this->load->view('login', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Error en Login/index: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Ocurrió un error al procesar la solicitud. Por favor, intente nuevamente.');
            redirect('login');
        }
    }

    public function authenticate() {
        try {
            log_message('info', 'Intento de autenticación desde la IP: ' . $this->input->ip_address());
            
            // Validar formulario
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                throw new Exception(implode(' ', $errors));
            }
            
            // Obtener y limpiar credenciales
            $email = $this->input->post('email', TRUE);
            $password = $this->input->post('password', TRUE);
            
            log_message('debug', 'Validando credenciales para: ' . $email);
            
            // 1. Verificar si son las credenciales del administrador
            if ($email === $this->admin_email && $password === $this->admin_password) {
                $this->_set_session($this->admin_data);
                log_message('info', 'Acceso de administrador exitoso: ' . $email);
                $this->_redirect_by_role('administrador');
                return;
            }
            
            // 2. Intentar autenticar con la base de datos si está disponible
            if (isset($this->usuario) && is_object($this->usuario)) {
                $usuario = $this->usuario->login($email, $password);
                
                if ($usuario) {
                    $user_data = [
                        'user_id' => $usuario->id_usuario,
                        'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
                        'email' => $usuario->correo,
                        'rol' => $usuario->rol,
                        'logged_in' => TRUE
                    ];
                    
                    $this->_set_session($user_data);
                    log_message('info', 'Acceso de usuario exitoso: ' . $email);
                    $this->_redirect_by_role($usuario->rol);
                    return;
                }
            }
            
            // 3. Si llegamos aquí, las credenciales son incorrectas
            log_message('warning', 'Intento de inicio de sesión fallido para: ' . $email);
            throw new Exception('Correo o contraseña incorrectos');
            
        } catch (Exception $e) {
            log_message('error', 'Error en autenticación: ' . $e->getMessage());
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
        try {
            // Registrar cierre de sesión
            $user_email = $this->session->userdata('email');
            $user_name = $this->session->userdata('nombre');
            
            if ($user_email) {
                log_message('info', "Cierre de sesión - Usuario: $user_email ($user_name)");
            }
            
            // Guardar mensaje de éxito antes de destruir la sesión
            $this->session->set_flashdata('success', 'Has cerrado sesión correctamente.');
            
            // Destruir todos los datos de la sesión
            $this->session->unset_userdata([
                'user_id',
                'nombre',
                'email',
                'rol',
                'logged_in',
                'user_data'
            ]);
            
            // Destruir la sesión
            $this->session->sess_destroy();
            
            // Eliminar la cookie de sesión
            $this->_destroy_session_cookie();
            
            // Redirigir al login
            redirect('login');
            
        } catch (Exception $e) {
            log_message('error', 'Error en Login/logout: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Ocurrió un error al cerrar la sesión.');
            redirect('login');
        }
    }
    
    /**
     * Establece los datos de sesión del usuario
     * 
     * @param array $user_data Datos del usuario para la sesión
     * @return void
     */
    private function _set_session($user_data) {
        $this->session->set_userdata($user_data);
    }
    
    /**
     * Redirige al usuario según su rol
     * 
     * @param string|null $rol Rol del usuario (opcional, si no se proporciona se obtiene de la sesión)
     * @return void
     */
    protected function _redirect_by_role($rol = null) {
        $rol = $rol ?: $this->session->userdata('rol');
        $redirect = $rol ? $rol . '/dashboard' : 'dashboard';
        
        if ($this->es_ajax()) {
            $this->json_exito('Autenticación exitosa', ['redirect' => $redirect]);
        } else {
            redirect($redirect);
        }
    }
    
    /**
     * Destruye la cookie de sesión
     * 
     * @return void
     */
    private function _destroy_session_cookie() {
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
    }
}
