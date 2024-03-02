<?php

set_time_limit(3600);  // Set to 5 minutes (adjust as needed)

// Define Workbench SQL connection credentials
if(!defined('WORKBENCH_SERVER_NAME')) {
    define('WORKBENCH_SERVER_NAME', 'inventory-database-do-user-2599605-0.c.db.ondigitalocean.com:25060');
}

if(!defined('WORKBENCH_USERNAME')) {
    define('WORKBENCH_USERNAME', 'junaid');
}

if(!defined('WORKBENCH_PASSWORD')) {
    define('WORKBENCH_PASSWORD', 'AVNS_ufjqHNNhDr_Pxg4FTFN');
}

if(!defined('WORKBENCH_DATABASE')) {
    define('WORKBENCH_DATABASE', 'pre_owned_db');
}

$GLOBALS['IMPORTER_PACKET_ID'] = get_option('importer_packet_id') !== false ? get_option('importer_packet_id') : 1;
$GLOBALS['MAXIMUM_LISTINGS_NUMBER'] = 3;
$GLOBALS['LISTINGS_CREATED'] = 0;
// Function to run on plugin activation

/**
 * Delete previous listings and associated post meta.
 */
function delete_previous_listings($stock)
{
    $args = array(
        'post_type'      => 'listings',
        'meta_query' => array(
            array(
                'key' => 'stock-number',
                'value' => $stock,
                'compare' => '=',
            ),
        ),
    );

    $listings = get_posts($args);

    foreach ($listings as $listing) {
        delete_listing($listing->ID);
    }
}

/**
 * Delete a single listing and its attachments.
 *
 * @param int $listing_id The ID of the listing to delete.
 */
function delete_listing($listing_id)
{
    $attachments = get_posts(array(
        'post_type'      => 'attachment',
        'posts_per_page' => -1,
        'post_parent'    => $listing_id,
    ));

    foreach ($attachments as $attachment) {
        wp_delete_attachment($attachment->ID, true);
    } 

    wp_delete_post($listing_id, true);
}
/**
 * Send email notification.
 *
 * @param string $to               Email recipient.
 * @param string $subject          Email subject.
 * @param string $message          Email message.
 * @param string $attachment_path  Optional. Path to the attachment file.
 */
function send_cron_status_email($subject, $message, $attachment_path = '')
{
    // $to = get_field('email_on_which_csv_file_importer_plugin_notifications_will_be_sent','options');
    $to = 'jasghar186@gmail.com';
    if (!empty($attachment_path)) {
        $attachment = chunk_split(base64_encode(file_get_contents($attachment_path)));

        $boundary = md5(time());
        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Disposition: inline\r\n";
        $body .= "\r\n";
        $body .= $message . "\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/csv; name=\"" . basename($attachment_path) . "\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"" . basename($attachment_path) . "\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "\r\n";
        $body .= $attachment . "\r\n";
        $body .= "--$boundary--\r\n";

        wp_mail($to, $subject, $body, $headers);
    } else {
        $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        );
        wp_mail($to, $subject, $message, $headers);
    }
}

function listCSVFiles() {
    $directory = WP_CONTENT_DIR . '/uploads/import-csv/';
    // Get all CSV files in the directory
    $files = glob($directory . '*.csv');

    // Check if there are any CSV files
    if( empty($files) ) {
        wp_mail('jasghar186@gmail.com', 'no csv file found in localhost', 'no csv found');
        return array();
    }
	$timezone = new DateTimeZone('America/Denver');
	$date = new DateTime('now', $timezone);
	$current_time = $date->format('Y-m-d h:i:s A');
	send_cron_status_email('New CSV File Found', 'New CSV file just found at ' . $current_time);
    return $files[0];
}

