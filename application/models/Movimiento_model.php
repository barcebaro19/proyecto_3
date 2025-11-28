<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    /**
     * Obtiene los movimientos recientes del sistema
     * 
     * @param int $limit Número máximo de movimientos a devolver
     * @return array Lista de movimientos recientes
     */
    public function obtener_recientes($limit = 10) {
        $this->db->select('
            m.id_movimiento,
            m.tipo_movimiento as tipo,
            m.cantidad,
            m.descripcion,
            m.fecha_movimiento as fecha,
            p.codigo_interno,
            r.nombre_referencia,
            CONCAT(u.nombre, " ", u.apellido) as usuario_nombre
        ');
        $this->db->from('movimientos_inventario m');
        $this->db->join('productos p', 'p.id_producto = m.id_producto', 'left');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('usuarios u', 'u.id_usuario = m.creado_por', 'left');
        $this->db->order_by('m.fecha_movimiento', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->result();
            // Formatear para la vista (compatibilidad con vistas que esperan producto_nombre)
            foreach ($result as &$row) {
                $row->producto_nombre = trim(($row->nombre_referencia ?? '') . ' ' . ($row->codigo_interno ?? ''));
            }
            return $result;
        }
        
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
     * @return int Número total de movimientos
     */
    public function count_all() {
        return $this->db->count_all('movimientos_inventario');
    }
    
    /**
     * Obtiene el conteo de movimientos por tipo
     * 
     * @return array
     */
    public function get_movimientos_por_tipo() {
        $this->db->select('tipo_movimiento, COUNT(*) as total');
        $this->db->group_by('tipo_movimiento');
        $query = $this->db->get('movimientos_inventario');
        return $query->result_array();
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
            'DATE_FORMAT(fecha_movimiento, "%Y-%m") as mes',
            // En BD los valores son 'entrada' y 'salida' (lowercase usually in enum, but let's be safe)
            'COUNT(CASE WHEN tipo_movimiento = "entrada" OR tipo_movimiento = "ENTRADA" THEN 1 END) as entradas',
            'COUNT(CASE WHEN tipo_movimiento = "salida" OR tipo_movimiento = "SALIDA" THEN 1 END) as salidas',
            'COUNT(*) as total'
        ]);
        $this->db->from('movimientos_inventario');
        $this->db->where('fecha_movimiento >=', $start_date);
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
        // Normalizar claves al esquema real
        $row = [
            'id_producto'      => $data['id_producto'],
            'tipo_movimiento'  => $data['tipo'] ?? $data['tipo_movimiento'],
            'cantidad'         => $data['cantidad'],
            'descripcion'      => $data['descripcion'] ?? null,
            'creado_por'       => $data['realizado_por'] ?? ($data['id_usuario'] ?? ($data['creado_por'] ?? null)),
            'fecha_movimiento' => $data['fecha'] ?? ($data['fecha_movimiento'] ?? date('Y-m-d H:i:s')),
        ];

        return $this->db->insert('movimientos_inventario', $row);
    }

    /**
     * Obtiene el historial de movimientos de un producto específico
     * 
     * @param int $id_producto ID del producto
     * @param int $limit Número máximo de registros a devolver
     * @return array Lista de movimientos del producto
     */
    public function get_movimientos_producto($id_producto, $limit = 10) {
        $this->db->select('m.*, u.nombre, u.apellido');
        $this->db->from('movimientos_inventario m');
        $this->db->join('usuarios u', 'u.id_usuario = m.creado_por', 'left');
        $this->db->where('m.id_producto', $id_producto);
        $this->db->order_by('m.fecha_movimiento', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Obtiene todos los movimientos de inventario con datos de producto y usuario
     * para mostrarlos en el panel de administración.
     *
     * @param int $limit Límite de registros a devolver (null para sin límite)
     * @return array Lista de movimientos
     */
    public function obtener_todos($limit = 200) {
        $this->db->select('
            m.id_movimiento,
            m.tipo_movimiento,
            m.cantidad,
            m.descripcion,
            m.fecha_movimiento,
            p.codigo_interno,
            r.codigo_referencia,
            r.nombre_referencia,
            u.nombre AS nombre_usuario,
            u.apellido AS apellido_usuario
        ');
        $this->db->from('movimientos_inventario m');
        $this->db->join('productos p', 'p.id_producto = m.id_producto', 'left');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia', 'left');
        $this->db->join('usuarios u', 'u.id_usuario = m.creado_por', 'left');
        $this->db->order_by('m.fecha_movimiento', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }
    /**
     * Obtiene los totales de entradas y salidas para un producto
     * 
     * @param int $id_producto ID del producto
     * @return object Objeto con propiedades total_entradas y total_salidas
     */
    public function obtener_totales_por_producto($id_producto) {
        $this->db->select('
            COALESCE(SUM(CASE WHEN tipo_movimiento = "entrada" OR tipo_movimiento = "ENTRADA" THEN cantidad ELSE 0 END), 0) as total_entradas,
            COALESCE(SUM(CASE WHEN tipo_movimiento = "salida" OR tipo_movimiento = "SALIDA" THEN cantidad ELSE 0 END), 0) as total_salidas
        ');
        $this->db->from('movimientos_inventario');
        $this->db->where('id_producto', $id_producto);
        return $this->db->get()->row();
    }
}
