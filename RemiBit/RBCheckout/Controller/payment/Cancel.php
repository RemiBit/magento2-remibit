<?php

namespace RemiBit\RBCheckout\Controller\Payment;

class Cancel extends \Magento\Framework\App\Action\Action
{

    protected $checkoutSession;
    protected $orderRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->messageManager->addError(__('Payment has been cancelled.'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        //change order status to cancel
        $order = $this->orderRepository->get($this->checkoutSession->getLastOrderId());

        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $items = $order->getItemsCollection();
        if ($order) {
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
            $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_CANCELED, __('Canceled by customer.'));
            $order->save();
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkout/cart');
        return $resultRedirect;
        //return $this->resultPageFactory->create();
    }
}