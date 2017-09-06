<?php

/**
 *
 * RoyalPay支付API异常类
 * @author Leijid
 *
 */
class RoyalPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
