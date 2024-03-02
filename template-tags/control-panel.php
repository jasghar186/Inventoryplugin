<?php

if (!defined('ABSPATH')) {
    exit;
}

function durango_inventory_options_page_content() {
    $content = ob_start();
    ?>

    <main class="wrap bg-white p-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="">Durango Inventory Managment Options</h1>
            <button class="btn btn-primary text-uppercase" type="submit">Save Changes</button>
        </div>
        
        <form action="#" method="POST" class="mt-4">
            <div class="form-field-wrap d-flex align-items-center gap-3">
                <label for="enable-filters-stickybar">
                    Enable Filters Stickybar
                </label>
                <?php 
                    $filterbar_sticky = intval(get_option('inventory_filterbar_sticky', 0));
                    echo '<input type="checkbox" id="enable-filters-stickybar" value="'.$filterbar_sticky.'" '.
                        ($filterbar_sticky === 1 ? 'checked' : '').' class="form-control" />'; 
                ?>

            </div>
        </form>
    </main>

    <?php
    $content = ob_get_clean();

    echo $content;
}