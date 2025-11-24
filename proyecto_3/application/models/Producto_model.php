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
}
