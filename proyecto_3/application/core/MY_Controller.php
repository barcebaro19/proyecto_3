<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador Base para la aplicación
 * 
 * Este controlador proporciona funcionalidades comunes a todos los controladores
 * como autenticación, autorización y manejo de respuestas.
 */
class MY_Controller extends CI_Controller {
    
    /**
     * Datos comunes para las vistas
     * @var array
     */
    protected $data = [];
    
    /**
     * Usuario actual
     * @var object
     */
    protected $usuario_actual = null;
    
    /**
     * Roles permitidos para acceder al controlador
     * Si está vacío, cualquier usuario autenticado puede acceder
     * @var array
     */
    protected $roles_permitidos = [];
    
    /**
     * Métodos que no requieren autenticación
     * @var array
     */
    protected $metodos_sin_auth = [];
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        
        // Cargar helpers y librerías comunes
        $this->load->helper(['url', 'form', 'security']);
        $this->load->library(['session', 'form_validation']);
        
        // Cargar modelos comunes
        $this->load->model('Usuario_model', 'usuario_model');
        
        // Inicializar datos comunes
        $this->init_common_data();
        
        // Verificar autenticación
        $this->check_auth();
    }
    
    /**
     * Inicializa datos comunes para todas las vistas
     */
    protected function init_common_data() {
        // Información del usuario actual
        if ($this->session->userdata('logged_in')) {
            $this->usuario_actual = (object)[
                'id' => $this->session->userdata('user_id'),
                'nombres' => $this->session->userdata('nombres'),
                'apellidos' => $this->session->userdata('apellidos'),
                'email' => $this->session->userdata('email'),
                'rol' => $this->session->userdata('rol')
            ];
            
            $this->data['usuario_actual'] = $this->usuario_actual;
        }
        
        // Configuración del sitio
        $this->data['site_title'] = 'Sistema de Gestión';
        $this->data['page_title'] = '';
        
        // Mensajes flash
        $this->data['mensaje_error'] = $this->session->flashdata('error');
        $this->data['mensaje_exito'] = $this->session->flashdata('success');
        $this->data['mensaje_info'] = $this->session->flashdata('info');
        $this->data['mensaje_advertencia'] = $this->session->flashdata('warning');
    }
    
    /**
     * Verifica la autenticación del usuario
     */
    protected function check_auth() {
        $metodo_actual = $this->router->fetch_method();
        
        // Si el método actual no requiere autenticación, continuar
        if (in_array($metodo_actual, $this->metodos_sin_auth)) {
            return;
        }
        
        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Debe iniciar sesión para acceder a esta página');
            redirect('auth/login');
        }
        
        // Si no hay roles específicos definidos, cualquier usuario autenticado puede acceder
        if (empty($this->roles_permitidos)) {
            return;
        }
        
        // Verificar si el rol del usuario está permitido
        $rol_usuario = $this->session->userdata('rol');
        if (!in_array($rol_usuario, $this->roles_permitidos)) {
            $this->session->set_flashdata('error', 'No tiene permisos para acceder a esta sección');
            redirect('inicio');
        }
    }
    
    /**
     * Carga una vista con el layout correspondiente
     * 
     * @param string $vista Nombre de la vista a cargar
     * @param array $data Datos adicionales para la vista
     * @param bool $cargar_vista Si es falso, solo devuelve la vista como string
     * @return mixed
     */
    protected function cargar_vista($vista, $data = [], $cargar_vista = true) {
        // Combinar datos adicionales con los datos comunes
        $data = array_merge($this->data, $data);
        
        // Determinar el layout según el rol del usuario
        $rol = $this->session->userdata('rol');
        $layout_path = 'layouts/';
        
        // Verificar si existe el layout específico del rol, si no, usar el default
        $header_path = $layout_path . $rol . '/header';
        $footer_path = $layout_path . $rol . '/footer';
        
        // Si no existe el header específico del rol, usar el header por defecto
        if (!file_exists(APPPATH . 'views/' . $header_path . '.php')) {
            $header_path = $layout_path . 'header';
        }
        
        // Si no existe el footer específico del rol, usar el footer por defecto
        if (!file_exists(APPPATH . 'views/' . $footer_path . '.php')) {
            $footer_path = $layout_path . 'footer';
        }
        
        if ($cargar_vista) {
            $this->load->view($header_path, $data);
            $this->load->view($vista, $data);
            $this->load->view($footer_path, $data);
            return true;
        }
        
        // Devolver la vista como string
        return $this->load->view($vista, $data, true);
    }
    
    /**
     * Devuelve una respuesta JSON estandarizada
     * 
     * @param bool $exito Indica si la operación fue exitosa
     * @param string $mensaje Mensaje de respuesta
     * @param mixed $datos Datos adicionales para incluir en la respuesta
     * @param int $codigo_http Código de estado HTTP
     * @return void
     */
    protected function json_response($exito, $mensaje = '', $datos = null, $codigo_http = 200) {
        $this->output
            ->set_status_header($codigo_http)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'exito' => $exito,
                'mensaje' => $mensaje,
                'datos' => $datos,
                'tiempo' => date('Y-m-d H:i:s')
            ]));
    }
    
    /**
     * Devuelve una respuesta de éxito en formato JSON
     * 
     * @param string $mensaje Mensaje de éxito
     * @param mixed $datos Datos adicionales
     * @param int $codigo_http Código de estado HTTP (por defecto 200)
     * @return void
     */
    protected function json_exito($mensaje = 'Operación exitosa', $datos = null, $codigo_http = 200) {
        $this->json_response(true, $mensaje, $datos, $codigo_http);
    }
    
    /**
     * Devuelve una respuesta de error en formato JSON
     * 
     * @param string $mensaje Mensaje de error
     * @param mixed $errores Detalles del error
     * @param int $codigo_http Código de estado HTTP (por defecto 400)
     * @return void
     */
    protected function json_error($mensaje = 'Ha ocurrido un error', $errores = null, $codigo_http = 400) {
        $this->json_response(false, $mensaje, ['errores' => $errores], $codigo_http);
    }
    
    /**
     * Valida los datos de entrada según las reglas proporcionadas
     * 
     * @param array $reglas Reglas de validación
     * @return bool|array TRUE si la validación es exitosa, array de errores si falla
     */
    protected function validar_entrada($reglas) {
        $this->form_validation->set_rules($reglas);
        
        if ($this->form_validation->run() === FALSE) {
            return [
                'exito' => false,
                'errores' => $this->form_validation->error_array()
            ];
        }
        
        return ['exito' => true];
    }
    
    /**
     * Verifica si la petición es de tipo AJAX
     * 
     * @return bool
     */
    protected function es_ajax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Maneja los errores de la aplicación
     * 
     * @param Exception $e Excepción capturada
     * @param string $mensaje Mensaje personalizado (opcional)
     */
    protected function manejar_error($e, $mensaje = 'Ha ocurrido un error inesperado') {
        log_message('error', $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
        
        if ($this->es_ajax()) {
            $this->json_error($mensaje, null, 500);
        } else {
            $this->session->set_flashdata('error', $mensaje);
            redirect('inicio');
        }
        
        exit;
    }
}
