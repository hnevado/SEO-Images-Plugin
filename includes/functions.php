<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Genera atributos ALT para imágenes sin ALT.
 *
 * @param array $images
 */

function seo_generate_alt($images) {
    $updated_count = 0;

    foreach ($images as $image) {
        $post_id = $image['post_id'];
        $post_content = get_post_field('post_content', $post_id);
        $post_title = get_the_title($post_id);
        $image_src = $image['image_src'];

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

    return $updated_count;
}