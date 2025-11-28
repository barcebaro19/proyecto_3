<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    protected $table = 'productos';
    protected $primary_key = 'id_producto';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtiene todos los productos con su referencia y estado
     */
    public function obtener_todos_con_referencia() {
        $this->db->select('p.id_producto, p.id_referencia, p.codigo_interno, p.codigo_proveedor, p.descripcion, p.talla, p.color, p.cantidad_stock, p.stock_minimo, p.precio_unitario, p.id_estado, p.fecha_creacion, r.codigo_referencia, r.nombre_referencia, r.descripcion as descripcion_referencia, e.nombre_estado');
        $this->db->from($this->table . ' p');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('estado_general e', 'e.id_estado = p.id_estado', 'left');
        $this->db->order_by('p.id_producto', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Obtiene un producto por ID
     */
    public function obtener_por_id($id) {
        $this->db->select('p.id_producto, p.id_referencia, p.codigo_interno, p.codigo_proveedor, p.descripcion, p.talla, p.color, p.cantidad_stock, p.stock_minimo, p.precio_unitario, p.id_estado, p.fecha_creacion, r.codigo_referencia, r.nombre_referencia, r.descripcion as descripcion_referencia, e.nombre_estado');
        $this->db->from($this->table . ' p');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('estado_general e', 'e.id_estado = p.id_estado', 'left');
        $this->db->where('p.' . $this->primary_key, $id);
        return $this->db->get()->row();
    }

    /**
     * Crea un nuevo producto
     */
    public function crear($data) {
        // Mapear solo columnas válidas de la tabla productos
        $producto = [
            'id_referencia'    => $data['id_referencia'],
            'id_inventario'    => $data['id_inventario'] ?? null,
            'codigo_interno'   => $data['codigo_interno'],
            'codigo_proveedor' => $data['codigo_proveedor'] ?? null,
            'descripcion'      => $data['descripcion'] ?? null,
            'talla'            => $data['talla'] ?? null,
            'color'            => $data['color'] ?? null,
            'cantidad_stock'   => $data['cantidad_stock'] ?? 0,
            'stock_minimo'     => $data['stock_minimo'] ?? 0,
            'precio_unitario'  => $data['precio_unitario'] ?? 0,
            'id_estado'        => $data['id_estado'] ?? 1,
            'creado_por'       => $data['creado_por'] ?? null,
            'fecha_creacion'   => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $producto);
        return $this->db->affected_rows() > 0 ? $this->db->insert_id() : false;
    }

    /**
     * Actualiza un producto
     */
    public function actualizar($id, $data) {
        if (empty($id) || empty($data)) {
            return false;
        }

        $data['fecha_modificacion'] = date('Y-m-d H:i:s');
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Borrado lógico del producto (id_estado = 0)
     */
    public function eliminar($id) {
        return $this->actualizar($id, ['id_estado' => 0]);
    }

    /**
     * Cantidad total de productos
     */
    public function contar_todos() {
        return $this->db->count_all($this->table);
    }

    /**
     * Obtiene productos con stock bajo (cantidad_stock <= stock_minimo)
     */
    public function obtener_bajo_stock($limit = 5) {
        $this->db->select('p.id_producto, p.id_referencia, p.codigo_interno, p.codigo_proveedor, p.descripcion, p.talla, p.color, p.cantidad_stock, p.stock_minimo, p.precio_unitario, p.id_estado, p.fecha_creacion, r.codigo_referencia, r.nombre_referencia');
        $this->db->from($this->table . ' p');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->where('p.cantidad_stock <= p.stock_minimo');
        $this->db->where('p.id_estado', 1);
        $this->db->order_by('p.cantidad_stock', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Alias para compatibilidad con el dashboard
     */
    public function get_bajo_stock($limit = 5) {
        return $this->obtener_bajo_stock($limit);
    }

    public function count_bajo_stock() {
        $this->db->from($this->table);
        $this->db->where('cantidad_stock <= stock_minimo');
        $this->db->where('id_estado', 1);
        return $this->db->count_all_results();
    }

    /**
     * Obtiene todos los productos con cálculos de inventario (Inicial, Entradas, Salidas, Saldo)
     */
    public function obtener_todos_con_calculos() {
        // Obtener productos base
        $productos = $this->obtener_todos_con_referencia();
        
        // Cargar modelo de movimientos si no está cargado
        if (!isset($this->movimiento_model)) {
            $this->load->model('Movimiento_model');
        }

        foreach ($productos as &$producto) {
            // Obtener totales de movimientos
            $totales = $this->Movimiento_model->obtener_totales_por_producto($producto->id_producto);
            
            $producto->total_entradas = $totales->total_entradas;
            $producto->total_salidas = $totales->total_salidas;
            
            // Lógica de cálculo inverso:
            // Saldo = Inv. Inicial - Salidas + Entradas
            // Inv. Inicial = Saldo + Salidas - Entradas
            $producto->saldo_actual = $producto->cantidad_stock;
            $producto->inv_inicial = $producto->cantidad_stock + $producto->total_salidas - $producto->total_entradas;
        }
        
        return $productos;
    }

    /**
     * Cuenta el total de productos
     */
    public function contar_productos() {
        $this->db->where('id_estado', 1);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Obtiene productos con stock bajo el mínimo
     */
    public function obtener_productos_bajo_stock($limit = null) {
        $this->db->select('p.*, r.nombre_referencia, r.codigo_referencia, e.nombre_estado');
        $this->db->from($this->table . ' p');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('estado_general e', 'e.id_estado = p.id_estado', 'left');
        $this->db->where('p.cantidad_stock <=', 'p.stock_minimo', FALSE);
        $this->db->where('p.id_estado', 1);
        $this->db->order_by('p.cantidad_stock', 'ASC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

    /**
     * Obtiene los últimos movimientos
     */
    public function obtener_ultimos_movimientos($limit = 10) {
        $this->db->select('m.*, p.codigo_interno, r.nombre_referencia, u.nombre as usuario_nombre');
        $this->db->from('movimientos_inventario m');
        $this->db->join('productos p', 'p.id_producto = m.id_producto', 'left');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('usuarios u', 'u.id_usuario = m.creado_por', 'left');
        $this->db->order_by('m.fecha_movimiento', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Obtiene productos más vendidos (más salidas)
     */
    public function obtener_mas_vendidos($limit = 10) {
        $this->db->select('p.*, r.nombre_referencia, r.codigo_referencia, 
                          COALESCE(SUM(CASE WHEN m.tipo_movimiento = "salida" THEN m.cantidad ELSE 0 END), 0) as total_salidas');
        $this->db->from($this->table . ' p');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('movimientos_inventario m', 'm.id_producto = p.id_producto', 'left');
        $this->db->where('p.id_estado', 1);
        $this->db->group_by('p.id_producto');
        $this->db->order_by('total_salidas', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Obtiene movimientos de un producto específico
     */
    public function obtener_movimientos_producto($id_producto) {
        $this->db->select('m.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido');
        $this->db->from('movimientos_inventario m');
        $this->db->join('usuarios u', 'u.id_usuario = m.creado_por', 'left');
        $this->db->where('m.id_producto', $id_producto);
        $this->db->order_by('m.fecha_movimiento', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Alias para obtener_todos_con_referencia (compatibilidad)
     */
    public function obtener_todos() {
        return $this->obtener_todos_con_referencia();
    }
}