function listingsBatchesImport() {

    // Update data in the Workbench SQL database
    $workbench_connection = new mysqli(WORKBENCH_SERVER_NAME, WORKBENCH_USERNAME, WORKBENCH_PASSWORD, WORKBENCH_DATABASE);

    /**
     * Check if connection is successfull or not
     * If in case connection fails then send an email to developer
     */

    if($workbench_connection->connect_error) {
        wp_mail('jasghar186@gmail.com',
        'Workbench SQL connection failed',
        'Workbench SQL connection failed while inserting listings' . $workbench_connection->connect_error,
        array('Content-Type: text/html; charset=UTF-8'));
        wp_die();
    }

    /**
     * Check if database exists or not
     */
    if (!$workbench_connection->select_db(WORKBENCH_DATABASE)) {
        wp_mail('jasghar186@gmail.com',
        'Workbench SQL database error',
        'Database not found' . $workbench_connection->error,
        array('Content-Type: text/html; charset=UTF-8'));
        wp_die();
    }

    // Get the current posts stock number and store in an array
    $presentStockNumbers = array();
    $stockArray = array(
        'post_type' => 'listings',
        'posts_per_page' => -1,
    );
    $stockPosts = get_posts( $stockArray );    
    foreach( $stockPosts as $post ) {
        $stockNumber = get_post_meta($post->ID, 'stock-number', true);
        $presentStockNumbers[] = strtoupper($stockNumber);
    }

    $file = listCSVFiles();
    if( !empty($file) ) {
        if (($handle = fopen($file, 'r')) !== false) {
            // Skip the header row
            $header = fgetcsv($handle);
            $listingsArray = array();
            $csvStockNumbers = array();
            $alreadyPresentStock = array();
            $totalListings = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $listing_data = array_combine($header, $data);
                if( !isset($listingsArray[$listing_data['Stock #']]) ) {
                    $listingsArray[$listing_data['Stock #']] = array();
                }
                $listingsArray[$listing_data['Stock #']][] = $listing_data;
                $csvStockNumbers[] = strtoupper($listing_data['Stock #']); 
                $totalListings++;
            }
            fclose($handle);

            // Delete Previous Listings
            $tobeDeletedStocks = array_diff($presentStockNumbers, $csvStockNumbers);
            if (!empty($tobeDeletedStocks)) {
                foreach ($tobeDeletedStocks as $stocks) {
                    delete_previous_listings($stocks);
                    $removedIndex = array_search($stocks, $presentStockNumbers);
                    if ($removedIndex !== false) {
                        unset($presentStockNumbers[$removedIndex]);
                    }
                }
            }

            foreach ($listingsArray as $stock => $listing) {
                $csvStock = strtoupper($stock);
                if (in_array($csvStock, $presentStockNumbers)) {
                    $alreadyPresentStock[] = $csvStock;
                    continue; // Skip this iteration if stock number is already present
                }
                error_log('listing created', $listing[0]['Year']);

                if( $GLOBALS['LISTINGS_CREATED'] === $GLOBALS['MAXIMUM_LISTINGS_NUMBER'] ) {
                    sleep(10);
                    $GLOBALS['LISTINGS_CREATED'] = 0;
                }else {
                    create_listing($listing[0]);
                    $presentStockNumbers[] = $csvStock;
                    $GLOBALS['LISTINGS_CREATED'] += 1;
                }
            }

            updateListingData($alreadyPresentStock, $listingsArray);

            /**
             * INSERT/ UPDATE data in external SQL DB
            */
            // durango_workbench_sql_db($workbench_connection, $listingsArray);
            /**
             * Close the connection to save up server resources
             */

            $workbench_connection->close();

            // Send email notification
            $timezone = new DateTimeZone('America/Denver');
            $date = new DateTime('now', $timezone);
            $current_time = $date->format('Y-m-d h:i:s A');
            $subject = 'CSV Import Complete';
            $message = 'CSV import process completed. Total listings '. $totalListings .' were imported on ' . $current_time . ' ' .'And the packet ID for this import is' . ' ' . '(' . $GLOBALS['IMPORTER_PACKET_ID'] . ')';
            send_cron_status_email($subject, $message, $file);

            update_option('importer_packet_id', intval($GLOBALS['IMPORTER_PACKET_ID']) + 1);

            // Remove the processed CSV file and move it to backups folder
            $backup_directory = WP_CONTENT_DIR . '/uploads/backups/';

            if (!file_exists($backup_directory)) {
                mkdir($backup_directory, 0755, true);
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);
			$backup_filename = basename($file, '.' . $extension) . '_' . date('Y-m-d_His') . '.' . $extension;
            $backup_path = $backup_directory . $backup_filename;
            if( rename($file, $backup_path) ) {
                unlink($file);
            }
        }
    }
}

