<?php
/**
 * Sedehoo Theme — functions.php
 * Handles: theme setup, CPT, WooCommerce, Elementor, Collections, Downloads, AJAX
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* =============================================================
   1. THEME SETUP
============================================================= */
function sedehoo_setup() {
    load_theme_textdomain( 'sedehoo', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
    add_theme_support( 'custom-logo', [ 'height' => 60, 'width' => 200, 'flex-width' => true, 'flex-height' => true ] );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Elementor full-width support
    add_theme_support( 'align-wide' );

    register_nav_menus( [
        'sidebar-primary'   => __( 'Sidebar – Primary Menu',   'sedehoo' ),
        'sidebar-secondary' => __( 'Sidebar – Secondary Links', 'sedehoo' ),
        'header-nav'        => __( 'Header Navigation',         'sedehoo' ),
        'footer-nav'        => __( 'Footer Navigation',         'sedehoo' ),
    ] );
}
add_action( 'after_setup_theme', 'sedehoo_setup' );

/* =============================================================
   2. ENQUEUE SCRIPTS & STYLES
============================================================= */
function sedehoo_enqueue() {
    wp_enqueue_style( 'sedehoo-fonts',
        'https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap',
        [], null );

    wp_enqueue_style( 'sedehoo-style', get_stylesheet_uri(),
        [ 'sedehoo-fonts' ], wp_get_theme()->get( 'Version' ) );

    wp_enqueue_script( 'sedehoo-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [], wp_get_theme()->get( 'Version' ), true );

    // Photo interactions (only on photo pages)
    if ( is_singular( 'sedehoo_photo' ) || is_post_type_archive( 'sedehoo_photo' ) || is_tax( 'photo_category' ) ) {
        wp_enqueue_script( 'sedehoo-photo',
            get_template_directory_uri() . '/assets/js/photo.js',
            [ 'sedehoo-main' ], wp_get_theme()->get( 'Version' ), true );
    }

    // Dashboard script
    if ( is_page_template( 'templates/dashboard.php' ) || is_page( 'dashboard' ) ) {
        wp_enqueue_script( 'sedehoo-dashboard',
            get_template_directory_uri() . '/assets/js/dashboard.js',
            [ 'sedehoo-main' ], wp_get_theme()->get( 'Version' ), true );
    }

    wp_localize_script( 'sedehoo-main', 'sedehooData', [
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'sedehoo_nonce' ),
        'homeUrl'  => home_url( '/' ),
        'themeUrl' => get_template_directory_uri(),
        'isLoggedIn' => is_user_logged_in(),
        'loginUrl' => wp_login_url( get_permalink() ),
        'dashboardUrl' => home_url( '/dashboard/' ),
        'currency' => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$',
    ] );
}
add_action( 'wp_enqueue_scripts', 'sedehoo_enqueue' );

/* =============================================================
   3. CUSTOMIZER CSS VARIABLES
============================================================= */
function sedehoo_customizer_css() {
    $accent    = get_theme_mod( 'sedehoo_accent_color',   '#7fff5f' );
    $accent2   = get_theme_mod( 'sedehoo_accent_color_2', '#4ade80' );
    $bg        = get_theme_mod( 'sedehoo_bg_color',        '#2e2f3b' );
    $bg2       = get_theme_mod( 'sedehoo_bg2_color',       '#000000' );
    $bg3       = get_theme_mod( 'sedehoo_bg3_color',       '#1a1d24' );
    $border    = get_theme_mod( 'sedehoo_border_color',    '#2a2d35' );
    $text      = get_theme_mod( 'sedehoo_text_color',      '#e8eaf0' );
    $muted     = get_theme_mod( 'sedehoo_muted_color',     '#7a7f8e' );
    $sidebar_w = absint( get_theme_mod( 'sedehoo_sidebar_width', 220 ) );

    echo "<style id='sedehoo-vars'>:root{
        --accent:{$accent};--accent2:{$accent2};
        --bg:{$bg};--bg2:{$bg2};--bg3:{$bg3};
        --border:{$border};--text:{$text};--muted:{$muted};
        --sidebar-w:{$sidebar_w}px;
    }</style>\n";
}
add_action( 'wp_head', 'sedehoo_customizer_css' );

