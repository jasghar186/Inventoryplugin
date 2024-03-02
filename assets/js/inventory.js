jQuery(document).ready(function ($) {

    const SPINNER_CLASSES = 'spinner-border spinner-border-sm text-primary';

    function startLoadingSpinner(targetElem) {
        targetElem.find('span')
            .addClass(SPINNER_CLASSES)
            .attr('role', 'filter load')
            .css({ width: '25.5px', height: '25.5px' })
            .children('i').addClass('d-none');
    }

    function stopLoadingSpinner(targetElem, success) {
        targetElem.find('span')
            .removeClass(SPINNER_CLASSES)
            .removeAttr('role')
            .css({ width: 'auto', height: 'auto' })
            .children('i')
            .addClass((success ? 'bi-dash-lg' : 'bi-plus-lg'))
            .removeClass((success ? 'bi-plus-lg' : 'bi-dash-lg'))
            .removeClass('d-none');
    }

    function toggleOptions(targetElem) {
        targetElem.find('span i').toggleClass('bi-plus-lg').toggleClass('bi-dash-lg');
    }

    // Load filters values using ajax on click
    $('.inventory-filterbar-title').click(function () {
        let targetElem = $(this);
        let filter = targetElem.data('title').trim().toLowerCase();
        let optionsLoaded = targetElem.data('options-loaded') || false;

        if (!optionsLoaded) {
            startLoadingSpinner(targetElem);

            $.ajax({
                type: 'POST',
                url: ajax_obj.ajax_url,
                data: {
                    filter: filter,
                    action: 'load_inventory_filters'
                },
                success: function (response) {
                    if (response.success) {
                        targetElem.attr('data-options-loaded', true);

                        if (!['mileage', 'original_price', 'exterior_color', 'interior_color'].includes(filter)) {
                            let html = '';
                            $(response.data.filter).each(function (_, value) {
                                const truncatedValue = value.length > 20 ? value.substring(0, 20) + '...' : value;

                                html += `<div class="d-flex align-items-center mb-3 position-relative">
                                    <input class="checkbox-filters ${filter}-filter-input me-3"
                                        data-type="${filter}"
                                        type="checkbox"
                                        name="listing_${filter}[]"
                                        id="inventory-filter-${filter}-checkbox_${value}"
                                        value="${value}"
                                        style="width:22px; height:22px;">
                                    <label for="inventory-filter-${filter}-checkbox_${value}"
                                        class="inventory-filterbar text-capitalize fw-bold font-segoe font-md text-link cursor-pointer text-dark">${truncatedValue}</label>
                                </div>`;
                            });
                            targetElem.next().html(html);
                        } else if (filter === 'exterior_color' || filter === 'interior_color') {
                            let html = '<div class="colorbox-wrapper d-flex flex-wrap gap-3">';
                            $(response.data.filter).each(function (index, colorObj) {
                                let value = colorObj.value !== '' && colorObj.value !== undefined ? colorObj.value : undefined;
                                let filteredKey = colorObj.key !== '' && colorObj.key !== undefined ? colorObj.key : undefined;

                                if (value !== undefined && filteredKey !== undefined) {
                                    let id = value.replace(/ /g, '_').replace(/[^\w\s]/g, '').toLowerCase();
                                    html += `<div class="flex-grow-1">
                                    <input type="checkbox" class="d-none checkbox-filters exterior_color-filter-input"
                                    data-type="exterior_color" name="exterior_color[]"
                                    id="${filter}-checkbox_${id}" value="${value}">
                                    <label for="${filter}-checkbox_${id}">
                                    <span class="d-inline-block color-filter-pills border rounded-circle-px cursor-pointer"
                                    data-color="${value}" data-color-code="FF0000" data-toggle="tooltip"
                                    data-placement="top" title="${value}" style="background:#${colorObj.key};width:50px;height:50px;"
                                    data-original-title="${value}"></span>
                                    </label>
                                    </div>`;
                                }
                            })
                            html += '</div>';
                            targetElem.next().html(html)
                        } else if (filter === 'mileage' || filter === 'original_price') {
                            let minValue = filter === 'mileage' ? Math.min.apply(null, response.data.filter) : Math.min.apply(null, response.data.filter);
                            let maxValue = filter === 'mileage' ? Math.max.apply(null, response.data.filter) : Math.max.apply(null, response.data.filter);
                            let stepSize = 10;

                            let html = `<div class="d-flex align-items-center justify-content-between mb-4 px-3
                            bg-grey-3">
                            <p class="fw-bold text-white font-segoe p-0 ${filter}_min_value">${(filter === 'original_price' ? '$' : '')} ${minValue.toLocaleString()}</p>
                            <p class="fw-bold text-white font-segoe p-0 ${filter}_max_value">${(filter === 'original_price' ? '$' : '')} ${maxValue.toLocaleString()}</p>
                            </div>`;
                            html += `<div class="range-container position-relative pb-3">
                            <div class="range-slider overflow-hidden position-absolute w-100 top-0 start-0">
                            <div class="range-highlight top-0 position-absolute h-100 bg-danger z-0"
                            style="left: 0%; width: 100%;">
                            </div>
                            </div>
                            <input type="range" name="${filter}" id="${filter}-min"
                            class="range-filters ${filter}_range_filters filter-min-field position-absolute w-100 m-0 p-0 bg-transparent
                            h-auto top-0 start-0" min="${minValue}" value="${minValue}" max="${maxValue}" step="${(maxValue - minValue) / stepSize}" data-filter="${filter}">
                            <input type="range" name="${filter}" id="${filter}-max"
                            class="range-filters ${filter}_range_filters position-absolute w-100 m-0 p-0 bg-transparent h-auto top-0 start-0"
                            min="${minValue}" value="${maxValue}" max="${maxValue}" step="${(maxValue - minValue) / stepSize}" data-filter="${filter}">
                            <input type="text" name="range-value" class="range-value
                            range-value-${filter}-value d-none" data-include="false">
                            
                            <input type="text" id="${filter}-value-hidden-field" value="${minValue + ',' + maxValue}" class="d-none" />
                            </div>`
                            targetElem.next().html(html)
                        }

                        stopLoadingSpinner(targetElem, true);
                    } else {
                        targetElem.removeAttr('data-options-loaded');
                        stopLoadingSpinner(targetElem, false);
                    }
                },
                error: function (_, error, status) {
                    alert(error + ' ' + status);
                    targetElem.removeAttr('data-options-loaded');
                    stopLoadingSpinner(targetElem, false);
                }
            });
        } else {
            toggleOptions(targetElem);
        }

        // Hide/Show expanding section
        targetElem.next().toggleClass('d-none');
    });


    /**
     * Load Inventory vehicles on page load
     */


    function durangoInventoryAjax(initialPageLoad, filtersArr) {
        const vehiclesContainer = $('.vehicles-container');
        const checkboxFilters = $('.checkbox-filters:checked');
        const searchFilters = $('.inventory-search-filters');
        let optionsArr = [];
        let paged = parseInt($('.vehicles-container').data('current-page'));
        if (!initialPageLoad) {
            // If its not initial page load request then get values of checked items and store in optionsArr

        }

        // Add a pre loader
        const preloader = Array.from({ length: 6 }, () =>
            `<div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-30">
            <div class="preloader-card pb-4">
            <div class="preloader-element preloader-image"></div>
            <div class="px-20">
            <div class="preloader-element preloader-title mt-3 mb-20"></div>
            <div class="d-flex">
            <div class="w-50 flex-grow-1">
            <div class="preloader-element preloader-meta mb-1"></div>
            <div class="preloader-element preloader-meta mb-1"></div>
            <div class="preloader-element preloader-meta mb-1"></div>
            <div class="preloader-element preloader-meta mb-1"></div>
            </div>
            <div class="w-50 flex-grow-1 gap-2 d-flex align-items-start pt-3 justify-content-center">
            <div class="preloader-element preloader-color"></div>
            <div class="preloader-element preloader-color"></div>
            </div>
            </div>
            
            <div class="preloader-element preloader-cta my-4"></div>
            <div class="d-flex gap-3">
            <div class="preloader-element flex-grow-1 preloader-badges"></div>
            <div class="preloader-element flex-grow-1 preloader-badges"></div>
            </div>
            </div>
            </div>
            </div>` ).join('')


        vehiclesContainer.html(preloader)

        // Add checkboxes values in optionsArr
        $('.checkbox-filters:checked').each(function (index, checkbox) {
            let checkboxVal = $(checkbox).val()
            let checkboxType = $(checkbox).data('type')

            if (checkboxVal !== '') {
                let existingTypeIndex = optionsArr.findIndex(item => item.type === checkboxType);

                if (existingTypeIndex === -1) {
                    // If the type doesn't exist, add it to optionsArr
                    optionsArr.push({
                        type: checkboxType,
                        values: [checkboxVal]
                    });
                } else {
                    // If the type already exists, add the value to the existing array
                    optionsArr[existingTypeIndex].values.push(checkboxVal);
                }
            }
        })

        // Add searchbar filter value in optionsArr
        if ($('.inventory-search-filters').val() !== '') {
            optionsArr.push({
                type: 'search',
                values: $('.inventory-search-filters').val().toLowerCase().trim()
            })
        }
        // Add price and mileage filter values
        let priceValue = $('#original_price-value-hidden-field').val();
        let mileageValue = $('#mileage-value-hidden-field').val();

        if (priceValue !== '' && priceValue !== undefined) {
            optionsArr.push({
                type: 'original_price',
                values: priceValue
            })
        }
        if (mileageValue !== '' && mileageValue !== undefined) {
            optionsArr.push({
                type: 'mileage',
                values: mileageValue
            })
        }

        // Add sorting dropdown values in optionsArr

        $.ajax({
            type: 'POST',
            url: ajax_obj.ajax_url,
            data: {
                optionsArr: optionsArr,
                paged: paged,
                action: 'update_inventory_vehicles'
            },
            success: function (response) {
                console.log(response)

                /** Function to output selected filter pills HTML */
                function durangoSelectedFiltersPills(index = 0, type, value) {
                    let html = `<div class="rounded-circle-px bg-grey-3 px-15 py-1 selected-filter-pill">
                        <span class="text-capitalize me-2 font-md lh-sm">${value}</span>
                        <span class="selected-filter-remove cursor-pointer"
                        data-id="${value}" data-val="${value}" data-type="${type}">x</span>
                    </div>`

                    return html;
                }

                if (response.success) {

                    $('.vehicles-container').html(response.data.cards)
                    $('.inventory_found_vehicles').html(response.data.vehicles_count)
                    $('.postCounts').html(`Viewing 1 - ${response.data.posts_per_page} Of ${response.data.vehicles_count}`)
                    $('.inventory_max_pages').html(response.data.max_num_pages)

                    // Show selected filters
                    let filterPills = '';
                    let currentUrl = window.location.href.toLowerCase();
                    let urlUpdatedString = '';

                    if (currentUrl.indexOf('?') !== -1) {
                        urlUpdatedString = currentUrl + '&'; // Use & as separator if query parameters exist
                    } else {
                        urlUpdatedString = currentUrl + '?'; // Use ? as separator if no query parameters exist
                    }

                    $(optionsArr).each((index, filter) => {
                        if (Array.isArray(filter.values)) {
                            $(filter.values).each((item, val) => {
                                filterPills += durangoSelectedFiltersPills(item, filter.type, val);
                                urlUpdatedString += encodeURIComponent(filter.type.toLowerCase() + '[]') + '=' + encodeURIComponent(val) + '&';
                            });
                        } else if (typeof filter.values === 'string') { // Check if the value is a string
                            filterPills += durangoSelectedFiltersPills(undefined, filter.type, filter.values);
                            urlUpdatedString += encodeURIComponent(filter.type.toLowerCase()) + '=' + encodeURIComponent(filter.values) + '&';
                        }
                    });

                    /** Add cards content */
                    $('.selected-filters-wrapper').html(filterPills); // Use 'append' instead of 'html' to add multiple pills
                    console.log(urlUpdatedString)

                    // Remove the trailing '&' from the URL string
                    urlUpdatedString = urlUpdatedString.slice(0, -1);
                    window.history.replaceState({}, '', urlUpdatedString);

                    /** Toggle Reset filter button based on the filters length */
                    $(document).find('.selected-filters-wrapper').find('.selected-filter-pill').length > 0 ?
                        $(document).find('.reset-filters').removeClass('d-none') : $(document).find('.reset-filters').addClass('d-none');

                    /** Add the applied filters to the URL */


                    /** Lazy Load Images */
                    window.lazyLoadImages()

                } else if (!response.success) {
                    $('.vehicles-container').html(response.data.not_found)
                }
            },
            error: function (XHR, error, status) {
                console.log(error + ' ' + status)
            }
        })
    }

    durangoInventoryAjax(true)


   

    /**
     * Search bar filters
     */
    // Write code here to sync values of primary and secondary filter bars on inventory page
    let searchFiltersTimeout;

    $('.inventory-search-filters').keyup((e) => {
        // Clear the previous timeout to restart the timer
        clearTimeout(searchFiltersTimeout);

        // Set a new timeout
        searchFiltersTimeout = setTimeout(() => {
            // This ajax will sent after 1 second of user inactivity
            durangoInventoryAjax(false);
        }, 1000);
    });

    $(document).on('change', '.checkbox-filters', function () {
        durangoInventoryAjax(false);
    })

    $(document).on('input', '.mileage_range_filters', function () {
        let minValue = parseInt($('#mileage-min').val())
        let maxValue = parseInt($('#mileage-max').val())
        let lowestValue = parseInt($('#mileage-min').attr('min'));
        let highestValue = parseInt($('#mileage-max').attr('max'));
        let step = parseInt($(this).attr('step'))

        if (minValue < 0 || minValue === undefined) {
            minValue = 0
        } else if (maxValue < 0 || maxValue === undefined) {
            maxValue = 0
        }

        if ((minValue + step) >= maxValue || minValue >= highestValue) {
            minValue = maxValue - step;
            $('#mileage-min').val(minValue);
        } else if ((maxValue - step) <= minValue || maxValue <= lowestValue) {
            maxValue = minValue + step;
            $('#mileage-max').val(maxValue);
        }

        $('.mileage_min_value').html(minValue.toLocaleString())
        $('.mileage_max_value').html(maxValue.toLocaleString())
        // Fill up the highlighted area
        let minPercent = ((minValue - lowestValue) / (highestValue - lowestValue)) * 100;
        let maxPercent = ((maxValue - lowestValue) / (highestValue - lowestValue)) * 100;
        $('.range-highlight').css({
            'left': minPercent + '%',
            'width': (maxPercent - minPercent) + '%'
        });
        $('#mileage-value-hidden-field').val(minValue + ',' + maxValue)
    })

    $(document).on('input', '.original_price_range_filters', function () {
        let minValue = parseInt($('#original_price-min').val())
        let maxValue = parseInt($('#original_price-max').val())
        let lowestValue = parseInt($('#original_price-min').attr('min'));
        let highestValue = parseInt($('#original_price-max').attr('max'));
        let step = parseInt($(this).attr('step'))

        if (minValue < 0 || minValue === undefined) {
            minValue = 0
        } else if (maxValue < 0 || maxValue === undefined) {
            maxValue = 0
        }

        if ((minValue + step) >= maxValue || minValue >= highestValue) {
            minValue = maxValue - step;
            $('#original_price-min').val(minValue);
        } else if ((maxValue - step) <= minValue || maxValue <= lowestValue) {
            maxValue = minValue + step;
            $('#original_price-max').val(maxValue);
        }

        $('.original_price_min_value').html(minValue.toLocaleString())
        $('.original_price_max_value').html(maxValue.toLocaleString())
        // Fill up the highlighted area
        let minPercent = ((minValue - lowestValue) / (highestValue - lowestValue)) * 100;
        let maxPercent = ((maxValue - lowestValue) / (highestValue - lowestValue)) * 100;
        $('.range-highlight').css({
            'left': minPercent + '%',
            'width': (maxPercent - minPercent) + '%'
        });
        $('#original_price-value-hidden-field').val(minValue + ',' + maxValue)
    })

    $(document).on('change', '.mileage_range_filters ', function () {
        durangoInventoryAjax(false)
    })
    $(document).on('change', '.original_price_range_filters ', function () {
        durangoInventoryAjax(false)
    })


});
