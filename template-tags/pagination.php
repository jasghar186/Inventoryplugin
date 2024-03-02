<?php
/**
 * Template for displaying pagination on inventory page
 */

if (!defined('ABSPATH')) {
    exit;
}

function durango_inventory_pagination() {
    $pagination = ob_start(); ?>
        <div class="vehicles_pagination">
            <nav class="pagination d-flex align-items-center justify-content-between
            flex-column flex-md-row mt-30">
                <div class="gap-3 mb-20 mb-md-0 d-flex align-items-center fw-bold text-uppercase
                text-grey-3 font-md">
                    <span class="postCounts"></span>
                    <span class="all-pages d-none d-md-inline-block text-link cursor-pointer">
                    <span>Show</span> All
                    </span>
                </div>
                <div class="links-page text-grey-3 font-md d-flex align-items-center fw-bold
                text-uppercase gap-2">Page
                    <input type="number" data-dummy="12" value="1" min="1" class="input-pagination text-center"
                    style="width:50px;" data-total="8">
                    of &nbsp; <span class="inventory_max_pages">8</span>
                    <a class="next page-numbers" href="#" data-page="2">
                        <i class="bi bi-chevron-right text-grey-3 fw-bold" aria-hidden="true"></i>
                    </a>
                </div>
                <span class="fw-bold text-uppercase text-grey-3 font-md mt-20 d-md-none show text-center
                all-pages">Show All</span>
            </nav>
        </div>

    <?php
    $pagination = ob_get_clean();
    return $pagination;
}
