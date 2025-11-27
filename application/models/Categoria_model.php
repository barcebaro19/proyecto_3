<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Cuenta el total de categorías activas en el sistema
     * 
     * @return int Número total de categorías activas
     */
    public function count_all() {
        $this->db->where('id_estado', 1);
        return $this->db->count_all_results('categorias');
    }
    
    /**
     * Obtiene categorías con el conteo de productos
     * 
     * @param int $limit Número máximo de categorías a devolver (opcional)
     * @return array Lista de categorías con el conteo de productos
     */
    public function obtener_con_conteo($limit = null) {
    try {
    if (!$this->db->table_exists('categorias') || !$this->db->table_exists('productos') || !$this->db->table_exists('referencias')) {
        return [];
    }

    $this->db->select('c.id_categoria, c.nombre, c.descripcion, COUNT(p.id_producto) as total_productos', false);
    $this->db->from('categorias c');
    $this->db->join('referencias r', 'r.id_categoria = c.id_categoria AND r.id_estado = 1', 'left');
    $this->db->join('productos p', 'p.id_referencia = r.id_referencia AND p.id_estado = 1', 'left');
    $this->db->where('c.id_estado', 1);
    $this->db->group_by('c.id_categoria, c.nombre, c.descripcion');
    $this->db->order_by('c.nombre', 'ASC'); 
            
            if ($limit !== null) {
                $this->db->limit($limit);
            }
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error en Categoria_model/obtener_con_conteo: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Alias para mantener compatibilidad con código existente
     */
    public function get_categorias_con_conteo($limit = null) {
        return $this->obtener_con_conteo($limit);
    }
    
    // Obtener todas las categorías
    public function obtener_todas() {
        $this->db->where('id_estado', 1); // Solo categorías activas
        $this->db->order_by('nombre', 'ASC');
        $this->db->select('id_categoria, nombre, descripcion, id_estado');
        return $this->db->get('categorias')->result();
    }

    // Obtener una categoría por ID
    public function obtener_por_id($id) {
        $this->db->where('id_categoria', $id);
        $this->db->where('id_estado', 1);
        $this->db->select('id_categoria, nombre, descripcion, id_estado');
        return $this->db->get('categorias')->row();
    }

    // Crear una nueva categoría
    public function crear($data) {
        return $this->db->insert('categorias', $data);
    }

    // Actualizar una categoría
    public function actualizar($id, $data) {
        $this->db->where('id_categoria', $id);
        return $this->db->update('categorias', $data);
    }

    // Eliminar una categoría (borrado lógico)
    public function eliminar($id) {
        // Verificar si la categoría está siendo usada
        if ($this->esta_en_uso($id)) {
            return false;
        }
        
        $this->db->where('id_categoria', $id);
        return $this->db->update('categorias', ['id_estado' => 0]);
    }

    // Verificar si una categoría está siendo usada por algún producto
    public function esta_en_uso($id) {
        $this->db->where('id_categoria', $id);
        $this->db->where('id_estado', 1);
        $count = $this->db->count_all_results('productos');
        
        return $count > 0;
    }


    // Obtener categorías para select
    public function obtener_para_select() {
        $this->db->select('id_categoria as id, nombre as text');
        $this->db->where('id_estado', 1);
        $this->db->order_by('nombre', 'ASC');
        return $this->db->get('categorias')->result();
    }
}
