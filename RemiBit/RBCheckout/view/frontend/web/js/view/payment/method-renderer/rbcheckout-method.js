/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                // redirectAfterPlaceOrder: false,
                template: 'RemiBit_RBCheckout/payment/rbcheckout'
            },

            // afterPlaceOrder: function () {
            //     var urlRedirect = url.build('remibit/payment');
            //     console.log(window.checkoutConfig);
            //     alert(urlRedirect);
            //     return false;
            // },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },


        });
    }
);
