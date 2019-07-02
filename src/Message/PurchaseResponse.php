<?php

namespace Omnipay\AfterPay\Message;

use function array_key_exists;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PurchaseResponse extends Response
{
    protected $script = 'https://portal.sandbox.afterpay.com/afterpay.js';

    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRedirectResponse()
    {
        $output = <<<EOF
<html>
<head>
    <title>Redirecting...</title>
    <script src="%s" async></script>
</head>
<body>
    <script>
    window.onload = function() {
        AfterPay.init(countryCode: "%s");
        AfterPay.redirect({token: "%s"});
    };
    </script>
</body>
</html>
EOF;

        $token = array_key_exists('token',$this->data) ? $this->data['token'] : ' ';
        $countryCode = array_key_exists('countrycode',$this->data) ? $this->data['token'] : 'AU';
        $output = sprintf($output, $this->getScriptUrl(), $countryCode, $token);

        return HttpResponse::create($output);
    }

    /**
     * @return string
     */
    public function getScriptUrl()
    {
        return $this->script;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return isset($this->data->token) ? $this->data->token : null;
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->getToken();
    }
}
