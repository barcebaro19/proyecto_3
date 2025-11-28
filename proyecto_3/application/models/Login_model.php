<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends MY_Model {
    
    protected $table = 'login';
    protected $table_id = 'usuario';
    
    public function __construct() {
        parent::__construct();
        $this->table = 'login';
        $this->table_id = 'usuario';
    }

    /**
     * Verifica las credenciales de un usuario
     * @param string $email
     * @param string $password
     * @return array|bool Datos del usuario si las credenciales son correctas, false en caso contrario
     */
    public function check_credentials($email, $password) {
        $this->db->where('email', $email);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() === 1) {
            $user = $query->row_array();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
}
