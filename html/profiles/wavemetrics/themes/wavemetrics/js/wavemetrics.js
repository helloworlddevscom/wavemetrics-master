(function ($) {
    $(document).ready(function () {
        
        var sidebarMenu = $('.sidebar-menu');
        var closeIcon = $('a.close-icon');
        var hamburger = $('a.hamburger');
        var siteHeader = $('#header');
        var siteTitle = $('h1');
        var siteHeaderHeight = siteHeader.outerHeight();
                
        hamburger.click(function (e) {
            e.preventDefault();
            if (sidebarMenu.hasClass('showMenu')) {
                $('body').css('overflow', 'auto');
                sidebarMenu.removeClass('showMenu');
            }
            else {
                $('body').css('overflow', 'hidden');
                sidebarMenu.addClass('showMenu');
            }
        });
        
        closeIcon.click(function (e) {
            e.preventDefault();
            $('body').css('overflow', 'auto');
            sidebarMenu.removeClass('showMenu');
        });
        
        
        
        
        
        //remove the placeholder text for custom prices on PayByInvoice products
        $('.payByInvoiceProduct :input').removeAttr('placeholder');





        //Toggle behavior for sidebar menu toggle item
        var sideBarMenuToggle = $('.toggle-container');
        
        sideBarMenuToggle.on('click', function () {
            $(this).next('ul').slideToggle();
        });
        
        
        
        
        
        
        
        

        $('body').css('padding-top', siteHeaderHeight + 'px');
        $('.sidebar-inner-wrapper').css('top', siteHeaderHeight + 'px');

        
        
        
        
        
        
        
     
        var scrollTop = $(window).scrollTop();
        
        
            
        
        
        
        
        
        
        $('a.slider-scroll-down').click(function(){
            $('html, body').animate({
                scrollTop: $( $.attr(this, 'href') ).offset().top
            }, 500);
            return false;
        });
        
        
        
        
        
        
        
        
        
        ////Accordion toggle behavior
        $('.accordion-content').hide();
        $('h2.accordion-title').on('click', function(){
            var accordion_id = $(this).attr('aria-controls');

            $('h2.accordion-title').removeClass('active-tab');
            $(this).addClass('active-tab');
            
            $('.accordion-content').not("#"+accordion_id).slideUp();
            $("#"+accordion_id).slideDown();
            
            $('h2.accordion-title').attr('aria-selected', 'false');
            $(this).attr('aria-selected', 'true');
            
            $('.accordion-content').attr('aria-hidden', 'true');
            $("#"+accordion_id).attr('aria-hidden', 'false');
        })
        
        $('h2.accordion-title').first().click();
        
        
        
        
        
        
        
        
        
        ////Release notes toggle behavior
        $('.release-notes').hide();
        $('.release-title').on('click', function(){
            
            $('.release-notes').slideToggle();
            
        })
                
        
        
        
        
        
        
        
        
        //Product Gallery Behavior
        var productThumbnail = $('.product-thumbnails img');
        var productMainImage = $('.main-product-image img');
        
        productThumbnail.each(function () {
            $(this).on('click', function () {
                productThumbnail.removeClass('highlitedProductThumbnail');
                $(this).addClass('highlitedProductThumbnail');
                var productThumbnailSource = $(this).attr('src');
                productMainImage.attr('src', productThumbnailSource);
            });
        });
        
        productThumbnail.first().click();
        
        
        
        
        
        
        
        
        
        ////Background Image Gallery
        $('body').append('<div class="photo-gallery-overlay"><div class="gallery-overlay-thumbstrip"><div class="thumbstrip-inner-wrapper"></div></div><div class="gallery-overlay-item"><span class="photo-gallery-prev">&larr;</span><span class="photo-gallery-next">&rarr;</span></div><span class="photo-gallery-close">x</span></div>');

        $('.background-image-wrapper .background-image-inner-wrapper').each(function () {

            //Editable Vars
            var photoGalleryItem = $(this).children('img');

            //Do not edit the value of these vars as they are created by jQuery above
            var galleryLength = photoGalleryItem.length - 1;
            var photoGalleryOverlay = $('.photo-gallery-overlay');
            var galleryOverlayItem = $('.gallery-overlay-item');
            var currentIndex = 0;

            photoGalleryItem.click(function () {

            $('body').css('overflow', 'hidden');

                var currentIndex = 0;

                //Thumbstrip Stuff
                var thumbStripInnerWrapper = $('.thumbstrip-inner-wrapper');

                photoGalleryItem.each(function () {
                    thumbStripInnerWrapper.append('<div class="thumbnail" data-index="' + ($(this).index()) + '" style="background-image: url(' + $(this).attr('src') + ');" data-thumbnail-url="' + $(this).attr('src') + '"></div>');
                });

                var thumbnail = $('.thumbnail');
                var thumbnailWidth = thumbnail.outerWidth();
                var thumbStripWidth = thumbnailWidth * (galleryLength + 1);
                thumbStripInnerWrapper.css('width', thumbStripWidth + 'px');


                $('.thumbnail').click(function () {
                    currentIndex = $(this).attr('data-index');
                    currentIndex = parseInt(currentIndex);
                    var galleryOverlayThumbnailSRC = $(this).attr('data-thumbnail-url');
                    galleryOverlayItem.css('background-image', 'url(' + galleryOverlayThumbnailSRC + ')');
                    //Thumbnail Highlight
                    thumbnail.removeClass('currentThumbnail');
                    if ($(this).attr('data-index') == currentIndex) {
                        $(this).addClass('currentThumbnail');
                    }
                });

                //Gallery Stuff
                currentIndex = $(this).index();
                var photoGalleryImageSRC = photoGalleryItem.eq(currentIndex).attr('src');
                galleryOverlayItem.attr('style', 'background-image: url(' + photoGalleryImageSRC + ')');
                photoGalleryOverlay.fadeIn(100);
                //Thumbnail Highlight
                thumbnail.each(function () {
                    if ($(this).attr('data-index') == currentIndex) {
                        $(this).addClass('currentThumbnail');
                    }
                });

                $('.photo-gallery-next').click(function () {
                    currentIndex = currentIndex + 1;
                    if (currentIndex > galleryLength) {
                        currentIndex = 0;
                    }
                    photoGalleryImageSRC = photoGalleryItem.eq(currentIndex).attr('src');
                    galleryOverlayItem.attr('style', 'background-image: url(' + photoGalleryImageSRC + ')');
                    //Thumbnail Highlight
                    thumbnail.removeClass('currentThumbnail');
                    thumbnail.each(function () {
                        if ($(this).attr('data-index') == currentIndex) {
                            $(this).addClass('currentThumbnail');
                        }
                    });
                });

                $('.photo-gallery-prev').click(function () {
                    currentIndex -= 1;
                    if (currentIndex < 0) {
                        currentIndex = galleryLength;
                    }
                    photoGalleryImageSRC = photoGalleryItem.eq(currentIndex).attr('src');
                    galleryOverlayItem.attr('style', 'background-image: url(' + photoGalleryImageSRC + ')');
                    //Thumbnail Highlight
                    thumbnail.removeClass('currentThumbnail');
                    thumbnail.each(function () {
                        if ($(this).attr('data-index') == currentIndex) {
                            $(this).addClass('currentThumbnail');
                        }
                    });
                });

                $('.photo-gallery-close').click(function () {
                    photoGalleryOverlay.fadeOut(100);
                    currentIndex = 0;
                    //Remove Thumbnail Highlight
                    thumbnail.removeClass('currentThumbnail');
                    thumbStripInnerWrapper.html('');
                    $('body').css('overflow', 'auto');
                });

            });            

        });
        
        
        
        

        
        
        
        
        $(window).on('load',function() {    

            // Same Height jQuery Function
            $.fn.sameHeight = function(addHeight, breakPoint) {
                // The addHeight value is used for offsetting box-sizing
                // box-sizing: border-box; conflicts with height syncing in jQuery

                // Is the window greater than your breakpoint value
                if ($(window).width() > breakPoint) {
                    // Cache the highest
                    var highestBox = 0;

                    // Select and loop the elements you want to equalise
                    $(this).each(function(){  

                        // If this element is higher than the cached highest then store it
                        if($(this).outerHeight() > highestBox) {
                            highestBox = ($(this).height()) + addHeight;
                        }

                    });  

                    // Set the height of all elements to whichever was highest 
                    $(this).height(highestBox);
                }

            };

            $('.product-cta-wrapper .product-cta-column h3').sameHeight(30, 800);
            $('.blog-post-teaser-wrapper').sameHeight(10, 800);
            $('.two-column').sameHeight(10, 800);
            $('.support-center-link').sameHeight(10, 800);
            $('figure.gallery-item').sameHeight(0, 800);

        });

        $(".product-dr").hide();

        $('.product-dr-link').click(function(){
            $(".product-dr").hide();
            var product_div = '#product-dr-'+this.dataset.id;
            $(product_div).fadeIn(2000);
            $('html, body').animate({
                scrollTop: $("#products-dr-links").offset().top-70
            }, 500);
        });
        
        
        
        
        
        
    });    
})(jQuery);