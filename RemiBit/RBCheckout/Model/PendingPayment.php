<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace RemiBit\RBCheckout\Model;
use Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order Statuses source model
 */
class PendingPayment extends Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = [
        \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
        \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING


    ];
}