/**
 * PlusMinus functionality for +,- buttons in product listing and cart page
 *
 * @type {{tierPricesOptions: {}, isCart: boolean, initPlusMinus: PlusMinusObject.initPlusMinus, bindEvents: PlusMinusObject.bindEvents, calculateMinusQtyPrice: PlusMinusObject.calculateMinusQtyPrice, calculatePlusQtyPrice: PlusMinusObject.calculatePlusQtyPrice, checkNextQtyPrice: PlusMinusObject.checkNextQtyPrice, updateQtyPrice: PlusMinusObject.updateQtyPrice, updateQtyPriceMessage: PlusMinusObject.updateQtyPriceMessage}}
 */
var PlusMinusObject = {

    /**
     * Products tier prices
     */
    tierPricesOptions : {},

    /**
     * If we are in cart it should be true
     */
    isCart : false,

    /**
     * Init PlusMinus functionality
     *
     * @param categoryTierPrices
     * @param isCart
     */
    initPlusMinus : function (categoryTierPrices, isCart) {
        var _this = this;

        if (categoryTierPrices !== undefined) {
            _this.tierPricesOptions = categoryTierPrices;

            if (isCart !== undefined) {
                _this.isCart = isCart;
            }

            _this.bindEvents();
        } else {
            console.log("Invalid tier prices options");
        }
    },

    /**
     * Bind all the events regarding to plus minus buttons
     */
    bindEvents : function() {
        var _this = this;

        jQuery('.qtyminus').on('click', function () {
            _this.calculateMinusQtyPrice(jQuery(this));
        });

        jQuery('.qtyplus').on('click', function () {
            _this.calculatePlusQtyPrice(jQuery(this));
        });
        
        jQuery('.qty-box').on('change', function() {
           _this.updateQtyPrice(jQuery(this));
        });
    },

    /**
     * Calculate qty and price after click on minus button
     *
     * @param elem
     */
    calculateMinusQtyPrice : function(elem) {
        var _this = this;
        var inputQty = elem.next();
        var currentQty = parseInt(inputQty.val());
        var productId = elem.attr('data-field').split('-');
        productId = productId[1];
        var productTiers = _this.tierPricesOptions['plusminus_' + productId];
        var minQty = parseInt(productTiers.minQty);
        var minPrice = parseFloat(productTiers.minPrice);
        var maxQty = parseInt(productTiers.maxQty);
        var maxPrice = parseFloat(productTiers.maxPrice);
        var updatedQty = 0;
        var updatedPrice = 0;

        if (productTiers.prices.length > 0) {
            if (currentQty <= minQty) {
                updatedQty = minQty;
                updatedPrice = minPrice;
            } else {
                if (currentQty > maxQty) {
                    updatedQty = currentQty - minQty;
                    updatedPrice = maxPrice;
                } else {
                    jQuery.each(productTiers.prices, function (index, value) {
                        var qty = parseInt(value.price_qty);

                        if (qty < currentQty) {
                            updatedQty = qty;
                            updatedPrice = parseFloat(value.price);
                        }
                    });
                }
            }
        } else {
            if (currentQty < 2) {
                updatedQty = 1;
            } else {
                updatedQty = currentQty - 1;
            }

            updatedPrice = maxPrice;
        }

        _this.updateQtyPriceMessage(inputQty, updatedQty, updatedPrice);
    },

    /**
     * Calculate qty and price after click on plus button
     *
     * @param elem
     */
    calculatePlusQtyPrice : function(elem) {
        var _this = this;
        var inputQty = elem.prev();
        var currentQty = parseInt(inputQty.val());
        var productId = elem.attr('data-field').split('-');
        productId = productId[1];
        var productTiers = _this.tierPricesOptions['plusminus_' + productId];
        var updatedQty = 0;
        var updatedPrice = 0;
        var minQty = parseInt(productTiers.minQty);
        var maxQty = parseInt(productTiers.maxQty);
        var maxPrice = parseFloat(productTiers.maxPrice);

        if (productTiers.prices.length > 0) {
            if (currentQty >= maxQty) {
                updatedQty = currentQty + minQty;
                updatedPrice = maxPrice;
            } else {
                jQuery.each(productTiers.prices, function (index, value) {
                    var qty = parseInt(value.price_qty);
                    if (qty > currentQty) {
                        updatedQty = qty;
                        updatedPrice = parseFloat(value.price);
                        return false;
                    }
                });
            }
        } else {
            if (currentQty < maxQty) {
                updatedQty = currentQty + 1;
            } else {
                updatedQty = currentQty;
            }

            updatedPrice = maxPrice;
        }

        _this.updateQtyPriceMessage(inputQty, updatedQty, updatedPrice);
    },

    /**
     * Will retrieve next qty when user types some qty not tracked in tier prices
     *
     * @param qtyToCheck
     * @param productTiers
     * @returns {*[]}
     */
    checkNextQtyPrice : function(qtyToCheck, productTiers) {
        var qtyToRet = 0;
        var priceToRet = 0;

        jQuery.each(productTiers.prices, function (index, value) {
            var qtyValue = parseInt(value.price_qty);
            var divResult = qtyToCheck / qtyValue;
            if (
                (divResult > 0 && divResult < 1 && qtyToRet == false)
                || (divResult == 1)
            ) {
                qtyToRet = qtyValue;
                priceToRet = parseFloat(value.price);
            }
        });

        return [qtyToRet, priceToRet];
    },

    /**
     * Logic for recalculate prices an qtys when user types the qty
     *
     * @param inputQty
     */
    updateQtyPrice : function(inputQty) {
        var _this = this;
        var updatedQty = 0;
        var updatedPrice = 0;
        var boxQty = inputQty.val();
        var productId = inputQty.attr('data-field').split('-');
        productId = productId[1];
        var productTiers = _this.tierPricesOptions['plusminus_' + productId];
        var minQty = productTiers.minQty;
        var maxQty = productTiers.maxQty;
        var minPrice = parseFloat(productTiers.minPrice);
        var maxPrice = parseFloat(productTiers.maxPrice);
        var qtyIncrement = minQty;

        if (boxQty > 0) {
            if (productTiers.prices.length > 0) {
                if (boxQty <= minQty) {
                    updatedQty = minQty;
                    updatedPrice = minPrice;
                } else {
                    if (boxQty > maxQty) {
                        var divResult = boxQty % maxQty;

                        if (divResult == 0) {
                            updatedQty = boxQty;
                            updatedPrice = maxPrice;
                        } else {
                            var incrDiv = boxQty / qtyIncrement;
                            updatedQty = Math.ceil(incrDiv) * qtyIncrement;
                            updatedPrice = maxPrice;
                        }
                    } else {
                        var qtyPrice = _this.checkNextQtyPrice(boxQty, productTiers);
                        updatedQty = qtyPrice[0];
                        updatedPrice = qtyPrice[1];
                    }
                }
            } else {
                if (boxQty > maxQty) {
                    updatedQty = maxQty;
                } else {
                    updatedQty = boxQty;
                }

                updatedPrice = maxPrice;
            }

            _this.updateQtyPriceMessage(inputQty, updatedQty, updatedPrice);
        } else {
            _this.updateQtyPriceMessage(inputQty, minQty, minPrice);
        }
    },

    /**
     * Update price and qty messages
     *
     * @param inputQty
     * @param updatedQty
     * @param updatedPrice
     */
    updateQtyPriceMessage : function (inputQty, updatedQty, updatedPrice) {
        var _this = this;

        inputQty.val(updatedQty);

        // Updating qty and price message for product lists
        if (_this.isCart === false) {
            var priceBox = inputQty.parent().parent().parent().prev().find('.minimal-price-link');
            var message = (updatedQty > 1) ? updatedQty + ' pairs' : '1 pair';
            var totalUpdatedPrice = updatedPrice * updatedQty;

            priceBox.find('.label .pairs').text(message);
            priceBox.find('.price').text('$' + totalUpdatedPrice.toFixed(2));
        }
    }
};