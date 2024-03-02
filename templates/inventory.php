<?php /* Template Name: Inventory Template
* Template displaying inventory page
*/

get_header();

/**
 * Inventory page searchbar
 */
function durango_inventory_searchbar($class = '', $iconClasses = '')
{
    $searchBar = ob_start();
?>
    <form onsubmit="return false" class="inventory-filterbar w-100 position-relative">
        <label for="inventory-search-bar" class="w-100">
            <input type="search" name="inventory_search" id="inventory-search-bar" data-type="search" class="inventory-search-filters w-100 search-filters" placeholder="Search" />
        </label>
        <i class="bi bi-search
    position-absolute search-filter-icon
    font-md cursor-pointer translate-middle-y
    top-50 text-grey-3" style="right: 12px;"></i>
    </form>

<?php
    $searchBar = ob_get_clean();

    return $searchBar;
}

/**
 * Inventory page searchbar
 */
function durango_inventory_filter($label)
{
    $checkbox = ob_start();
    $id = str_replace(' ', '_', $label);
?>
    <div class="border-bottom-sidebar">
        <div class="inventory-filterbar-title d-flex justify-content-between align-items-center flex-wrap cursor-pointer py-15" data-title="<?php echo $id; ?>">
            <h2 class="text-primary font-20 fw-bold p-0 font-helvetica m-0 text-capitalize">
                <?php echo $label; ?>
            </h2>
            <span class="d-flex align-items-center">
                <i class="bi bi-plus-lg font-20 fw-bold text-primary"></i>
            </span>
        </div>
        <div class="expanding-section d-none" data-expand="<?php echo $id; ?>"></div>
    </div>

<?php
    $checkbox = ob_get_clean();

    return $checkbox;
}

?>