/**
 Create a function to update the updated values of listings */

 function updateListingData($alreadyPresentStock, $listingsArray) {
    $listing = array(); // Initialize the array
    if( !empty($alreadyPresentStock) ) {
        $args = array(
            'post_type' => 'listings',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'stock-number',
                    'value' => $alreadyPresentStock,
                    'compare' => 'IN',
                ),
            ),
        );
        $updateQuery = get_posts($args);
        $listing = [];
        $listingsStocks = [];

        if (!empty($updateQuery)) {

            foreach ($updateQuery as $query) {
                $listing_data = get_post_meta($query->ID, '', true); // post meta data of each post
                $listingStock = get_post_meta($query->ID, 'stock-number', true); // stock number of current post

                if( !isset($listing[$listingStock]) ) {
                    $listing[$listingStock] = [];
                }
                $listingsStocks[$listingStock][] = $query->ID; // id of the posts with key of stock number
                $listing[$listingStock][] = $listing_data; // store listing data with key of stock number
            }
        } else {
            $listing = [];
        }

        // Update data
        foreach( $listingsArray as $stock => $listingArr ) {
            if( array_key_exists($stock, $listing) ){
                $keysArr = array(
                    'Transmission' => 'transmission',
                    'Series' => 'series',
                    'Colour' => 'exterior-color',
                    'Interior Color' => 'interior-color',
                    'City MPG' => 'city_mpg',
                    'Highway MPG' => 'highway_mpg',
                    'Price' => 'original_price',
                    'Other Price' => 'current_price',
                    'Series Detail' => 'series-detail',
                    'Certification' => 'certification',
                );
                foreach( $keysArr as $CSVkey => $metaKey ) {
                    if( $listingArr[0][$CSVkey] !== $listing[$stock][0][$metaKey][0] ) {
                        update_post_meta($listingsStocks[$stock][0], $metaKey, $listingArr[0][$CSVkey]);
                        if( $metaKey === 'city_mpg' || $metaKey === 'highway_mpg' ||
                            $metaKey === 'original_price' || $metaKey === 'current_price' ) {
                                $listing_options = get_post_meta($listingsStocks[$stock][0], 'listing_options', true);
                                $listing_options = !empty($listing_options) ? unserialize($listing_options) : [];
                                $val = $metaKey === 'city_mpg' || $metaKey === 'highway_mpg' || $metaKey === 'original_price' ? 'value' : 'original';
                                $ke = $metaKey === 'original_price' || $metaKey === 'current_price' ? 'price' : $metaKey;
                                $listing_options[$ke][$val] = $listingArr[0][$CSVkey];
                                update_post_meta($listingsStocks[$stock][0], 'listing_options', serialize($listing_options));
                        }
                        // Delete Transient if any post meta value changes
                        delete_transient('product_card_' . $listingsStocks[$stock][0]);
                    }
                }
            
                if( $listingArr[0]['Photos Last Modified Date'] !== $listing[$stock][0]['photos-last-modified-date'][0] ) {
                    update_post_meta($listingsStocks[$stock][0], 'photos-last-modified-date', $listingArr[0]['Photos Last Modified Date']);
                    // Attach images
                    $image_urls = explode('|', $listingArr[0]['Photo Url List']);
                    if( empty($image_urls) ) {
                        $image_urls = array();
                    }
                    $image_urls = array_map('trim', $image_urls);
                    $image_urls = array_filter($image_urls, function( $urls ) {
                        return filter_var($urls, FILTER_VALIDATE_URL) !== false;
                    });
                    upload_image_from_url($image_urls, $listingsStocks[$stock][0]);
                    // Delete transient if photo gallery changes
                    delete_transient('product_card_' . $listingsStocks[$stock][0]);
                }
            }
        }
        return $listing;
    }
 }
/**
 * Create a new listing from the given data.
 *
 * @param array $data The listing data.
 *
 * @return int|false The ID of the created listing, or false on failure.
 */
