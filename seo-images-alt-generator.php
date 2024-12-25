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

// Agregar el menú en el administrador
add_action('admin_menu', function () {
    add_menu_page(
        'SEO Images',
        'SEO Images',
        'manage_options',
        'seo-images',
        'seo_images_page',
        'dashicons-format-image',
        6
    );
});

// Función para mostrar el listado de imágenes sin ALT
function seo_images_page() {
 
}

// Función para generar los ALT
function seo_generate_alt($images) {
 
}

?>