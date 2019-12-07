// =============================================
// Primary Break Points
// =============================================

// These should be used with the bp (max-width, xx) mixin
// where a min-width is used, remember to +1 to break correctly
// If these are changed, they must also be updated in _var.scss

var bp = {
    xsmall: 479,
    small: 599,
    medium: 770,
    large: 979,
    xlarge: 1199
}

// ==============================================
// Pointer abstraction
// ==============================================

/**
 * This class provides an easy and abstracted mechanism to determine the
 * best pointer behavior to use -- that is, is the user currently interacting
 * with their device in a touch manner, or using a mouse.
 *
 * Since devices may use either touch or mouse or both, there is no way to
 * know the user's preferred pointer type until they interact with the site.
 *
 * To accommodate this, this class provides a method and two events
 * to determine the user's preferred pointer type.
 *
 * - getPointer() returns the last used pointer type, or, if the user has
 *   not yet interacted with the site, falls back to a Modernizr test.
 *
 * - The mouse-detected event is triggered on the window object when the user
 *   is using a mouse pointer input, or has switched from touch to mouse input.
 *   It can be observed in this manner: $j(window).on('mouse-detected', function(event) { // custom code });
 *
 * - The touch-detected event is triggered on the window object when the user
 *   is using touch pointer input, or has switched from mouse to touch input.
 *   It can be observed in this manner: $j(window).on('touch-detected', function(event) { // custom code });
 */
var PointerManager = {
    MOUSE_POINTER_TYPE: 'mouse',
    TOUCH_POINTER_TYPE: 'touch',
    POINTER_EVENT_TIMEOUT_MS: 500,
    standardTouch: false,
    touchDetectionEvent: null,
    lastTouchType: null,
    pointerTimeout: null,
    pointerEventLock: false,

    getPointerEventsSupported: function() {
        return this.standardTouch;
    },

    getPointerEventsInputTypes: function() {
        if (window.navigator.pointerEnabled) { //IE 11+
            //return string values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE: 'mouse',
                TOUCH: 'touch',
                PEN: 'pen'
            };
        } else if (window.navigator.msPointerEnabled) { //IE 10
            //return numeric values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE:  0x00000004,
                TOUCH:  0x00000002,
                PEN:    0x00000003
            };
        } else { //other browsers don't support pointer events
            return {}; //return empty object
        }
    },

    /**
     * If called before init(), get best guess of input pointer type
     * using Modernizr test.
     * If called after init(), get current pointer in use.
     */
    getPointer: function() {
        // On iOS devices, always default to touch, as this.lastTouchType will intermittently return 'mouse' if
        // multiple touches are triggered in rapid succession in Safari on iOS
        if(Modernizr.ios) {
            return this.TOUCH_POINTER_TYPE;
        }

        if(this.lastTouchType) {
            return this.lastTouchType;
        }

        return Modernizr.touch ? this.TOUCH_POINTER_TYPE : this.MOUSE_POINTER_TYPE;
    },

    setPointerEventLock: function() {
        this.pointerEventLock = true;
    },
    clearPointerEventLock: function() {
        this.pointerEventLock = false;
    },
    setPointerEventLockTimeout: function() {
        var that = this;

        if(this.pointerTimeout) {
            clearTimeout(this.pointerTimeout);
        }

        this.setPointerEventLock();
        this.pointerTimeout = setTimeout(function() { that.clearPointerEventLock(); }, this.POINTER_EVENT_TIMEOUT_MS);
    },

    triggerMouseEvent: function(originalEvent) {
        if(this.lastTouchType == this.MOUSE_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.MOUSE_POINTER_TYPE;
        $j(window).trigger('mouse-detected', originalEvent);
    },
    triggerTouchEvent: function(originalEvent) {
        if(this.lastTouchType == this.TOUCH_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.TOUCH_POINTER_TYPE;
        $j(window).trigger('touch-detected', originalEvent);
    },

    initEnv: function() {
        if (window.navigator.pointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'pointermove';
        } else if (window.navigator.msPointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'MSPointerMove';
        } else {
            this.touchDetectionEvent = 'touchstart';
        }
    },

    wirePointerDetection: function() {
        var that = this;

        if(this.standardTouch) { //standard-based touch events. Wire only one event.
            //detect pointer event
            $j(window).on(this.touchDetectionEvent, function(e) {
                switch(e.originalEvent.pointerType) {
                    case that.getPointerEventsInputTypes().MOUSE:
                        that.triggerMouseEvent(e);
                        break;
                    case that.getPointerEventsInputTypes().TOUCH:
                    case that.getPointerEventsInputTypes().PEN:
                        // intentionally group pen and touch together
                        that.triggerTouchEvent(e);
                        break;
                }
            });
        } else { //non-standard touch events. Wire touch and mouse competing events.
            //detect first touch
            $j(window).on(this.touchDetectionEvent, function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerTouchEvent(e);
            });

            //detect mouse usage
            $j(document).on('mouseover', function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerMouseEvent(e);
            });
        }
    },

    init: function() {
        this.initEnv();
        this.wirePointerDetection();
    }
};

