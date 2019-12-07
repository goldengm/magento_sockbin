
var sideMinicart = {
    itemsList: '#cart-sidebar',
    openBtn: '.cd-btn',
    panel: '.cd-panel',
    panelHeader: '.cd-panel-header',
    closeBtn: '.cd-panel-close',
    curtain: '.curtain-overlay',
    curtainClassName: 'curtain-overlay',
    minimalItemsHeight: 200,
    init: function() {
        var _this = this;
        
        // set height for item and make div scrollable
        this.calculate();
        this.createCurtain();
        
        jQuery(window).resize(function() {
           _this.calculate(); 
        });

        // bind open and close events
        this.bindEvents();
        
        jQuery(document).ajaxComplete(function( event, xhr, settings ) {
            //set timeout to recalculate minicart elements height
            setTimeout(function(){ _this.calculate(); }, 1500);
        });

    },
    bindEvents: function() {
        var _this = this;

        //open the lateral panel
        jQuery(this.openBtn).on('click', function(event){
                event.preventDefault();
                _this.open();
        });
        //close the lateral panel
        jQuery('.cd-panel').on('click', function(event){
                if( jQuery(event.target).is(_this.panel) || jQuery(event.target).is(_this.closeBtn) ) { 
                        jQuery(_this.panel).removeClass('is-visible');
                        _this.hideCurtain();
                        // clear messages
                        jQuery('.minicart-message').text('');
                        event.preventDefault();
                }
        });
    },
    open: function() {
        jQuery(this.panel).addClass('is-visible');
        jQuery('#header-cart').addClass('skip-active');
        this.showCurtain();
    },
    calculate: function() {
        /* restart height */
        jQuery(this.itemsList).attr('style','');
        jQuery('.minicart-wrapper').attr('style','');
        jQuery('#cart-sidebar').attr('style','');
        
        var winH = jQuery(window).height();
        var panelHeaderHeight = jQuery(this.panelHeader).height();
        jQuery(this.itemsList).height(scrollBoxHeight+'px').css('overflow-y','visible');
        var noneScrollHeight = jQuery('#header-cart').height()-jQuery(this.itemsList).height()+panelHeaderHeight;
        var scrollBoxHeight = winH - noneScrollHeight;
        if (scrollBoxHeight < this.minimalItemsHeight) {
            scrollBoxHeight = this.minimalItemsHeight;
        }
        
        jQuery(this.itemsList).height(scrollBoxHeight+'px').css('overflow-y','auto');
        
        var wrapperHeight = winH - panelHeaderHeight
        jQuery(this.panel).find('.minicart-wrapper').height(wrapperHeight+'px')
        
        // remove subtotal
        jQuery(this.panel).find('#shopping-cart-totals-table tr').eq(1).remove();
        // prevent body scroll when scrolling specific element
        this.stopScroll('.minicart-wrapper');
        this.stopScroll(this.itemsList);
        
    },
    itemsScroll: function() {

    },
    stopScroll: function(elem) {
        var _this = this;
        jQuery(elem).on( 'mousewheel', function ( e ) {
            var event = e.originalEvent,
                d = event.wheelDelta || -event.detail;
                this.scrollTop += ( d < 0 ? 1 : -1 ) * 30;
                
            e.preventDefault();
        });
    },
    createCurtain: function() {
        jQuery('<div class="'+this.curtainClassName+'"></div>').appendTo('body');
    },
    showCurtain: function() {
        jQuery(this.curtain).addClass('visible');
    },
    hideCurtain: function() {
        jQuery(this.curtain).removeClass('visible');
    }
}
        
jQuery(document).ready(function(){
    sideMinicart.init();
});


