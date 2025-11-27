<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {
    
    // Solo administradores pueden acceder a este controlador
    protected $roles_permitidos = ['administrador'];
    
    // Métodos que no requieren autenticación (si los hay)
    protected $metodos_sin_auth = [];

    public function __construct() {
        parent::__construct();
        
        // Cargar modelos necesarios
        $this->load->model('Usuario_model', 'usuario');
        $this->load->model('Rol_model', 'rol');
        $this->load->model('Referencia_model', 'referencia');
        
        // Cargar Estado_model con verificación de existencia
        $estado_model_path = APPPATH . 'models/Estado_model.php';
        if (file_exists($estado_model_path)) {
            require_once($estado_model_path);
            $this->estado = new Estado_model();
        } else {
            log_message('error', 'No se encontró el archivo del modelo: ' . $estado_model_path);
            show_error('Error al cargar los recursos necesarios. Por favor, intente más tarde.', 500);
        }
        
        $this->load->model('Producto_model', 'producto');
        $this->load->model('Movimiento_model', 'movimiento');
        $this->load->model('Categoria_model', 'categoria');
    
        // Cargar librería de validación
        $this->load->library('form_validation');
    
        // Configuración de validación de formularios
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
        
        // Verificar autenticación
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        
        // Verificar rol
        $rol = $this->session->userdata('rol');
        if ($rol !== 'administrador') {
            $this->session->set_flashdata('error', 'No tiene permisos para acceder a esta sección');
            redirect(($rol ? $rol . '/dashboard' : 'login'));
        }
    }

    /**
     * Actualiza la información de un usuario
     */
    public function actualizar_usuario() {
        // Verificar si es una petición POST
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Método no permitido');
            redirect('admin/usuarios');
            return;
        }

        // Obtener el ID del usuario
        $id_usuario = $this->input->post('id_usuario');
        
        // Validar que el usuario exista
        $usuario = $this->usuario->obtener_por_id($id_usuario);
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
            return;
        }

        // Configurar reglas de validación
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('correo', 'Correo electrónico', 'required|valid_email|trim|max_length[100]');
        $this->form_validation->set_rules('telefono', 'Teléfono', 'trim|max_length[20]');
        $this->form_validation->set_rules('direccion', 'Dirección', 'trim|max_length[255]');
        $this->form_validation->set_rules('fecha_nacimiento', 'Fecha de nacimiento', 'trim');
        $this->form_validation->set_rules('id_genero', 'Género', 'trim|integer');
        $this->form_validation->set_rules('id_estado_civil', 'Estado civil', 'trim|integer');
        $this->form_validation->set_rules('id_rol', 'Rol', 'required|integer');
        $this->form_validation->set_rules('id_estado', 'Estado', 'required|integer');
        
        // Validar contraseña solo si se proporciona
        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Contraseña', 'min_length[6]|matches[password2]');
            $this->form_validation->set_rules('password2', 'Confirmar Contraseña', 'required|matches[password]');
        }

        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, volver al formulario con los errores
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/editar_usuario/' . $id_usuario);
            return;
        }

        // Preparar datos para actualizar, usando columnas reales de la tabla usuarios
        $datos = [
            'nombre'           => $this->input->post('nombre'),
            'apellido'         => $this->input->post('apellido'),
            'correo'           => $this->input->post('correo'),
            'telefono'         => $this->input->post('telefono'),
            'direccion'        => $this->input->post('direccion'),
            'fecha_nacimiento' => $this->input->post('fecha_nacimiento'),
            'id_genero'        => $this->input->post('id_genero'),
            'id_estado_civil'  => $this->input->post('id_estado_civil'),
            'id_rol'           => $this->input->post('id_rol'),
            'id_estado'        => $this->input->post('id_estado')
        ];
        
        // Debug: Verificar los datos antes de actualizar
        log_message('debug', 'Datos a actualizar: ' . print_r($datos, true));

        // Si se proporcionó una nueva contraseña, delegar el hash al modelo usando la columna `contrasena`
        if (!empty($password)) {
            $datos['contrasena'] = $password;
        }

        // Actualizar el usuario
        $actualizado = $this->usuario->actualizar($id_usuario, $datos);

        if ($actualizado) {
            $this->session->set_flashdata('success', 'Usuario actualizado correctamente');
            redirect('admin/usuarios');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar el usuario');
            redirect('admin/usuarios/editar/' . $id_usuario);
        }
    }

    /**
     * Muestra el detalle de un usuario
     * @param int $id ID del usuario
     */
    /**
     * Cambia el estado de un usuario (activar/desactivar)
     */
    public function cambiar_estado_usuario() {
        // Verificar si es una petición AJAX
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(403)->set_output('Acceso no permitido');
            return;
        }

        // Validar datos de entrada
        $this->form_validation->set_rules('id', 'ID de usuario', 'required|integer');
        $this->form_validation->set_rules('estado', 'Estado', 'required|in_list[0,1]');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Datos de entrada no válidos',
                    'errors' => $this->form_validation->error_array()
                ]));
            return;
        }

        $id_usuario = $this->input->post('id');
        $nuevo_estado = $this->input->post('estado');

        // Verificar si el usuario existe
        $usuario = $this->usuario->obtener_por_id($id_usuario);
        if (!$usuario) {
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]));
            return;
        }

        // Verificar si el usuario que realiza la acción es administrador
        if ($this->session->userdata('rol') !== 'administrador') {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No tiene permisos para realizar esta acción'
                ]));
            return;
        }

        // Actualizar el estado del usuario
        $actualizado = $this->usuario->actualizar($id_usuario, [
            'id_estado' => $nuevo_estado,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);

        if ($actualizado) {
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => $nuevo_estado == 1 ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente',
                    'nuevo_estado' => $nuevo_estado
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el estado del usuario. Intente nuevamente.'
                ]));
        }
    }


    /**
     * Muestra el detalle de un usuario (vista solo lectura)
     */
    public function ver_usuario($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de usuario no especificado');
            redirect('admin/usuarios');
        }

        $usuario = $this->usuario->obtener_por_id($id);

        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
        }

        // Normalizar propiedades mínimas para la vista de detalle
        if (isset($usuario->nombres) || isset($usuario->apellidos)) {
            $usuario->nombre = $usuario->nombres ?? '';
            $usuario->apellido = $usuario->apellidos ?? '';
            $usuario->correo = $usuario->email ?? '';
        }

        $data = [
            'title'   => 'Detalle de Usuario',
            'usuario' => $usuario
        ];

        $this->cargar_vista('admin/usuarios/ver', $data);
    }

    /**
     * Muestra el formulario de edición de un usuario
     */
    public function editar_usuario($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de usuario no especificado');
            redirect('admin/usuarios');
        }

        // Obtener datos del usuario
        $usuario = $this->usuario->obtener_por_id($id);

        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
        }

        // Asegurarse de que los nombres de las propiedades sean consistentes
        if (isset($usuario->nombres) || isset($usuario->apellidos)) {
            $usuario->nombre = $usuario->nombres ?? '';
            $usuario->apellido = $usuario->apellidos ?? '';
            $usuario->correo = $usuario->email ?? '';
        } elseif (isset($usuario->nombre) || isset($usuario->apellido)) {
            $usuario->nombres = $usuario->nombre ?? '';
            $usuario->apellidos = $usuario->apellido ?? '';
            $usuario->email = $usuario->correo ?? '';
        }

        $roles   = $this->rol->get_roles();
        $estados = $this->estado->get_estados();

        $data = [
            'title'   => 'Editar Usuario: ' . ($usuario->nombre . ' ' . $usuario->apellido),
            'usuario' => $usuario,
            'roles'   => $roles,
            'estados' => $estados
        ];

        $this->cargar_vista('admin/usuarios/editar', $data);
    }
    
    /**
     * Obtiene los datos de un usuario para edición (AJAX)
     * @param int $id ID del usuario
     */
    public function obtener_usuario($id = null) {
        // Verificar si es una petición AJAX
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(403)->set_output('Acceso no permitido');
            return;
        }
        
        // Validar ID
        if (!$id) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'ID de usuario no especificado'
                ]));
            return;
        }
        
        // Debug: Verificar carga del modelo
        if (!isset($this->usuario)) {
            log_message('error', 'El modelo de usuario no se ha cargado correctamente');
            $this->load->model('Usuario_model', 'usuario');
        }
        
        // Obtener datos del usuario
        $usuario = $this->usuario->obtener_por_id($id);
        
        if ($usuario) {
            // No devolver información sensible
            unset($usuario->password, $usuario->token_recuperacion);
            
            // Debug: Verificar datos del usuario
            log_message('debug', 'Datos del usuario en el controlador: ' . print_r([
                'id' => $usuario->id_usuario ?? 'No definido',
                'nombre' => $usuario->nombre ?? 'No definido',
                'apellido' => $usuario->apellido ?? 'No definido',
                'rol' => $usuario->nombre_rol ?? 'No definido',
                'estado' => $usuario->nombre_estado ?? 'No definido'
            ], true));
            
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status' => 'success',
                    'data' => $usuario
                ]));
        } else {
            log_message('error', 'No se encontró el usuario con ID: ' . $id);
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ]));
        }
    }
    
    public function index() {
        // Redirigir al dashboard
        $this->dashboard();
    }
    
    /**
     * Muestra el dashboard principal
     */
    public function dashboard() {
        try {
            // Inicializar arrays vacíos para evitar errores
            $productos_bajo_stock = [];
            $actividad_reciente = [];
            $productos_por_categoria = [];
            
            // Intentar cargar los datos, pero continuar incluso si hay errores
            try {
                $productos_bajo_stock = $this->producto->obtener_bajo_stock(5);
            } catch (Exception $e) {
                log_message('error', 'Error al obtener productos bajo stock: ' . $e->getMessage());
            }
            
            try {
                $actividad_reciente = $this->movimiento->get_ultimos_movimientos(10);
            } catch (Exception $e) {
                log_message('error', 'Error al obtener actividad reciente: ' . $e->getMessage());
            }
            
            try {
                $productos_por_categoria = $this->categoria->obtener_con_conteo();
            } catch (Exception $e) {
                log_message('error', 'Error al obtener productos por categoría: ' . $e->getMessage());
            }
            
            // Mapear actividad reciente para la vista
        $actividad_mapped = [];
        if (!empty($actividad_reciente)) {
            foreach ($actividad_reciente as $act) {
                $actividad_mapped[] = (object)[
                    'tipo' => ucfirst($act->tipo),
                    'descripcion' => ($act->producto_nombre ?? 'Producto') . ' - ' . ($act->descripcion ?? ''),
                    'fecha' => $act->fecha,
                    'cantidad' => $act->cantidad
                ];
            }
        }

        // Obtener movimientos por mes para el gráfico (Simulado o Real)
        // Por ahora usaremos datos reales si existen, o vacíos
        $movimientos_ultimos_meses = []; 
        // TODO: Implementar lógica de gráfico real en Movimiento_model si es necesario

        // Inicializar array de datos para la vista con valores por defecto
        $data = [
            'title' => 'Panel de Administración',
            'resumen_rapido' => [
                [
                    'titulo' => 'Usuarios',
                    'valor' => method_exists($this->usuario, 'count_all') ? $this->usuario->count_all() : 0,
                    'url' => site_url('admin/usuarios')
                ],
                [
                    'titulo' => 'Productos',
                    'valor' => method_exists($this->producto, 'contar_todos') ? $this->producto->contar_todos() : 0,
                    'url' => site_url('admin/productos')
                ],
                [
                    'titulo' => 'Categorías',
                    'valor' => method_exists($this->categoria, 'contar_todos') ? $this->categoria->contar_todos() : 0,
                    'url' => site_url('admin/categorias')
                ],
                [
                    'titulo' => 'Stock Bajo',
                    'valor' => method_exists($this->producto, 'contar_bajo_stock') ? $this->producto->contar_bajo_stock() : 0,
                    'url' => site_url('admin/productos?filter=low_stock')
                ]
            ],
            'productos_bajo_stock' => $productos_bajo_stock,
            'actividad_reciente' => $actividad_mapped,
            'ultimos_movimientos' => [], // Para la tabla de abajo, si se usa
            'productos_por_categoria' => $productos_por_categoria,
            'movimientos_ultimos_meses' => $movimientos_ultimos_meses,
            'total_usuarios' => method_exists($this->usuario, 'count_all') ? $this->usuario->count_all() : 0,
            'total_productos' => method_exists($this->producto, 'contar_todos') ? $this->producto->contar_todos() : 0,
            'total_categorias' => method_exists($this->categoria, 'contar_todos') ? $this->categoria->contar_todos() : 0,
            'productos_bajo_stock_count' => method_exists($this->producto, 'contar_bajo_stock') ? $this->producto->contar_bajo_stock() : 0,
            'alertas' => [
                'productos_bajo_stock' => $productos_bajo_stock
            ],
            'total_bajo_stock' => is_array($productos_bajo_stock) ? count($productos_bajo_stock) : 0
        ];

        // Cargar la vista del dashboard usando el layout unificado
        $this->cargar_vista('admin/dashboard', $data);
        
    } catch (Exception $e) {
        // En caso de error, mostrar mensaje y loguear el error
        log_message('error', 'Error en el dashboard: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        
        // Mostrar error en un layout consistente
        $error_data['content_view'] = 'errors/error_general';
        $error_data['error_message'] = 'Error al cargar el dashboard. Por favor, verifica los logs para más detalles.';
        $this->load->view('layouts/admin', $error_data);
    }
    }

    /**
     * Muestra la lista de usuarios
     */
    public function usuarios() {
        try {
            // Obtener datos usando los modelos
            $usuarios = $this->usuario->obtener_todos_con_rol();
            $roles = $this->rol->get_roles();
            $estados = $this->estado->get_estados();
            
            // Preparar datos para la vista
            $data = [
                'title' => 'Gestión de Usuarios',
                'usuarios' => $usuarios,
                'roles' => array_column($roles, 'nombre', 'id_rol'),
                'estados' => array_column($estados, 'nombre', 'id_estado')
            ];
            
            // Cargar la vista usando el método estándar
            $this->cargar_vista('admin/usuarios/index', $data);

        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la lista de usuarios');
        }
    }

    /**
     * LISTADO DE REFERENCIAS
     */
    public function referencias() {
        try {
            $referencias = $this->referencia->obtener_todas_con_categoria();
            $categorias  = $this->categoria->obtener_todas();

            $data = [
                'title'       => 'Gestión de Referencias',
                'referencias' => $referencias,
                'categorias'  => $categorias
            ];

            $this->cargar_vista('admin/referencias/index', $data);

        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la lista de referencias');
        }
    }

    /**
     * VER DETALLE DE REFERENCIA
     */
    public function ver_referencia($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de referencia no especificado');
            redirect('admin/referencias');
        }

        $referencia = $this->referencia->obtener_por_id($id);

        if (!$referencia) {
            $this->session->set_flashdata('error', 'Referencia no encontrada');
            redirect('admin/referencias');
        }

        $data = [
            'title'      => 'Detalle de Referencia',
            'referencia' => $referencia
        ];

        $this->cargar_vista('admin/referencias/ver', $data);
    }

    /**
     * FORMULARIO DE EDICIÓN DE REFERENCIA
     */
    public function editar_referencia($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de referencia no especificado');
            redirect('admin/referencias');
        }

        $referencia = $this->referencia->obtener_por_id($id);

        if (!$referencia) {
            $this->session->set_flashdata('error', 'Referencia no encontrada');
            redirect('admin/referencias');
        }

        $categorias = $this->categoria->obtener_todas();
        $estados    = $this->estado->get_estados();

        $data = [
            'title'      => 'Editar Referencia',
            'referencia' => $referencia,
            'categorias' => $categorias,
            'estados'    => $estados
        ];

        $this->cargar_vista('admin/referencias/editar', $data);
    }

    /**
     * CREAR REFERENCIA (AJAX)
     */
    public function crear_referencia() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('codigo_referencia', 'Código', 'required|trim|max_length[80]');
        $this->form_validation->set_rules('nombre_referencia', 'Nombre', 'required|trim|max_length[150]');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
            return;
        }

        $data = [
            'codigo_referencia' => $this->input->post('codigo_referencia'),
            'nombre_referencia' => $this->input->post('nombre_referencia'),
            'descripcion'       => $this->input->post('descripcion'),
            'id_categoria'      => $this->input->post('id_categoria') ?: null,
            'id_estado'         => $this->input->post('id_estado') ?: 1,
            'creado_por'        => $this->session->userdata('usuario_id')
        ];

        $id = $this->referencia->crear($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $id ? true : false,
                'message' => $id ? 'Referencia creada correctamente' : 'No se pudo crear la referencia',
                'id'      => $id
            ]));
    }

    /**
     * ACTUALIZAR REFERENCIA
     */
    public function actualizar_referencia() {
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Método no permitido');
            redirect('admin/referencias');
            return;
        }

        $id_referencia = $this->input->post('id_referencia');

        $this->form_validation->set_rules('codigo_referencia', 'Código', 'required|trim|max_length[80]');
        $this->form_validation->set_rules('nombre_referencia', 'Nombre', 'required|trim|max_length[150]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/editar_referencia/' . $id_referencia);
            return;
        }

        $data = [
            'codigo_referencia' => $this->input->post('codigo_referencia'),
            'nombre_referencia' => $this->input->post('nombre_referencia'),
            'descripcion'       => $this->input->post('descripcion'),
            'id_categoria'      => $this->input->post('id_categoria') ?: null,
            'id_estado'         => $this->input->post('id_estado') ?: 1
        ];

        $ok = $this->referencia->actualizar($id_referencia, $data);

        if ($ok) {
            $this->session->set_flashdata('success', 'Referencia actualizada correctamente');
        } else {
            $this->session->set_flashdata('error', 'No se pudo actualizar la referencia');
        }

        redirect('admin/referencias');
    }

    /**
     * ELIMINAR REFERENCIA (AJAX, borrado lógico)
     */
    public function eliminar_referencia($id = null) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (empty($id) || !is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ID de referencia no válido'
                ]));
            return;
        }

        $ok = $this->referencia->eliminar($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $ok ? true : false,
                'message' => $ok ? 'Referencia eliminada correctamente' : 'No se pudo eliminar la referencia'
            ]));
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear_usuario() {
        // Verificar si es una petición AJAX
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        try {
            // Validar datos del formulario contra la estructura real de la tabla usuarios
            $reglas = [
                ['field' => 'nombre', 'label' => 'Nombre', 'rules' => 'required|trim|max_length[80]'],
                ['field' => 'apellido', 'label' => 'Apellido', 'rules' => 'required|trim|max_length[80]'],
                ['field' => 'correo', 'label' => 'Correo electrónico', 'rules' => 'required|valid_email|is_unique[usuarios.correo]'],
                ['field' => 'contrasena', 'label' => 'Contraseña', 'rules' => 'required|min_length[6]'],
                ['field' => 'id_rol', 'label' => 'Rol', 'rules' => 'required|numeric']
            ];
            
            $validacion = $this->validar_entrada($reglas);
            if (!$validacion['exito']) {
                $this->json_error('Por favor corrija los errores del formulario', $validacion['errores']);
                return;
            }
            
            // Verificar si el correo ya existe
            $correo = $this->input->post('correo');
            if ($this->usuario->email_existe($correo)) {
                $this->json_error('El correo electrónico ya está registrado', ['correo' => 'El correo electrónico ya está en uso']);
                return;
            }
            
            // Preparar datos para crear el usuario (coincidiendo con Usuario_model::crear)
            $data = [
                'tipo_documento'   => $this->input->post('tipo_documento'),
                'numero_documento' => $this->input->post('numero_documento'),
                'nombre'           => $this->input->post('nombre'),
                'apellido'         => $this->input->post('apellido'),
                'fecha_nacimiento' => $this->input->post('fecha_nacimiento'),
                'id_genero'        => $this->input->post('id_genero'),
                'telefono'         => $this->input->post('telefono'),
                'correo'           => $correo,
                'contrasena'       => $this->input->post('contrasena'), // El modelo se encarga del hash
                'direccion'        => $this->input->post('direccion'),
                'id_estado_civil'  => $this->input->post('id_estado_civil'),
                'id_rol'           => $this->input->post('id_rol'),
                'id_estado'        => 1
            ];
            
            // Crear el usuario
            $usuario_id = $this->usuario->crear($data);
            
            if ($usuario_id) {
                $this->json_exito('Usuario creado exitosamente', ['usuario_id' => $usuario_id]);
            } else {
                throw new Exception('No se pudo crear el usuario');
            }
            
        } catch (Exception $e) {
            $this->json_error('Error al crear el usuario: ' . $e->getMessage());
        }
    }


    /**
     * Método para manejar la actualización de usuarios
     * 
     * Nota: La implementación de este método se encuentra en la línea 56
     * para evitar duplicación de código.
     */

    /**
     * Eliminar un usuario (cambiar estado a inactivo)
     * @param int|null $id ID del usuario a eliminar
     */
    public function eliminar_usuario($id = null) {
        try {
            // Cargar el modelo de usuarios
            $this->load->model('Usuario_model', 'usuario');
            
            // Validar ID
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de usuario no válido');
            }
            
            // Verificar que el usuario exista
            $usuario = $this->usuario->obtener_por_id($id);
            if (!$usuario) {
                throw new Exception('El usuario que intenta eliminar no existe');
            }
            
            // No permitir auto-eliminación
            if ($id == $this->session->userdata('usuario_id')) {
                throw new Exception('No puede eliminar su propio usuario mientras está autenticado');
            }
            
            // Eliminar el usuario usando el modelo (cambio de estado a inactivo)
            $result = $this->usuario->eliminar($id);
            
            if ($result) {
                $response = [
                    'status' => 'success',
                    'message' => 'Usuario desactivado correctamente'
                ];
            } else {
                throw new Exception('No se pudo desactivar el usuario');
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error en eliminar_usuario: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            
            $response = [
                'status' => 'error',
                'message' => 'Error al desactivar el usuario: ' . $e->getMessage()
            ];
            
            if (ENVIRONMENT === 'development') {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * LISTADO DE PRODUCTOS
     */
    /**
     * LISTADO DE PRODUCTOS
     */
    public function productos() {
        try {
            // Obtener productos con cálculos de inventario
            $productos = $this->producto->obtener_todos_con_calculos();

            // Obtener referencias para combos
            $referencias = $this->db->get('referencias')->result();

            $data = [
                'title'       => 'Gestión de Productos',
                'productos'   => $productos,
                'referencias' => $referencias
            ];

            $this->cargar_vista('admin/productos/index', $data);

        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la lista de productos');
        }
    }

    /**
     * REGISTRAR MOVIMIENTO (AJAX)
     */
    public function registrar_movimiento() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('id_producto', 'Producto', 'required|integer');
        $this->form_validation->set_rules('tipo_movimiento', 'Tipo', 'required|in_list[entrada,salida]');
        $this->form_validation->set_rules('cantidad', 'Cantidad', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('descripcion', 'Descripción', 'trim|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            // Log para depuración
            log_message('error', 'Error validación movimiento: ' . $errors . ' POST: ' . json_encode($this->input->post()));
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Error de validación: ' . strip_tags($errors)
                ]));
            return;
        }

        $id_producto = $this->input->post('id_producto');
        $tipo = $this->input->post('tipo_movimiento');
        $cantidad = (int)$this->input->post('cantidad');
        $descripcion = $this->input->post('descripcion');

        // Obtener producto actual
        $producto = $this->producto->obtener_por_id($id_producto);
        if (!$producto) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Producto no encontrado']));
            return;
        }

        // Validar stock suficiente para salidas
        if ($tipo === 'salida' && $producto->cantidad_stock < $cantidad) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Stock insuficiente']));
            return;
        }

        // Iniciar transacción
        $this->db->trans_start();

        // 1. Registrar en historial de movimientos
        $data_movimiento = [
            'id_producto' => $id_producto,
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'descripcion' => $descripcion,
            'fecha_movimiento' => date('Y-m-d H:i:s'),
            'creado_por' => $this->session->userdata('usuario_id')
        ];
        $this->db->insert('movimientos_inventario', $data_movimiento);

        // 2. Actualizar stock del producto
        $nuevo_stock = ($tipo === 'entrada') 
            ? $producto->cantidad_stock + $cantidad 
            : $producto->cantidad_stock - $cantidad;

        $this->producto->actualizar($id_producto, ['cantidad_stock' => $nuevo_stock]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Error al registrar el movimiento']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'message' => 'Movimiento registrado correctamente']));
        }
    }

    /**
     * LISTADO DE MOVIMIENTOS DE INVENTARIO
     */
    public function movimientos() {
        try {
            $movimientos = $this->movimiento->obtener_todos();

            $data = [
                'title'       => 'Movimientos de Inventario',
                'movimientos' => $movimientos
            ];

            $this->cargar_vista('admin/movimientos/index', $data);

        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar los movimientos de inventario');
        }
    }

    /**
     * VER DETALLE DE PRODUCTO
     */
    public function ver_producto($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de producto no especificado');
            redirect('admin/productos');
        }

        $producto = $this->producto->obtener_por_id($id);

        if (!$producto) {
            $this->session->set_flashdata('error', 'Producto no encontrado');
            redirect('admin/productos');
        }

        $data = [
            'title'    => 'Detalle de Producto',
            'producto' => $producto
        ];

        $this->cargar_vista('admin/productos/ver', $data);
    }

    /**
     * FORMULARIO DE EDICIÓN DE PRODUCTO
     */
    public function editar_producto($id = null) {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID de producto no especificado');
            redirect('admin/productos');
        }

        $producto = $this->producto->obtener_por_id($id);

        if (!$producto) {
            $this->session->set_flashdata('error', 'Producto no encontrado');
            redirect('admin/productos');
        }

        $referencias = $this->db->get('referencias')->result();

        $data = [
            'title'       => 'Editar Producto',
            'producto'    => $producto,
            'referencias' => $referencias
        ];

        $this->cargar_vista('admin/productos/editar', $data);
    }

    /**
     * CREAR PRODUCTO (AJAX)
     */
    public function crear_producto() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Reglas básicas de validación
        $this->form_validation->set_rules('id_referencia', 'Referencia', 'required|integer');
        $this->form_validation->set_rules('codigo_interno', 'Código interno', 'required|trim|max_length[80]');
        $this->form_validation->set_rules('cantidad_stock', 'Stock', 'required|integer');
        $this->form_validation->set_rules('stock_minimo', 'Stock mínimo', 'required|integer');
        $this->form_validation->set_rules('precio_unitario', 'Precio unitario', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
            return;
        }

        $data = [
            'id_referencia'    => $this->input->post('id_referencia'),
            'codigo_interno'   => $this->input->post('codigo_interno'),
            'codigo_proveedor' => $this->input->post('codigo_proveedor'),
            'descripcion'      => $this->input->post('descripcion'),
            'talla'            => $this->input->post('talla'),
            'color'            => $this->input->post('color'),
            'cantidad_stock'   => (int)$this->input->post('cantidad_stock'),
            'stock_minimo'     => (int)$this->input->post('stock_minimo'),
            'precio_unitario'  => (float)$this->input->post('precio_unitario'),
            'id_estado'        => (int)$this->input->post('id_estado'),
            'creado_por'       => $this->session->userdata('usuario_id')
        ];

        $id = $this->producto->crear($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $id ? true : false,
                'message' => $id ? 'Producto creado correctamente' : 'No se pudo crear el producto',
                'id'      => $id
            ]));
    }

    /**
     * ACTUALIZAR PRODUCTO
     */
    public function actualizar_producto() {
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Método no permitido');
            redirect('admin/productos');
            return;
        }

        $id_producto = $this->input->post('id_producto');

        $this->form_validation->set_rules('id_referencia', 'Referencia', 'required|integer');
        $this->form_validation->set_rules('codigo_interno', 'Código interno', 'required|trim|max_length[80]');
        $this->form_validation->set_rules('cantidad_stock', 'Stock', 'required|integer');
        $this->form_validation->set_rules('stock_minimo', 'Stock mínimo', 'required|integer');
        $this->form_validation->set_rules('precio_unitario', 'Precio unitario', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/editar_producto/' . $id_producto);
            return;
        }

        $data = [
            'id_referencia'    => $this->input->post('id_referencia'),
            'codigo_interno'   => $this->input->post('codigo_interno'),
            'codigo_proveedor' => $this->input->post('codigo_proveedor'),
            'descripcion'      => $this->input->post('descripcion'),
            'talla'            => $this->input->post('talla'),
            'color'            => $this->input->post('color'),
            'cantidad_stock'   => (int)$this->input->post('cantidad_stock'),
            'stock_minimo'     => (int)$this->input->post('stock_minimo'),
            'precio_unitario'  => (float)$this->input->post('precio_unitario'),
            'id_estado'        => (int)$this->input->post('id_estado')
        ];

        $ok = $this->producto->actualizar($id_producto, $data);

        if ($ok) {
            $this->session->set_flashdata('success', 'Producto actualizado correctamente');
        } else {
            $this->session->set_flashdata('error', 'No se pudo actualizar el producto');
        }

        redirect('admin/productos');
    }

    /**
     * ELIMINAR PRODUCTO (AJAX, borrado lógico)
     */
    public function eliminar_producto($id = null) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (empty($id) || !is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ID de producto no válido'
                ]));
            return;
        }

        $ok = $this->producto->eliminar($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $ok,
                'message' => $ok ? 'Producto eliminado correctamente' : 'No se pudo eliminar el producto'
            ]));
    }

    /**
     * OBTENER SIGUIENTE CÓDIGO INTERNO PARA UNA REFERENCIA (AJAX)
     */
    public function obtener_siguiente_codigo() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id_referencia = $this->input->post('id_referencia');

        if (empty($id_referencia) || !is_numeric($id_referencia)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ID de referencia no válido'
                ]));
            return;
        }

        // Obtener la referencia
        $referencia = $this->db->get_where('referencias', ['id_referencia' => $id_referencia])->row();

        if (!$referencia) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Referencia no encontrada'
                ]));
            return;
        }

        // Obtener el último código interno para esta referencia
        $this->db->select('codigo_interno');
        $this->db->from('productos');
        $this->db->where('id_referencia', $id_referencia);
        $this->db->like('codigo_interno', $referencia->codigo_referencia, 'after');
        $this->db->order_by('id_producto', 'DESC');
        $this->db->limit(1);
        $ultimo_producto = $this->db->get()->row();

        // Generar el siguiente código
        if ($ultimo_producto) {
            // Extraer el número del último código (ej: CAMIS-S-AZUL-001 -> 001)
            $codigo_base = $referencia->codigo_referencia . '-';
            $ultimo_codigo = $ultimo_producto->codigo_interno;
            
            // Intentar extraer el número al final
            if (preg_match('/-(\d+)$/', $ultimo_codigo, $matches)) {
                $numero = intval($matches[1]) + 1;
            } else {
                $numero = 1;
            }
        } else {
            $numero = 1;
        }

        // Formatear el código con ceros a la izquierda
        $prefijo = !empty($referencia->codigo_referencia) ? $referencia->codigo_referencia : 'PROD';
        $siguiente_codigo = $prefijo . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'codigo' => $siguiente_codigo,
                'codigo_referencia' => $referencia->codigo_referencia
            ]));
    }

    /**
     * MÉTODO TEMPORAL PARA CORREGIR BASE DE DATOS
     */
    public function fix_db() {
        $sql = "CREATE TABLE IF NOT EXISTS movimientos_inventario (
            id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
            id_producto INT NOT NULL,
            tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
            cantidad INT NOT NULL,
            descripcion TEXT,
            fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
            creado_por INT,
            INDEX (id_producto)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($this->db->query($sql)) {
            echo "<h1>¡Tabla creada correctamente!</h1>";
            echo "<p>La tabla 'movimientos_inventario' ha sido creada.</p>";
            echo "<a href='" . site_url('admin/productos') . "'>Volver a Productos</a>";
        } else {
            echo "<h1>Error</h1>";
            echo "<p>No se pudo crear la tabla: " . $this->db->error()['message'] . "</p>";
        }
    }
    /**
     * VISTA DE MATRIZ DE INVENTARIO (Estilo Excel)
     */
    public function inventario_matriz() {
        // Obtener todos los productos con sus cálculos
        $productos = $this->producto->obtener_todos_con_calculos();
        
        // Agrupar por Referencia + Color
        $matriz = [];
        $tallas_permitidas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'UNICA']; // Tallas estándar + Única

        foreach ($productos as $p) {
            $key = $p->nombre_referencia . '|' . $p->color;
            
            if (!isset($matriz[$key])) {
                $matriz[$key] = [
                    'referencia' => $p->nombre_referencia,
                    'color' => $p->color,
                    'tallas' => [],
                    'total_stock' => 0,
                    'total_entradas' => 0,
                    'total_salidas' => 0,
                    'total_inv_inicial' => 0
                ];
            }

            // Normalizar talla (mayúsculas, quitar espacios)
            $talla = strtoupper(trim($p->talla));
            
            $matriz[$key]['tallas'][$talla] = [
                'id' => $p->id_producto,
                'stock' => $p->cantidad_stock,
                'inv_inicial' => $p->inv_inicial,
                'entradas' => $p->total_entradas,
                'salidas' => $p->total_salidas
            ];

            // Sumar totales
            $matriz[$key]['total_stock'] += $p->cantidad_stock;
            $matriz[$key]['total_entradas'] += $p->total_entradas;
            $matriz[$key]['total_salidas'] += $p->total_salidas;
            $matriz[$key]['total_inv_inicial'] += $p->inv_inicial;
        }

        $data = [
            'title' => 'Matriz de Inventario',
            'matriz' => $matriz,
            'tallas_cols' => $tallas_permitidas
        ];

        $this->cargar_vista('admin/inventario/matriz', $data);
    }
}