function create_listing($data)
{
    // Create a new post
    $listing_id = wp_insert_post(array(
        'post_title'   => $data['Year'] . ' ' . $data['Make'] . ' ' . $data['Model'] . ' ' . $data['Series'],
        'post_content' => $data['Description'],
        'post_type'    => 'listings',
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
    ));

    if (is_wp_error($listing_id)) {
        return false;
    }

    // Set custom fields
    $post_metas = array(
        'DealerId'               => 'dealer_id',
        'VIN'                    => 'vin_number',
        'Stock #'                => 'stock_number',
        'New/Used'               => 'condition',
        'Year'                   => 'year',
        'Make'                   => 'make',
        'Model'                  => 'model',
        'Model Number'           => 'model_number',
        'Body'                   => 'body_style',
        'Body Type'              => 'type_of_vehicle',
        'Transmission'           => 'transmission',
        'Series'                 => 'series',
        'Body Door Ct'           => 'doors',
        'Odometer'               => 'odometer',
        'Engine Cylinder Ct'     => 'cylinders',
        'Engine Displacement'    => 'engine_displacement',
        'Drivetrain Desc'        => 'drivetrain',
        'Colour'                 => 'exterior_color',
        'Interior Color'         => 'interior_color',
        'Invoice'                => 'invoice',
        'Other Price'            => 'current_price',
        'Book Value'             => 'book_value',
        'Price'                  => 'original_price',
        'Inventory Date'         => 'inventory_date',
        'Certified'              => 'certified',
        'Description'            => 'description',
        'Features'               => 'features',
        'City MPG'               => 'city_mpg',
        'Highway MPG'            => 'highway_mpg',
        'Photos Last Modified Date' => 'photos_last_modified_date',
        'Status Code'            => 'car_sold',
        'Cost'                   => 'cost',
        'Series Detail'          => 'series_detail',
        'Inspection Checklist #' => 'inspection_checklist_number',
        'Engine Description'     => 'engine',
        'Certification'          => 'certification',
        'Option Codes'           => 'option_codes',
        'MiscPrice1'             => 'miscprice_1',
        'MiscPrice2'             => 'miscprice_2',
        'MiscPrice3'             => 'miscprice_3',
        'Disposition'            => 'disposition',
        'Fuel Type'              => 'fuel_type',
    );

    foreach ($post_metas as $key => $meta) {
        update_post_meta($listing_id, $meta, trim($data[$key]));
    }

    $post_name =  $data['Year'] . '-' . $data['Make'] . '-' . $data['Model'] . '-' . $data['Stock #'];
    // Update post slug
    $post_data = array(
        'ID' => $listing_id,
        'post_name' => $post_name,
    );
    wp_update_post($post_data);

    // Regenerate permalink
    $permalink = get_permalink($listing_id);
	update_post_meta($listing_id, 'postName', $data['Year'] . ' ' . $data['Make'] . ' ' . $data['Model'] . ' ' . $data['Series']);
    // Attach images
    $image_urls = explode('|', $data['Photo Url List']);
	if( empty($image_urls) ) {
        $image_urls = array();
    }
    $image_urls = array_map('trim', $image_urls);
    $image_urls = array_filter($image_urls, function( $urls ) {
        return filter_var($urls, FILTER_VALIDATE_URL) !== false;
    });
    upload_image_from_url($image_urls, $listing_id);

    $listing_options = get_post_meta($listing_id, 'listing_options', true);
    if (empty($listing_options)) {
        $listing_options = [];
    } else {
        $listing_options = unserialize($listing_options);
    }

    $listing_options['price']['value'] = $data['Price'];
    $listing_options['price']['original'] = $data['Other Price'];
    $listing_options['city_mpg']['value'] = $data['City MPG'];
    $listing_options['highway_mpg']['value'] = $data['Highway MPG'];

    update_post_meta($listing_id, 'listing_options', serialize($listing_options));
    return $listing_id;
}
/**
 * Upload an image from URL and return the attachment ID.
 *
 * @param string $image_url The URL of the image to upload.
 *
 * @return int|false The ID of the uploaded image attachment, or false on failure.
 */
