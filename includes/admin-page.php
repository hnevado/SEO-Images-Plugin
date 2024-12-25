<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Agregar el menú al administrador
 */
function seo_images_add_menu() {
    add_menu_page(
        'SEO Images',
        'SEO Images',
        'manage_options',
        'seo-images',
        'seo_images_page',
        'dashicons-format-image',
        6
    );
}

/**
 * Mostrar la página de configuración del plugin
 */
function seo_images_page() {
    
    global $wpdb;

    $posts = $wpdb->get_results("
        SELECT ID, post_title, post_content
        FROM {$wpdb->prefix}posts
        WHERE post_type IN ('post', 'page') AND post_status = 'publish'
    ");

    //Inicializamos array
    $images_without_alt = [];

    foreach ($posts as $post) {
        //Buscamos  todas las etiquetas <img> en el contenido del post y y verificamos si el atributo alt está presente pero vacío o ausente.
        if (preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*alt=["\']?["\']?[^>]*>/i', $post->post_content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $img) {
                $src = $img[1];
                $alt_attr = '';

                if (preg_match('/alt=["\']([^"\']*)["\']/', $img[0], $alt_match)) {
                    $alt_attr = $alt_match[1];
                }

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
        $updated_count = seo_generate_alt($images_without_alt);

        echo '<div class="notice notice-success">';
        echo '<p>Se actualizaron ' . $updated_count . ' imágenes con atributos ALT.</p>';
        echo '</div>';
    }

    echo '</div>';
}