/**
 * This class manages the main navigation and supports infinite nested
 * menus which support touch, mouse click, and hover correctly.
 *
 * The following is the expected behavior:
 *
 * - Hover with an actual mouse should expand the menu (at any level of nesting)
 * - Click with an actual mouse will follow the link, regardless of any children
 * - Touch will follow links without children, and toggle submenus of links with children
 *
 * Caveats:
 * - According to Mozilla's documentation (https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Touch_events),
 *   Firefox has disabled Apple-style touch events on desktop, so desktop devices using Firefox will not support
 *   the desired touch behavior.
 */
var MenuManager = {
    // These variables are used to detect incorrect touch / mouse event order
    mouseEnterEventObserved: false,
    touchEventOrderIncorrect: false,
    cancelNextTouch: false,

    /**
     * This class manages touch scroll detection
     */
    TouchScroll: {
        /**
         * Touch which moves the screen vertically more than
         * this many pixels will be considered a scroll.
         */
        TOUCH_SCROLL_THRESHOLD: 20,

        touchStartPosition: null,

        /**
         * Note scroll position so that scroll action can be detected later.
         * Should probably be called on touchstart (or similar) event.
         */
        reset: function() {
            this.touchStartPosition = $j(window).scrollTop();
        },

        /**
         * Determines if touch was actually a scroll. Should probably be checked
         * on touchend (or similar) event.
         * @returns {boolean}
         */
        shouldCancelTouch: function() {
            if(this.touchStartPosition == null) {
                return false;
            }

            var scroll = $j(window).scrollTop() - this.touchStartPosition;
            return Math.abs(scroll) > this.TOUCH_SCROLL_THRESHOLD;
        }
    },

    /**
     * Determines if small screen behavior should be used.
     *
     * @returns {boolean}
     */
    useSmallScreenBehavior: function() {
        return Modernizr.mq("screen and (max-width:" + bp.medium + "px)");
    },

    /**
     * Toggles a given menu item's visibility.
     * On large screens, also closes sibling and children of sibling menus.
     *
     * @param target
     */
    toggleMenuVisibility: function(target) {
        var link = $j(target);
        var li = link.closest('li');

        if(!this.useSmallScreenBehavior()) {
            // remove menu-active from siblings and children of siblings
            li.siblings()
              .removeClass('menu-active')
              .find('li')
              .removeClass('menu-active');
            //remove menu-active from children
            li.find('li.menu-active').removeClass('menu-active');
        }

        //toggle current item's active state
        li.toggleClass('menu-active');
    },

    // --------------------------------------------
    // Initialization methods
    //

    /**
     * Initialize MenuManager and wire all required events.
     * Should only be called once.
     *
     */
    init: function() {
        this.wirePointerEvents();
    },

    /**
     * This method observes an absurd number of events
     * depending on the capabilities of the current browser
     * to implement expected header navigation functionality.
     *
     * The goal is to separate interactions into four buckets:
     * - pointer enter using an actual mouse
     * - pointer leave using an actual mouse
     * - pointer down using an actual mouse
     * - pointer down using touch
     *
     * Browsers supporting PointerEvent events will use these
     * to differentiate pointer types.
     *
     * Browsers supporting Apple-style will use those events
     * along with mouseenter / mouseleave to emulate pointer events.
     */
    wirePointerEvents: function() {
        var that = this;
        var pointerTarget = $j('#primary-menu a.has-children');
        var hoverTarget = $j('#primary-menu li');

        if(PointerManager.getPointerEventsSupported()) {
            // pointer events supported, so observe those type of events

            var enterEvent = window.navigator.pointerEnabled ? 'pointerenter' : 'mouseenter';
            var leaveEvent = window.navigator.pointerEnabled ? 'pointerleave' : 'mouseleave';
            var fullPointerSupport = window.navigator.pointerEnabled;

            hoverTarget.on(enterEvent, function(e) {
                if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                    || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                    if(fullPointerSupport) {
                        that.mouseEnterAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.mouseEnterAction(e, this);
                    }
                }
            }).on(leaveEvent, function(e) {
                if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                    || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                    if(fullPointerSupport) {
                        that.mouseLeaveAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.mouseLeaveAction(e, this);
                    }
                }
            });

            if(!fullPointerSupport) {
                //click event doesn't have pointer type on it.
                //observe MSPointerDown to set pointer type for click to find later

                pointerTarget.on('MSPointerDown', function(e) {
                    $j(this).data('pointer-type', e.originalEvent.pointerType);
                });
            }

            pointerTarget.on('click', function(e) {
                var pointerType = fullPointerSupport ? e.originalEvent.pointerType : $j(this).data('pointer-type');

                if(pointerType === undefined || pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {
                    that.mouseClickAction(e, this);
                } else {
                    if(fullPointerSupport) {
                        that.touchAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.touchAction(e, this);
                    }
                }

                $j(this).removeData('pointer-type'); // clear pointer type hint from target, if any
            });
        } else {
            //pointer events not supported, use Apple-style events to simulate

            hoverTarget.on('mouseenter', function(e) {
                // Touch events should cancel this event if a touch pointer is used.
                // Record that this method has fired so that erroneous following
                // touch events (if any) can respond accordingly.
                that.mouseEnterEventObserved = true;
                that.cancelNextTouch = true;

                that.mouseEnterAction(e, this);
            }).on('mouseleave', function(e) {
                that.mouseLeaveAction(e, this);
            });

            $j(window).on('touchstart', function(e) {
                if(that.mouseEnterEventObserved) {
                    // If mouse enter observed before touch, then device touch
                    // event order is incorrect.
                    that.touchEventOrderIncorrect = true;
                    that.mouseEnterEventObserved = false; // Reset test
                }

                // Reset TouchScroll in order to detect scroll later.
                that.TouchScroll.reset();
            });

            pointerTarget.on('touchend', function(e) {
                $j(this).data('was-touch', true); // Note that element was invoked by touch pointer

                e.preventDefault(); // Prevent mouse compatibility events from firing where possible

                if(that.TouchScroll.shouldCancelTouch()) {
                    return; // Touch was a scroll -- don't do anything else
                }

                if(that.touchEventOrderIncorrect) {
                    that.PartialTouchEventsSupport.touchAction(e, this);
                } else {
                    that.touchAction(e, this);
                }
            }).on('click', function(e) {
                if($j(this).data('was-touch')) { // Event invoked after touch
                    e.preventDefault(); // Prevent following link
                    return; // Prevent other behavior
                }

                that.mouseClickAction(e, this);
            });
        }
    },

     // --------------------------------------------
     // Behavior "buckets"
     //

    /**
     * Browsers with incomplete PointerEvent support (such as IE 10)
     * require special event management. This collection of methods
     * accommodate such browsers.
     */
    PartialPointerEventsSupport: {
        /**
         * Without proper pointerenter / pointerleave / click pointerType support,
         * we have to use mouseenter events. These end up triggering
         * lots of mouseleave events that can be misleading.
         *
         * Each touch mouseenter and click event that ends up triggering
         * an undesired mouseleave increments this lock variable.
         *
         * Mouseleave events are cancelled if this variable is > 0,
         * and then the variable is decremented regardless.
         */
        mouseleaveLock: 0,

        /**
         * Handles mouse enter behavior, but if using touch,
         * toggle menus in the absence of full PointerEvent support.
         *
         * @param event
         * @param target
         */
        mouseEnterAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.mouseEnterAction(event, target);
                return;
            }

            event.stopPropagation();

            var jtarget = $j(target);
            if(!jtarget.hasClass('level0')) {
                this.mouseleaveLock = jtarget.parents('li').length + 1;
            }

            MenuManager.toggleMenuVisibility(target);
        },

        /**
         * Handles mouse leave behaivor, but obeys the mouseleaveLock
         * to allow undesired mouseleave events to be cancelled.
         *
         * @param event
         * @param target
         */
        mouseLeaveAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.mouseLeaveAction(event, target);
                return;
            }

            if(this.mouseleaveLock > 0) {
                this.mouseleaveLock--;
                return; // suppress duplicate mouseleave event after touch
            }

            $j(target).removeClass('menu-active'); //hide all menus
        },

        /**
         * Does no work on its own, but increments mouseleaveLock
         * to prevent following undesireable mouseleave events.
         *
         * @param event
         * @param target
         */
        touchAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.touchAction(event, target);
                return;
            }
            event.preventDefault(); // prevent following link
            this.mouseleaveLock++;
        }
    },

    /**
     * Browsers with incomplete Apple-style touch event support
     * (such as the legacy Android browser) sometimes fire
     * touch events out of order. In particular, mouseenter may
     * fire before the touch events. This collection of methods
     * accommodate such browsers.
     */
    PartialTouchEventsSupport: {
        /**
         * Toggles visibility of menu, unless suppressed by previous
         * out of order mouseenter event.
         *
         * @param event
         * @param target
         */
        touchAction: function(event, target) {
            if(MenuManager.cancelNextTouch) {
                // Mouseenter has already manipulated the menu.
                // Suppress this undesired touch event.
                MenuManager.cancelNextTouch = false;
                return;
            }

            MenuManager.toggleMenuVisibility(target);
        }
    },

    /**
     * On large screens, show menu.
     * On small screens, do nothing.
     *
     * @param event
     * @param target
     */
    mouseEnterAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            return; // don't do mouse enter functionality on smaller screens
        }

        $j(target).addClass('menu-active'); //show current menu
    },

    /**
     * On large screens, hide menu.
     * On small screens, do nothing.
     *
     * @param event
     * @param target
     */
    mouseLeaveAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            return; // don't do mouse leave functionality on smaller screens
        }

        $j(target).removeClass('menu-active'); //hide all menus
    },

    /**
     * On large screens, don't interfere so that browser will follow link.
     * On small screens, toggle menu visibility.
     *
     * @param event
     * @param target
     */
    mouseClickAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            event.preventDefault(); //don't follow link
            this.toggleMenuVisibility(target); //instead, toggle visibility
        }
    },

    /**
     * Toggle menu visibility, and prevent event default to avoid
     * undesired, duplicate, synthetic mouse events.
     *
     * @param event
     * @param target
     */
    touchAction: function(event, target) {
        this.toggleMenuVisibility(target);

        event.preventDefault();
    }
};

