<?php
/** Template displaying vehicle meta on single listing page */

function durango_inventory_vehicle_meta_box($stock_number, $vin_number, $make, $model, $year) {
    $box = ob_start();
    ?>
    <div class="details-box bg-grey-7 p-15 py-4">
        <div class="row">
            <div class="col-12 col-lg-7">
                <h1 class="p-0 font-inter text-dark mb-20 fw-bold font-30"><?php echo get_the_title(); ?></h1>
                <div class="d-flex align-items-center justify-content-start flex-wrap">
                    <div class="me-3 font-inter font-sm text-uppercase text-dark">
                        <span class="me-1">Stock: </span>
                        <span>
                            <?php echo $stock_number; ?>
                        </span>
                    </div>
                    <div class="me-3 font-inter font-sm text-uppercase">
                        <span class="me-1">VIN: </span>
                        <span>
                            <?php echo $vin_number; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5 mt-3 mt-lg-0 d-flex align-items-center
            justify-content-between justify-content-lg-end detail-bar-action-icons">
                <!-- Details Tag -->
                <div class="details-action-icon d-flex align-items-center">
                    <i class="bi bi-tag close-upgradeVehicle icon-details-tag font-30 text-fourth"></i>
                </div>

                <!-- We Found other cars notification -->
                <span class="font-inter font-md more-cars-found mr-2 text-to-reveal overflow-hidden
                d-none d-lg-inline-block"
                style="min-width: 0px; transition: min-width 0.5s ease 0s; flex-basis: 0px;display:none;">
                    We found other cars you might like!
                </span>

                <!-- Page View Switcher -->
                <div class="details-action-icon star-empty-icon align-items-center position-relative
                text-reveal-container justify-content-end d-flex" style="width: auto;">
                    <i class="bi bi-star font-30 text-fourth cursor-pointer"></i>
                </div>
                <div class="details-action-icon star-active-icon d-none">
                    <i class="bi bi-star-fill font-30 text-fourth cursor-pointer"></i>
                </div>

                <!-- Price alert icon -->
                <div class="details-action-icon price-alert-simple-icon">
                    <i class="bi bi-bell font-30 text-fourth cursor-pointer sidebar-popup-trigger"
                    data-popup="sticky-cta" data-vin="<?php echo $vin_number; ?>"
                    data-stock="<?php echo $stock_number; ?>"
                    data-make="<?php echo $make; ?>"
                    data-model="<?php echo $model; ?>"
                    data-year="<?php echo $year; ?>"
                    data-popup-function="vehicle-price-alert"></i>
                </div>
                <div class="details-action-icon price-alert-active-icon d-none align-items-center
                justify-content-end">
                    <!-- Price Drop Alert Text -->
                    <span class="font-inter font-md more-cars-found mr-2 text-to-reveal
                    overflow-hidden" style="min-width: 0px; transition: min-width 0.5s ease 0s;
                    flex-basis: 0px;">Get Price Drop Alerts!!</span>
                    <!-- Icon -->
                    <i class="bi bi-bell-fill sidebar-popup-trigger"
                    data-stock="<?php echo $stock_number; ?>"
                    data-make="<?php echo $make; ?>"
                    data-model="<?php echo $model; ?>"
                    data-year="<?php echo $year; ?>"
                    data-popup-function="vehicle-price-alert"
                    data-popup="sticky-cta"></i>
                </div>

                <!-- Vehicle Share Icon -->
                <div class="details-action-icon">
                    <i class="bi bi-share-fill sidebar-popup-trigger font-30 text-fourth cursor-pointer
                    icon-share" data-popup="sticky-cta"
                    data-stock="<?php echo $stock_number; ?>"
                    data-make="<?php echo $make; ?>"
                    data-model="<?php echo $model; ?>"
                    data-year="<?php echo $year; ?>"
                    data-popup-function="vehicle-share"></i>
                </div>

                <!-- Like, Liked icon -->
                <div class="details-action-icon make-vehicle-like"
                data-id="<?php echo get_the_ID(); ?>">
                    <i class="bi bi-suit-heart font-30 text-fourth cursor-pointer"></i>
                </div>
                <div class="details-action-icon make-vehicle-liked d-none"
                data-id="<?php echo get_the_ID(); ?>">
                    <i class="bi bi-suit-heart-fill"></i>
                </div>

                <!-- Telephone Icon -->
                <div class="details-action-icon">
                    <a href="tel:18558941386">
                        <i class="bi bi-telephone font-30 text-fourth cursor-pointer"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
    $box = ob_get_clean();

    echo $box;
}