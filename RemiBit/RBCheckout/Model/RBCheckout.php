<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace RemiBit\RBCheckout\Model;
/**
 * Pay In Store payment method model
 */
class RBCheckout extends \Magento\Payment\Model\Method\AbstractMethod
{
	/**
	 * Payment code
	 *
	 * @var string
	 */
	protected $_code = 'rbcheckout';
	/**
	 * Availability option
	 *
	 * @var bool
	 */
	protected $_isOffline                   = false;
    protected $_canOrder                    = true;
    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    protected $_canCancelInvoice            = true;
    protected $_canVoid                     = true;
    protected $_canRefundInvoicePartial     = true;
    protected $_canAuthorize                = true;
    protected $_canReviewPayment            = true;
}
