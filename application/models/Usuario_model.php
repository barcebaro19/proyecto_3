<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    /**
     * Modelo unificado de Usuarios
     * Este archivo es la fuente de verdad para la gestión de usuarios.
     * Reemplaza a User_model.php y Usuario_model_new.php
     */

    protected $table = 'usuarios';
    protected $primary_key = 'id_usuario';
    protected $return_type = 'object';
    // Columnas reales de la tabla usuarios
    // tipo_documento, numero_documento, nombre, apellido, fecha_nacimiento,
    // id_genero, telefono, correo, contrasena, intentos_fallidos, ultimo_acceso,
    // fecha_bloqueo, direccion, id_estado_civil, id_rol, id_estado,
    // fecha_creacion, fecha_modificacion

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('security');
    }
    
    public function obtener_todos_con_rol() {
    $this->db->select('u.id_usuario, u.tipo_documento, u.numero_documento, u.nombre, u.apellido, u.fecha_nacimiento, u.id_genero, u.telefono, u.correo, u.contrasena, u.direccion, u.id_estado_civil, u.id_rol, u.id_estado, u.fecha_creacion, u.ultimo_acceso, r.nombre_rol AS nombre_rol, e.nombre_estado AS nombre_estado');
    $this->db->from($this->table . ' u');
    $this->db->join('roles r', 'r.id_rol = u.id_rol', 'left');
    $this->db->join('estado_general e', 'e.id_estado = u.id_estado', 'left');
    return $this->db->get()->result();
}

    /**
     * Obtiene un usuario por su ID con información de roles
     * @param int $id ID del usuario
     * @return object Datos del usuario
     */
    public function obtener_por_id($id) {
        // Debug: Verificar conexión
        $db_connected = $this->db->initialize();
        log_message('debug', 'Conexión a la base de datos: ' . ($db_connected ? 'Éxito' : 'Error'));
        
        // Obtener datos del usuario con los nombres de columnas correctos
        $this->db->select('u.id_usuario, u.tipo_documento, u.numero_documento, u.nombre, u.apellido, u.fecha_nacimiento, u.id_genero, u.telefono, u.correo, u.contrasena, u.direccion, u.id_estado_civil, u.id_rol, u.id_estado, u.fecha_creacion, u.ultimo_acceso, r.nombre_rol, e.nombre_estado');
        $this->db->from($this->table . ' u');
        $this->db->join('roles r', 'r.id_rol = u.id_rol', 'left');
        $this->db->join('estado_general e', 'e.id_estado = u.id_estado', 'left');
        $this->db->where('u.id_usuario', $id);
        
        // Habilitar depuración de consultas
        $this->db->db_debug = TRUE;
        $query = $this->db->get();
        $this->db->db_debug = FALSE;
        
        $usuario = $query->row();
        
        // Debug: Verificar datos obtenidos
        log_message('debug', 'Datos del usuario obtenidos: ' . print_r($usuario, TRUE));
        
        // Asegurar compatibilidad con nombres de propiedades
        if ($usuario) {
            // Agregar alias para compatibilidad
            if (isset($usuario->nombre_rol)) {
                $usuario->rol_nombre = $usuario->nombre_rol;
            }
            if (isset($usuario->nombre_estado)) {
                $usuario->estado_nombre = $usuario->nombre_estado;
            }
        }
        
        return $usuario;
    }
    
    /**
     * Cuenta el total de usuarios activos
     * @return int Número de usuarios activos
     */
    public function count_all() {
        $this->db->where('id_estado', 1);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Crea un nuevo usuario en la base de datos
     * @param array $data Datos del usuario a crear
     * @return int|bool ID del usuario creado o FALSE en caso de error
     */
    public function crear($data) {
        // Validar campos requeridos
        if (empty($data['correo']) || empty($data['contrasena'])) {
            return false;
        }

        // Preparar datos del usuario según la estructura real
        $usuario = [
            'tipo_documento'   => $data['tipo_documento'] ?? null,
            'numero_documento' => $data['numero_documento'] ?? null,
            'nombre'           => $data['nombre'] ?? '',
            'apellido'         => $data['apellido'] ?? '',
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'id_genero'        => $data['id_genero'] ?? null,
            'telefono'         => $data['telefono'] ?? null,
            'correo'           => $data['correo'],
            'contrasena'       => password_hash($data['contrasena'], PASSWORD_DEFAULT),
            'direccion'        => $data['direccion'] ?? null,
            'id_estado_civil'  => $data['id_estado_civil'] ?? null,
            'id_rol'           => $data['id_rol'] ?? 3,
            'id_estado'        => $data['id_estado'] ?? 1,
            'fecha_creacion'   => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $usuario);
        
        return $this->db->affected_rows() > 0 ? $this->db->insert_id() : false;
    }
    
    /**
     * Verifica las credenciales de un usuario
     * @param string $email Correo electrónico
     * @param string $password Contraseña sin encriptar
     * @return object|bool Datos del usuario si las credenciales son correctas, FALSE en caso contrario
     */
    public function verificar_credenciales($correo, $password) {
        $this->db->select('u.id_usuario, u.tipo_documento, u.numero_documento, u.nombre, u.apellido, u.fecha_nacimiento, u.id_genero, u.telefono, u.correo, u.contrasena, u.direccion, u.id_estado_civil, u.id_rol, u.id_estado, u.fecha_creacion, u.ultimo_acceso, r.nombre_rol as nombre_rol');
        $this->db->from($this->table . ' u');
        $this->db->join('roles r', 'r.id_rol = u.id_rol', 'left');
        $this->db->where('u.correo', $correo);
        $this->db->where('u.id_estado', 1); // Usuarios activos
        $usuario = $this->db->get()->row();

        if ($usuario && password_verify($password, $usuario->contrasena)) {
            // Agregar propiedad 'rol' para compatibilidad con Login controller
            $usuario->rol = $usuario->nombre_rol;
            return $usuario;
        }
        
        return false;
    }

    // Método usado por Login::authenticate
    public function login($correo, $password)
    {
        return $this->verificar_credenciales($correo, $password);
    }
    
    /**
     * Actualiza la información de un usuario
     * @param int $id ID del usuario
     * @param array $data Datos a actualizar
     * @return bool TRUE si la actualización fue exitosa, FALSE en caso contrario
     */
    public function actualizar($id, $data) {
        if (empty($id) || empty($data)) {
            return false;
        }

        // Si se está actualizando la contraseña, hashearla
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        }

        $data['fecha_modificacion'] = date('Y-m-d H:i:s');
        
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);
        
        return $this->db->affected_rows() >= 0; // Retorna true incluso si no se modificó ningún campo
    }

    /**
     * Elimina un usuario (cambia su estado a inactivo)
     * @param int $id ID del usuario
     * @return bool TRUE si la operación fue exitosa, FALSE en caso contrario
     */
    public function eliminar($id) {
        return $this->actualizar($id, ['id_estado' => 0]);
    }

    /**
     * Actualiza la última vez que el usuario inició sesión
     * @param int $id ID del usuario
     * @return bool TRUE si la actualización fue exitosa
     */
    public function actualizar_ultimo_acceso($id) {
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, [
            'ultimo_acceso' => date('Y-m-d H:i:s')
        ]);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Verifica si un correo electrónico ya está registrado
     * @param string $email Correo electrónico a verificar
     * @param int $excluir_id ID del usuario a excluir de la búsqueda
     * @return bool TRUE si el correo ya existe, FALSE en caso contrario
     */
    public function email_existe($correo, $excluir_id = null) {
        $this->db->where('correo', $correo);
        if ($excluir_id) {
            $this->db->where($this->primary_key . ' !=', $excluir_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Obtiene todos los roles disponibles
     * @return array Lista de roles
     */
    public function obtener_roles() {
        return $this->db->get('roles')->result();
    }

    /**
     * Obtiene todos los estados disponibles
     * @return array Lista de estados
     */
    public function obtener_estados() {
        return $this->db->get('estado_general')->result();
    }

    /**
     * Busca usuarios por término de búsqueda
     * @param string $termino Término de búsqueda
     * @return array Lista de usuarios que coinciden con el término
     */
    public function buscar($termino) {
        $this->db->select('u.id_usuario, u.tipo_documento, u.numero_documento, u.nombre, u.apellido, u.fecha_nacimiento, u.id_genero, u.telefono, u.correo, u.contrasena, u.direccion, u.id_estado_civil, u.id_rol, u.id_estado, u.fecha_creacion, u.ultimo_acceso, r.nombre_rol as nombre_rol');
        $this->db->from($this->table . ' u');
        $this->db->join('roles r', 'r.id_rol = u.id_rol', 'left');
        $this->db->group_start();
        $this->db->like('u.nombre', $termino);
        $this->db->or_like('u.apellido', $termino);
        $this->db->or_like('u.correo', $termino);
        $this->db->or_like('r.nombre_rol', $termino);
        $this->db->group_end();
        $this->db->where('u.id_estado', 1); // Solo usuarios activos
        
        return $this->db->get()->result();
    }
}
