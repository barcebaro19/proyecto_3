<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debug extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        // Verificar tablas
        $tables = $this->db->list_tables();
        echo "<h2>Tablas en la base de datos:</h2>";
        echo "<pre>";
        print_r($tables);
        echo "</pre>";

        // Verificar datos en tablas clave
        $tables_to_check = ['usuarios', 'productos', 'categorias', 'movimientos'];
        
        foreach ($tables_to_check as $table) {
            if (in_array($table, $tables)) {
                echo "<h3>Datos en la tabla $table:</h3>";
                $query = $this->db->get($table);
                echo "<pre>";
                print_r($query->result_array());
                echo "</pre>";
                
                // Mostrar estructura de la tabla
                echo "<h4>Estructura de la tabla $table:</h4>";
                $fields = $this->db->field_data($table);
                echo "<pre>";
                print_r($fields);
                echo "</pre>";
            } else {
                echo "<p style='color: red;'>La tabla $table no existe en la base de datos.</p>";
            }
            echo "<hr>";
        }
    }
}
