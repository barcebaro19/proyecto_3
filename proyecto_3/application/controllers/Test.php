<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Habilitar reporte de errores
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
    
    public function index() {
        $this->verificar_tabla_roles();
        
        echo "<h1>¡Prueba exitosa!</h1>";
        echo "<p>Si puedes ver este mensaje, el enrutamiento básico está funcionando correctamente.</p>";
        echo "<p><a href='/proyecto_3/login'>Ir al login</a></p>";
        
        // Mostrar información del servidor
        echo "<h3>Información del servidor:</h3>";
        echo "<ul>";
        echo "<li>PHP Version: " . phpversion() . "</li>";
        echo "<li>CodeIgniter Version: " . CI_VERSION . "</li>";
        echo "<li>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
        echo "<li>mod_rewrite: " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Habilitado' : 'Deshabilitado') . "</li>";
        echo "</ul>";
    }
    
    public function phpinfo() {
        phpinfo();
    }
    
    /**
     * Método temporal para verificar la estructura de la tabla roles
     */
    public function verificar_tabla_roles() {
        $this->load->database();
        
        // Verificar si la tabla existe
        if (!$this->db->table_exists('roles')) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;'>";
            echo "<h3>Error: La tabla 'roles' no existe en la base de datos.</h3>";
            echo "<p>Por favor, asegúrate de que la tabla 'roles' exista en tu base de datos.</p>";
            echo "</div>";
            return;
        }
        
        // Obtener la estructura de la tabla
        $fields = $this->db->field_data('roles');
        
        echo "<div style='background: #e2f0fd; color: #004085; padding: 15px; margin: 10px 0; border: 1px solid #b8daff; border-radius: 4px;'>";
        echo "<h3>Estructura de la tabla 'roles':</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Nombre</th><th>Tipo</th><th>Máx. longitud</th></tr>";
        
        foreach ($fields as $field) {
            echo "<tr>";
            echo "<td>" . $field->name . "</td>";
            echo "<td>" . $field->type . "</td>";
            echo "<td>" . $field->max_length . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Mostrar algunos datos de ejemplo
        $query = $this->db->get('roles');
        echo "<h4>Datos de ejemplo en la tabla:</h4>";
        echo "<pre>";
        print_r($query->result_array());
        echo "</pre>";
        
        echo "</div>";
    }
}