function upload_image_from_url($image_urls, $listingID)
{
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/post.php';

    $attachment_ids = array();

    foreach ($image_urls as $image_url) {
        if (!empty($image_url)) {
            $filename = md5($image_url) . '_' . time() . '_' . basename($image_url);
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . $filename;
            $upload_url = $upload_dir['url'] . '/' . $filename;

            // Check if attachment already exists
            if (post_exists($filename)) {
                $attachment = get_page_by_title($filename, OBJECT, 'attachment');
                $attachment_ids[] = $attachment->ID;
                continue;
            }

            // Delay for network or server latency
            usleep(500000); // Sleep for 500 milliseconds (0.5 seconds)

            // Download the image
            $image_data = file_get_contents($image_url);
            if ($image_data === false) {
                // Error handling code
                continue; // Skip to the next image URL
            }

            $file = fopen($upload_path, 'w');
            fwrite($file, $image_data);
            fclose($file);

            // Determine the MIME type using the file extension
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mime_type = 'image/' . $file_extension; // Assumes all images have an extension

            $attachment = array(
                'guid'           => $upload_url,
                'post_mime_type' => $mime_type,
                'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            $attachment_id = wp_insert_attachment($attachment, $upload_path, $listingID);

            if (!is_wp_error($attachment_id)) {
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                // Set the featured image if not already set
                if (!has_post_thumbnail($listingID)) {
                    set_post_thumbnail($listingID, $attachment_id);
                }

                $attachment_ids[] = $attachment_id;
            }
        }
    }

    update_post_meta($listingID, 'gallery_images', serialize($attachment_ids));
    return $attachment_ids;
}

// Deactivate
register_deactivation_hook( __FILE__, 'listings_importer_deactivate' ); 

function listings_importer_deactivate() {
    wp_clear_scheduled_hook( 'listings_csv_file_count' );
    wp_clear_scheduled_hook( 'listings_batches_import' );
}


/**
 * INSERT/ UPDATE data in external SQL DB
 */
function durango_workbench_sql_db($workbench_connection, $listingsArray) {
    foreach($listingsArray as $stock => $listing_data) {
    /**
    * If connection is successfull then run insert statement and insert $listingsArray
    */
    $insert_query = "INSERT INTO `inventory` (DealerId, VIN, StockNumber, NewUsed, Year,
    Make, Model, ModelNumber, Body, BodyType, Transmission, Series, BodyDoorCount, Odometer,
    EngineCylinderCount, EngineDisplacement, DrivetrainDesc, Colour, InteriorColor,
    Invoice, OtherPrice, BookValue, Price, InventoryDate, Certified, Description, Features,
    PhotoUrlList, CityMPG, HighwayMPG, PhotosLastModifiedDate, StatusCode,
    Cost,SeriesDetail,InspectionChecklistNumber,EngineDescription,Certification, OptionCodes, MiscPrice1,
    MiscPrice2,MiscPrice3,Disposition,FuelType,postTitle,PacketID) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    // Prepare the statement
    $stmt = $workbench_connection->prepare($insert_query);
    $ppTitle = $listing_data[0]['Year'] . ' ' . $listing_data[0]['Make'] . ' ' . $listing_data[0]['Model'] . ' ' . $listing_data[0]['Series'];
    // Check if the statement preparation was successful
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param(
        'ssssisssssssiiidssssiiisssssiissssssssssssssi',
        $listing_data[0]['DealerId'],   
        $listing_data[0]['VIN'],   
        $listing_data[0]['Stock #'],   
        $listing_data[0]['New/Used'],   
        $listing_data[0]['Year'], 
        $listing_data[0]['Make'], 
        $listing_data[0]['Model'], 
        $listing_data[0]['Model Number'], 
        $listing_data[0]['Body'], 
        $listing_data[0]['Body Type'], 
        $listing_data[0]['Transmission'], 
        $listing_data[0]['Series'],
        $listing_data[0]['Body Door Ct'],
        $listing_data[0]['Odometer'],
        $listing_data[0]['Engine Cylinder Ct'],
        $listing_data[0]['Engine Displacement'],
        $listing_data[0]['Drivetrain Desc'],
        $listing_data[0]['Colour'],
        $listing_data[0]['Interior Color'],
        $listing_data[0]['Invoice'],
        $listing_data[0]['Other Price'],
        $listing_data[0]['Book Value'],
        $listing_data[0]['Price'],
        $listing_data[0]['Inventory Date'],
        $listing_data[0]['Certified'],
        $listing_data[0]['Description'],
        $listing_data[0]['Features'],
        $listing_data[0]['Photo Url List'],
        $listing_data[0]['City MPG'],
        $listing_data[0]['Highway MPG'],
        $listing_data[0]['Photos Last Modified Date'],
        $listing_data[0]['Status Code'],
        $listing_data[0]['Cost'],
        $listing_data[0]['Series Detail'],
        $listing_data[0]['Inspection Checklist #'],
        $listing_data[0]['Engine Description'],
        $listing_data[0]['Certification'],
        $listing_data[0]['Option Codes'],
        $listing_data[0]['MiscPrice1'],
        $listing_data[0]['MiscPrice2'],
        $listing_data[0]['MiscPrice3'],
        $listing_data[0]['Disposition'],
        $listing_data[0]['Fuel Type'],
        $ppTitle,
        $GLOBALS['IMPORTER_PACKET_ID'],
        );

        // Execute the statement
        $stmt->execute();

        // Check if the execution was successful
        if($stmt->affected_rows <= 0) {
            wp_mail(
            'jasghar186@gmail.com',
            'SQL data insertion failed',
            'data insertion failed' . ' ' . $stmt->error,
            array('Content-Type: text/html; charset=UTF-8')
            );
            wp_die();
        }
    } else {
        wp_mail(
        'jasghar186@gmail.com',
        'SQL statement preparation failed',
        'Statement preparation failed' . ' ' . $workbench_connection->error,
        array('Content-Type: text/html; charset=UTF-8')
        );
        wp_die();
    }

        // Close the statement
        $stmt->close();
    }
}