<?php
/*
Plugin Name: Durango Inventory Managment
Description: This plugin imports and download CSV files data into your website
Version: 1.0
Author: Junaid Asghar
Author URI: https://hirejunaid.com
Plugin URI: https://hirejunaid.com
*/

register_activation_hook(__FILE__, 'durango_inventory_plugin_activation');

function durango_inventory_plugin_activation() {
    custom_cron_plugin_activate();
    // Add sticky filterbar option in db
    if(intval(get_option('durango_inventory_plugin_activated')) !== 1) {
        update_option('inventory_filterbar_sticky', true);
        update_option('durango_inventory_plugin_activated', 1);
        update_option('quick_call_phone_number', '+1 (855) 894-1386');
    }
}

require_once __DIR__ . '/functions.php';

// Register custom page templates
add_filter( 'theme_page_templates', 'durango_inventory_register_templates' );
function durango_inventory_register_templates( $templates ) {
    $templates['inventory.php'] = 'Inventory';
    return $templates;
}

// Load custom page template
add_filter( 'template_include', 'durango_inventory_load_template' );
function durango_inventory_load_template( $template ) {
    if ( is_page_template( 'inventory.php' ) ) {
        $template = plugin_dir_path( __FILE__ ) . 'templates/inventory.php';
    }
    return $template;
}

// On plugin deactivation
register_deactivation_hook(__FILE__, 'durango_inventory_plugin_deactivation');

function durango_inventory_plugin_deactivation() {
    // Remove the scheduled cron job
    wp_clear_scheduled_hook('listings_batches_import');
}