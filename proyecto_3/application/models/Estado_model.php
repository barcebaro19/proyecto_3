<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estado_model extends CI_Model {
    
    private $tabla = 'estado_general';
    private $pk = 'id_estado';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Obtener todos los estados
     * @param bool $activos Si es true, solo devuelve los estados activos
     * @return array Lista de estados
     */
    public function get_estados($activos = false) {
        if ($activos) {
            $this->db->where('id_estado !=', 4); // Excluir estado "Eliminado"
        }
        $query = $this->db->get($this->tabla);
        return $query->result();
    }
    
    /**
     * Obtener un estado por su ID
     * @param int $id ID del estado
     * @return object|null Objeto del estado o null si no se encuentra
     */
    public function get_estado($id) {
        $this->db->where($this->pk, $id);
        $query = $this->db->get($this->tabla);
        return $query->row();
    }
    
    /**
     * Obtener el nombre de un estado por su ID
     * @param int $id_estado ID del estado
     * @return string Nombre del estado o 'Desconocido' si no se encuentra
     */
    public function get_nombre_estado($id_estado) {
        $this->db->select('nombre_estado');
        $this->db->where($this->pk, $id_estado);
        $query = $this->db->get($this->tabla);
        $row = $query->row();
        return $row ? $row->nombre_estado : 'Desconocido';
    }
    
    /**
     * Obtener estados para un select
     * @param bool $incluir_eliminado Si es true, incluye el estado "Eliminado"
     * @return array Lista de estados para usar en un select
     */
    public function get_estados_select($incluir_eliminado = false) {
        $this->db->select("$this->pk as id, nombre_estado as text");
        
        if (!$incluir_eliminado) {
            $this->db->where("$this->pk !=", 4); // Excluir estado "Eliminado"
        }
        
        $this->db->order_by('nombre_estado', 'ASC');
        $query = $this->db->get($this->tabla);
        return $query->result();
    }
    
    /**
     * Verificar si un estado existe
     * @param int $id_estado ID del estado a verificar
     * @return bool True si el estado existe, False en caso contrario
     */
    public function existe_estado($id_estado) {
        $this->db->where($this->pk, $id_estado);
        $query = $this->db->get($this->tabla);
        return $query->num_rows() > 0;
    }
    
    /**
     * Obtener la clase CSS para el badge del estado
     * @param int $id_estado ID del estado
     * @return string Clases CSS para el badge
     */
    public function get_clase_estado($id_estado) {
        $clases = [
            1 => 'bg-success bg-opacity-10 text-success',  // Activo
            2 => 'bg-danger bg-opacity-10 text-danger',    // Inactivo
            3 => 'bg-warning bg-opacity-10 text-warning',  // Pendiente
            4 => 'bg-secondary bg-opacity-10 text-secondary' // Eliminado
        ];
        
        return $clases[$id_estado] ?? 'bg-light text-dark';
    }
    
    /**
     * Obtener el ícono para el estado
     * @param int $id_estado ID del estado
     * @return string Clase del ícono de Font Awesome
     */
    public function get_icono_estado($id_estado) {
        $iconos = [
            1 => 'fa-check-circle',  // Activo
            2 => 'fa-times-circle',  // Inactivo
            3 => 'fa-clock',         // Pendiente
            4 => 'fa-trash'          // Eliminado
        ];
        
        return $iconos[$id_estado] ?? 'fa-question-circle';
    }
}
