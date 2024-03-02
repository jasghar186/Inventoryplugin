jQuery(document).ready(function ($) {
    // Lazy load the images
    // Function to check if an element is in the viewport
    function isElementInViewport(el) {
        var rect = el.getBoundingClientRect();
        return (
            (rect.top <= window.innerHeight && rect.bottom >= 0) ||
            (rect.bottom >= 0 && rect.top <= 150)
        );
    }
    function lazyLoadImages() {
        // Select all images and iframes with data-src attribute
        $('img[data-src], iframe[data-src]').each(function () {
            if (isElementInViewport(this)) {
                // If the element is in the viewport, copy data-src to src
                $(this).attr('src', $(this).data('src'));
                // Remove the data-src attribute to avoid unnecessary processing in the future
                $(this).removeAttr('data-src');
            }
        });
    }

    lazyLoadImages();

    // Load images on scroll
    $(document).scroll(function () {
        lazyLoadImages();
    });
    // Load images on slick slider navigation
    $('.slick-arrow').click(function () {
        lazyLoadImages();
    })

    // Export lazyload globally
    window.lazyLoadImages = lazyLoadImages;

})