<main class="inventory-wrap">
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs py-3 px-4 font-helvetica lh-sm" aria-label="breadcrumbs">
        <p>
            <a href="<?php echo site_url(); ?>" class="fs-6 text-dark fw-bold text-capitalize font-helvetica">Home</a>
            <span class="separator fs-6 ms-1 me-2">/</span>
            <span class="fs-6 fw-bold text-capitalize text-dark font-helvetica"><?php the_title(); ?></span>
        </p>
    </nav>

    <!-- Entry Content -->
    <div class="entry-content mt-lg-4 pt-lg-5 pb-3 pb-md-4 pb-lg-5 position-relative">
        <div class="px-4">
            <div class="inventory-content-wrapper row">
                <div class="col-12 col-md-5 col-lg-3">
                    <div class="inventory-filterbar <?php echo intval(get_option('inventory_filterbar_sticky')) === 1 ?
                    'position-sticky overflow-y-scroll vh-100 pe-3' : ''; ?>" style="top:150px;">
                        <div class="border-bottom d-none d-md-flex justify-content-between align-items-center pb-2 mb-20">
                            <h2 class="fw-bold p-0 font-helvetica m-0 text-primary fs-5">Filter</h2>
                            <div class="inventory-sidebar-toggle d-flex align-items-center">
                                <img src="<?php echo site_url(); ?>/wp-content/plugins/inventory/assets/images/inventory-filter-image.webp" alt="inventory filter icon" loading="lazy" height="18" width="18">
                            </div>
                        </div>

                        <!-- Filters Searchbar -->
                        <div class="main-search-filters w-100 position-relative border-bottom-sidebar mt-10 pb-20">
                            <?php echo durango_inventory_searchbar(); ?>
                        </div>

                        <?php
                        // Checkbox Filters
                        echo durango_inventory_filter('year');
                        echo durango_inventory_filter('make');
                        echo durango_inventory_filter('model');
                        echo durango_inventory_filter('body style');
                        echo durango_inventory_filter('type of vehicle');
                        echo durango_inventory_filter('doors');
                        echo durango_inventory_filter('mileage');
                        echo durango_inventory_filter('cylinders');
                        echo durango_inventory_filter('drivetrain');
                        echo durango_inventory_filter('transmission');
                        echo durango_inventory_filter('exterior color');
                        echo durango_inventory_filter('interior color');
                        echo durango_inventory_filter('original price');
                        echo durango_inventory_filter('certified');
                        echo durango_inventory_filter('fuel type');
                        ?>
                        <!-- Banner Images -->
                        <a href="https://www.kbb.com/instant-cash-offer/W/70317903/43A6F9B8-DB6C-48C0-A360-F658B2176E3E"
                        class="d-block w-100 my-4" target="_blank">
                            <img src="<?php echo site_url(); ?>/wp-content/plugins/inventory/assets/images/kbb-banner.webp"
                            alt="Kelly Blue Book Instant Cash Offer"
                            title="Kelly Blue Book Instant Cash Offer"
                            loading="lazy"
                            width="236"
                            height="256" />
                        </a>
                        <a href="https://insurance.polly.co/?dealershipId=F8E92137-96A1-B377-4517-9A6322F35AD0&campaignId=DPIAwebsite&__hstc=7873965.d260c032869087a80468f6c80173ad9b.1673564858571.1673564858571.1673564858571.1&__hssc=7873965.1.1673564858572&__hsfp=275471683&hsCtaTracking=64f86256-f92a-4caa-8498-4ae83f1ab63c%7C592199c5-215a-45ab-ac30-680c7a210694"
                        class="d-block w-100 my-4" target="_blank">
                            <img src="<?php echo site_url(); ?>/wp-content/plugins/inventory/assets/images/insurance-polly-banner.webp"
                            alt="Polly Insuranve Get A Quote"
                            title="Polly Insuranve Get A Quote"
                            loading="lazy"
                            width="236"
                            height="256" />
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-9">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-grey-3 fw-bold font-sm p-0 m-0 font-segoe"><span class="inventory_found_vehicles">00</span> Vehicles Matching</p>
                        <div class="header-cta-filters d-flex justify-content-end align-items-center gap-3">
                            <div class="inventory-layout-changer d-flex align-items-center column-gap-3">
                                <span class="inventory-layout-change-grid d-flex
                                align-items-center active font-30 text-grey-3 cursor-pointer">
                                    <i class="bi bi-grid-fill"></i>
                                </span>
                                <span class="inventory-layout-change-list d-flex
                                align-items-center font-30 text-grey-3 cursor-pointer">
                                    <i class="bi bi-list"></i>
                                </span>
                            </div>
                            <div class="inventory-sort-wrap d-flex align-items-center column-gap-2">
                                <p class="text-grey-3 fw-bold fs-lg p-0 m-0 font-segoe">
                                    Sort by:
                                </p>
                                <div class="position-relative">
                                    <select name="inventory-sort" id="inventory-sort" title="inventory-sort"
                                    class="h-100 font-sm dropdown-filters main-sort-filter" data-type="sort-by">
                                        <option value="bad-value" disabled selected>Select An Option</option>
                                        <option value="low-to-high">Price (lowest to highest)</option>
                                        <option value="high-to-low">Price (highest to lowest)</option>
                                        <option value="mileage-lowest">Mileage - Lowest</option>
                                        <option value="mileage-highest">Mileage - Highest</option>
                                        <option value="year-lowest">Year - Lowest</option>
                                        <option value="year-highest">Year - Highest</option>
                                        <option value="listings-a-z">Make/Model - A to Z</option>
                                        <option value="listings-z-a">Make/Model - Z to A</option>
                                        <option value="listing-date-new">Date Listed - Newest</option>
                                        <option value="listing-date-old">Date Listed - Oldest</option>
                                        <option value="listing-new-to-old">Newest to Oldest</option>
                                    </select>

                                    <span class="position-absolute translate-middle-y top-50 text-grey-3">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Filters -->
                    <div class="pt-3 d-flex justify-content-start flex-wrap gap-3 flex-wrap align-items-center">
                        <div class="selected-filters-wrapper d-flex justify-content-start align-items-center gap-3 flex-wrap">
                            
                        </div>
                        <div class="d-none d-lg-inline-block">
                            <button class="reset-filters rounded-circle-px bg-transparent py-1 px-15 font-md
                            lh-sm d-none">Clear all</button>
                        </div>
                    </div>

                    <!-- Listings Cards -->
                    <div class="row position-relative vehicles-container pt-5" data-current-page="1" data-max-pages="1">
                        
                    </div>

                    <!-- Inventory Pagination -->
                    <?php echo durango_inventory_pagination(); ?>

                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>