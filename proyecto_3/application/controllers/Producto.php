<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Zend\Barcode\Barcode as ZendBarcode;

class Producto extends MY_Controller {
    
    // Roles permitidos para acceder a este controlador
    protected $roles_permitidos = ['administrador', 'jefe', 'empleado'];
    
    // Métodos que no requieren autenticación (si los hay)
    protected $metodos_sin_auth = ['ver'];

    public function __construct() {
        parent::__construct();
        $this->load->model('Producto_model');
        $this->load->model('Categoria_model');
        
        // Configuración de validación de formularios
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    }

    /**
     * Listar todos los productos
     */
    public function index() {
        try {
            $data = [
                'title' => 'Gestión de Productos',
                'productos' => $this->Producto_model->obtener_todos()
            ];
            
            // Cargar la vista según el rol
            $vista = $this->session->userdata('rol') . '/productos/index';
            $this->cargar_vista($vista, $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la lista de productos');
        }
    }

    /**
     * Ver detalles de un producto
     * @param int $id ID del producto
     */
    public function ver($id = null) {
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de producto no válido');
            }
            
            $producto = $this->Producto_model->obtener_por_id($id);
            
            if (empty($producto)) {
                show_404();
            }
            
            $data = [
                'title' => 'Detalles del Producto',
                'producto' => $producto,
                'movimientos' => $this->Producto_model->obtener_movimientos_producto($id)
            ];
            
            // Cargar la vista según el rol
            $vista = $this->session->userdata('rol') . '/productos/ver';
            $this->cargar_vista($vista, $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar los detalles del producto');
        }
    }

