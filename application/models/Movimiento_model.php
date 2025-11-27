<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');

        // Auto-fix: Create table if it doesn't exist
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
        $this->db->query($sql);
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
            p.color,
            p.talla
        ');
        $this->db->from('movimientos_inventario m');
        $this->db->join('productos p', 'p.id_producto = m.id_producto');
        $this->db->join('referencias r', 'r.id_referencia = p.id_referencia');
        $this->db->order_by('m.fecha_movimiento', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->result();
            // Formatear para la vista
            foreach ($result as &$row) {
                $row->producto_nombre = $row->nombre_referencia . ' ' . $row->color . ' ' . $row->talla;
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
        $this->db->select('
            m.id_movimiento,
            m.tipo_movimiento,
            m.cantidad,
            m.descripcion,
            m.fecha_movimiento,
            p.codigo_interno,
            p.color,
            p.talla,
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
            COALESCE(SUM(CASE WHEN tipo_movimiento = "entrada" THEN cantidad ELSE 0 END), 0) as total_entradas,
            COALESCE(SUM(CASE WHEN tipo_movimiento = "salida" THEN cantidad ELSE 0 END), 0) as total_salidas
        ');
        $this->db->from('movimientos_inventario');
        $this->db->where('id_producto', $id_producto);
        return $this->db->get()->row();
    }
}
