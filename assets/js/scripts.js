/*
* EDD Compare URL
* Function does 3 things:
* 1. Appends download ID to the inner text of a hidden div when button is clicked
* 2. Hides the compare button and shows a go button instead
* 3. Updates the URL for each go button each time another is clicked
*
* @since 0.1
 */
function eddCompareURL(id) {
    var url = "edd-compare-url";
    document.getElementById(url).innerHTML = document.getElementById(url).innerHTML + id + ',';
    document.getElementById('edd-compare-button-' + id).style.display = 'none';
    document.getElementById('edd-compare-go-button-' + id).style.display = 'inline-block';
    var elements = document.getElementsByClassName("edd-compare-go");
    for (var i = 0; i < elements.length; i++) {
        var link = document.getElementById(url).innerHTML.replace(/\&amp\;/g, "&").slice(0, - 1);
        elements[i].setAttribute("href", link);
    }
}

jQuery( function( $ ) {

    /**
     * Create a productsTable object, storing its interior members
     * @param {object} element jQuery Object
     */
    function productsTable( element ) {
        this.element = element;
        this.table = this.element.children('.edd-compare-products-table');
        this.productsWrapper = this.table.children('.edd-compare-products-wrapper');
        this.tableColumns = this.productsWrapper.children('.edd-compare-products-columns');
        this.products = this.tableColumns.children('.product');
        //additional properties here
        this.productsNumber = this.products.length;
		this.productWidth = this.products.eq(0).width();
		this.productsTopInfo = this.table.find('.top-info');
		this.featuresTopInfo = this.table.children('.features').children('.top-info');
		this.topInfoHeight = this.featuresTopInfo.innerHeight() + 30;
		this.leftScrolling = false;
		this.filterBtn = this.element.find('.filter');
		this.resetBtn = this.element.find('.reset');
		this.filtering = false,
		this.selectedproductsNumber = 0;
		this.filterActive = false;
		this.navigation = this.table.children('.edd-compare-products-navigation');
        // bind table events
        this.bindEvents();
    }

    productsTable.prototype.bindEvents = function() {
        var self = this;

        self.productsWrapper.on('scroll', function(){
            if(!self.leftScrolling) {
                self.leftScrolling = true;
                (!window.requestAnimationFrame) ? setTimeout(function(){self.updateLeftScrolling();}, 250) : window.requestAnimationFrame(function(){self.updateLeftScrolling();});
            }
        });

        self.products.on('click', '.top-info', function(){
            var product = $(this).parents('.product');
            if( !self.filtering && product.hasClass('selected') ) {
                product.removeClass('selected');
                self.selectedproductsNumber = self.selectedproductsNumber - 1;
                self.upadteFilterBtn();
            } else if( !self.filtering && !product.hasClass('selected') ) {
                product.addClass('selected');
                self.selectedproductsNumber = self.selectedproductsNumber + 1;
                self.upadteFilterBtn();
            }
        });
        
        self.filterBtn.on('click', function(event){
            event.preventDefault();
            if(self.filterActive) {
                self.filtering =  true;
                self.showSelection();
                self.filterActive = false;
                self.filterBtn.removeClass('active');
            }
        });
        //reset product selection
        self.resetBtn.on('click', function(event){
            event.preventDefault();
            if( self.filtering ) {
                self.filtering =  false;
                self.resetSelection();
            } else {
                self.products.removeClass('selected');
            }
            self.selectedproductsNumber = 0;
            self.upadteFilterBtn();
        });

        this.navigation.on('click', 'a', function(event){
            event.preventDefault();
            self.updateSlider( $(event.target).hasClass('next') );
        });
    }

    productsTable.prototype.upadteFilterBtn = function() {
        //show/hide filter btn
        if( this.selectedproductsNumber >= 2 ) {
            this.filterActive = true;
            this.filterBtn.addClass('active');
        } else {
            this.filterActive = false;
            this.filterBtn.removeClass('active');
        }
    }

    productsTable.prototype.updateLeftScrolling = function() {
        var totalTableWidth = parseInt(this.tableColumns.eq(0).outerWidth(true)),
            tableViewport = parseInt(this.element.width()),
            scrollLeft = this.productsWrapper.scrollLeft();

        ( scrollLeft > 0 ) ? this.table.addClass('scrolling') : this.table.removeClass('scrolling');

        if( this.table.hasClass('top-fixed') && checkMQ() == 'desktop') {
            setTranformX(this.productsTopInfo, '-'+scrollLeft);
            setTranformX(this.featuresTopInfo, '0');
        }

        this.leftScrolling =  false;

        this.updateNavigationVisibility(scrollLeft);
    }

    productsTable.prototype.updateNavigationVisibility = function(scrollLeft) {
        ( scrollLeft > 0 ) ? this.navigation.find('.prev').removeClass('inactive') : this.navigation.find('.prev').addClass('inactive');
        ( scrollLeft < this.tableColumns.outerWidth(true) - this.productsWrapper.width() && this.tableColumns.outerWidth(true) > this.productsWrapper.width() ) ? this.navigation.find('.next').removeClass('inactive') : this.navigation.find('.next').addClass('inactive');
    }

    productsTable.prototype.updateTopScrolling = function(scrollTop) {
        var offsetTop = this.table.offset().top,
            tableScrollLeft = this.productsWrapper.scrollLeft();

        if ( offsetTop <= scrollTop && offsetTop + this.tableHeight - this.topInfoHeight >= scrollTop ) {
            //fix products top-info && arrows navigation
            if( !this.table.hasClass('top-fixed') && $(document).height() > offsetTop + $(window).height() + 200) { 
                this.table.addClass('top-fixed').removeClass('top-scrolling');
                if( checkMQ() == 'desktop' ) {
                    this.productsTopInfo.css('top', '0');
                    this.navigation.find('a').css('top', '0px');
                }
            }

        } else if( offsetTop <= scrollTop ) {
            //product top-info && arrows navigation -  scroll with table
            this.table.removeClass('top-fixed').addClass('top-scrolling');
            if( checkMQ() == 'desktop' )  {
                this.productsTopInfo.css('top', (this.tableHeight - this.topInfoHeight) +'px');
                this.navigation.find('a').css('top', (this.tableHeight - this.topInfoHeight) +'px');
            }
        } else {
            //product top-info && arrows navigation -  reset style
            this.table.removeClass('top-fixed top-scrolling');
            this.productsTopInfo.attr('style', '');
            this.navigation.find('a').attr('style', '');
        }

        this.updateLeftScrolling();
    }

    productsTable.prototype.updateProperties = function() {
        this.tableHeight = this.table.height();
        this.productWidth = this.products.eq(0).width();
        this.topInfoHeight = this.featuresTopInfo.innerHeight() + 30;
        this.tableColumns.css('width', this.productWidth*this.productsNumber + 'px');
    }

    productsTable.prototype.showSelection = function() {
        this.element.addClass('filtering');
        this.filterProducts();
    }

    productsTable.prototype.resetSelection = function() {
        this.tableColumns.css('width', this.productWidth*this.productsNumber + 'px');
        this.element.removeClass('no-product-transition');
        this.resetProductsVisibility();
    }

    productsTable.prototype.filterProducts = function() {
        var self = this,
            containerOffsetLeft = self.tableColumns.offset().left,
            scrollLeft = self.productsWrapper.scrollLeft(),
            selectedProducts = this.products.filter('.selected'),
            numberProducts = selectedProducts.length;

        selectedProducts.each(function(index){
            var product = $(this),
                leftTranslate = containerOffsetLeft + index*self.productWidth + scrollLeft - product.offset().left;
            setTranformX(product, leftTranslate);

            if(index == numberProducts - 1 ) {
                product.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
                    setTimeout(function(){
                        self.element.addClass('no-product-transition');
                    }, 50);
                    setTimeout(function(){
                        self.element.addClass('filtered');
                        self.productsWrapper.scrollLeft(0);
                        self.tableColumns.css('width', self.productWidth*numberProducts + 'px');
                        selectedProducts.attr('style', '');
                        product.off('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend');
                        self.updateNavigationVisibility(0);
                    }, 100);
                });
            }
        });

        if( $('.no-csstransitions').length > 0 ) {
            //browser not supporting css transitions
            self.element.addClass('filtered');
            self.productsWrapper.scrollLeft(0);
            self.tableColumns.css('width', self.productWidth*numberProducts + 'px');
            selectedProducts.attr('style', '');
            self.updateNavigationVisibility(0);
        }
    }

    productsTable.prototype.resetProductsVisibility = function() {
        var self = this,
            containerOffsetLeft = self.tableColumns.offset().left,
            selectedProducts = this.products.filter('.selected'),
            numberProducts = selectedProducts.length,
            scrollLeft = self.productsWrapper.scrollLeft(),
            n = 0;

        self.element.addClass('no-product-transition').removeClass('filtered');

        self.products.each(function(index){
            var product = $(this);
            if (product.hasClass('selected')) {
                n = n + 1;
                var leftTranslate = (-index + n - 1)*self.productWidth;
                setTranformX(product, leftTranslate);
            }
        });

        setTimeout(function(){
            self.element.removeClass('no-product-transition filtering');
            setTranformX(selectedProducts, '0');
            selectedProducts.removeClass('selected').attr('style', '');
        }, 50);
    }

    productsTable.prototype.updateSlider = function(bool) {
        var scrollLeft = this.productsWrapper.scrollLeft();
        scrollLeft = ( bool ) ? scrollLeft + this.productWidth : scrollLeft - this.productWidth;

        if( scrollLeft < 0 ) scrollLeft = 0;
        if( scrollLeft > this.tableColumns.outerWidth(true) - this.productsWrapper.width() ) scrollLeft = this.tableColumns.outerWidth(true) - this.productsWrapper.width();

        this.productsWrapper.animate( {scrollLeft: scrollLeft}, 200 );
    }

    var comparisonTables = [];
    $('.edd-compare-products').each(function(){
        //create a productsTable object for each .edd-compare-products
        comparisonTables.push(new productsTable($(this)));
    });

    var windowScrolling = false;
    //detect window scroll - fix product top-info on scrolling
    $(window).on('scroll', function(){
        if(!windowScrolling) {
            windowScrolling = true;
            (!window.requestAnimationFrame) ? setTimeout(checkScrolling, 250) : window.requestAnimationFrame(checkScrolling);
        }
    });

    var windowResize = false;
    //detect window resize - reset .edd-compare-products properties
    $(window).on('resize', function(){
        if(!windowResize) {
            windowResize = true;
            (!window.requestAnimationFrame) ? setTimeout(checkResize, 250) : window.requestAnimationFrame(checkResize);
        }
    });

    function checkScrolling(){
        var scrollTop = $(window).scrollTop();
        comparisonTables.forEach(function(element){
            element.updateTopScrolling(scrollTop);
        });

        windowScrolling = false;
    }

    function checkResize(){
        comparisonTables.forEach(function(element){
            element.updateProperties();
        });

        windowResize = false;
    }

    function checkMQ() {
        //check if mobile or desktop device
        return window.getComputedStyle(comparisonTables[0].element.get(0), '::after').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
    }

    function setTranformX(element, value) {
        element.css({
            '-moz-transform': 'translateX(' + value + 'px)',
            '-webkit-transform': 'translateX(' + value + 'px)',
            '-ms-transform': 'translateX(' + value + 'px)',
            '-o-transform': 'translateX(' + value + 'px)',
            'transform': 'translateX(' + value + 'px)'
        });
    }
    
} );