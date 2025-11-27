<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Cargar helpers y librerías
        $this->load->helper(['url', 'form', 'date']);
        $this->load->library(['session', 'form_validation']);
        
        // Cargar modelo de usuario
        $this->load->model('Usuario_model', 'usuario');
    }

    public function index() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
            return;
        }
        
        $data = [
            'title' => 'Registro de Usuario',
            'message' => $this->session->flashdata('message'),
            'error' => $this->session->flashdata('error')
        ];
        
        $this->load->view('registro', $data);
    }
    
    /**
     * Procesa el formulario de registro
     */
    public function procesar() {
        // Verificar conexión a la base de datos
        $this->load->database();
        
        // Verificar si hay errores de conexión
        if (!$this->db->conn_id) {
            log_message('error', 'No se pudo conectar a la base de datos: ' . $this->db->error()['message']);
            $this->session->set_flashdata('error', 'Error de conexión a la base de datos. Por favor, contacta al administrador.');
            redirect('registro');
            return;
        }
        
        // Verificar si la tabla usuarios existe
        if (!$this->db->table_exists('usuarios')) {
            log_message('error', 'La tabla usuarios no existe en la base de datos');
            $this->session->set_flashdata('error', 'Error en la configuración de la base de datos. Por favor, contacta al administrador.');
            redirect('registro');
            return;
        }

        // Si ya está autenticado, redirigir
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
            return;
        }

        // Debug: Mostrar datos del POST
        log_message('debug', 'Datos del formulario: ' . print_r($this->input->post(), true));

        // Configurar reglas de validación
        $this->form_validation->set_rules('nombres', 'Nombres', 'required|trim|min_length[2]|max_length[80]');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'required|trim|min_length[2]|max_length[80]');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[usuarios.correo]', [
            'is_unique' => 'Este correo electrónico ya está registrado.'
        ]);
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirmar Contraseña', 'required|matches[password]');
        $this->form_validation->set_rules('tipo_documento', 'Tipo de Documento', 'required');
        $this->form_validation->set_rules('numero_documento', 'Número de Documento', 'required|is_unique[usuarios.numero_documento]', [
            'is_unique' => 'Este número de documento ya está registrado.'
        ]);
        $this->form_validation->set_rules('telefono', 'Teléfono', 'trim|min_length[7]|max_length[30]');
        $this->form_validation->set_rules('direccion', 'Dirección', 'trim|max_length[255]');
        $this->form_validation->set_rules('fecha_nacimiento', 'Fecha de Nacimiento', 'required');

        // Ejecutar validación
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver a cargar la vista con errores
            $error_messages = validation_errors();
            log_message('debug', 'Error de validación: ' . $error_messages);
            $this->session->set_flashdata('error', $error_messages);
            
            // Mantener los datos del formulario para rellenar
            $this->session->set_flashdata('form_data', $this->input->post());
            
            redirect('registro');
            return;
        }

        // Preparar datos del usuario según la estructura de la tabla
        $user_data = [
            'tipo_documento' => $this->input->post('tipo_documento'),
            'numero_documento' => $this->input->post('numero_documento'),
            'nombre' => $this->input->post('nombres'),
            'apellido' => $this->input->post('apellidos'),
            'fecha_nacimiento' => $this->input->post('fecha_nacimiento'),
            'id_genero' => 1, // Valor por defecto, ajustar según sea necesario
            'telefono' => $this->input->post('telefono') ?: null,
            'correo' => $this->input->post('email'),
            'contrasena' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'direccion' => $this->input->post('direccion') ?: null,
            'id_estado_civil' => 1, // Valor por defecto, ajustar según sea necesario
            'id_rol' => 3, // 3 = bodeguero por defecto
            'id_estado' => 1, // 1 = Activo
            'intentos_fallidos' => 0,
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];

        // Debug: Mostrar datos que se intentarán guardar
        log_message('debug', 'Intentando guardar usuario: ' . print_r($user_data, true));

        // Verificar si el correo ya existe
        $this->db->where('correo', $user_data['correo']);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('error', 'Este correo electrónico ya está registrado.');
            $this->session->set_flashdata('form_data', $this->input->post());
            redirect('registro');
            return;
        }

        // Intentar guardar el usuario
        $result = $this->usuario->crear($user_data);
        
        // Verificar si hubo un error en la inserción
        $error = $this->db->error();
        if (!empty($error['message'])) {
            log_message('error', 'Error de base de datos: ' . $error['message']);
            $this->session->set_flashdata('error', 'Error al guardar en la base de datos: ' . $error['message']);
            $this->session->set_flashdata('form_data', $this->input->post());
            redirect('registro');
            return;
        }

        if ($result) {
            // Éxito al crear el usuario
            log_message('info', 'Usuario registrado exitosamente: ' . $user_data['correo']);
            $this->session->set_flashdata('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
            redirect('login');
        } else {
            // Error al guardar
            log_message('error', 'Error al registrar el usuario: ' . print_r($this->db->error(), true));
            $this->session->set_flashdata('error', 'Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.');
            $this->session->set_flashdata('form_data', $this->input->post());
            redirect('registro');
        }
    }
    
    /**
     * Redirige al usuario según su rol
     */
    private function _redirect_by_role($rol = null) {
        $role = $rol ?: $this->session->userdata('rol');
        
        switch ($role) {
            case 'administrador':
                redirect('admin/dashboard');
                break;
            case 'jefe':
                redirect('jefe/dashboard');
                break;
            case 'bodeguero':
                redirect('bodeguero/dashboard');
                break;
            default:
                redirect('usuario/dashboard');
                break;
        }
    }
}
