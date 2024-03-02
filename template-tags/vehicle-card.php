<?php
/**
 * Template for displaying vehicle card on inventory page
 */

if (!defined('ABSPATH')) {
    exit;
}

 function durango_inventory_vehicle_card() {
    $card = ob_start(); ?>
    <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-30" data-window="">
        <div class="position-relative mb-3 mb-md-0 bg-white vehicle-card-wrapper">
            <div class="card-image-wrapper overflow-hidden">
                <!-- Recently viewed card -->
                <h3 style="height:40px;" class="px-10 py-10 recently-viewed-card m-0
                text-center text-dark font-helvetica font-md
                fw-bold d-none">Recently Viewed</h3>

                <!-- Spaced in case recently viewed badge is missing -->
                <h3 style="height:40px;" class="px-10 py-10 m-0 text-center text-dark
                font-helvetica font-lg font-weight-bold fake-recent-view-badge d-none"></h3>

                <!-- Vehicle image slider -->
                <div class="listing-image-slider overflow-hidden position-relative">
                    <div class="listing-image-slider-inner h-100 w-100 mb-4">
                        <?php
                            if (has_post_thumbnail()) {
                                $attachment_url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
                                echo '<a href="' . get_the_permalink() . '" class="d-inline-block w-100">' .
                                    '<img data-src="'.$attachment_url.'" alt="' . get_the_title() . '" title="' . get_the_title() . '" decoding="async" width="1024" height="768" class="attachment-post-thumbnail size-post-thumbnail wp-post-image img-fluid w-100" loading="lazy" />' .
                                    '</a>';
                            }                            
                        ?>
                    </div>
                    <!-- Vehicle compare box -->
                    <div class="vehicle-compare-box p-1
                    position-absolute d-flex align-items-center justify-content-end
                    w-auto top-0 end-0">
                        <label for="vehicle_compare_<?php echo get_the_ID(); ?>" class="text-white fw-bold font-segoe p-0 font-sm lh-sm me-2">Compare</label>
                        <form class="inventory-products-bar__compare-listing-form
                        d-flex align-items-center">
                            <input type="checkbox"
                            id="vehicle_compare_<?php echo get_the_ID(); ?>"
                            class="chk-compare position-relative bg-white"
                            value="<?php echo get_the_ID(); ?>" />
                        </form>
                    </div>
                </div>

                <!-- Vehicle Card Content -->
                <div class="card-content-wrapper px-20">
                    <!-- Manager Specials Badge -->
                    <div class="managers-specials-badge mb-1 d-none">
                        <span class="badge bg-danger rounded-0 p-2
                        border border-dark font-segoe text-capitalize font-xs lh-xs">Manager Specials</span>
                    </div>
                    <!-- Card Title -->
                    <div class="d-flex align-items-start justify-content-between
                    mb-20 vehicle-title-wrapper overflow-hidden">
                        <h2 class="text-grey-3 font-helvetica
                        font-lg p-0 m-0 fw-bold lh-base"><?php echo get_the_title(); ?></h2>
                        <span class="icon-heart card-vehicle-like cursor-pointer" data-icon-show="true" data-id="000000">
                            <i class="bi bi-heart"></i>
                        </span>
                        <span class="icon-heart card-vehicle-liked cursor-pointer d-none" data-icon-show="false" data-id="00000">
                            <i class="bi bi-heart-fill"></i>
                        </span>

                        <!-- Show this price element only in the list view -->
                        <div class="listview-visible d-none align-items-center
                        justify-content-end listview-price">
                            <h3 class="text-capitalize p-0 m-0 font-helvetica font-xl
                            font-weight-bold text-grey-3">our best price</h3>
                            <h3 class="p-0 m-0 font-helvetica font-20 font-weight-bold text-grey-3">$ 44,488</h3>
                        </div>
                    </div>

                    <!-- Vehicle fields -->
                    <div class="d-flex align-items-start justify-content-between mb-30">
                        <div class="w-50">
                            <!-- Show this element only in list view -->
                            <div class="listview-visible d-none mb-3">
                                <span class="font-sm text-grey-3 text-uppercase mr-3">
                                    VIN: JTEKU5JR9N5987047
                                </span>
                                <span class="font-sm text-grey-3 text-uppercase">
                                    Stock #: T8631A
                                </span>
                            </div>

                            <!-- Hide this element in list view -->
                            <?php
                            echo durango_inventory_card_vehicle_meta('Stock #', 'stock_number', 'listview-hidden');
                            echo durango_inventory_card_vehicle_meta('Mileage', 'mileage', '');
                            echo durango_inventory_card_vehicle_meta('Drivetrain', 'drivetrain', '');
                            echo durango_inventory_card_vehicle_meta('Certified', 'certified', '');
                            
                            ?>
                            
                            <div class="card-colors-wrapper d-flex">
                            </div>
                        </div>

                        <div class="w-50">
                            <p class="font-helvetica font-sm text-grey-3
                            fw-normal text-end listview-hidden">Colors:</p>
                            <!-- Hide this element in list view -->
                            <div class="position-relative d-flex align-items-center
                            justify-content-end mt-3 listview-hidden">

                                <span class="card-color-ball exterior-color-ball
                                rounded-circle-px me-2 d-none" data-toggle="tooltip" data-placement="top" title="" data-key="midnight black metallic" style="background:#011635" data-original-title="Exterior Color: Midnight Black Metallic">
                                </span>
                            </div>

                            <!-- Show this element in list view -->
                            <div class="vehicle-stickers-container listview-visible d-none mt-3">
                                <a href="javascript:void(0)" class="listing-card__cta w-50 d-inline-block" data-name="carfax" data-vas-vin="JTEKU5JR9N5987047">
                                    <img src="https://stage.valueautosdurango.com/wp-content/uploads/2023/11/carfax-badge.webp" alt="Carfax" width="160" height="33" loading="lazy" class="w-100 img-fluid">
                                </a>
                                <a href="javascript:void(0)" class="listview-visible d-none listing-card__cta w-50" data-name="window" data-vas-vin="JTEKU5JR9N5987047">
                                    <img src="https://stage.valueautosdurango.com/wp-content/uploads/2023/11/window-sticker-badge-1.jpg" alt="window-sticker-badge" width="319" height="71" loading="lazy" class="w-100 img-fluid">
                                </a>
                                <a href="javascript:void(0)" class="listing-card__cta listing-info__quick-view w-50 d-inline-block" data-name="velocity" data-vas-vin="JTEKU5JR9N5987047">
                                    <img src="https://stage.valueautosdurango.com/wp-content/uploads/2023/11/velocity-engage-badge-1.webp" alt="Velocity Engage" width="291" height="85" loading="lazy" class="w-100 img-fluid">
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between
                        mb-30 vehicle-price-wrapper listview-hidden">
                        <h3 class="text-capitalize p-0 m-0 font-helvetica font-20
                            font-weight-bold text-grey-3">
                            our best price
                        </h3>
                        <h3 class="p-0 m-0 font-helvetica font-20 font-weight-bold
                            text-grey-3">$ 
                            <?php echo number_format(intval(get_post_meta(get_the_ID(), 'original_price', true))); ?>
                        </h3>
                    </div>

                    <!-- Vehicle CTA -->
                    <div class="mb-20 explore-more-cta">
                        <a href="<?php echo get_the_permalink(); ?>"
                        class="btn btn-primary w-100 d-inline-block font-weight-bold rounded">
                            Explore More
                        </a>
                    </div>

                    <!-- Vehicle History -->
                    <div class="d-flex align-items-center justify-content-between
                            mb-20 vehicle-cta-wrapper listview-hidden">
                        <span class="font-sm font-helvetica font-weight-normal
                                text-grey-3">
                            History Report
                        </span>
                        <a href="<?php echo get_the_permalink(); ?>" class="font-sm font-helvetica font-weight-normal
                                text-sixth">
                            View Details &gt;&gt;
                        </a>
                    </div>

                    <!-- Vehicle Card Stickers -->
                    <div class="d-flex align-items-center
                            justify-content-between pb-4 vehicle-stickers-wrapper
                            listview-hidden">
                        <div class="w-50 listview-visible d-none">

                        </div>
                        <div class="vehicle-stickers-container d-flex align-items-center">
                            <a href="javascript:void(0)" class="listing-card__cta w-50 d-inline-block" data-name="carfax" data-vas-vin="JTEKU5JR9N5987047">
                                <img src="https://stage.valueautosdurango.com/wp-content/uploads/2023/11/carfax-badge.webp" alt="Carfax" width="160" height="33" loading="lazy" class="w-100 img-fluid">
                            </a>

                            <a href="javascript:void(0)" class="listview-visible d-none
                                    listing-card__cta w-50" data-name="window" data-vas-vin="JTEKU5JR9N5987047">
                                <img src="" alt="window-sticker-badge" width="319" height="71" loading="lazy" class="w-100 img-fluid">
                            </a>
                            <a href="javascript:void(0)" class="listing-card__cta listing-info__quick-view w-50 d-inline-block" data-name="velocity" data-vas-vin="JTEKU5JR9N5987047">
                                <img src="https://stage.valueautosdurango.com/wp-content/uploads/2023/11/velocity-engage-badge-1.webp" alt="Velocity Engage" width="291" height="85" loading="lazy" class="w-100 img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $card = ob_get_clean();
    return $card;
 }

 function durango_inventory_card_vehicle_meta($label, $meta, $listview) {
    $vehicle_meta = ob_start(); ?>
    <div class="d-flex align-items-center justify-content-between <?php echo $listview; ?>">
        <span class="font-helvetica font-sm
        text-grey-3 fw-normal">
            <?php echo $label; ?>
        </span>
        <span class="font-helvetica font-sm
        text-grey-3 fw-normal">
            <?php echo get_post_meta(get_the_ID(), $meta, true); ?>
        </span>
    </div>

    <?php
    $vehicle_meta = ob_get_clean();
    return $vehicle_meta;
 }