<?php

namespace RemiBit\RBCheckout\Controller\Payment;

class Response extends \Magento\Framework\App\Action\Action
{

    protected $scopeConfig;
    protected $url;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->url->getUrl('remibit/payment/check');
        $post_string = [];
        foreach ($_POST as $key => $value) {
            $post_string[] = "<input type='hidden' name='$key' value='$value'/>";
        }

        $loading = ' <div style="width: 100%; height: 100%;top: 50%; padding-top: 10px;padding-left: 10px;  left: 50%; transform: translate(40%, 40%)"><div style="width: 150px;height: 150px;border-top: #CC0000 solid 5px; border-radius: 50%;animation: a1 2s linear infinite;position: absolute"></div> </div> <style>*{overflow: hidden;}@keyframes a1 {to{transform: rotate(360deg)}}</style>';

        $html_form = '<form action="' . $url . '" method="post" id="authorize_payment_form">' . implode('', $post_string) . '<input type="submit" id="submit_authorize_payment_form" style="display: none"/>' . $loading . '</form><script>document.getElementById("submit_authorize_payment_form").click();</script>';

        echo $html_form;
        die();
    }


}