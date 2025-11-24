<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referencia_model extends CI_Model {

    protected $table = 'referencias';
    protected $primary_key = 'id_referencia';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_todas_con_categoria() {
        $this->db->select('r.id_referencia, r.codigo_referencia, r.nombre_referencia, r.descripcion, r.id_estado, r.creado_por, r.id_categoria, r.fecha_creacion, r.fecha_modificacion, c.nombre as nombre_categoria, e.nombre_estado');
        $this->db->from($this->table . ' r');
        $this->db->join('categorias c', 'c.id_categoria = r.id_categoria', 'left');
        $this->db->join('estado_general e', 'e.id_estado = r.id_estado', 'left');
        $this->db->order_by('r.id_referencia', 'ASC');
        return $this->db->get()->result();
    }

    public function obtener_por_id($id) {
        $this->db->select('r.id_referencia, r.codigo_referencia, r.nombre_referencia, r.descripcion, r.id_estado, r.creado_por, r.id_categoria, r.fecha_creacion, r.fecha_modificacion, c.nombre as nombre_categoria, e.nombre_estado');
        $this->db->from($this->table . ' r');
        $this->db->join('categorias c', 'c.id_categoria = r.id_categoria', 'left');
        $this->db->join('estado_general e', 'e.id_estado = r.id_estado', 'left');
        $this->db->where('r.' . $this->primary_key, $id);
        return $this->db->get()->row();
    }

    public function crear($data) {
        $ref = [
            'codigo_referencia' => $data['codigo_referencia'],
            'nombre_referencia' => $data['nombre_referencia'],
            'descripcion'       => $data['descripcion'] ?? null,
            'id_estado'         => $data['id_estado'] ?? 1,
            'creado_por'        => $data['creado_por'] ?? null,
            'id_categoria'      => $data['id_categoria'] ?? null,
            'fecha_creacion'    => date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->table, $ref);
        return $this->db->affected_rows() > 0 ? $this->db->insert_id() : false;
    }

    public function actualizar($id, $data) {
        if (empty($id) || empty($data)) return false;
        $data['fecha_modificacion'] = date('Y-m-d H:i:s');
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    public function eliminar($id) {
        return $this->actualizar($id, ['id_estado' => 4]); // 4 = Eliminado en estado_general
    }

    public function contar_todas() {
        return $this->db->count_all($this->table);
    }
}
