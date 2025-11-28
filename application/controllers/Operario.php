<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operario extends MY_Controller {
    
    // Solo operarios pueden acceder a este controlador
    protected $roles_permitidos = ['operario'];
    
    public function __construct() {
        parent::__construct();
        
        // Cargar modelos necesarios
        $this->load->model('Producto_model', 'producto');
        $this->load->model('Movimiento_model', 'movimiento');
        $this->load->model('Categoria_model', 'categoria');
        $this->load->model('Referencia_model', 'referencia');
        $this->load->model('Estado_model', 'estado');
    }
    
    public function index() {
        $this->dashboard();
    }
    
    /**
     * Dashboard del Operario
     */
    /**
     * Dashboard del Operario
     */
    public function dashboard() {
        try {
            // Datos para el dashboard
            $productos_bajo_stock = $this->producto->obtener_bajo_stock(5);
            $ultimos_movimientos = $this->movimiento->obtener_recientes(10);
            
            // Estadísticas rápidas
            $total_productos = $this->producto->contar_todos();
            $total_movimientos = $this->movimiento->count_all();

            // --- Lógica de Matriz para el Dashboard ---
            $productos = $this->producto->obtener_todos_con_calculos();
            $matriz = [];
            $tallas_permitidas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'UNICA'];

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

                $talla = strtoupper(trim($p->talla));
                
                $matriz[$key]['tallas'][$talla] = [
                    'id' => $p->id_producto,
                    'stock' => $p->cantidad_stock,
                    'inv_inicial' => $p->inv_inicial,
                    'entradas' => $p->total_entradas,
                    'salidas' => $p->total_salidas
                ];

                $matriz[$key]['total_stock'] += $p->cantidad_stock;
                $matriz[$key]['total_entradas'] += $p->total_entradas;
                $matriz[$key]['total_salidas'] += $p->total_salidas;
                $matriz[$key]['total_inv_inicial'] += $p->inv_inicial;
            }
            // ------------------------------------------
            
            $data = [
                'title' => 'Panel de Operario',
                'productos_bajo_stock' => $productos_bajo_stock,
                'ultimos_movimientos' => $ultimos_movimientos,
                'stats' => [
                    'total_productos' => $total_productos,
                    'total_movimientos' => $total_movimientos
                ],
                'matriz' => $matriz,
                'tallas_cols' => $tallas_permitidas
            ];
            
            $this->cargar_vista('operario/dashboard', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar el dashboard');
        }
    }

    /**
     * Tabla Principal (Matriz de Inventario)
     */
    public function tabla_principal() {
        try {
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
                'title' => 'Tabla Principal de Inventario',
                'matriz' => $matriz,
                'tallas_cols' => $tallas_permitidas
            ];

            $this->cargar_vista('operario/tabla_principal', $data);

        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar la tabla principal');
        }
    }
    
    /**
     * Gestión de Productos
     */
    public function productos() {
        try {
            $productos = $this->producto->obtener_todos_con_calculos();
            
            $data = [
                'title' => 'Gestión de Inventario',
                'productos' => $productos
            ];
            
            $this->cargar_vista('operario/productos/index', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar productos');
        }
    }
    
    /**
     * Ver detalle de producto
     */
    public function ver_producto($id = null) {
        if (!$id) redirect('operario/productos');
        
        try {
            $producto = $this->producto->obtener_por_id($id);
            if (!$producto) {
                $this->session->set_flashdata('error', 'Producto no encontrado');
                redirect('operario/productos');
            }
            
            $movimientos = $this->movimiento->get_movimientos_producto($id);
            
            $data = [
                'title' => 'Detalle de Producto',
                'producto' => $producto,
                'movimientos' => $movimientos
            ];
            
            $this->cargar_vista('operario/productos/ver', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al ver producto');
        }
    }
    
    /**
     * Crear nuevo producto
     */
    public function crear_producto() {
        // Si es POST, procesar formulario
        if ($this->input->post()) {
            $this->form_validation->set_rules('codigo_interno', 'Código Interno', 'required|is_unique[productos.codigo_interno]');
            $this->form_validation->set_rules('id_referencia', 'Referencia', 'required');
            $this->form_validation->set_rules('cantidad_stock', 'Stock Inicial', 'required|numeric');
            $this->form_validation->set_rules('stock_minimo', 'Stock Mínimo', 'required|numeric');
            
            if ($this->form_validation->run()) {
                $data = [
                    'codigo_interno' => $this->input->post('codigo_interno'),
                    'id_referencia' => $this->input->post('id_referencia'),
                    'cantidad_stock' => $this->input->post('cantidad_stock'),
                    'stock_minimo' => $this->input->post('stock_minimo'),
                    'precio_unitario' => $this->input->post('precio_unitario'),
                    'talla' => $this->input->post('talla'),
                    'color' => $this->input->post('color'),
                    'descripcion' => $this->input->post('descripcion'),
                    'id_estado' => 1, // Activo por defecto
                    'creado_por' => $this->session->userdata('user_id')
                ];
                
                if ($this->producto->crear($data)) {
                    $this->session->set_flashdata('success', 'Producto creado correctamente');
                    redirect('operario/productos');
                } else {
                    $this->session->set_flashdata('error', 'Error al crear producto');
                }
            }
        }
        
        // Cargar vista de formulario
        $data = [
            'title' => 'Nuevo Producto',
            'referencias' => $this->referencia->obtener_todas_con_categoria(),
            'categorias' => $this->categoria->obtener_todas()
        ];
        
        $this->cargar_vista('operario/productos/crear', $data);
    }
    
    /**
     * Editar producto existente
     */
    public function editar_producto($id = null) {
        if (!$id) redirect('operario/productos');
        
        $producto = $this->producto->obtener_por_id($id);
        if (!$producto) redirect('operario/productos');
        
        if ($this->input->post()) {
            // Validaciones
            $this->form_validation->set_rules('cantidad_stock', 'Stock', 'required|numeric');
            
            if ($this->form_validation->run()) {
                $data = [
                    'cantidad_stock' => $this->input->post('cantidad_stock'),
                    'stock_minimo' => $this->input->post('stock_minimo'),
                    'precio_unitario' => $this->input->post('precio_unitario'),
                    'talla' => $this->input->post('talla'),
                    'color' => $this->input->post('color'),
                    'descripcion' => $this->input->post('descripcion')
                ];
                
                if ($this->producto->actualizar($id, $data)) {
                    $this->session->set_flashdata('success', 'Producto actualizado correctamente');
                    redirect('operario/productos');
                } else {
                    $this->session->set_flashdata('error', 'Error al actualizar');
                }
            }
        }
        
        $data = [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'referencias' => $this->referencia->obtener_todas_con_categoria()
        ];
        
        $this->cargar_vista('operario/productos/editar', $data);
    }
    
    /**
     * Gestión de Movimientos (Entradas/Salidas)
     */
    public function movimientos() {
        try {
            $movimientos = $this->movimiento->obtener_todos(100); // Últimos 100 movimientos
            $productos = $this->producto->obtener_todos_con_referencia();
            
            $data = [
                'title' => 'Control de Movimientos',
                'movimientos' => $movimientos,
                'productos' => $productos
            ];
            
            $this->cargar_vista('operario/movimientos/index', $data);
            
        } catch (Exception $e) {
            $this->manejar_error($e, 'Error al cargar movimientos');
        }
    }
    
    /**
     * Registrar nuevo movimiento (AJAX)
     */
    public function registrar_movimiento() {
        if (!$this->es_ajax()) show_404();
        
        $reglas = [
            ['field' => 'id_producto', 'label' => 'Producto', 'rules' => 'required|numeric'],
            ['field' => 'tipo_movimiento', 'label' => 'Tipo', 'rules' => 'required|in_list[entrada,salida]'],
            ['field' => 'cantidad', 'label' => 'Cantidad', 'rules' => 'required|numeric|greater_than[0]'],
            ['field' => 'descripcion', 'label' => 'Descripción', 'rules' => 'trim|max_length[255]']
        ];
        
        $validacion = $this->validar_entrada($reglas);
        if (!$validacion['exito']) {
            $this->json_error('Error de validación', $validacion['errores']);
            return;
        }
        
        $id_producto = $this->input->post('id_producto');
        $tipo = $this->input->post('tipo_movimiento');
        $cantidad = $this->input->post('cantidad');
        
        // Verificar stock para salidas
        if ($tipo === 'salida') {
            $producto = $this->producto->obtener_por_id($id_producto);
            if ($producto->cantidad_stock < $cantidad) {
                $this->json_error('Stock insuficiente para realizar la salida');
                return;
            }
        }
        
        // Iniciar transacción
        $this->db->trans_start();
        
        // 1. Registrar movimiento
        $data_mov = [
            'id_producto' => $id_producto,
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'descripcion' => $this->input->post('descripcion'),
            'fecha_movimiento' => date('Y-m-d H:i:s'),
            'creado_por' => $this->session->userdata('user_id') // Usar ID de sesión correcto
        ];
        
        // Nota: Asegurarse de que 'user_id' es la clave correcta en sesión. 
        // En MY_Controller se usa 'user_id' pero en Admin.php vi 'usuario_id'.
        // Voy a usar $this->usuario_actual->id que se setea en MY_Controller
        if (isset($this->usuario_actual)) {
            $data_mov['creado_por'] = $this->usuario_actual->id;
        }
        
        $this->db->insert('movimientos_inventario', $data_mov);
        
        // 2. Actualizar stock
        $producto = $this->producto->obtener_por_id($id_producto);
        $nuevo_stock = ($tipo === 'entrada') 
            ? $producto->cantidad_stock + $cantidad 
            : $producto->cantidad_stock - $cantidad;
            
        $this->producto->actualizar($id_producto, ['cantidad_stock' => $nuevo_stock]);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->json_error('Error al registrar el movimiento');
        } else {
            $this->json_exito('Movimiento registrado correctamente');
        }
    }

    /**
     * OBTENER SIGUIENTE CÓDIGO INTERNO PARA UNA REFERENCIA (AJAX)
     */
    public function obtener_siguiente_codigo() {
        if (!$this->es_ajax()) show_404();

        $id_referencia = $this->input->post('id_referencia');

        if (empty($id_referencia) || !is_numeric($id_referencia)) {
            $this->json_error('ID de referencia no válido');
            return;
        }

        // Obtener la referencia
        $referencia = $this->db->get_where('referencias', ['id_referencia' => $id_referencia])->row();

        if (!$referencia) {
            $this->json_error('Referencia no encontrada');
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

        $this->json_exito('Código generado', [
            'codigo' => $siguiente_codigo,
            'codigo_referencia' => $referencia->codigo_referencia
        ]);
    }
    /**
     * Perfil del usuario
     */
    public function perfil() {
        // Por ahora, redirigir al dashboard con un mensaje
        $this->session->set_flashdata('info', 'El módulo de perfil está en construcción.');
        redirect('operario/dashboard');
    }
}
