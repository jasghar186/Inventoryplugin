<?php
/**
 * Code to manage the filters
 */
add_action( 'wp_ajax_load_inventory_filters', 'load_inventory_filters_callback' );
add_action( 'wp_ajax_nopriv_load_inventory_filters', 'load_inventory_filters_callback' );
function load_inventory_filters_callback() {
    // Include colors file
    require_once('predefined-colors.php');
    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : null;
    $values = [];
    $args = array(
        'post_type' => 'listings',
        'posts_per_page' => -1,
    );

    $filter_values = get_posts($args);

    if( ! empty($filter_values) ) {
        foreach( $filter_values as $value ) {
            $filter_value = get_post_meta( $value->ID, $filter, true ); // post meta value

            /**
             * If its a color filter
             */
            if( $filter === 'exterior_color' || $filter === 'interior_color' ) {
                $filter_value = htmlspecialchars( trim( strtolower($filter_value) ) ); // red blue berry
                $filter_value = explode(' ', $filter_value); // ['red', 'blue', 'berry']
                $colorArr = [];
                foreach ($filter_value as $color) {
                    $returned_color = preDefinedColors($color);
                    if (!empty($returned_color) && !is_null($returned_color)) {
                        $values[] = $returned_color;
                        // Break the loop as you found a match
                        break;
                    }
                }                
            }

            if( ! empty($filter_value) && $filter !== 'exterior_color' && $filter !== 'interior_color' ) {
                $values[] = $filter_value;
            }
        }
    }

    // Remove duplicates from $values array to only send unique values
    
    $values = array_unique($values, SORT_REGULAR);
    $values = array_values($values);

    wp_send_json_success(
        array(
            'filter' => $values,
        )
    );
    wp_die();
}

/**
 * Code to update inventory vehicles
 */
add_action( 'wp_ajax_update_inventory_vehicles', 'update_inventory_vehicles_callback' );
add_action( 'wp_ajax_nopriv_update_inventory_vehicles', 'update_inventory_vehicles_callback' );
function update_inventory_vehicles_callback() {
    $options_arr = isset($_POST['optionsArr']) ?$_POST['optionsArr'] : array();
    $paged = isset($_POST['paged']) ? sanitize_text_field($_POST['paged']) : 1;
    $posts_per_page = 12;

    $args = array(
        'post_type' => 'listings',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'meta_query' => array(
            'relation' => 'AND',
        )
    );

    if( !empty($options_arr) ) {
        // push values in $args
        foreach($options_arr as $options) {
            $type = $options['type'];
            $values = $options['values'];

            if( $type === 'mileage' || $type === 'original_price' ) {
                $values = explode(',', $values);
            }
            
            if( is_array($values) ) {
                $data = array(
                    'key' => $type,
                    'value' => $values,
                    'compare' => 'IN',
                );
                if( $type === 'mileage' || $type === 'original_price' ) {
                    $data['type'] = 'NUMERIC';
                }
            }else {
                // If the type is search
                if( $type === 'search' ) {
                    $data = array(
                        'key' => 'postName',
                        'value' => $values,
                        'compare' => 'LIKE'
                    );
                }else {
                    $data = array(
                        'key' => $type,
                        'value' => $values,
                        'compare' => 'LIKE',
                    );
                }
            }

            $args['meta_query'][] = $data;
        }
    }

    $vehicle_query = new WP_Query($args);
    $found_vehicles = $vehicle_query->found_posts;
    $max_num_pages = $vehicle_query->max_num_pages;

    if( $vehicle_query->have_posts() ) {
        $vehicle_cards = [];
        while($vehicle_query->have_posts()) {
            $vehicle_query->the_post();
            $vehicle_cards[] = durango_inventory_vehicle_card();
        }
        wp_reset_postdata();

        wp_send_json_success(
            array(
                'cards' => $vehicle_cards,
                'vehicles_count' => $found_vehicles,
                'posts_per_page' => $posts_per_page,
                'max_num_pages' => $max_num_pages,
                'not_found' => null,
                'options_arr' => $args,
            )
        );

    }else {
        // if no posts found
        $not_found = '<div class="no-listings-banner">
        <div class="no-listings-found mt-30 d-flex justify-content-center align-items-center py-5 px-4 bg-transparent">
        <div class="py-3">
        <h2 class="text-dark lh-lg fw-bold font-helvetica p-0 m-0 text-center">
        Sorry, no listings found matching your search result. Please try with diffrent search or contact sales.
        </h2>
        </div>
        </div>
        <div class="mt-30">
        <h2 class="color_black font_helvetica text_capitalize text_center p_0 relatedlistingsHeadingtext">
        Here are some other vehicles you may be interested in:</h2>
        <a href="https://stage.valueautosdurango.com/inventory"
        class="d_block text_center relatedlistingslink text_uppercase">
        View all used inventory
        </a>
        </div>
        </div>';

        wp_send_json_error(
            array(
                'cards' => null,
                'vehicles_count' => 0,
                'posts_per_page' => $posts_per_page,
                'max_num_pages' => 0,
                'not_found' => $not_found,
                'options_arr' => $args,
            )
        );
        exit;
    }

    wp_die();
}