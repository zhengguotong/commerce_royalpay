<?php

/**
 *
 * 接口访问类，包含所有Royal支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交扫码支付为15s，其他均为10s）
 * @author Leijid
 *
 */
class RoyalPayApi
{
    /**
     *
     * 汇率查询，nonce_str、time不需要填入
     * @param RoyalPayExchangeRate $inputObj
     * @param int $timeOut
     * @throws RoyalPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function exchangeRate($inputObj, $timeOut = 10)
    {
        // $partnerCode = RoyalPayConfig::PARTNER_CODE;
        // $url = "https://mpay.royalpay.com.au/api/v1.0/gateway/partners/$partnerCode/exchange_rate";
        // $inputObj->setTime(self::getMillisecond());//时间戳
        // $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        // $inputObj->setSign();
        // $response = self::getJsonCurl($url, $inputObj, $timeOut);
        // $result = RoyalPayResults::init($response);
        // return $result;
    }

    /**
     *
     * QR下单，nonce_str、time不需要填入
     * @param RoyalPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws RoyalPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function qrOrder($inputObj, $timeOut = 15)
    {
        $partnerCode = $inputObj->getParnterCode();
        $orderId = $inputObj->getOrderId();
        $url = "https://mpay.royalpay.com.au/api/v1.0/gateway/partners/". $partnerCode."/orders/" . $orderId;
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);

        $result = RoyalPayResults::init($response);
        return $result;
    }

  
    /**
     *
     * 产生随机字符串，不长于30位
     * @param int $length
     * @return $str 产生的随机字符串
     */
    public static function getNonceStr($length = 30)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 以get方式提交json到对应的接口url
     *
     * @param string $url
     * @param object $inputObj
     * @param int $second url执行超时时间，默认30s
     * @throws RoyalPayException
     */
    private static function getJsonCurl($url, $inputObj, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //如果有配置代理这里就设置代理
       
        $url .= '?' . $inputObj->toQueryParams();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        //GET提交方式
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new RoyalPayException("curl出错，错误码:$error");
        }
    }

    /**
     * 以put方式提交json到对应的接口url
     *
     * @param string $url
     * @param object $inputObj
     * @param int $second url执行超时时间，默认30s
     * @throws RoyalPayException
     */
    private static function putJsonCurl($url, $inputObj, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        $url .= '?' . $inputObj->toQueryParams();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        //PUT提交方式
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inputObj->toBodyParams());
        //运行curl
        $data = curl_exec($ch);


        if ($error = curl_error($ch)) {
            curl_close($ch);
            error_log(print_r($error));
            return FALSE;
        }

        curl_close($ch);
     
        return $data;
    }

    /**
     *
     * QR支付跳转，nonce_str、time不需要填入
     * @param string $pay_url
     * @param RoyalPayRedirect $inputObj
     * @throws RoyalPayException
     * @return $pay_url 成功时返回，其他抛异常
     */
     public static function getQRRedirectUrl($pay_url, $inputObj)
     {
         $inputObj->setTime(self::getMillisecond());//时间戳
         $inputObj->setNonceStr(self::getNonceStr());//随机字符串
         $inputObj->setSign();
         $pay_url .= '?' . $inputObj->toQueryParams();
         return $pay_url;
     }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }
}

