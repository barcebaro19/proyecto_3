<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'application/third_party/Autoloader.php';
require_once 'application/third_party/psr/Autoloader.php';
class Home extends CI_Controller {
    protected $benchmark;

    public function __construct() {
        parent::__construct();
        // Cargar el helper de URL para usar base_url()
        $this->load->helper('url');
        // Cargar la base de datos
        $this->load->database();
    }

    public function index() {
        $this->load->view('home');
    }

    public function base() {
        $this->vista('base');
    }

    /**
     * Función para probar la conexión a la base de datos
     * Accesible desde: [URL]/home/test_db_connection
     */
    public function test_db_connection() {
        // Intentar conectar a la base de datos
        if ($this->db->conn_id) {
            echo '<div style="text-align: center; padding: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;">';
            echo '<h3>✅ Conexión exitosa a la base de datos</h3>';
            echo '<p>Base de datos: ' . $this->db->database . '</p>';
            echo '<p>Host: ' . $this->db->hostname . '</p>';
            echo '<p>Usuario: ' . $this->db->username . '</p>';
            echo '<p>Controlador: ' . $this->db->dbdriver . '</p>';
            echo '</div>';
            
            // Opcional: Mostrar algunas tablas si existen
            echo '<div style="margin: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">';
            echo '<h4>Tablas en la base de datos:</h4>';
            $tables = $this->db->list_tables();
            if (!empty($tables)) {
                echo '<ul>';
                foreach ($tables as $table) {
                    echo '<li>' . $table . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No se encontraron tablas en la base de datos.</p>';
            }
            echo '</div>';
        } else {
            echo '<div style="text-align: center; padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
            echo '<h3>❌ Error de conexión a la base de datos</h3>';
            echo '<p>No se pudo conectar a la base de datos. Por favor verifica la configuración.</p>';
            echo '<p>Error: ' . $this->db->error()['message'] . '</p>';
            echo '</div>';
        }
        
        // Enlace para volver al inicio
        echo '<div style="text-align: center; margin: 20px;">';
        echo '<a href="' . site_url() . '" class="btn btn-primary">Volver al Inicio</a>';
        echo '</div>';
    }
}
