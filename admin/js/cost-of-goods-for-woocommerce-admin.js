(function (jQuery) {
    jQuery.fn.inputFilter = function (callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function (e) {
            // Convert commas to dots for consistent internal numeric representation
            var inputVal = this.value.replace(',', '.');
            if (callback(inputVal)) {
                if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                    jQuery(this).removeClass("input-error");
                    this.setCustomValidity("");
                }
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                jQuery(this).addClass("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    };

    function calculateProfit() {
        // Replace comma with dot before parsing to float
        var costOfGoods = parseFloat(jQuery('input#cost_of_goods').val().replace(',', '.'));
        var profitField = jQuery('input#profit');
        var regularPrice = parseFloat(jQuery('input#_regular_price').val().replace(',', '.'));
        var rewriteRegularPriceChecked = jQuery('input#rewrite_regular_price').is(':checked');
        var regularPriceVat = rewriteRegularPriceChecked ? parseFloat(jQuery('input#regular_price_vat').val().replace(',', '.')) : NaN;

        if (!isNaN(regularPriceVat) && rewriteRegularPriceChecked) {
            regularPrice = regularPriceVat;
        }

        if (isNaN(costOfGoods) || isNaN(regularPrice)) {
            profitField.val('0');
        } else {
            let profit = parseFloat(regularPrice) - parseFloat(costOfGoods);
            profitField.val(profit.toFixed(2));
        }
    }

    jQuery('input#cost_of_goods, input#regular_price_vat, input#_regular_price, input#rewrite_regular_price').on('keyup keypress blur change', function () {
        jQuery(this).inputFilter(function (value) {
            // Allow dot and comma as decimal points, ensure internal use as dot
            return /^\d*[.,]?\d*$/.test(value);
        }, "Only numeric values are allowed (e.g., 20.50 or 20,50)");

        calculateProfit();
    });

    jQuery('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
        jQuery('.woocommerce_variation').each(function (index) {
            let regularPriceField = jQuery('#variable_regular_price_' + index);
            let costOfGoodsField = jQuery('#cost_of_goods_' + index);
            let profitField = jQuery('#profit_' + index);

            jQuery(costOfGoodsField).on('keyup keypress blur change', function () {
                jQuery(this).inputFilter(function (value) {
                    return /^\d*\.?\d*$/.test(value.replace(',', '.'));
                }, "Only numeric values are allowed (e.g., 20.50)");
            });
        });
    });
}(jQuery));
