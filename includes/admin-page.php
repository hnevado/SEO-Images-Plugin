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
add_action('admin_menu', 'seo_images_add_menu');

/**
 * Mostrar la página de configuración del plugin
 */
function seo_images_page() {
    global $wpdb;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_alt']) && $_POST['generate_alt'] == '1') {

        // Protección CSRF
        if (!isset($_POST['seo_generate_alt_nonce']) || !wp_verify_nonce($_POST['seo_generate_alt_nonce'], 'seo_generate_alt_action')) {
            wp_die('La validación CSRF ha fallado. Por favor, intenta de nuevo.');
        }

        // Llamada a la función de generación de ALT
        $images_without_alt = seo_get_images_without_alt(); 
        $updated_count = seo_generate_alt($images_without_alt);

        // Mostrar notificación de éxito
        add_settings_error(
            'seo_images_messages',
            'seo_images_message',
            "Se actualizaron $updated_count imágenes con atributos ALT.",
            'updated'
        );
    }

    // Obtener imágenes sin ALT
    $images_without_alt = seo_get_images_without_alt();

    // Mostrar notificaciones (si las hay)
    settings_errors('seo_images_messages');

    ?>
    <div class="wrap">
        <h1>SEO Images ALT Generator</h1>
        <table class="widefat">
            <thead>
                <tr><th>URL Imagen</th><th>Ubicación</th></tr>
            </thead>
            <tbody>
                <?php if (empty($images_without_alt)) : ?>
                    <tr>
                        <td colspan="2">No hay imágenes sin atributo ALT insertadas en páginas o entradas.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($images_without_alt as $image): ?>
                        <tr>
                            <td><a href="<?php echo esc_url($image['image_src']); ?>" target="_blank"><?php echo esc_url($image['image_src']); ?></a></td>
                            <td><a href="<?php echo get_permalink($image['post_id']); ?>" target="_blank"><?php echo esc_html($image['post_title']); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($images_without_alt)) : ?>
            <form method="POST">
                <?php wp_nonce_field('seo_generate_alt_action', 'seo_generate_alt_nonce'); ?>
                <input type="hidden" name="generate_alt" value="1">
                <button type="submit" class="button button-primary">Generar ALT</button>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Obtener imágenes sin atributo ALT
 */
function seo_get_images_without_alt() {
    global $wpdb;

    // Consultar entradas y páginas publicadas
    $posts = $wpdb->get_results("
        SELECT ID, post_title, post_content
        FROM {$wpdb->prefix}posts
        WHERE post_type IN ('post', 'page') AND post_status = 'publish'
    ");

    $images_without_alt = [];

    foreach ($posts as $post) {
        // Analizar imágenes en el contenido
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

        // Analizar la imagen destacada
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if ($thumbnail_id) {
            $thumbnail_url = wp_get_attachment_url($thumbnail_id);
            $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            if (empty($thumbnail_alt)) {
                $images_without_alt[] = [
                    'post_id' => $post->ID,
                    'post_title' => $post->post_title,
                    'image_src' => $thumbnail_url,
                ];
            }
        }
    }

    return $images_without_alt;
}