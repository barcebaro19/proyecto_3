<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rol_model extends CI_Model {
    
    private $tabla = 'roles';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Obtener todos los roles
     */
    public function get_roles($activos = true) {
        if ($activos) {
            $this->db->where('estado', 1);
        }
        $this->db->select('id_rol, nombre_rol as nombre, estado');
        $query = $this->db->get($this->tabla);
        return $query->result();
    }
    
    /**
     * Obtener un rol por su ID
     */
    public function get_rol($id) {
        $this->db->where('id_rol', $id);
        $this->db->select('id_rol, nombre_rol as nombre, estado');
        $query = $this->db->get($this->tabla);
        return $query->row();
    }
    
    /**
     * Crear un nuevo rol
     */
    public function crear_rol($data) {
        $this->db->insert($this->tabla, $data);
        return $this->db->insert_id();
    }
    
    /**
     * Actualizar un rol existente
     */
    public function actualizar_rol($id, $data) {
        $this->db->where('id_rol', $id);
        return $this->db->update($this->tabla, $data);
    }
    
    /**
     * Eliminar un rol (eliminación lógica)
     */
    public function eliminar_rol($id) {
        $this->db->where('id_rol', $id);
        return $this->db->update($this->tabla, ['estado' => 0]);
    }
    
    /**
     * Verificar si un rol tiene permisos específicos
     */
    public function tiene_permiso($rol_id, $permiso) {
        // Implementar lógica de verificación de permisos según tu sistema
        // Este es solo un ejemplo básico
        $this->db->select('rp.id_rol_permiso');
        $this->db->from('roles_permisos rp');
        $this->db->join('permisos p', 'p.id_permiso = rp.id_permiso');
        $this->db->where('rp.id_rol', $rol_id);
        $this->db->where('p.nombre', $permiso);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    /**
     * Obtener roles para un select
     */
    public function get_roles_select() {
        $this->db->select('id_rol, nombre_rol as nombre');
        $this->db->where('estado', 1);
        $query = $this->db->get($this->tabla);
        return $query->result();
    }
}