/* =============================================================
   4. CUSTOMIZER SETTINGS
============================================================= */
function sedehoo_customize_register( $wp_customize ) {
    $wp_customize->get_section( 'title_tagline' )->title = __( 'Site Identity & Logo', 'sedehoo' );

    // Colors panel
    $wp_customize->add_panel( 'sedehoo_colors', [ 'title' => __( 'Theme Colors', 'sedehoo' ), 'priority' => 30 ] );

    $colors = [
        'accent_color'   => [ __( 'Accent Color',       'sedehoo' ), '#7fff5f', '--accent' ],
        'accent_color_2' => [ __( 'Accent 2',           'sedehoo' ), '#4ade80', '--accent2' ],
        'bg_color'       => [ __( 'Background',         'sedehoo' ), '#2e2f3b', '--bg' ],
        'bg2_color'      => [ __( 'Background 2',       'sedehoo' ), '#000000', '--bg2' ],
        'bg3_color'      => [ __( 'Background 3',       'sedehoo' ), '#1a1d24', '--bg3' ],
        'border_color'   => [ __( 'Border',             'sedehoo' ), '#2a2d35', '--border' ],
        'text_color'     => [ __( 'Text',               'sedehoo' ), '#e8eaf0', '--text' ],
        'muted_color'    => [ __( 'Muted Text',         'sedehoo' ), '#7a7f8e', '--muted' ],
    ];
    foreach ( $colors as $key => $data ) {
        $wp_customize->add_setting( "sedehoo_{$key}", [ 'default' => $data[1], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "sedehoo_{$key}", [
            'label' => $data[0], 'section' => 'colors', 'panel' => 'sedehoo_colors',
        ] ) );
    }

    // Sidebar settings
    $wp_customize->add_section( 'sedehoo_sidebar', [ 'title' => __( 'Sidebar', 'sedehoo' ), 'priority' => 35 ] );
    $wp_customize->add_setting( 'sedehoo_sidebar_width', [ 'default' => '220', 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_sidebar_width', [ 'label' => __( 'Width (px)', 'sedehoo' ), 'section' => 'sedehoo_sidebar', 'type' => 'number', 'input_attrs' => [ 'min' => 160, 'max' => 320, 'step' => 4 ] ] );
    $wp_customize->add_setting( 'sedehoo_logo_text',    [ 'default' => 'Sedehoo', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_logo_text',    [ 'label' => __( 'Logo Text', 'sedehoo' ), 'section' => 'sedehoo_sidebar', 'type' => 'text' ] );
    $wp_customize->add_setting( 'sedehoo_logo_subtext', [ 'default' => 'Photos', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_logo_subtext', [ 'label' => __( 'Logo Subtitle', 'sedehoo' ), 'section' => 'sedehoo_sidebar', 'type' => 'text' ] );

    // Header
    $wp_customize->add_section( 'sedehoo_header', [ 'title' => __( 'Header', 'sedehoo' ), 'priority' => 36 ] );
    $wp_customize->add_setting( 'sedehoo_btn_signin_text', [ 'default' => 'Sign In', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_btn_signin_text', [ 'label' => __( 'Sign In Text', 'sedehoo' ), 'section' => 'sedehoo_header', 'type' => 'text' ] );
    $wp_customize->add_setting( 'sedehoo_btn_cta_text',   [ 'default' => 'Get Pro', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_btn_cta_text',   [ 'label' => __( 'CTA Button Text', 'sedehoo' ), 'section' => 'sedehoo_header', 'type' => 'text' ] );
    $wp_customize->add_setting( 'sedehoo_btn_cta_url',    [ 'default' => '#', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'postMessage' ] );
    $wp_customize->add_control( 'sedehoo_btn_cta_url',    [ 'label' => __( 'CTA URL', 'sedehoo' ), 'section' => 'sedehoo_header', 'type' => 'url' ] );

    // Footer
    $wp_customize->add_section( 'sedehoo_footer', [ 'title' => __( 'Footer', 'sedehoo' ), 'priority' => 40 ] );
    $wp_customize->add_setting( 'sedehoo_footer_copyright', [
        'default'           => '© ' . date( 'Y' ) . ' Sedehoo. All rights reserved.',
        'sanitize_callback' => 'wp_kses_post', 'transport' => 'postMessage',
    ] );
    $wp_customize->add_control( 'sedehoo_footer_copyright', [ 'label' => __( 'Copyright Text', 'sedehoo' ), 'section' => 'sedehoo_footer', 'type' => 'textarea' ] );
}
add_action( 'customize_register', 'sedehoo_customize_register' );

/* =============================================================
   5. WIDGET AREAS
============================================================= */
function sedehoo_widgets_init() {
    register_sidebar( [ 'name' => __( 'Sidebar Widgets', 'sedehoo' ), 'id' => 'sidebar-widget-area',
        'before_widget' => '<div class="sidebar-widget %2$s">', 'after_widget' => '</div>',
        'before_title' => '<div class="sidebar-section-label">', 'after_title' => '</div>' ] );

    register_sidebar( [ 'name' => __( 'Footer Widgets', 'sedehoo' ), 'id' => 'footer-widget-area',
        'before_widget' => '<div class="footer-widget %2$s">', 'after_widget' => '</div>',
        'before_title' => '<h4>', 'after_title' => '</h4>' ] );
}
add_action( 'widgets_init', 'sedehoo_widgets_init' );


/* =============================================================
   7. ELEMENTOR INTEGRATION
============================================================= */
// Allow Elementor to edit pages with theme header/footer
function sedehoo_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_location( 'header' );
    $elementor_theme_manager->register_location( 'footer' );
}
add_action( 'elementor/theme/register_locations', 'sedehoo_elementor_locations' );

// Ensure Elementor pages get proper body class for full-width
function sedehoo_elementor_body_class( $classes ) {
    if ( function_exists( 'elementor_theme_do_location' ) ) {
        $classes[] = 'elementor-page';
    }
    return $classes;
}
add_filter( 'body_class', 'sedehoo_elementor_body_class' );

/* =============================================================
   8. WOOCOMMERCE INTEGRATION
============================================================= */
// Remove WC default wrappers (we use our own layout)
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end' );
add_action( 'woocommerce_before_main_content', 'sedehoo_woo_before' );
add_action( 'woocommerce_after_main_content',  'sedehoo_woo_after' );

function sedehoo_woo_before() { echo '<div class="sedehoo-woo-wrap">'; }
function sedehoo_woo_after()  { echo '</div>'; }

// Connect photo CPT to WooCommerce product via meta _wc_product_id
function sedehoo_get_photo_product_id( $photo_id ) {
    return get_post_meta( $photo_id, '_wc_product_id', true );
}

function sedehoo_photo_is_free( $photo_id ) {
    $product_id = sedehoo_get_photo_product_id( $photo_id );
    if ( ! $product_id ) return true;
    $product = wc_get_product( $product_id );
    if ( ! $product ) return true;
    return ( (float) $product->get_price() === 0.0 );
}

/* =============================================================
   9. AJAX: ADD / REMOVE COLLECTION
============================================================= */
function sedehoo_ajax_collection() {
    check_ajax_referer( 'sedehoo_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error( [ 'message' => __( 'Please log in to save photos.', 'sedehoo' ) ] );

    $user_id  = get_current_user_id();
    $photo_id = absint( $_POST['photo_id'] ?? 0 );
    $action   = sanitize_text_field( $_POST['collection_action'] ?? 'add' );

    if ( ! $photo_id ) wp_send_json_error( [ 'message' => __( 'Invalid photo.', 'sedehoo' ) ] );

    $collection = get_user_meta( $user_id, 'sedehoo_collection', true );
    if ( ! is_array( $collection ) ) $collection = [];

    if ( $action === 'add' ) {
        if ( ! in_array( $photo_id, $collection ) ) $collection[] = $photo_id;
        $in = true;
        $msg = __( 'Added to collection!', 'sedehoo' );
    } else {
        $collection = array_values( array_diff( $collection, [ $photo_id ] ) );
        $in = false;
        $msg = __( 'Removed from collection.', 'sedehoo' );
    }

    update_user_meta( $user_id, 'sedehoo_collection', $collection );
    wp_send_json_success( [ 'message' => $msg, 'in_collection' => $in, 'count' => count( $collection ) ] );
}
add_action( 'wp_ajax_sedehoo_collection', 'sedehoo_ajax_collection' );

/* =============================================================
   10. AJAX: DISCOVER SIMILAR PHOTOS
============================================================= */
function sedehoo_ajax_similar() {
    check_ajax_referer( 'sedehoo_nonce', 'nonce' );
    $photo_id = absint( $_POST['photo_id'] ?? 0 );
    if ( ! $photo_id ) wp_send_json_error();

    // Get categories of current photo
    $terms = wp_get_post_terms( $photo_id, 'photo_category', [ 'fields' => 'ids' ] );
    $tags  = wp_get_post_terms( $photo_id, 'photo_tag',      [ 'fields' => 'ids' ] );

    $tax_query = [];
    if ( ! empty( $terms ) ) {
        $tax_query[] = [ 'taxonomy' => 'photo_category', 'field' => 'term_id', 'terms' => $terms, 'operator' => 'IN' ];
    }
    if ( count( $tax_query ) > 1 ) $tax_query['relation'] = 'OR';

    $similar = new WP_Query( [
        'post_type'      => 'sedehoo_photo',
        'posts_per_page' => 8,
        'post__not_in'   => [ $photo_id ],
        'tax_query'      => $tax_query ?: [],
        'orderby'        => 'rand',
    ] );

    $photos = [];
    foreach ( $similar->posts as $p ) {
        $thumb = get_the_post_thumbnail_url( $p->ID, 'medium' ) ?: '';
        $photos[] = [
            'id'    => $p->ID,
            'title' => esc_html( $p->post_title ),
            'url'   => get_permalink( $p->ID ),
            'thumb' => $thumb,
        ];
    }
    wp_reset_postdata();
    wp_send_json_success( $photos );
}
add_action( 'wp_ajax_sedehoo_similar',        'sedehoo_ajax_similar' );
add_action( 'wp_ajax_nopriv_sedehoo_similar', 'sedehoo_ajax_similar' );

/* =============================================================
   11. AJAX: DOWNLOAD PHOTO
============================================================= */
function sedehoo_ajax_download() {
    check_ajax_referer( 'sedehoo_nonce', 'nonce' );
    $photo_id = absint( $_POST['photo_id'] ?? 0 );
    if ( ! $photo_id ) wp_send_json_error( [ 'message' => __( 'Invalid photo.', 'sedehoo' ) ] );

    $is_free   = sedehoo_photo_is_free( $photo_id );
    $product_id = sedehoo_get_photo_product_id( $photo_id );

    if ( $is_free ) {
        // Log the download
        sedehoo_log_download( get_current_user_id(), $photo_id, 'free' );
        $download_url = get_post_meta( $photo_id, '_download_file_url', true );
        if ( ! $download_url ) {
            $download_url = get_the_post_thumbnail_url( $photo_id, 'full' );
        }
        wp_send_json_success( [ 'type' => 'free', 'url' => esc_url( $download_url ) ] );
    } else {
        // Return product info for checkout popup
        $product = wc_get_product( $product_id );
        $price   = $product ? wc_price( $product->get_price() ) : '';
        $checkout_url = wc_get_checkout_url();
        // Add to cart URL
        $add_to_cart_url = $product ? $product->add_to_cart_url() : '';
        wp_send_json_success( [
            'type'           => 'paid',
            'price'          => $price,
            'product_id'     => $product_id,
            'checkout_url'   => $checkout_url,
            'add_to_cart_url'=> $add_to_cart_url,
            'thumb'          => get_the_post_thumbnail_url( $photo_id, 'thumbnail' ),
            'title'          => get_the_title( $photo_id ),
        ] );
    }
}
add_action( 'wp_ajax_sedehoo_download',        'sedehoo_ajax_download' );
add_action( 'wp_ajax_nopriv_sedehoo_download', 'sedehoo_ajax_download' );

/* =============================================================
   12. DOWNLOAD LOG
============================================================= */
function sedehoo_log_download( $user_id, $photo_id, $type = 'free' ) {
    if ( ! $user_id ) return;
    $downloads = get_user_meta( $user_id, 'sedehoo_downloads', true );
    if ( ! is_array( $downloads ) ) $downloads = [];
    // Prepend newest first
    array_unshift( $downloads, [
        'photo_id'   => $photo_id,
        'type'       => $type,
        'date'       => current_time( 'timestamp' ),
        'title'      => get_the_title( $photo_id ),
        'thumb'      => get_the_post_thumbnail_url( $photo_id, 'thumbnail' ),
    ] );
    // Keep only last 50
    $downloads = array_slice( $downloads, 0, 50 );
    update_user_meta( $user_id, 'sedehoo_downloads', $downloads );
}

// Log download when WooCommerce order completes
function sedehoo_woo_order_complete( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;
    $user_id = $order->get_customer_id();
    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        // Find photo linked to this product
        $photos = get_posts( [
            'post_type'  => 'sedehoo_photo',
            'meta_key'   => '_wc_product_id',
            'meta_value' => $product_id,
            'numberposts'=> 1,
            'fields'     => 'ids',
        ] );
        if ( $photos ) {
            sedehoo_log_download( $user_id, $photos[0], 'paid' );
        }
    }
}
add_action( 'woocommerce_order_status_completed', 'sedehoo_woo_order_complete' );

/* =============================================================
   13. AJAX: DASHBOARD DATA
============================================================= */
function sedehoo_ajax_dashboard_data() {
    check_ajax_referer( 'sedehoo_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) wp_send_json_error();
    $user_id    = get_current_user_id();
    $collection = get_user_meta( $user_id, 'sedehoo_collection', true );
    $downloads  = get_user_meta( $user_id, 'sedehoo_downloads',  true );
    $collection = is_array( $collection ) ? $collection : [];
    $downloads  = is_array( $downloads )  ? $downloads  : [];

    // Hydrate collection
    $col_data = [];
    foreach ( array_slice( $collection, 0, 50 ) as $pid ) {
        $col_data[] = [
            'id'    => $pid,
            'title' => get_the_title( $pid ),
            'url'   => get_permalink( $pid ),
            'thumb' => get_the_post_thumbnail_url( $pid, 'medium' ) ?: '',
        ];
    }
    wp_send_json_success( [
        'collection' => $col_data,
        'downloads'  => array_slice( $downloads, 0, 20 ),
        'counts' => [
            'collection' => count( $collection ),
            'downloads'  => count( $downloads ),
        ],
    ] );
}
add_action( 'wp_ajax_sedehoo_dashboard_data', 'sedehoo_ajax_dashboard_data' );

/* =============================================================
   14. PAGE TEMPLATES REGISTRATION
============================================================= */
function sedehoo_page_templates( $templates ) {
    $templates['templates/full-width.php'] = __( 'Full Width (No Sidebar)', 'sedehoo' );
    $templates['templates/dashboard.php']  = __( 'User Dashboard',          'sedehoo' );
    $templates['templates/studio.php']     = __( 'Studio Page',             'sedehoo' );
    $templates['templates/video.php']      = __( 'Video Page',              'sedehoo' );
    return $templates;
}
add_filter( 'theme_page_templates', 'sedehoo_page_templates' );

/* =============================================================
   15. HELPER FUNCTIONS
============================================================= */
function sedehoo_get( $key, $fallback = '' ) {
    return get_theme_mod( 'sedehoo_' . $key, $fallback );
}

function sedehoo_is_in_collection( $photo_id, $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    if ( ! $user_id ) return false;
    $collection = get_user_meta( $user_id, 'sedehoo_collection', true );
    return is_array( $collection ) && in_array( (int) $photo_id, array_map( 'intval', $collection ) );
}

// Flush rewrite rules on activation
function sedehoo_flush_rewrite() { flush_rewrite_rules(); }
add_action( 'after_switch_theme', 'sedehoo_flush_rewrite' );

/* =============================================================
   16. ADMIN: Photo Meta Boxes
============================================================= */
function sedehoo_add_photo_meta_boxes() {
    add_meta_box( 'sedehoo_photo_details', __( 'Photo Details & Pricing', 'sedehoo' ),
        'sedehoo_photo_meta_callback', 'sedehoo_photo', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sedehoo_add_photo_meta_boxes' );

function sedehoo_photo_meta_callback( $post ) {
    wp_nonce_field( 'sedehoo_photo_meta', 'sedehoo_photo_meta_nonce' );
    $product_id   = get_post_meta( $post->ID, '_wc_product_id', true );
    $download_url = get_post_meta( $post->ID, '_download_file_url', true );
    $photographer = get_post_meta( $post->ID, '_photographer_name', true );
    $resolution   = get_post_meta( $post->ID, '_photo_resolution', true );
    $file_size    = get_post_meta( $post->ID, '_file_size_mb', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label><?php _e( 'WooCommerce Product ID (for paid photos)', 'sedehoo' ); ?></label></th>
            <td><input type="number" name="sedehoo_wc_product_id" value="<?php echo esc_attr( $product_id ); ?>" class="regular-text" placeholder="Leave empty for free photo" /></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Download File URL (for free photos)', 'sedehoo' ); ?></label></th>
            <td><input type="url" name="sedehoo_download_url" value="<?php echo esc_attr( $download_url ); ?>" class="large-text" placeholder="https://..." /></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Photographer Name', 'sedehoo' ); ?></label></th>
            <td><input type="text" name="sedehoo_photographer" value="<?php echo esc_attr( $photographer ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Resolution (e.g. 4000×3000)', 'sedehoo' ); ?></label></th>
            <td><input type="text" name="sedehoo_resolution" value="<?php echo esc_attr( $resolution ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label><?php _e( 'File Size (MB)', 'sedehoo' ); ?></label></th>
            <td><input type="text" name="sedehoo_file_size" value="<?php echo esc_attr( $file_size ); ?>" class="small-text" /></td>
        </tr>
    </table>
    <?php
}

function sedehoo_save_photo_meta( $post_id ) {
    if ( ! isset( $_POST['sedehoo_photo_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['sedehoo_photo_meta_nonce'], 'sedehoo_photo_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = [
        'sedehoo_wc_product_id'  => '_wc_product_id',
        'sedehoo_download_url'   => '_download_file_url',
        'sedehoo_photographer'   => '_photographer_name',
        'sedehoo_resolution'     => '_photo_resolution',
        'sedehoo_file_size'      => '_file_size_mb',
    ];
    foreach ( $fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }
}
add_action( 'save_post_sedehoo_photo', 'sedehoo_save_photo_meta' );

/* =============================================================
   17. REMOVE "ADD TO POST" BUTTON & MEDIA BUTTON FOR VIDEO/PHOTO CPTs
============================================================= */
function sedehoo_remove_add_to_post_button() {
    global $post;
    if ( ! $post ) return;
    if ( in_array( $post->post_type, [ 'sedehoo_video', 'sedehoo_photo' ] ) ) {
        // Remove media upload buttons and editor toolbar extras
        remove_action( 'media_buttons', 'media_buttons' );
        // Hide the default content editor for video CPT
        if ( $post->post_type === 'sedehoo_video' ) {
            remove_post_type_support( 'sedehoo_video', 'editor' );
            remove_post_type_support( 'sedehoo_video', 'thumbnail' );
        }
    }
}
add_action( 'admin_head-post.php',     'sedehoo_remove_add_to_post_button' );
add_action( 'admin_head-post-new.php', 'sedehoo_remove_add_to_post_button' );

/* Hide "Add to Post" media button and Delete link via CSS for video/photo */
function sedehoo_admin_cpt_styles() {
    global $post, $pagenow;
    if ( ! $post ) return;
    if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ] ) ) return;
    if ( ! in_array( $post->post_type, [ 'sedehoo_video', 'sedehoo_photo' ] ) ) return;
    echo '<style>
        /* Hide Add to Post / Add Media button */
        #wp-content-media-buttons,
        .wp-media-buttons,
        button.insert-media,
        #insert-media-button { display: none !important; }
        /* Hide Move to Trash / Delete link for Videos */
        ' . ( $post->post_type === 'sedehoo_video' ? '#delete-action, .submitdelete { display:none !important; }' : '' ) . '
        /* Hide Delete for Photos too */
        ' . ( $post->post_type === 'sedehoo_photo' ? '#delete-action, .submitdelete { display:none !important; }' : '' ) . '
    </style>';
}
add_action( 'admin_head', 'sedehoo_admin_cpt_styles' );

/* =============================================================
   18. SECURITY HARDENING
============================================================= */
// Remove WordPress version from head and feeds
remove_action( 'wp_head', 'wp_generator' );
function sedehoo_remove_version( $src ) {
    if ( strpos( $src, 'ver=' ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src',  'sedehoo_remove_version', 9999 );
add_filter( 'script_loader_src', 'sedehoo_remove_version', 9999 );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// Disable REST API for unauthenticated users (non-blocking, just removes user enumeration)
function sedehoo_disable_user_rest( $access ) {
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_forbidden', __( 'Authentication required.', 'sedehoo' ), [ 'status' => 401 ] );
    }
    return $access;
}
// Only block /users endpoint enumeration
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) return $result;
    if ( ! is_user_logged_in() ) {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if ( strpos( $request_uri, '/wp/v2/users' ) !== false ) {
            return new WP_Error( 'rest_forbidden', 'Authentication required.', [ 'status' => 401 ] );
        }
    }
    return $result;
} );

// Remove oEmbed/Embed links from head
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// Disable author scans / login enumeration
function sedehoo_no_author_enum() {
    if ( ! is_admin() && isset( $_GET['author'] ) ) {
        wp_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'init', 'sedehoo_no_author_enum' );

// Login error messages — don't reveal which field is wrong
function sedehoo_login_errors() {
    return __( 'Incorrect credentials. Please try again.', 'sedehoo' );
}
add_filter( 'login_errors', 'sedehoo_login_errors' );

// Security headers
function sedehoo_security_headers() {
    if ( ! is_admin() ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
    }
}
add_action( 'send_headers', 'sedehoo_security_headers' );

// Sanitize filename on upload
function sedehoo_sanitize_filename( $filename ) {
    $info = pathinfo( $filename );
    $ext  = ! empty( $info['extension'] ) ? '.' . strtolower( $info['extension'] ) : '';
    $name = sanitize_title( $info['filename'] );
    return $name . $ext;
}
add_filter( 'sanitize_file_name', 'sedehoo_sanitize_filename' );

/* =============================================================
   19. SEO OPTIMIZATION
============================================================= */
// Open Graph meta tags (basic, non-plugin)
function sedehoo_og_meta() {
    if ( is_singular() ) {
        global $post;
        $title       = esc_attr( get_the_title() );
        $description = esc_attr( wp_trim_words( get_the_excerpt() ?: strip_tags( $post->post_content ), 25 ) );
        $image       = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: '';
        $url         = get_permalink();
        $type        = 'article';
        echo "\n<!-- Sedehoo OG Tags -->\n";
        echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
        echo '<meta property="og:title" content="' . $title . '">' . "\n";
        echo '<meta property="og:description" content="' . $description . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
        if ( $image ) echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . $title . '">' . "\n";
        echo '<meta name="twitter:description" content="' . $description . '">' . "\n";
        if ( $image ) echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    } elseif ( is_home() || is_front_page() ) {
        $title       = esc_attr( get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ) );
        $description = esc_attr( get_bloginfo( 'description' ) );
        echo "\n<!-- Sedehoo OG Tags -->\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . $title . '">' . "\n";
        echo '<meta property="og:description" content="' . $description . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( home_url( '/' ) ) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary">' . "\n";
    }
}
add_action( 'wp_head', 'sedehoo_og_meta', 5 );

// Canonical URL
function sedehoo_canonical() {
    if ( is_singular() ) {
        echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
    } elseif ( is_home() || is_front_page() ) {
        echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'sedehoo_canonical', 6 );

// SEO meta description tag for photos/videos
function sedehoo_meta_description() {
    if ( is_singular( [ 'sedehoo_photo', 'sedehoo_video', 'page', 'post' ] ) ) {
        global $post;
        $desc = get_the_excerpt() ?: wp_trim_words( strip_tags( $post->post_content ), 25 );
        if ( $desc ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
        }
    } elseif ( is_home() || is_front_page() ) {
        $desc = get_bloginfo( 'description' );
        if ( $desc ) echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'sedehoo_meta_description', 4 );

// Structured Data (Schema.org) for Photos
function sedehoo_schema_photo() {
    if ( ! is_singular( 'sedehoo_photo' ) ) return;
    global $post;
    $img   = get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '';
    $title = get_the_title();
    $url   = get_permalink();
    $date  = get_the_date( 'c' );
    $author_id = $post->post_author;
    $photographer = get_post_meta( $post->ID, '_photographer_name', true ) ?: get_the_author_meta( 'display_name', $author_id );
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'ImageObject',
        'name'        => $title,
        'contentUrl'  => $img,
        'url'         => $url,
        'datePublished' => $date,
        'author'      => [ '@type' => 'Person', 'name' => $photographer ],
        'description' => get_the_excerpt() ?: '',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'sedehoo_schema_photo', 7 );

// Structured Data for Videos
function sedehoo_schema_video() {
    if ( ! is_singular( 'sedehoo_video' ) ) return;
    global $post;
    $img   = get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '';
    $title = get_the_title();
    $url   = get_permalink();
    $date  = get_the_date( 'c' );
    $schema = [
        '@context'     => 'https://schema.org',
        '@type'        => 'VideoObject',
        'name'         => $title,
        'thumbnailUrl' => $img,
        'url'          => $url,
        'uploadDate'   => $date,
        'description'  => get_the_excerpt() ?: '',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'sedehoo_schema_video', 7 );

// Performance: Preconnect to Google Fonts
function sedehoo_preconnect_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'sedehoo_preconnect_fonts', 1 );

// Add loading="lazy" to all images not already having it (post content)
function sedehoo_add_lazy_loading( $content ) {
    return preg_replace( '/<img((?!.*\bloading\b)[^>]*)>/i', '<img$1 loading="lazy">', $content );
}
add_filter( 'the_content', 'sedehoo_add_lazy_loading' );