// ==============================================
// jQuery Init
// ==============================================

// Use $j(document).ready() because Magento executes Prototype inline
$j(document).ready(function () {

    // ==============================================
    // Shared Vars
    // ==============================================

    // Document
    var w = $j(window);
    var d = $j(document);
    var body = $j('body');

    Modernizr.addTest('ios', function () {
        return navigator.userAgent.match(/(iPad|iPhone|iPod)/g);
    });

    //initialize pointer abstraction manager
    PointerManager.init();

    /* Wishlist Toggle Class */

    $j(".change").click(function (e) {
        $j( this ).toggleClass('active');
        e.stopPropagation();
    });

    $j(document).click(function (e) {
        if (! $j(e.target).hasClass('.change')) $j(".change").removeClass('active');
    });


    // ==============================================
    // Header Menus
    // ==============================================

    // initialize menu
    MenuManager.init();

    // Prevent sub menus from spilling out of the window.
    function preventMenuSpill() {
        var windowWidth = $j(window).width();
        $j('ul.level0').each(function(){
            var ul = $j(this);
            //Show it long enough to get info, then hide it.
            ul.addClass('position-test');
            ul.removeClass('spill');
            var width = ul.outerWidth();
            var offset = ul.offset().left;
            ul.removeClass('position-test');
            //Add the spill class if it will spill off the page.
            if ((offset + width) > windowWidth) {
                    ul.addClass('spill');
            }
        });
    }
    preventMenuSpill();
    $j(window).on('delayed-resize', preventMenuSpill);

    // ==============================================
    // Enquire JS
    // ==============================================

    enquire.register('screen and (min-width: ' + (bp.medium + 1) + 'px)', {
        match: function () {
            $j('.menu-active').removeClass('menu-active');
            $j('.sub-menu-active').removeClass('sub-menu-active');
            $j('.skip-active').removeClass('skip-active');
        },
        unmatch: function () {
            $j('.menu-active').removeClass('menu-active');
            $j('.sub-menu-active').removeClass('sub-menu-active');
            $j('.skip-active').removeClass('skip-active');
        }
    });

    // ==============================================
    // UI Pattern - Media Switcher
    // ==============================================

    // Used to swap primary product photo from thumbnails.

    var mediaListLinks = $j('.media-list').find('a');
    var mediaPrimaryImage = $j('.primary-image').find('img');

    if (mediaListLinks.length) {
        mediaListLinks.on('click', function (e) {
            e.preventDefault();
            var self = $j(this);
            mediaPrimaryImage.attr('src', self.attr('href'));
        });
    }

    // ==============================================
    // UI Pattern - ToggleSingle
    // ==============================================

    // Use this plugin to toggle the visibility of content based on a toggle link/element.
    // This pattern differs from the accordion functionality in the Toggle pattern in that each toggle group acts
    // independently of the others. It is named so as not to be confused with the Toggle pattern below
    //
    // This plugin requires a specific markup structure. The plugin expects a set of elements that it
    // will use as the toggle link. It then hides all immediately following siblings and toggles the sibling's
    // visibility when the toggle link is clicked.
    //
    // Example markup:
    // <div class="block">
    //     <div class="block-title">Trigger</div>
    //     <div class="block-content">Content that should show when </div>
    // </div>
    //
    // JS: jQuery('.block-title').toggleSingle();
    //
    // Options:
    //     destruct: defaults to false, but if true, the plugin will remove itself, display content, and remove event handlers


    jQuery.fn.toggleSingle = function (options) {

        // passing destruct: true allows
        var settings = $j.extend({
                destruct: false
        }, options);

        return this.each(function () {
            if (!settings.destruct) {
                $j(this).on('click', function () {
                    $j(this).toggleClass('active').next().toggleClass('no-display');
                });
                // Hide the content
                $j(this).next().addClass('no-display');
            } else {
                // Remove event handler so that the toggle link can no longer be used
                $j(this).off('click');
                // Remove all classes that were added by this plugin
                $j(this).removeClass('active').next().removeClass('no-display');
            }
        });
    }

    // ==============================================
    // UI Pattern - Toggle Content (tabs and accordions in one setup)
    // ==============================================

    $j('.toggle-content').each(function () {
        var wrapper = jQuery(this);

        var hasTabs = wrapper.hasClass('tabs');
        var hasAccordion = wrapper.hasClass('accordion');
        var startOpen = wrapper.hasClass('open');

        var dl = wrapper.children('dl:first');
        var dts = dl.children('dt');
        var panes = dl.children('dd');
        var groups = new Array(dts, panes);

        //Create a ul for tabs if necessary.
        if (hasTabs) {
            var ul = jQuery('<ul class="toggle-tabs"></ul>');
            dts.each(function () {
                var dt = jQuery(this);
                var li = jQuery('<li></li>');
                li.html(dt.html());
                ul.append(li);
            });
            ul.insertBefore(dl);
            var lis = ul.children();
            groups.push(lis);
        }

        //Add "last" classes.
        var i;
        for (i = 0; i < groups.length; i++) {
            groups[i].filter(':last').addClass('last');
        }

        function toggleClasses(clickedItem, group) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }

        //Toggle on tab (dt) click.
        dts.on('click', function (e) {
            //They clicked the current dt to close it. Restore the wrapper to unclicked state.
            if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                wrapper.removeClass('accordion-open');
            } else {
                //They're clicking something new. Reflect the explicit user interaction.
                wrapper.addClass('accordion-open');
            }
            toggleClasses(jQuery(this), dts);
        });

        //Toggle on tab (li) click.
        if (hasTabs) {
            lis.on('click', function (e) {
                toggleClasses(jQuery(this), lis);
            });
            //Open the first tab.
            lis.eq(0).trigger('click');
        }

        //Open the first accordion if desired.
        if (startOpen) {
           dts.eq(0).trigger('click');
        }

    });


    // ==============================================
    // Layered Navigation Block
    // ==============================================

    // On product list pages, we want to show the layered nav/category menu immediately above the product list.
    // While it would make more sense to just move the .block-layered-nav block rather than .col-left-first
    // (since other blocks can be inserted into left_first), it creates simpler code to move the entire
    // .col-left-first block, so that is the approach we're taking
    if ($j('.col-left-first > .block').length && $j('.category-products').length) {
        enquire.register('screen and (max-width: ' + bp.medium + 'px)', {
            match: function () {
                $j('.col-left-first').insertBefore($j('.category-products'));
            },
            unmatch: function () {
                // Move layered nav back to left column
                $j('.col-left-first').insertBefore($j('.col-main'));
            }
        });
    }

    // ==============================================
    // 3 column layout
    // ==============================================

    // On viewports smaller than 1000px, move the right column into the left column
    if ($j('.main-container.col3-layout').length > 0) {
        enquire.register('screen and (max-width: 1000px)', {
            match: function () {
                var rightColumn = $j('.col-right');
                var colWrapper = $j('.col-wrapper');
                rightColumn.appendTo(colWrapper);
            },
            unmatch: function () {
                var rightColumn = $j('.col-right');
                var main = $j('.main');
                rightColumn.appendTo(main);
            }
        });
    }


    // ==============================================
    // Block collapsing (on smaller viewports)
    // ==============================================

    enquire.register('(max-width: ' + bp.medium + 'px)', {
        setup: function () {
            this.toggleElements = $j(
                // This selects the menu on the My Account and CMS pages
                '.col-left-first .block:not(.block-layered-nav) .block-title, ' +
                '.col-left-first .block-layered-nav .block-subtitle--filter, ' +
                '.sidebar:not(.col-left-first) .block .block-title'
            );
        },
        match: function () {
            this.toggleElements.toggleSingle();
        },
        unmatch: function () {
            this.toggleElements.toggleSingle({destruct: true});
        }
    });


    // ==============================================
    // OPC - Progress Block
    // ==============================================

    if ($j('body.checkout-onepage-index').length) {
        enquire.register('(max-width: ' + bp.large + 'px)', {
            match: function () {
                $j('#checkout-step-review').prepend($j('#checkout-progress-wrapper'));
            },
            unmatch: function () {
                $j('.col-right').prepend($j('#checkout-progress-wrapper'));
            }
        });
    }


    // ==============================================
    // Checkout Cart - events
    // ==============================================

    if ($j('body.checkout-cart-index').length) {
        $j('input[name^="cart"]').focus(function () {
            $j(this).siblings('button').fadeIn();
        });
    }


    // ==============================================
    // Gift Registry Styles
    // ==============================================

    if ($j('.a-left').length) {
        enquire.register('(max-width: ' + bp.large + 'px)', {
            match: function () {
                $j('.gift-info').each(function() {
                  $j(this).next('td').children('textarea').appendTo(this).children();
                })
            },
            unmatch: function () {
                $j('.left-note').each(function() {
                    $j(this).prev('td').children('textarea').appendTo(this).children();
                })
            }
        });
    }

    if ($j('.a-left').length) {
        enquire.register('(max-width: ' + bp.large + 'px)', {
            match: function () {
                $j('.gift-info').each(function() {
                    $j(this).next('td').children('textarea').appendTo(this).children();
                })
            },
            unmatch: function () {
                $j('.left-note').each(function() {
                    $j(this).prev('td').children('textarea').appendTo(this).children();
                })
            }
        });
    }


    // ==============================================
    // Product Listing - Align action buttons/links
    // ==============================================

    // Since the number of columns per grid will vary based on the viewport size, the only way to align the action
    // buttons/links is via JS

    if ($j('.products-grid').length) {
        var alignProductGridActions = function () {
            // Loop through each product grid on the page
            $j('.products-grid').each(function(){
                var gridRows = []; // This will store an array per row
                var tempRow = [];
                productGridElements = $j(this).children('li');
                productGridElements.each(function (index) {
                    // The JS ought to be agnostic of the specific CSS breakpoints, so we are dynamically checking to find
                    // each row by grouping all cells (eg, li elements) up until we find an element that is cleared.
                    // We are ignoring the first cell since it will always be cleared.
                    if ($j(this).css('clear') != 'none' && index != 0) {
                        gridRows.push(tempRow); // Add the previous set of rows to the main array
                        tempRow = []; // Reset the array since we're on a new row
                    }
                    tempRow.push(this);

                    // The last row will not contain any cells that clear that row, so we check to see if this is the last cell
                    // in the grid, and if so, we add its row to the array
                    if (productGridElements.length == index + 1) {
                        gridRows.push(tempRow);
                    }
                });

                $j.each(gridRows, function () {
                    var tallestProductInfo = 0;
                    $j.each(this, function () {
                        // Since this function is called every time the page is resized, we need to remove the min-height
                        // and bottom-padding so each cell can return to its natural size before being measured.
                        $j(this).find('.product-name').css({
                            'height': ''
                        });

                        // We are checking the height of .product-info (rather than the entire li), because the images
                        // will not be loaded when this JS is run.
                        var productNameHeight = $j(this).find('.product-name').height();
                        // Space above .actions element
                        var actionSpacing = 10;

                        // Add height of two elements. This is necessary since .actions is absolutely positioned and won't
                        // be included in the height of .product-info
                        var totalHeight = productNameHeight + actionSpacing;
                        if (totalHeight > tallestProductInfo) {
                            tallestProductInfo = totalHeight;
                        }

                    });
                    // Set the height of all .product-info elements in a row to the tallest height
                    $j.each(this, function () {
                        $j(this).find('.product-name').css('height', tallestProductInfo);
                    });
                });
            });
        }
        alignProductGridActions();

        // Since the height of each cell and the number of columns per page may change when the page is resized, we are
        // going to run the alignment function each time the page is resized.
        $j(window).on('delayed-resize', function (e, resizeEvent) {
            alignProductGridActions();
        });
    }

    // ==============================================
    // Generic, efficient window resize handler
    // ==============================================

    // Using setTimeout since Web-Kit and some other browsers call the resize function constantly upon window resizing.
    var resizeTimer;
    $j(window).resize(function (e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            $j(window).trigger('delayed-resize', e);
        }, 250);
    });
});


