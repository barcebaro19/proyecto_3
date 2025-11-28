<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria extends MY_Controller {
    
    // Solo administradores pueden acceder a este controlador
    protected $roles_permitidos = ['administrador'];
    
    // Métodos que no requieren autenticación (si los hay)
    protected $metodos_sin_auth = ['get_categorias_select2'];

    public function __construct() {
        parent::__construct();
        $this->load->model('Categoria_model');
        
        // Configuración de validación de formularios
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    }

    /**
     * Listar todas las categorías
     */
    public function index() {
        try {
            $data = [
                'title' => 'Gestión de Categorías',
                'categorias' => $this->Categoria_model->obtener_todas()
            ];
            
            $this->cargar_vista('admin/categorias/index', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la lista de categorías');
        }
    }

    /**
     * Mostrar formulario para crear una nueva categoría
     */
    public function crear() {
        $data = [
            'title' => 'Nueva Categoría',
            'modo' => 'crear'
        ];
        
        $this->cargar_vista('admin/categorias/formulario', $data);
    }

    /**
     * Almacenar una nueva categoría
     */
    public function guardar() {
        try {
            // Verificar si es una petición AJAX
            $es_ajax = $this->input->is_ajax_request();
            
            // Reglas de validación
            $reglas = [
                ['field' => 'nombre', 'label' => 'Nombre', 'rules' => 'required|max_length[100]|is_unique[categorias.nombre]'],
                ['field' => 'descripcion', 'label' => 'Descripción', 'rules' => 'max_length[255]']
            ];
            
            $validacion = $this->validar_entrada($reglas);
            
            if (!$validacion['exito']) {
                if ($es_ajax) {
                    $this->json_error('Por favor corrija los errores del formulario', $validacion['errores']);
                } else {
                    $this->session->set_flashdata('error', 'Por favor corrija los errores del formulario');
                    $this->session->set_flashdata('errores', $validacion['errores']);
                    redirect('categoria/crear');
                }
                return;
            }
            
            // Preparar datos
            $data = [
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'id_estado' => 1 // Activo por defecto
            ];
            
            // Crear la categoría
            $categoria_id = $this->Categoria_model->crear($data);
            
            if ($categoria_id) {
                $mensaje = 'Categoría creada exitosamente';
                
                if ($es_ajax) {
                    $this->json_exito($mensaje, ['categoria_id' => $categoria_id]);
                } else {
                    $this->session->set_flashdata('success', $mensaje);
                    redirect('categoria');
                }
            } else {
                throw new Exception('No se pudo crear la categoría');
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al crear la categoría: ' . $e->getMessage();
            
            if ($this->input->is_ajax_request()) {
                $this->json_error($mensaje);
            } else {
                $this->session->set_flashdata('error', $mensaje);
                redirect('categoria/crear');
            }
        }
    }

    // Mostrar formulario para editar una categoría
    public function editar($id) {
        $data['categoria'] = $this->Categoria_model->obtener_por_id($id);
        
        if (empty($data['categoria'])) {
            show_404();
        }
        
        $data['title'] = 'Editar Categoría';
        
        $this->load->view($this->session->userdata('rol') . '/header', $data);
        $this->load->view('admin/categorias/editar', $data);
        $this->load->view($this->session->userdata('rol') . '/footer');
    }

    // Actualizar una categoría existente
    public function actualizar($id) {
        // Verificar si la categoría existe
        $categoria = $this->Categoria_model->obtener_por_id($id);
        
        if (empty($categoria)) {
            show_404();
        }
        
        // Reglas de validación
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]|callback_check_nombre_unico[' . $id . ']');
        $this->form_validation->set_rules('descripcion', 'Descripción', 'max_length[255]');
        
        if ($this->form_validation->run() === FALSE) {
            $this->editar($id);
        } else {
            $data = [
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion')
            ];
            
            if ($this->Categoria_model->actualizar($id, $data)) {
                $this->session->set_flashdata('success', 'Categoría actualizada exitosamente');
                redirect('categoria');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la categoría');
                $this->editar($id);
            }
        }
    }

    // Cambiar el estado de una categoría (activar/desactivar)
    public function cambiar_estado($id) {
        $categoria = $this->Categoria_model->obtener_por_id($id);
        
        if (empty($categoria)) {
            show_404();
        }
        
        $nuevo_estado = $categoria->id_estado == 1 ? 2 : 1; // 1: Activo, 2: Inactivo
        
        if ($this->Categoria_model->actualizar($id, ['id_estado' => $nuevo_estado])) {
            $estado_texto = $nuevo_estado == 1 ? 'activada' : 'desactivada';
            $this->session->set_flashdata('success', 'Categoría ' . $estado_texto . ' exitosamente');
        } else {
            $this->session->set_flashdata('error', 'Error al cambiar el estado de la categoría');
        }
        
        redirect('categoria');
    }

    // Eliminar una categoría (borrado lógico)
    public function eliminar($id) {
        $categoria = $this->Categoria_model->obtener_por_id($id);
        
        if (empty($categoria)) {
            show_404();
        }
        
        // Verificar si la categoría está siendo usada por algún producto
        if ($this->Categoria_model->esta_en_uso($id)) {
            $this->session->set_flashdata('error', 'No se puede eliminar la categoría porque está siendo utilizada por uno o más productos');
            redirect('categoria');
        }
        
        if ($this->Categoria_model->eliminar($id)) {
            $this->session->set_flashdata('success', 'Categoría eliminada exitosamente');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar la categoría');
        }
        
        redirect('categoria');
    }
    
    // Verificar si el nombre de la categoría es único (para validación)
    public function check_nombre_unico($nombre, $id) {
        $this->db->where('nombre', $nombre);
        $this->db->where('id_categoria !=', $id);
        $query = $this->db->get('categorias');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_nombre_unico', 'El {field} ya está en uso');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    // Obtener categorías para select2 (API)
    public function get_categorias_select2() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        
        $results = $this->Categoria_model->buscar_para_select($search, $page);
        
        echo json_encode([
            'results' => $results,
            'pagination' => ['more' => (count($results) == 10)] // Asumiendo un límite de 10 por página
        ]);
    }
}
