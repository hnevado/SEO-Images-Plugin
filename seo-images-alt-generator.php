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
    global $wpdb;

    // Consulta para obtener las entradas y páginas con contenido
    $posts = $wpdb->get_results("
        SELECT ID, post_title, post_content
        FROM {$wpdb->prefix}posts
        WHERE post_type IN ('post', 'page') AND post_status = 'publish'
    ");

    $images_without_alt = [];

    // Analizar el contenido de cada entrada/página
    foreach ($posts as $post) {
        if (preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*alt=["\']?["\']?[^>]*>/i', $post->post_content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $img) {
                $src = $img[1];
                $alt_attr = '';

                // Extraer el valor del atributo ALT si existe
                if (preg_match('/alt=["\']([^"\']*)["\']/', $img[0], $alt_match)) {
                    $alt_attr = $alt_match[1];
                }

                // Si el ALT está vacío, agregarlo a la lista
                if (empty($alt_attr)) {
                    $images_without_alt[] = [
                        'post_id' => $post->ID,
                        'post_title' => $post->post_title,
                        'image_src' => $src,
                    ];
                }
            }
        }
    }

    // Mostrar la página del plugin
    echo '<div class="wrap">';
    echo '<h1>SEO Images ALT Generator</h1>';
    echo '<table class="widefat">';
    echo '<thead><tr><th>URL Imagen</th><th>Ubicación</th></tr></thead><tbody>';

    if (empty($images_without_alt)) {
        echo '<tr><td colspan="2">No hay imágenes sin atributo ALT insertadas en páginas o entradas.</td></tr>';
    } else {
        foreach ($images_without_alt as $image) {
            echo '<tr>';
            echo '<td><a href="' . esc_url($image['image_src']) . '" target="_blank">' . esc_url($image['image_src']) . '</a></td>';
            echo '<td><a href="' . get_permalink($image['post_id']) . '" target="_blank">' . esc_html($image['post_title']) . '</a></td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';

    if (!empty($images_without_alt)) {
        echo '<form method="post">';
        echo '<input type="hidden" name="generate_alt" value="1">';
        echo '<button type="submit" class="button button-primary">Generar ALT</button>';
        echo '</form>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_alt']) && $_POST['generate_alt'] == '1') {
        seo_generate_alt($images_without_alt);
    }

    echo '</div>';
}

// Función para generar los ALT
function seo_generate_alt($images) {
    $updated_count = 0;

    foreach ($images as $image) {
        $post_id = $image['post_id'];
        $post_content = get_post_field('post_content', $post_id);
        $post_title = get_the_title($post_id);
        $image_src = $image['image_src'];

        // Actualizar solo el atributo ALT en el contenido
        $updated_content = preg_replace_callback(
            '/(<img[^>]*src=["\']' . preg_quote($image_src, '/') . '["\'][^>]*?)alt=["\']?[^"\']*["\']?([^>]*>)/i',
            function ($matches) use ($post_title) {
                return $matches[1] . 'alt="' . esc_attr($post_title) . '" ' . $matches[2];
            },
            $post_content
        );

        if ($updated_content !== $post_content) {
            wp_update_post([
                'ID' => $post_id,
                'post_content' => $updated_content,
            ]);
            $updated_count++;
        }
    }

    echo '<div class="notice notice-success">';
    echo '<p>Se actualizaron ' . $updated_count . ' imágenes con atributos ALT.</p>';
    echo '</div>';
}

?>