// ==============================================
// PDP - needs to be available outside document.ready scope
// ==============================================
var ProductMediaManager = {
    imageWrapper: null,

    lightBox: function() {
        $j('.open-lightbox').magnificPopup({
          type: 'image',
          mainClass: 'mfp-no-margins mfp-with-zoom',
          image: {
            verticalFit: true
          }
        });
    },

    swapImage: function(targetImage) {
        targetImage = $j(targetImage);
        targetImage.addClass('gallery-image');

        var imageGallery = $j('.product-image-gallery');

        if(targetImage[0].complete) { //image already loaded -- swap immediately

            imageGallery.find('.gallery-image').removeClass('visible');

            //move target image to correct place, in case it's necessary
            imageGallery.append(targetImage);

            //reveal new image
            targetImage.addClass('visible');

        } else { //need to wait for image to load
            imageGallery.addClass('loading'); //add spinner
            imageGallery.append(targetImage); //move target image to correct place, in case it's necessary
            
            //wait until image is loaded
            imagesLoaded(targetImage, function() {
                imageGallery.removeClass('loading');//remove spinner
                imageGallery.find('.gallery-image').removeClass('visible');//hide old image
                targetImage.addClass('visible'); //reveal new image
            });
            
        }
    },

    wireThumbnails: function() {
        // Trigger image change event on thumbnail click
        $j('.product-image-thumbs .thumb-link').click(function(e) {
            e.preventDefault();
            var jlink = $j(this);
            var target = $j('#image-' + jlink.data('image-index'));
            var imageSrc = target.attr('src');

            $j('.product-image-thumbs .thumb-link').removeClass('active');
            jlink.addClass('active');


            $j('.open-lightbox').attr('href', imageSrc).find('img').attr('src', imageSrc);

            ProductMediaManager.swapImage(target);
        });
    },

    init: function() {

        $j('.product-image-thumbs .thumb-link').first().addClass('active');

        ProductMediaManager.imageWrapper = $j('.product-img-box');
        ProductMediaManager.wireThumbnails();
        $j(document).trigger('product-media-loaded', ProductMediaManager);
        
        if( $j().magnificPopup ) {
            ProductMediaManager.lightBox();
        }

    }
};

