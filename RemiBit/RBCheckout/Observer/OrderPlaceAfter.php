<?php
namespace RemiBit\RBCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class OrderPlaceAfter implements ObserverInterface {

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;


    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(Observer $observer) {

        $order = $observer->getEvent()->getOrder();
        $is_success = $this->checkoutSession->getSuccessPaid();
        $level = 'DEBUG';
        // saved in var/log/debug.log
        $this->logger->log($level,'RemiBit Debug', array('msg'=>'order_placed', 'order_id' => $order->getIncrementId(),  'status' => $order->getStatus(), 'state' => $order->getStatus(), 'is_success' => $is_success));
        if($order->getPayment() && $order->getPayment()->getMethod() === 'rbcheckout' && !$is_success) {
            $order_id = $order->getIncrementId();
            $billing = $order->getBillingAddress()->getData();
            $shipping = $order->getShippingAddress()->getData();
            $config = $this->scopeConfig->getValue("payment/rbcheckout");

            $time_stamp = time();
            $transaction_key = $config['trans_key'];
            $total = (float)$order->getGrandTotal();

            if (function_exists('hash_hmac')) {
                $hash_d = hash_hmac('md5', sprintf('%s^%s^%s^%s^%s',
                    $config['login'],
                    $order_id,
                    $time_stamp,
                    $total,
                    $order->getOrderCurrencyCode()
                ), $transaction_key);
            } else {
                $hash_d = bin2hex(mhash($config['trans_md5'], sprintf('%s^%s^%s^%s^%s',
                    $config['login'],
                    $order_id,
                    $time_stamp,
                    $total,
                    $order->getOrderCurrencyCode()
                ), $transaction_key));
            }

            $params = array(
                'x_login' =>   $config['login'],
                'x_amount' => $total,
                'x_invoice_num' => $order_id,
                'x_relay_response' => 'TRUE',
                'x_fp_sequence' => $order_id,
                'x_fp_hash' => $hash_d,
                'x_show_form' => 'PAYMENT_FORM',
                'x_version' => '1.0',
                'x_type' => 'AUTH_CAPTURE',
                'x_relay_url' => $this->url->getUrl('remibit/payment/response'),
                'x_currency_code' => $order->getOrderCurrencyCode(),
                'x_fp_timestamp' => $time_stamp,
                'x_first_name' => $order->getCustomerFirstname(),
                'x_last_name' => $order->getCustomerLastname(),
                'x_company' => $billing['company'],
                'x_address' => $billing['street'],
                'x_city' => $billing['city'],
                'x_state' =>  $billing['region'],
                'x_zip' => $billing['postcode'],
                'x_country' => $billing['country_id'],
                'x_phone' => $billing['telephone'],
                'x_email' => $order->getCustomerEmail(),
                'x_tax' => '',
                'x_cancel_url' => $this->url->getUrl('remibit/payment/cancel'),
                'x_cancel_url_text' => 'Cancel Payment',
                'x_test_request' => 'FALSE',
                'x_ship_to_first_name' => $shipping['firstname'],
                'x_ship_to_last_name' => $shipping['lastname'],
                'x_ship_to_company' => $shipping['company'],
                'x_ship_to_address' => $shipping['street'],
                'x_ship_to_city' => $shipping['city'],
                'x_ship_to_state' => $shipping['region'],
                'x_ship_to_zip' => $shipping['postcode'],
                'x_ship_to_country' => $shipping['country_id'],
                'x_freight' => ''
            );

            $gateway_url = $config['cgi_url'];
            $this->sendTransactionToGateway($gateway_url, $params);
            exit;
        } else {
            if($is_success) {
                $this->checkoutSession->unsSuccessPaid();
            }
            return $this;
        }
    }

    private function sendTransactionToGateway($url, $parameters)
    {
        $post_string = array();

        foreach ($parameters as $key => $value) {
            $post_string[] = "<input type='hidden' name='$key' value='$value'/>";
        }

        $loading = ' <div style="width: 100%; height: 100%;top: 50%; padding-top: 10px;padding-left: 10px;  left: 50%; transform: translate(40%, 40%)"><div style="width: 150px;height: 150px;border-top: #CC0000 solid 5px; border-radius: 50%;animation: a1 2s linear infinite;position: absolute"></div> </div> <style>*{overflow: hidden;}@keyframes a1 {to{transform: rotate(360deg)}}</style>';

        $html_form = '<form action="' . $url . '" method="post" id="authorize_payment_form">' . implode('', $post_string) . '<input type="submit" id="submit_authorize_payment_form" style="display: none"/>' . $loading . '</form><script>document.getElementById("submit_authorize_payment_form").click();</script>';

        echo $html_form;
        die();
    }
}