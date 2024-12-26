<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Función responsable de generar el atributo alt para las imágenes 
 * que no tienen este atributo en las páginas y entradas de WordPress.
 * @param array $images
 */

function seo_generate_alt($images) {
    $updated_count = 0;

    foreach ($images as $image) {

        $post_id = $image['post_id']; //ID del post (entrada o página)
        $post_content = get_post_field('post_content', $post_id); //Contenido del post
        $post_title = get_the_title($post_id); //El título del post, que se utilizará como valor para el atributo alt
        $image_src = $image['image_src']; //La URL de la imagen

        //preg_replace_callback —> Realiza una búsqueda y sustitución de una expresión regular usando una llamada de retorno
        //https://www.php.net/manual/es/function.preg-replace-callback.php

        $updated_content = preg_replace_callback(
            '/(<img[^>]*src=["\']' . preg_quote($image_src, '/') . '["\'][^>]*?)alt=["\']?[^"\']*["\']?([^>]*>)/i',
            function ($matches) use ($post_title) {
                return $matches[1] . 'alt="' . esc_attr($post_title) . '" ' . $matches[2];
            },
            $post_content
        );

        //Si el contenido actualizado es diferente al contenido original (el atributo alt se ha agregado), actualizamos el post
        if ($updated_content !== $post_content) {
            wp_update_post([
                'ID' => $post_id,
                'post_content' => $updated_content,
            ]);
            $updated_count++;
        }

        // Actualizar el ALT de la imagen destacada (thumbnail)
        $thumbnail_id = get_post_thumbnail_id($post_id); // ID de la imagen destacada
        $thumbnail_url = wp_get_attachment_url($thumbnail_id); // URL de la imagen destacada

        if ($thumbnail_id && $thumbnail_url === $image_src) {
            $existing_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            if (empty($existing_alt)) {
                update_post_meta($thumbnail_id, '_wp_attachment_image_alt', $post_title);
                $updated_count++;
            }
        }
        
    }

    return $updated_count;
}