<?php

namespace RemiBit\RBCheckout\Controller\Payment;
use \Magento\Sales\Model\Order;

class Check extends \Magento\Framework\App\Action\Action
{

    protected $checkoutSession;
    protected $orderRepository;
    protected $scopeConfig;
    protected $objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $config = $this->scopeConfig->getValue("payment/rbcheckout");
        //change order status to review
        //$order_id = $_POST['x_invoice_num'];
        $order_id = $this->checkoutSession->getLastOrderId();
        $order = $this->orderRepository->get($order_id);
        if ($this->validate($config['trans_signature_key'])) {
            $this->checkoutSession->setSuccessPaid(true);
            $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
            $this->checkoutSession->setLastOrderId($order->getEntityId());
            $orderState = Order::STATE_PROCESSING;
            $order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
            $order->addStatusToHistory(Order::STATE_PROCESSING, __('Order paid. Tx: ' . $_POST['x_trans_id']));
            $order->save();
            $this->messageManager->addSuccess(__('Payment completed successfully .'));
            return $this->_redirect('checkout/onepage/success');
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();

            $this->messageManager->addError(__('Payment failed.'));
            $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
            if ($order) {
                $items = $order->getItemsCollection();
                foreach ($items as $item) {
                    try {
                        $cart->addOrderItem($item);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        if ($this->_objectManager->get('Magento\Checkout\Model\Session')->getUseNotice(true)) {
                            $this->messageManager->addNotice($e->getMessage());
                        } else {
                            $this->messageManager->addError($e->getMessage());
                        }
                        return $resultRedirect->setPath('*/*/history');
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                        return $resultRedirect->setPath('checkout/cart');
                    }
                }
                $cart->save();
                $order->cancel();
                $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CANCELED, __('Payment failed.'));
                $order->save();
            }

            $resultRedirect->setPath('checkout/cart');
            return $resultRedirect;
        }
    }

    function validate($signature_key)
    {
        if (isset($_POST['x_trans_id'])) {
            $hashData = implode('^', [
                $_POST['x_trans_id'],
                $_POST['x_test_request'],
                $_POST['x_response_code'],
                $_POST['x_auth_code'],
                $_POST['x_cvv2_resp_code'],
                $_POST['x_cavv_response'],
                $_POST['x_avs_code'],
                $_POST['x_method'],
                $_POST['x_account_number'],
                $_POST['x_amount'],
                $_POST['x_company'],
                $_POST['x_first_name'],
                $_POST['x_last_name'],
                $_POST['x_address'],
                $_POST['x_city'],
                $_POST['x_state'],
                $_POST['x_zip'],
                $_POST['x_country'],
                $_POST['x_phone'],
                $_POST['x_fax'],
                $_POST['x_email'],
                $_POST['x_ship_to_company'],
                $_POST['x_ship_to_first_name'],
                $_POST['x_ship_to_last_name'],
                $_POST['x_ship_to_address'],
                $_POST['x_ship_to_city'],
                $_POST['x_ship_to_state'],
                $_POST['x_ship_to_zip'],
                $_POST['x_ship_to_country'],
                $_POST['x_invoice_num'],
            ]);
            $digest = strtoupper(HASH_HMAC('sha512', "^" . $hashData . "^", hex2bin($signature_key)));
            if ($_POST['x_response_code'] != '' && (strtoupper($_POST['x_SHA2_Hash']) == $digest)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
