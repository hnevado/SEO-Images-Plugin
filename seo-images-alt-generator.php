<?php
/*
Plugin Name: SEO Images ALT Generator
Description: Añade automáticamente atributos ALT a imágenes que no lo tienen, basados en el nombre de la página o entrada.
Version: 1.0
Author: Héctor Nevado (@hnevado.dev)
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Definir constantes
define('SEO_IMAGES_PATH', plugin_dir_path(__FILE__));
define('SEO_IMAGES_URL', plugin_dir_url(__FILE__));

// Cargar archivos requeridos
require_once SEO_IMAGES_PATH . 'includes/admin-page.php';
require_once SEO_IMAGES_PATH . 'includes/functions.php';

// Hooks iniciales
add_action('admin_menu', 'seo_images_add_menu');

?>