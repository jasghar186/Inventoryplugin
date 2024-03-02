<?php

/**
 * Register the function to run on plugin activation
 */

// Enqueue scripts
function durango_inventory_enqueue_scripts() {
    $inventory_plugin_version = wp_get_theme()->get('Version');
    // wp_enqueue_style('durango_inventory_plugin_styles', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0');
    wp_enqueue_style('durango_inventory_plugin_bootstrap_styles', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', array(), '1.0');
    // Bootstrap icons
    wp_enqueue_style('durango_inventory_plugin_bootstrap_icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3');
    wp_enqueue_style('durango_inventory_plugin_styles', plugin_dir_url(__FILE__) . 'assets/css/style.css?unique='.time(), array(), '1.0');

    // Script Files
    wp_enqueue_script('durango_inventory_bootstrap_script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.2',true);
    wp_enqueue_script('durango_inventory_plugin_script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), $inventory_plugin_version, true);
    wp_enqueue_script('durango_inventory_script', plugin_dir_url(__FILE__) . 'assets/js/inventory.js', array('jquery'), $inventory_plugin_version,true);
    wp_localize_script('durango_inventory_script', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
    wp_localize_script('durango_inventory_plugin_script', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'durango_inventory_enqueue_scripts');

// Enqueue scripts in admin side
function durango_inventory_enqueue_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] === 'durango-inventory-options') {
        wp_enqueue_style('durango_inventory_plugin_bootstrap_styles', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', array(), '1.0');
    }
}
add_action('admin_enqueue_scripts', 'durango_inventory_enqueue_admin_scripts');

// require_once plugin_dir_url(__FILE__) . 'template-tags/control-panel.php';
require_once('template-tags/control-panel.php');
require_once('import-csv/importer.php');
require_once('template-tags/vehicle-card.php');

add_filter( 'cron_schedules', 'cronTwoHoursDelay' );
function cronTwoHoursDelay( $schedules ) {
    $schedules['two_hours'] = array(
        'interval' => 7200,
        'display'  => esc_html__( 'Every Two Hours' ), );
    return $schedules;
}

function custom_cron_plugin_activate() {
    // Schedule the daily cron job
    if( !wp_next_scheduled('listings_batches_import') ) {
        wp_schedule_event(time(), 'two_hours', 'listings_batches_import');
    }
}
add_action('listings_batches_import', 'listingsBatchesImport');

function durango_options_page() {
    add_menu_page(
        'Inventory Options',
        'Inventory Options',
        'manage_options',
        'durango-inventory-options',
        'durango_inventory_options_page_content',
        'dashicons-admin-generic',
        30
    );
}
add_action('admin_menu', 'durango_options_page');

// Register Custom Post Type
if ( ! function_exists('durango_inventory_listings') ) {
    function durango_inventory_listings() {
        $labels = array(
            'name'                  => _x( 'Listings', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Listing', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Listings', 'text_domain' ),
            'name_admin_bar'        => __( 'Listing', 'text_domain' ),
            'archives'              => __( 'Listing Archives', 'text_domain' ),
            'attributes'            => __( 'Listing Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Listing:', 'text_domain' ),
            'all_items'             => __( 'All Listings', 'text_domain' ),
            'add_new_item'          => __( 'Add New Listing', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Listing', 'text_domain' ),
            'edit_item'             => __( 'Edit Listing', 'text_domain' ),
            'update_item'           => __( 'Update Listing', 'text_domain' ),
            'view_item'             => __( 'View Listing', 'text_domain' ),
            'view_items'            => __( 'View Listings', 'text_domain' ),
            'search_items'          => __( 'Search Listings', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into Listing', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Listing', 'text_domain' ),
            'items_list'            => __( 'Listings list', 'text_domain' ),
            'items_list_navigation' => __( 'Listings list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter listings list', 'text_domain' ),
        );
        $args = array(
            'label'                 => __( 'Listing', 'text_domain' ),
            'description'           => __( 'Manage Listings', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
            'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-car',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'listings', $args );

        require_once('template-tags/pagination.php');
    
    }
    add_action( 'init', 'durango_inventory_listings', 0 );
}


// Include filters.php file
require_once('template-tags/filters.php');

function custom_template_include($template) {
    if (is_singular('listings')) {
        // Check if the template file exists in the plugin directory
        $custom_template = plugin_dir_path(__FILE__) . 'single-listings.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_template_include');