    /**
     * Mostrar formulario para crear un nuevo producto
     * Solo para administradores
     */
    public function crear() {
        try {
            // Verificar permisos
            if ($this->session->userdata('rol') !== 'administrador') {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            $data = [
                'title' => 'Crear Producto',
                'categorias' => $this->Categoria_model->obtener_todas(),
                'modo' => 'crear'
            ];
            
            $this->cargar_vista('admin/productos/formulario', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, $e->getMessage());
        }
    }
    
    /**
     * Almacenar un nuevo producto
     * Solo para administradores
     */
    public function guardar() {
        try {
            // Verificar permisos
            if ($this->session->userdata('rol') !== 'administrador') {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            // Verificar si es una petición AJAX
            $es_ajax = $this->input->is_ajax_request();
            
            // Reglas de validación
            $reglas = [
                ['field' => 'codigo', 'label' => 'Código', 'rules' => 'required|is_unique[productos.codigo]'],
                ['field' => 'nombre', 'label' => 'Nombre', 'rules' => 'required|max_length[200]'],
                ['field' => 'id_categoria', 'label' => 'Categoría', 'rules' => 'required|numeric'],
                ['field' => 'precio', 'label' => 'Precio', 'rules' => 'required|numeric|greater_than[0]'],
                ['field' => 'stock', 'label' => 'Stock', 'rules' => 'required|integer|greater_than_equal_to[0]'],
                ['field' => 'stock_minimo', 'label' => 'Stock Mínimo', 'rules' => 'required|integer|greater_than_equal_to[0]']
            ];
            
            $validacion = $this->validar_entrada($reglas);
            
            if (!$validacion['exito']) {
                if ($es_ajax) {
                    $this->json_error('Por favor corrija los errores del formulario', $validacion['errores']);
                } else {
                    $this->session->set_flashdata('error', 'Por favor corrija los errores del formulario');
                    $this->session->set_flashdata('errores', $validacion['errores']);
                    redirect('producto/crear');
                }
                return;
            }
            
            // Preparar datos
            $data = [
                'codigo' => $this->input->post('codigo'),
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'id_categoria' => $this->input->post('id_categoria'),
                'precio' => $this->input->post('precio'),
                'stock' => $this->input->post('stock'),
                'stock_minimo' => $this->input->post('stock_minimo'),
                'id_estado' => 1 // Activo por defecto
            ];
            
            // Iniciar transacción
            $this->db->trans_begin();
            
            try {
                // Crear el producto
                $producto_id = $this->Producto_model->crear($data);
                
                if (!$producto_id) {
                    throw new Exception('No se pudo crear el producto');
                }
                
                // Registrar movimiento de entrada inicial
                $movimiento = [
                    'id_producto' => $producto_id,
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $data['stock'],
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $this->session->userdata('user_id'),
                    'observaciones' => 'Carga inicial de inventario'
                ];
                
                if (!$this->Producto_model->registrar_movimiento($movimiento)) {
                    throw new Exception('Error al registrar el movimiento de inventario');
                }
                
                // Si todo salió bien, confirmar la transacción
                $this->db->trans_commit();
                
                $mensaje = 'Producto creado exitosamente';
                
                if ($es_ajax) {
                    $this->json_exito($mensaje, ['producto_id' => $producto_id]);
                } else {
                    $this->session->set_flashdata('success', $mensaje);
                    redirect('producto');
                }
                
            } catch (Exception $e) {
                // Si hay un error, deshacer la transacción
                $this->db->trans_rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al crear el producto: ' . $e->getMessage();
            
            if ($this->input->is_ajax_request()) {
                $this->json_error($mensaje);
            } else {
                $this->session->set_flashdata('error', $mensaje);
                redirect('producto/crear');
            }
        }
    }

    /**
     * Actualizar un producto existente
     * Solo para administradores
     * @param int $id ID del producto a actualizar
     */
    public function actualizar($id = null) {
        try {
            // Verificar permisos
            if ($this->session->userdata('rol') !== 'administrador') {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de producto no válido');
            }
            
            // Verificar si el producto existe
            $producto = $this->Producto_model->obtener_por_id($id);
            if (empty($producto)) {
                show_404();
            }
            
            // Verificar si es una petición AJAX
            $es_ajax = $this->input->is_ajax_request();
            
            // Reglas de validación
            $reglas = [
                ['field' => 'codigo', 'label' => 'Código', 'rules' => 'required|callback_check_codigo_unico['.$id.']'],
                ['field' => 'nombre', 'label' => 'Nombre', 'rules' => 'required|max_length[200]'],
                ['field' => 'id_categoria', 'label' => 'Categoría', 'rules' => 'required|numeric'],
                ['field' => 'precio', 'label' => 'Precio', 'rules' => 'required|numeric|greater_than[0]'],
                ['field' => 'stock_minimo', 'label' => 'Stock Mínimo', 'rules' => 'required|integer|greater_than_equal_to[0]'],
                ['field' => 'id_estado', 'label' => 'Estado', 'rules' => 'required|in_list[1,2]']
            ];
            
            $validacion = $this->validar_entrada($reglas);
            
            if (!$validacion['exito']) {
                if ($es_ajax) {
                    $this->json_error('Por favor corrija los errores del formulario', $validacion['errores']);
                } else {
                    $this->session->set_flashdata('error', 'Por favor corrija los errores del formulario');
                    $this->session->set_flashdata('errores', $validacion['errores']);
                    redirect('producto/editar/' . $id);
                }
                return;
            }
            
            // Preparar datos
            $data = [
                'codigo' => $this->input->post('codigo'),
                'nombre' => $this->input->post('nombre'),
                'descripcion' => $this->input->post('descripcion'),
                'id_categoria' => $this->input->post('id_categoria'),
                'precio' => $this->input->post('precio'),
                'stock_minimo' => $this->input->post('stock_minimo'),
                'id_estado' => $this->input->post('id_estado')
            ];
            
            // Actualizar el producto
            if ($this->Producto_model->actualizar($id, $data)) {
                $mensaje = 'Producto actualizado exitosamente';
                
                if ($es_ajax) {
                    $this->json_exito($mensaje);
                } else {
                    $this->session->set_flashdata('success', $mensaje);
                    redirect('producto');
                }
            } else {
                throw new Exception('No se pudo actualizar el producto');
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al actualizar el producto: ' . $e->getMessage();
            
            if ($this->input->is_ajax_request()) {
                $this->json_error($mensaje);
            } else {
                $this->session->set_flashdata('error', $mensaje);
                redirect('producto/editar/' . $id);
            }
        }    
    }

    /**
     * Mostrar formulario para editar un producto
     * Solo para administradores
     * @param int $id ID del producto a editar
     */
    public function editar($id = null) {
        try {
            // Verificar permisos
            if ($this->session->userdata('rol') !== 'administrador') {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de producto no válido');
            }
            
            $producto = $this->Producto_model->obtener_por_id($id);
            
            if (empty($producto)) {
                show_404();
            }
            
            $data = [
                'title' => 'Editar Producto',
                'producto' => $producto,
                'categorias' => $this->Categoria_model->obtener_todas(),
                'modo' => 'editar'
            ];
            
            $this->cargar_vista('admin/productos/formulario', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, $e->getMessage());
        }
    }

    /**
     * Eliminar un producto (solo admin)
     * @param int $id ID del producto a eliminar
     */
    public function eliminar($id = null) {
        try {
            // Verificar permisos
            if ($this->session->userdata('rol') !== 'administrador') {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de producto no válido');
            }
            
            $producto = $this->Producto_model->obtener_por_id($id);
            
            if (empty($producto)) {
                show_404();
            }
            
            $this->Producto_model->eliminar($id);
            $this->session->set_flashdata('success', 'Producto eliminado exitosamente');
            redirect('producto');
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al eliminar el producto');
        }
    }

    /**
     * Actualizar stock de un producto (admin y jefe)
     * @param int $id ID del producto
     */
    public function actualizar_stock($id = null) {
        try {
            // Verificar permisos
            $rol = $this->session->userdata('rol');
            if (!in_array($rol, ['administrador', 'jefe'])) {
                throw new Exception('No tienes permiso para realizar esta acción');
            }
            
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de producto no válido');
            }
            
            // Verificar si el producto existe
            $producto = $this->Producto_model->obtener_por_id($id);
            if (empty($producto)) {
                show_404();
            }
            
            // Verificar si es una petición AJAX
            $es_ajax = $this->input->is_ajax_request();
            
            // Reglas de validación
            $reglas = [
                ['field' => 'tipo_movimiento', 'label' => 'Tipo de Movimiento', 'rules' => 'required|in_list[entrada,salida]'],
                ['field' => 'cantidad', 'label' => 'Cantidad', 'rules' => 'required|integer|greater_than[0]'],
                ['field' => 'observaciones', 'label' => 'Observaciones', 'rules' => 'max_length[500]']
            ];
            
            $validacion = $this->validar_entrada($reglas);
            
            if (!$validacion['exito']) {
                if ($es_ajax) {
                    $this->json_error('Por favor corrija los errores del formulario', $validacion['errores']);
                } else {
                    $this->session->set_flashdata('error', 'Por favor corrija los errores del formulario');
                    $this->session->set_flashdata('errores', $validacion['errores']);
                    redirect('producto/ver/' . $id);
                }
                return;
            }
            
            $tipo_movimiento = $this->input->post('tipo_movimiento');
            $cantidad = (int)$this->input->post('cantidad');
            
            // Calcular nuevo stock
            $nuevo_stock = ($tipo_movimiento === 'entrada') ? 
                $producto->stock + $cantidad : 
                $producto->stock - $cantidad;
            
            // Validar que no haya stock negativo
            if ($nuevo_stock < 0) {
                throw new Exception('No hay suficiente stock para realizar esta operación');
            }
            
            // Iniciar transacción
            $this->db->trans_begin();
            
            try {
                // Actualizar stock
                $actualizado = $this->Producto_model->actualizar($id, ['stock' => $nuevo_stock]);
                
                if (!$actualizado) {
                    throw new Exception('Error al actualizar el stock del producto');
                }
                
                // Registrar movimiento
                $movimiento = [
                    'id_producto' => $id,
                    'tipo_movimiento' => $tipo_movimiento,
                    'cantidad' => $cantidad,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $this->session->userdata('user_id'),
                    'observaciones' => $this->input->post('observaciones')
                ];
                
                if (!$this->Producto_model->registrar_movimiento($movimiento)) {
                    throw new Exception('Error al registrar el movimiento de inventario');
                }
                
                // Si todo salió bien, confirmar la transacción
                $this->db->trans_commit();
                
                $mensaje = 'Stock actualizado exitosamente';
                
                if ($es_ajax) {
                    $this->json_exito($mensaje, ['nuevo_stock' => $nuevo_stock]);
                } else {
                    $this->session->set_flashdata('success', $mensaje);
                    redirect('producto/ver/' . $id);
                }
                
            } catch (Exception $e) {
                // Si hay un error, deshacer la transacción
                $this->db->trans_rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al actualizar el stock: ' . $e->getMessage();
            
            if ($this->input->is_ajax_request()) {
                $this->json_error($mensaje);
            } else {
                $this->session->set_flashdata('error', $mensaje);
                redirect('producto/ver/' . $id);
            }
        }
    }

    /**
     * Obtener productos para Select2 (AJAX)
     */
    public function get_productos_select2() {
        try {
            if (!$this->input->is_ajax_request()) {
                throw new Exception('Acceso no permitido');
            }
            
            $search = $this->input->get('q');
            $productos = $this->Producto_model->buscar_para_select2($search);
            
            $this->json_exito('', $productos);
            
        } catch (Exception $e) {
            $this->json_error('Error al obtener la lista de productos: ' . $e->getMessage());
        }
    }
    
    /**
     * Verificar si un código de producto ya existe (excepto para el ID actual)
     * @param string $codigo Código a verificar
     * @param int $id ID del producto actual (opcional)
     * @return bool
     */
    public function check_codigo_unico($codigo, $id = 0) {
        $existe = $this->Producto_model->codigo_excepto($codigo, $id);
        
        if ($existe) {
            $this->form_validation->set_message('check_codigo_unico', 'El {field} ya está en uso');
            return false;
        }
        
        return true;
    }

    // Generar código de barras
    public function codigo_barras($id) {
        $producto = $this->Producto_model->obtener_por_id($id);
        
        if (empty($producto)) {
            show_404();
        }
        
        // Cargar la biblioteca de códigos de barras
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        
        // Configuración del código de barras
        $barcodeOptions = [
            'text' => $producto->codigo,
            'barHeight' => 50,
            'factor' => 1.5
        ];
        
        $rendererOptions = [];
        
        // Renderizar el código de barras
        $barcode = Zend\Barcode\Barcode::factory(
            'code128', 'image', $barcodeOptions, $rendererOptions
        );
        $imageResource = $barcode->draw();
        
        // Mostrar la imagen
        header('Content-Type: image/png');
        imagepng($imageResource);
        imagedestroy($imageResource);
    }
}