// Sock Bin Functions
var SockBin = {

    /**
     * Initialize all functions.
     */
    init: function() {
        SockBin.stickyHeader();
        SockBin.removeSticky();
        SockBin.showHideTabs();
        SockBin.filterCat();
        SockBin.moreLink();
        SockBin.moveBlocks();
        // SockBin.qty();
        SockBin.select();
        SockBin.cart();
        SockBin.socialPopup();
        SockBin.menu();
        SockBin.search();
        SockBin.carouselInit();
        SockBin.tooltip();
    },

    /**
     * Add sticky header.
     */
    stickyHeader: function() {

        if ( $j('#header').hasClass('no-sticky') ) {
            return;
        }

        if ( $j('.wrapper').height() < 1000 ) {
            return;
        }

        var headerOffset = 200;

        if ( $j(window).scrollTop() > headerOffset && $j(window).width() > bp.large ) {
            $j('#header').addClass('sticky-header');
            $j('body').addClass('has-sticky-header');
        } else {
            $j('#header').removeClass('sticky-header');
            $j('body').removeClass('has-sticky-header');
        }
    },

    /**
     * Remove sticky header on small window.
     */
    removeSticky: function() {

        if ( $j('#header').hasClass('no-sticky') ) {
            return;
        }

        enquire.register('screen and (max-width: 1170px)', {
            match: function () {
                $j('#header').removeClass('sticky-header').addClass('no-sticky');
                $j('body').removeClass('has-sticky-header');
            },
            unmatch: function () {
                $j('#header').removeClass('no-sticky');
            }
        });
    },

    /**
     * Tabs - payment method on firecheckout page.
     */
    showHideTabs: function() {
        $j('.category-tabs li a, .checkout__payment-method li > a ').on( 'click', function(e) {
            e.preventDefault();
            
            var tab_id = $j(this).attr('href');

            $j('.category-tabs li, .checkout__payment-method li ').removeClass('active');
            $j('.tab, .checkout__payment').removeClass('active');

            $j(this).parent().addClass('active');
            $j(tab_id).addClass('active');
        });
    },

    /**
     * Toggle category filters fields.
     */
    filterCat: function() {
        $j(document).on('click', '.refine-search', function(e) {
            e.preventDefault();
            var button = $j(this);
            if ( button.hasClass('active') ) {
                button.removeClass('active');
                button.text( button.attr('data-open') );
                $j('.product-fields .product-field').hide();
            } else {
                button.addClass('active');
                button.text( button.attr('data-close') );
                $j('.product-fields .product-field').show();
            }
        });
    },

    /**
     * Toggle category filters fields on window resize.
     */
    filterCatShowHide: function() {
        enquire.register('screen and (max-width: ' + bp.medium + 'px)', {
            match: function () {
                if ( $j('.refine-search').hasClass('active') ) {
                    $j('.product-fields .product-field').show();
                } else {
                    $j('.product-fields .product-field').hide();
                }
            },
            unmatch: function () {
                $j('.product-fields .product-field').show();
            }
        });
    },

    /**
     * Read more Link on caegory/search page.
     */
    moreLink: function() {
        var showChar = 100,
            ellipsestext = "...",
            moretext = "Read More",
            lesstext = "Show less";

        $j('.more').each( function() {
            var content = $j(this).html();
     
            if ( content.length > showChar ) {
                var c = content.substr(0, showChar),
                    h = content.substr(showChar, content.length - showChar),
                    html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                $j(this).html(html);
            }
     
        });
     
        $j(".morelink").on( 'click', function(e) {
            e.preventDefault();
            var moreLink = $j(this);
            if ( moreLink.hasClass("less") ) {
                moreLink.removeClass("less");
                moreLink.html(moretext);
            } else {
                moreLink.addClass("less");
                moreLink.html(lesstext);
            }
            moreLink.parent().prev().toggle();
            moreLink.prev().toggle();
        });
    },

    /**
     * Move blocks.
     */
    moveBlocks: function() {
        var $checkoutLinks = $j('.checkout-types.bottom'),
            $productLinks = $j('.product-shipping-info'),
            $socks = $j('.section--more-socks');

        if ( $checkoutLinks.length > 0 ) {
            enquire.register('(max-width: ' + bp.medium + 'px)', {
                match: function () {
                    var $links = $j('.checkout-types.bottom > li.links');
                    $links.appendTo( $checkoutLinks );
                },
                unmatch: function () {
                    var $links = $j('.checkout-types.bottom > li.links');
                    $links.prependTo( $checkoutLinks );
                }
            });
        }

        if ( $productLinks.length > 0 ) {
            enquire.register('(max-width: ' + bp.medium + 'px)', {
                match: function () {
                    var $content = $j('.product-collateral .tab-content .std');
                    $productLinks.appendTo( $content );
                },
                unmatch: function () {
                    var $info = $j('.product-collateral .tab-content .std .product-shipping-info'),
                        $img = $j('.product-img-box');
                    $info.appendTo( $img );
                }
            });
       }

        if ( $socks.length > 0 ) {
            enquire.register('(max-width: 1170px)', {
                match: function () {
                    var $newsletter = $j('.section--more-socks .col.col--3'),
                        $content = $j('.section--about .container .row');
                    $newsletter.appendTo( $content );
                },
                unmatch: function () {
                    var $socks = $j('.section--more-socks .container .row'),
                        $newsletter = $j('.section--about .col.col--3');
                    $newsletter.appendTo( $socks );
                }
            });
        }
    },

    /**
     * Quantity Increment.
     */
    qty: function() {
        // Increment the value
        $j('.qtyplus').on( 'click', function(e) {
            e.preventDefault();
            var field = $j(this).attr('data-field'),
                currentVal = parseInt($j('#'+field).val()),
                pairs = $j(this).closest('.product-info').find('.pairs');
            if (!isNaN(currentVal)) {
                $j('#'+field).val(currentVal + 1);
                if ( currentVal + 1 > 1 ) {
                    pairs.text(currentVal + 1 + ' pairs');
                } else {
                    pairs.text(currentVal + 1 + ' pair');
                }
            } else {
                $j('#'+field).val(0);
                pairs.text(0 + ' pair');
            }
        });
        
        // Decrement the value
        $j('.qtyminus').on( 'click', function(e) {
            e.preventDefault();
            var field = $j(this).attr('data-field'),
                currentVal = parseInt($j('#'+field).val()),
                pairs = $j(this).closest('.product-info').find('.pairs');
            if (!isNaN(currentVal) && currentVal > 0) {
                $j('#'+field).val(currentVal - 1);
                if ( currentVal - 1 > 1 ) {
                    pairs.text(currentVal - 1 + ' pairs');
                } else {
                    pairs.text(currentVal - 1 + ' pair');
                }
            } else {
                $j('#'+field).val(0);
                pairs.text(0 + ' pair');
            }
        });
    },

    /**
     * Select boxes.
     */
    select: function() {

        // -------------------------------------------
        // Add selectric to select boxes
        // -------------------------------------------
        
        $select = $j('.select-fancy select, li:not(.region) .select-checkout select, .v-fix select, .validate-cc-type-select');
        $select.each(function(index, el) {
            $j(this).selectric({
                arrowButtonMarkup: '<b class="button"></b>',
                disableOnMobile: false,
                onChange: function(element) {
                    $j(element).change();
                },
            });         
        });

        // -------------------------------------------
        // Trigger click on select boxes
        // -------------------------------------------
        
        $j.fn.openSelect = function() {
            return this.each( function(index, el) {
                if (document.createEvent) {
                    var event = document.createEvent("MouseEvents");
                    event.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                    el.dispatchEvent(event);
                } else if (element.fireEvent) {
                    el.fireEvent("onmousedown");
                }
            });
        };

        if( navigator.userAgent.toLowerCase().indexOf('firefox') > -1 ) {
            $j('#billing-new-address-form .select-wrapper .btn').on('click', function(e) {
                var select = document.getElementById('billing:region_id');
                ExpandSelect(select);
            });

            $j('#shipping-new-address-form .select-wrapper .btn').on('click', function(e) {
                var select = document.getElementById('shipping:region_id');
                ExpandSelect(select);
            });
        } else {
            $j('.select-wrapper .btn').on('click', function(e) {
                $j(this).next().openSelect();
            });
        }

        // -------------------------------------------
        // Change QTY items on Cart
        // -------------------------------------------
        
        var $qtyEle = $j('#shopping-cart-table .qty-select');
        
        if( $qtyEle.length > 0 ) {
            $qtyEle.each(function(index, el) {
                SockBin.selectCheckout($j(this));
            });

            $qtyEle.on('change', function(e) {
                if( $qtyEle.val() >= $j('#max_item_qty').val() ) {
                    SockBin.selectCheckout($j(this));
                } else {
                    SockBin.selectCheckout($j(this));
                    $j(this).closest('.the-cart-form').submit();
                }
            });

            $j('.product-actions input.qty').on('focus', function() {
                $j(this).next('.button').css('display','inline-block');
            });
        }
    },

    /**
     * Show/hide select box when select product qty
     * on shopping cart.
     * 
     * @param object element
     */
    selectCheckout: function( element ) {
        var value = parseInt( element.val() );
        if( value >= $j('#max_item_qty').val() ) {
            element.closest('.select-fancy').hide();
            element.closest('.product-cart-actions').find('input.qty').val(value).show();
            element.closest('.product-cart-info').find('input.qty').val(value).show();
        } else {
            element.closest('.product-cart-actions').find('input.qty').val(value);
            element.closest('.product-cart-info').find('input.qty').val(value);
        }
    },

    /**
     * Show/Hide coupon form on cart page.
     */
    cart: function() {
        var $cartCoupon = $j('.checkbox--order-coupon');
        if( $cartCoupon.length > 0 ) {
            $cartCoupon.on( 'click', function() {
                if ( $j(this).is(':checked') ) {
                    $j('#discount-coupon-form').show();
                } else {
                    $j('#discount-coupon-form').hide();
                }
            });
        }
    },

    /**
     * Fix social popup height on windows resize.
     */
    socialPopup: function() {
        var height = $j(window).outerHeight();
        if ( height < 600 ) {
            $j('.apptha_header_logo_Div').css('height', height);
        } else {
            $j('.apptha_header_logo_Div').css('height', 'auto');
        }
    },

    /**
     * Trigger primary menu on mobile devices.
     */
    menu: function() {
        $j('.primary-menu-trigger a').on( 'click', function(e) {
            e.preventDefault();
            $j(this).parent().toggleClass('active');
            $j('body').toggleClass('primary-menu-open');
        });
    },

    /**
     * Top search.
     */
    search: function() {
        $j('.mobile-links .search a').on( 'click', function(e) {
            e.preventDefault();
            $j(this).parent().toggleClass('active');
            $j('.top-search').toggleClass('visible');
        });
    },

    /**
     * Related/CrossSell products carousel.
     */
    carouselInit: function() {
        var $carousel = $j(".box-related .products-grid, .crosssell .products-grid");
        if( $carousel.length > 0 ) {

            if( ! $j().owlCarousel ) {
                return true;
            }

            $carousel.owlCarousel({
                autoPlay: 3000,
                dots: true,
                items: 4,
                center: true,
                margin: 5,
                itemsDesktop: [1170, 3],
                itemsDesktopSmall: [770, 1]
            });
        }
    },

    /**
     * Tooltip.
     */
    tooltip: function() {

        if( ! $j().tooltipster ) {
            return true;
        }

        $j('.tooltip').tooltipster({
            theme: 'tooltipster-light'
        });
    }
};

$j(document).ready(function() {
    ProductMediaManager.init();
    SockBin.init();

    $j('.about-slideshow-1 .cycle-slideshow').cycle({
        timeout: 5000,
        speed: 400,
        // autoHeight: "container",
        fx: "scrollHorz",
        pager: ".about-slideshow-1 .cycle-pager"
    });

    $j('.about-slideshow-2 .cycle-slideshow').cycle({
        timeout: 5000,
        speed: 400,
        // autoHeight: "container",
        fx: "scrollHorz",
        pager: ".about-slideshow-2 .cycle-pager"
    });
    
    // Window Scroll
    $j(window).on( 'scroll', function() {
        SockBin.stickyHeader();
    });

    // Window Resize
    $j(window).on( 'resize', function() {
        SockBin.removeSticky();
        SockBin.filterCatShowHide();
        SockBin.moveBlocks();
        SockBin.socialPopup();
    });
});
