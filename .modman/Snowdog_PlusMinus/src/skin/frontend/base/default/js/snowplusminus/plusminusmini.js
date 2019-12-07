/**
 * Plus Minus object for minicart
 *
 * @type {{tierPricesOptions: {}, initPlusMinus: PlusMinusObjectMini.initPlusMinus, bindEvents: PlusMinusObjectMini.bindEvents, calculateMinusQtyPrice: PlusMinusObjectMini.calculateMinusQtyPrice, calculatePlusQtyPrice: PlusMinusObjectMini.calculatePlusQtyPrice, checkNextQtyPrice: PlusMinusObjectMini.checkNextQtyPrice, updateQtyPrice: PlusMinusObjectMini.updateQtyPrice, updateQtyPriceMessage: PlusMinusObjectMini.updateQtyPriceMessage}}
 */
var PlusMinusObjectMini = {

    /**
     * Products tier prices
     */
    tierPricesOptions : {},

    /**
     * Init PlusMinus functionality
     *
     * @param categoryTierPrices
     */
    initPlusMinus : function (categoryTierPrices) {
        var _this = this;

        if (categoryTierPrices !== undefined) {
            _this.tierPricesOptions = categoryTierPrices;

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

        jQuery('.minicart-wrapper .qtyminusmini').on('click', function () {
            _this.calculateMinusQtyPrice(jQuery(this));
        });

        jQuery('.minicart-wrapper .qtyplusmini').on('click', function () {
            _this.calculatePlusQtyPrice(jQuery(this));
        });

        jQuery('.minicart-wrapper .qtymini').on('change', function() {
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

        _this.updateQtyPriceMessage(inputQty, updatedQty);
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
        var minQty = parseInt(productTiers.minQty);
        var maxQty = parseInt(productTiers.maxQty);
        var maxPrice = parseFloat(productTiers.maxPrice);
        var updatedQty = 0;
        var updatedPrice = 0;

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

        _this.updateQtyPriceMessage(inputQty, updatedQty);
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

            _this.updateQtyPriceMessage(inputQty, updatedQty);
        } else {
            _this.updateQtyPriceMessage(inputQty, updatedQty);
        }
    },

    /**
     * Update price and qty messages
     *
     * @param inputQty
     * @param updatedQty
     */
    updateQtyPriceMessage : function (inputQty, updatedQty) {
        var qtyButton = inputQty.parent().find('.quantity-button');

        inputQty.val(updatedQty);
        qtyButton.addClass('visible');
        qtyButton.prop('disabled', false)
    }
};