<?php
/** Template displaying single listing page */

/** Define some global variables */
if( isset($_GET['view_images']) ) {
    $view_images = $_GET['view_images'];
}

$post_id = get_the_ID();
$main_image = get_post_meta($post_id, '_thumbnail_id', true);
$call_for_info = get_option('quick_call_phone_number');
$call_for_info_format = str_replace(array('+', '(', ')', '-', ' '), '', $call_for_info);
$main_price = get_post_meta($post_id, 'original_price', true);

/** If $main_price is empty or null then use CTA markup */
if(empty($main_price)) {
    // If $main_price is empty, use CTA markup
    $main_price = '<a href="tel:' . $call_for_info_format . '" class="quick-call-link text-dark">
    <i class="fa fa-phone"></i>
    </a>';
} else {
    // Format the price if it exists
    $main_price = '$ ' . number_format($main_price);
}

$vehicle_title = get_the_title();
$gallery_images = get_post_meta($post_id, 'gallery_images', true);
/** If $gallery images exists then unserialize it */
if(empty($gallery_images)) {
    $gallery_images = array();
} else {
    $gallery_images = unserialize($gallery_images); // This will have the attachments IDs
}

$vehicle_model = get_post_meta($post_id, 'model', true);
$vehicle_make = get_post_meta($post_id, 'make', true);
$vehicle_year = get_post_meta($post_id, 'year', true);
$vehicle_type = get_post_meta($post_id, 'type_of_vehicle', true);
$exterior_color = get_post_meta($post_id, 'exterior_color', true);
$interior_color = get_post_meta($post_id, 'interior_color', true);
$mileage = get_post_meta($post_id, 'mileage', true);

/** Format the mileage value if not empty */
if(!empty($mileage)) {
    $mileage = number_format($mileage);
}

$stock_number = get_post_meta($post_id, 'stock_number', true);
$drivetrain = get_post_meta($post_id, 'drivetrain', true);
$engine = get_post_meta($post_id, 'engine', true);
$vin_number = get_post_meta($post_id, 'vin_number', true);
$certified = get_post_meta($post_id, 'certified', true);
$body_style = get_post_meta($post_id, 'body_style', true);
$transmission = get_post_meta($post_id, 'transmission', true);
$doors = get_post_meta($post_id, 'doors', true);
$cylinders = get_post_meta($post_id, 'cylinders', true);
$fuel_type = get_post_meta($post_id, 'fuel_type', true);
$series = get_post_meta($post_id, 'series', true);
$certification = get_post_meta($post_id, 'certification', true);
$city_mpg = get_post_meta($post_id, 'city_mpg', true);
$highway_mpg = get_post_meta($post_id, 'highway_mpg', true);
$features = get_post_meta($post_id, 'features', true);

/** If $features is not empty then explode */
if(empty($features)) {
    $features = array();
}else {
    $features = explode('|', $features);
}

$dealer = get_post_meta($post_id, 'dealer_id', true);
$engine_displacement = get_post_meta($post_id, 'engine_displacement', true);

/** Make an array  */
// $detailsArray = array(
//     'engine' => $vehicleEngine,
//     'stock #' => $vehicleStock,
//     'vin number' => $vehicleVin,
//     'year' => $vehicleYear,
//     'make' => $vehicleMake,
//     'model' => $vehicleModel,
//     'mileage' => $vehicleMileage,
//     'certified' => $vehicleCertified,
//     'body Style' => $vehicleBodyStyle,
//     'transmission' => $vehicleTransmission,
//     'doors' => $vehicleDoors,
//     'cylinders' => $vehicleCylinders,
//     'drivetrain' => $vehicleDrivetrain,
//     'fuel type' => $vehicleFuelType,
//     'exterior color' => $vehicleExteriorColor,
//     'interior color' => $vehicleInteriorColor,
//     'series' => $vehicleSeries,
//     'certification' => $vehicleCertification,
// );

// $disclaimer = ( get_field('vdp_disclaimer_text','options') && !empty(get_field('vdp_disclaimer_text','options')) ? get_field('vdp_disclaimer_text','options') : null );

require_once('template-tags/vehicle-meta.php');

get_header();

?>

<style>
    .inventory-container {
        width: 100%;
        max-width: 1600px;
        margin: auto;
        padding-left: 15px;
        padding-right: 15px;
    }
    @media (min-width: 767px) {
        .inventory-container {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
    @media (min-width: 1200px) {
        .inventory-container {
            padding-left: 30px;
            padding-right: 30px;
        }
    }
</style>

<div class="inner-page inventory-listing VDP-content-wrapper"
data-listing="<?php echo $post_id; ?>" data-make="<?php echo $vehicle_make; ?>"
itemscope itemtype="http://schema.org/Vehicle">
    <!-- Breadcrumbs -->
    <div class="vehicle-breadcrumbs inventory-container py-20 d-none d-md-block">
        <?php
        echo "<nav class='rank-math-breadcrumb' aria-label='breadcrumbs'>
        <a href='".site_url()."' class='text-dark fw-bold font-md'>Home</a>
        <span class='separator text-dark fw-bold font-md mx-1'>/</span>
        <a href='".site_url() ."/inventory/?search=".$vehicle_type."' class='last text-dark fw-bold font-md'>
        ". $vehicle_type ."</a><span class='separator text-dark fw-bold font-md mx-1'>/</span>
        <span class='last text-dark fw-bold font-md'>". $vehicle_make . ' ' . $vehicle_model ."</span></nav>"; ?>
    </div>

    <!-- Main Content Started -->
    <div class="p-0 px-md-3 px-lg-4">
        <?php durango_inventory_vehicle_meta_box($stock_number, $vin_number, $vehicle_make, $vehicle_model, $vehicle_year); ?>

    </div>


</div>


<?php get_footer(); ?>