<?php

/**
 *
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出json格式的参数、从json读取数据对象等
 * @author Leijid
 *
 */
class RoyalPayDataBase
{
    protected $pathValues = array();

    protected $queryValues = array();

    protected $bodyValues = array();

    protected $partner_code = '';
    protected $credential_code = '';


    public function setPartnerInfo($code, $credential)
    {
        $this->partner_code  = $code;
        $this->credential_code = $credential;
    }

    public function getParnterCode()
    {
        return $this->partner_code;
    }

    public function getCredentialCode()
    {
        $this->credential_code;
    }

    /**
     * 设置随机字符串，不长于30位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->queryValues['nonce_str'] = $value;
    }

    /**
     * 获取随机字符串，不长于30位。推荐随机数生成算法的值
     * @return 值
     **/
    public function getNonceStr()
    {
        return $this->queryValues['nonce_str'];
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function isNonceStrSet()
    {
        return array_key_exists('nonce_str', $this->queryValues);
    }

    /**
     * 设置时间戳
     * @param long $value
     **/
    public function setTime($value)
    {
        $this->queryValues['time'] = $value;
    }

    /**
     * 获取时间戳
     * @return 值
     **/
    public function getTime()
    {
        return $this->queryValues['time'];
    }

    /**
     * 判断时间戳是否存在
     * @return true 或 false
     **/
    public function isTimeSet()
    {
        return array_key_exists('time', $this->queryValues);
    }

    /**
     * 设置签名，详见签名生成算法
     * @param string $value
     **/
    public function setSign()
    {
        $sign = $this->makeSign();
        $this->queryValues['sign'] = $sign;
        return $sign;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return 值
     **/
    public function getSign()
    {
        return $this->queryValues['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return true 或 false
     **/
    public function isSignSet()
    {
        return array_key_exists('sign', $this->queryValues);
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function toQueryParams()
    {
        $buff = "";
        foreach ($this->queryValues as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 格式化参数格式化成json参数
     */
    public function toBodyParams()
    {
        return json_encode($this->bodyValues);
    }

    /**
     * 格式化签名参数
     */
    public function toSignParams()
    {
        $buff = "";
        $buff .= $this->partner_code. '&' . $this->getTime() . '&' . $this->getNonceStr() . "&" . $this->credential_code;
        error_log($buff);
        return $buff;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用setSign方法赋值
     */
    public function makeSign()
    {
        //签名步骤一：构造签名参数
        $string = $this->toSignParams();
        //签名步骤三：SHA256加密
        $string = hash('sha256', utf8_encode($string));
        //签名步骤四：所有字符转为小写
        $result = strtolower($string);
        return $result;
    }

    /**
     * 获取设置的path参数值
     */
    public function getPathValues()
    {
        return $this->pathValues;
    }

    /**
     * 获取设置的query参数值
     */
    public function getQueryValues()
    {
        return $this->queryValues;
    }

    /**
     * 获取设置的body参数值
     */
    public function getBodyValues()
    {
        return $this->bodyValues;
    }
}

/**
 *
 * 接口调用结果类
 * @author Leijid
 *
 */
class RoyalPayResults extends RoyalPayDataBase
{

    /**
     *
     * 使用数组初始化
     * @param array $array
     */
    public function fromArray($array)
    {
        $this->bodyValues = json_decode($array, true);
    }

    /**
     * 将json转为array
     * @param string $json
     * @throws RoyalPayException
     *
     * 返回信息:
     * return_code          return_msg
     * --------------------------------------
     * ORDER_NOT_EXIST      订单不存在
     * ORDER_MISMATCH       订单号与商户不匹配
     * SYSTEMERROR          系统内部异常
     * INVALID_SHORT_ID     商户编码不合法或没有对应商户
     * SIGN_TIMEOUT         签名超时，time字段与服务器时间相差超过5分钟
     * INVALID_SIGN         签名错误
     * PARAM_INVALID        参数不符合要求，具体细节可参考return_msg字段
     * NOT_PERMITTED        未开通网关支付权限
     * --------------------------------------
     */
    public static function init($array)
    {
        $obj = new self();
        $obj->fromArray($array);
        return $obj->getBodyValues();
    }
}

/**
 *
 * 汇率查询输入对象
 * @author Leijid
 *
 */
class RoyalPayExchangeRate extends RoyalPayDataBase
{

}

/**
 * 统一下单对象
 * @author Leijid
 */
class RoyalPayUnifiedOrder extends RoyalPayDataBase
{
    /**
     * 设置商户支付订单号，同一商户唯一
     * @param string $value
     **/
    public function setOrderId($value)
    {
        $this->pathValues['order_id'] = $value;
    }

    /**
     * 获取商户支付订单号
     * @return 值
     **/
    public function getOrderId()
    {
        return $this->pathValues['order_id'];
    }

    /**
     * 判断商户支付订单号是否存在
     * @return true 或 false
     **/
    public function isOrderIdSet()
    {
        return array_key_exists('order_id', $this->pathValues);
    }

    /**
     * 设置订单标题
     * @param string $value
     **/
    public function setDescription($value)
    {
        $this->bodyValues['description'] = $value;
    }

    /**
     * 获取订单标题
     * @return 值
     **/
    public function getDescription()
    {
        return $this->bodyValues['description'];
    }

    /**
     * 判断订单标题是否存在
     * @return true 或 false
     **/
    public function isDescriptionSet()
    {
        return array_key_exists('description', $this->bodyValues);
    }

    /**
     * 设置金额，单位为货币最小单位
     * @param string $value
     **/
    public function setPrice($value)
    {
        $this->bodyValues['price'] = $value;
    }

    /**
     * 获取金额，单位为货币最小单位
     * @return 值
     **/
    public function getPrice()
    {
        return $this->bodyValues['price'];
    }

    /**
     * 判断金额是否存在
     * @return true 或 false
     **/
    public function isPriceSet()
    {
        return array_key_exists('price', $this->bodyValues);
    }

    /**
     * 设置币种代码
     * 默认值: AUD
     * 允许值: AUD, CNY
     * @param string $value
     **/
    public function setCurrency($value)
    {
        $this->bodyValues['currency'] = $value;
    }

    /**
     * 获取币种代码
     * 默认值: AUD
     * 允许值: AUD, CNY
     * @return 值
     **/
    public function getCurrency()
    {
        return $this->bodyValues['currency'];
    }

    /**
     * 判断币种代码是否存在
     * @return true 或 false
     **/
    public function isCurrencySet()
    {
        return array_key_exists('currency', $this->bodyValues);
    }

    /**
     * 设置支付通知url,不填则不会推送支付通知
     * @param string $value
     **/
    public function setNotifyUrl($value)
    {
        $this->bodyValues['notify_url'] = $value;
    }

    /**
     * 获取支付通知url
     * @return 值
     **/
    public function getNotifyUrl()
    {
        return $this->bodyValues['notify_url'];
    }

    /**
     * 判断支付通知url是否存在
     * @return true 或 false
     **/
    public function isNotifyUrlSet()
    {
        return array_key_exists('notify_url', $this->bodyValues);
    }

    /**
     * 设置操作人员标识
     * @param string $value
     **/
    public function setOperator($value)
    {
        $this->bodyValues['operator'] = $value;
    }

    /**
     * 获取操作人员标识
     * @return 值
     **/
    public function getOperator()
    {
        return $this->bodyValues['operator'];
    }

    /**
     * 判断操作人员标识是否存在
     * @return true 或 false
     **/
    public function isOperatorSet()
    {
        return array_key_exists('operator', $this->bodyValues);
    }

}

/**
 * QRCode支付跳转对象
 * @author Leijid
 */
class RoyalPayRedirect extends RoyalPayDataBase
{
    /**
     * 设置支付成功后跳转页面
     * @param string $value
     **/
    public function setRedirect($value)
    {
        $this->queryValues['redirect'] = $value;
    }

    /**
     * 获取支付成功后跳转页面
     * @return 值
     **/
    public function getRedirect()
    {
        return $this->queryValues['redirect'];
    }

    /**
     * 判断支付成功后跳转页面是否存在
     * @return true 或 false
     **/
    public function isRedirectSet()
    {
        return array_key_exists('redirect', $this->queryValues);
    }
}

/**
 * jsapi支付跳转对象
 * @author Leijid
 */
class RoyalPayJsApiRedirect extends RoyalPayRedirect
{
    /**
     * 设置是否直接支付
     * @param string $value
     **/
    public function setDirectPay($value)
    {
        $this->queryValues['directpay'] = $value;
    }

    /**
     * 获取是否直接支付
     * @return 值
     **/
    public function getDirectPay()
    {
        return $this->queryValues['directpay'];
    }

    /**
     * 判断直接支付是否存在
     * @return true 或 false
     **/
    public function isDirectPaySet()
    {
        return array_key_exists('directpay', $this->queryValues);
    }
}

/**
 * 线下支付订单
 * @author Leijid
 */
class RoyalPayMicropayOrder extends RoyalPayUnifiedOrder
{
    /**
     * 设置设备ID
     * @param string $value
     **/
    public function setDeviceId($value)
    {
        $this->bodyValues['device_id'] = $value;
    }

    /**
     * 获取设备ID
     * @return 值
     **/
    public function getDeviceId()
    {
        return $this->bodyValues['device_id'];
    }

    /**
     * 判断设备ID是否存在
     * @return true 或 false
     **/
    public function isDeviceIdSet()
    {
        return array_key_exists('device_id', $this->bodyValues);
    }

    /**
     * 设置扫描用户微信客户端得到的支付码
     * @param string $value
     **/
    public function setAuthCode($value)
    {
        $this->bodyValues['auth_code'] = $value;
    }

    /**
     * 获取扫描用户微信客户端得到的支付码
     * @return 值
     **/
    public function getAuthCode()
    {
        return $this->bodyValues['auth_code'];
    }

    /**
     * 判断扫描用户微信客户端得到的支付码是否存在
     * @return true 或 false
     **/
    public function isAuthCodeSet()
    {
        return array_key_exists('auth_code', $this->bodyValues);
    }
}

/**
 * 线下QRCode支付单
 */
class RoyalPayRetailQRCode extends RoyalPayUnifiedOrder
{
    /**
     * 设置设备ID
     * @param string $value
     **/
    public function setDeviceId($value)
    {
        $this->bodyValues['device_id'] = $value;
    }

    /**
     * 获取设备ID
     * @return 值
     **/
    public function getDeviceId()
    {
        return $this->bodyValues['device_id'];
    }

    /**
     * 判断设备ID是否存在
     * @return true 或 false
     **/
    public function isDeviceIdSet()
    {
        return array_key_exists('device_id', $this->bodyValues);
    }
}

/**
 * 查询订单状态对象
 * @author Leijid
 */
class RoyalPayOrderQuery extends RoyalPayDataBase
{
    /**
     * 设置商户支付订单号，同一商户唯一
     * @param string $value
     **/
    public function setOrderId($value)
    {
        $this->pathValues['order_id'] = $value;
    }

    /**
     * 获取商户支付订单号
     * @return 值
     **/
    public function getOrderId()
    {
        return $this->pathValues['order_id'];
    }

    /**
     * 判断商户支付订单号是否存在
     * @return true 或 false
     **/
    public function isOrderIdSet()
    {
        return array_key_exists('order_id', $this->pathValues);
    }
}

/**
 * 申请退款对象
 * @author Leijid
 */
class RoyalPayApplyRefund extends RoyalPayDataBase
{
    /**
     * 设置商户支付订单号，同一商户唯一
     * @param string $value
     **/
    public function setOrderId($value)
    {
        $this->pathValues['order_id'] = $value;
    }

    /**
     * 获取商户支付订单号
     * @return 值
     **/
    public function getOrderId()
    {
        return $this->pathValues['order_id'];
    }

    /**
     * 判断商户支付订单号是否存在
     * @return true 或 false
     **/
    public function isOrderIdSet()
    {
        return array_key_exists('order_id', $this->pathValues);
    }

    /**
     * 设置商户退款单号
     * @param string $value
     **/
    public function setRefundId($value)
    {
        $this->pathValues['refund_id'] = $value;
    }

    /**
     * 获取商户退款单号
     * @return 值
     **/
    public function getRefundId()
    {
        return $this->pathValues['refund_id'];
    }

    /**
     * 判断商户退款单号是否存在
     * @return true 或 false
     **/
    public function isRefundIdSet()
    {
        return array_key_exists('refund_id', $this->pathValues);
    }

    /**
     * 设置退款金额，单位是货币最小单位
     * @param string $value
     **/
    public function setFee($value)
    {
        $this->bodyValues['fee'] = $value;
    }

    /**
     * 获取退款金额
     * @return 值
     **/
    public function getFee()
    {
        return $this->bodyValues['fee'];
    }

    /**
     * 判断退款金额是否存在
     * @return true 或 false
     **/
    public function isFeeSet()
    {
        return array_key_exists('fee', $this->bodyValues);
    }
}

/**
 * 查询退款状态对象
 * @author Leijid
 */
class RoyalPayQueryRefund extends RoyalPayDataBase
{
    /**
     * 设置商户支付订单号，同一商户唯一
     * @param string $value
     **/
    public function setOrderId($value)
    {
        $this->pathValues['order_id'] = $value;
    }

    /**
     * 获取商户支付订单号
     * @return 值
     **/
    public function getOrderId()
    {
        return $this->pathValues['order_id'];
    }

    /**
     * 判断商户支付订单号是否存在
     * @return true 或 false
     **/
    public function isOrderIdSet()
    {
        return array_key_exists('order_id', $this->pathValues);
    }

    /**
     * 设置商户退款单号
     * @param string $value
     **/
    public function setRefundId($value)
    {
        $this->pathValues['refund_id'] = $value;
    }

    /**
     * 获取商户退款单号
     * @return 值
     **/
    public function getRefundId()
    {
        return $this->pathValues['refund_id'];
    }

    /**
     * 判断商户退款单号是否存在
     * @return true 或 false
     **/
    public function isRefundIdSet()
    {
        return array_key_exists('refund_id', $this->pathValues);
    }
}

/**
 * 查询退款状态对象
 * @author Leijid
 */
class RoyalPayQueryOrders extends RoyalPayDataBase
{
    /**
     * 设置订单创建日期，'yyyyMMdd'格式，澳洲东部时间，不填默认查询所有订单
     * @param string $value
     **/
    public function setDate($value)
    {
        $this->queryValues['date'] = $value;
    }

    /**
     * 获取订单创建日期
     * @return 值
     **/
    public function getDate()
    {
        return $this->queryValues['date'];
    }

    /**
     * 判断订单创建日期是否存在
     * @return true 或 false
     **/
    public function isDateSet()
    {
        return array_key_exists('date', $this->queryValues);
    }

    /**
     * 设置订单状态
     * ALL:全部订单，包括未完成订单和已关闭订单
     * PAID:只列出支付过的订单，包括存在退款订单
     * REFUNDED:只列出存在退款订单
     * 默认值: ALL
     * 允许值: 'ALL', 'PAID', 'REFUNDED'
     * @param string $value
     **/
    public function setStatus($value = 'ALL')
    {
        $this->queryValues['status'] = $value;
    }

    /**
     * 获取订单状态
     * @return 值
     **/
    public function getStatus()
    {
        return $this->queryValues['status'];
    }

    /**
     * 判断订单状态是否存在
     * @return true 或 false
     **/
    public function isStatusSet()
    {
        return array_key_exists('status', $this->queryValues);
    }

    /**
     * 设置页码，从1开始计算
     * 默认值: 1
     * @param int $value
     **/
    public function setPage($value = 1)
    {
        $this->queryValues['page'] = $value;
    }

    /**
     * 获取页码
     * @return 值
     **/
    public function getPage()
    {
        return $this->queryValues['page'];
    }

    /**
     * 判断页码是否存在
     * @return true 或 false
     **/
    public function isPageSet()
    {
        return array_key_exists('page', $this->queryValues);
    }

    /**
     * 设置每页条数
     * 默认值: 10
     * @param int $value
     **/
    public function setLimit($value = 10)
    {
        $this->queryValues['limit'] = $value;
    }

    /**
     * 获取每页条数
     * @return 值
     **/
    public function getLimit()
    {
        return $this->queryValues['limit'];
    }

    /**
     * 判断每页条数是否存在
     * @return true 或 false
     **/
    public function isLimitSet()
    {
        return array_key_exists('limit', $this->queryValues);
    }
}