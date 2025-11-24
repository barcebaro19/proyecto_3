<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    /**
     * Obtiene los últimos movimientos de inventario
     * 
     * @param int $limit Número máximo de movimientos a devolver
     * @return array Temporalmente retorna un array vacío hasta que las tablas estén listas
     */
    /**
     * Obtiene los movimientos recientes del sistema
     * 
     * @param int $limit Número máximo de movimientos a devolver
     * @return array Lista de movimientos recientes
     */
    public function obtener_recientes($limit = 10) {
        $this->db->select('t.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido');
        $this->db->from('trazabilidad t');
        $this->db->join('usuarios u', 'u.id_usuario = t.realizado_por', 'left');
        $this->db->order_by('t.fecha_accion', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        
        // Si no hay movimientos, devolver un array vacío
        return [];
    }
    
    /**
     * Alias para mantener compatibilidad con código existente
     */
    public function get_ultimos_movimientos($limit = 10) {
        return $this->obtener_recientes($limit);
    }
    
    /**
     * Cuenta el total de movimientos en el sistema
     * 
     * @return int Retorna 0 ya que la tabla movimientos no existe actualmente
     */
    /**
     * Cuenta el total de movimientos en el sistema
     * 
     * @return int Número total de movimientos
     */
    public function count_all() {
        return 0; // Retorna 0 temporalmente
    }
    
    /**
     * Obtiene el conteo de movimientos por tipo
     * 
     * @return array Temporalmente retorna un array vacío hasta que las tablas estén listas
     */
    public function get_movimientos_por_tipo() {
        return []; // Temporalmente retorna un array vacío hasta que las tablas estén listas
    }
    
    /**
     * Obtiene estadísticas de movimientos de los últimos meses
     * 
     * @param int $months Número de meses a incluir
     * @return array Datos para el gráfico de movimientos
     */
    public function get_movimientos_ultimos_meses($months = 6) {
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-$months months", strtotime($end_date)));
        
        $this->db->select([
            'DATE_FORMAT(fecha, "%Y-%m") as mes',
            'COUNT(CASE WHEN m.tipo = "entrada" THEN 1 END) as entradas',
            'COUNT(CASE WHEN m.tipo = "salida" THEN 1 END) as salidas',
            'COUNT(*) as total'
        ]);
        $this->db->from('movimientos m');
        $this->db->where('m.fecha >=', $start_date);
        $this->db->group_by('mes');
        $this->db->order_by('mes', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Registra un nuevo movimiento en el inventario
     * 
     * @param array $data Datos del movimiento
     * @return bool True si se registró correctamente, False en caso contrario
     */
    public function registrar_movimiento($data) {
        return $this->db->insert('movimientos_inventario', $data);
    }

    /**
     * Obtiene el historial de movimientos de un producto específico
     * 
     * @param int $id_producto ID del producto
     * @param int $limit Número máximo de registros a devolver
     * @return array Lista de movimientos del producto
     */
    public function get_movimientos_producto($id_producto, $limit = 10) {
        $this->db->where('id_producto', $id_producto);
        $this->db->order_by('fecha_movimiento', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get('movimientos_inventario')->result();
    }

    /**
     * Obtiene todos los movimientos de inventario con datos de producto y usuario
     * para mostrarlos en el panel de administración.
     *
     * @param int $limit Límite de registros a devolver (null para sin límite)
     * @return array Lista de movimientos
     */
    public function obtener_todos($limit = 200) {
        $this->db->select('ig.*, r.codigo_referencia, r.nombre_referencia, e.nombre_estado, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario');
        $this->db->from('inventario_global ig');
        $this->db->join('referencias r', 'r.id_referencia = ig.id_referencia', 'left');
        $this->db->join('estado_general e', 'e.id_estado = ig.id_estado', 'left');
        $this->db->join('usuarios u', 'u.id_usuario = ig.creado_por', 'left');
        $this->db->order_by('ig.fecha_creacion', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }
}
