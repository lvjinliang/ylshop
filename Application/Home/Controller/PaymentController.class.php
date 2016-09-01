<?php
namespace Home\Controller;
use Think\Controller;
class PaymentController extends CommonController {
    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        layout(false);
        //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
        $payment_code = I('get.code');
        $orderNo = I('get.id');
        $Payment = D('Payment');
        $paymentData = $Payment->getPaymentByCode($payment_code);
        if (empty($paymentData)) {
            echo '该支付方式尚末开通…';
            echo '<script>window.history.back();</script>';
            exit();
        }
        $Order = D('Order');
        //检测是否有货
        if (!$Order->checkOrderHasGoods($orderNo)) {
            //echo '该订单中有商品缺货…';
            echo '<script>window.location.href='.U('Home/checkout/success',array('id'=>$orderNo)).';</script>';
            exit();
        }

        $config['email'] = $paymentData['email'];
        $config['key'] = $paymentData['key'];
        $config['partner'] = $paymentData['partner'];


        $orderData = $Order->getDetailByOrderNo($orderNo);
        $pay = new \Think\Pay($payment_code, $config);

        $vo = new \Think\Pay\PayVo();
        $vo->setBody('感谢在'.C('SHOP_NAME').'上购物')
            ->setFee($orderData['total']) //支付金额
            ->setOrderNo($orderNo)
            ->setTitle(C('SHOP_NAME').'订单')
            ->setCallback("Home/payment/pay")
            ->setUrl(U("Home/payment/pay_success"))
            ->setParam(array('order_id' => $orderNo));
        echo $pay->buildRequestForm($vo);

    }

    /**
     * 订单支付
     */
    public function pay($info) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
            $Order = D('Order');
            $data['payment_status_id'] = 1;
            $data['payment_buyer_email'] = $info['buyer_email'];
            $data['payment_buyer_id'] = $info['buyer_id'];
            $data['payment_no'] = $info['trade_no'];
            $data['payment_date'] = time();
            $data['date_updated'] = time();
            $result = $Order->setOrderPaymentStatus($info['out_trade_no'], $data);
        } else {
            $result = false;
        }
        return $result;
    }


    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        $Payment = D('Payment');
        $paymentData = $Payment->getPaymentByCode($apitype);
        if (empty($paymentData)) {
            echo '该支付方式尚末开通';
            exit();
        }
        $config['email'] = $paymentData['email'];
        $config['key'] = $paymentData['key'];
        $config['partner'] = $paymentData['partner'];

        $pay = new \Think\Pay($apitype, $config);

        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }
        $Order = D('Order');
        //验证
        if ($pay->verifyNotify($notify)) {
            $info = $pay->getInfo(); //获取订单信息

            $payinfo = M("Pay")->field(true)
                               ->where(array('out_trade_no' => $info['out_trade_no']))
                               ->order(array('id'=>'desc'))
                               ->find();

            if ($info['status']&&$info['money']==$payinfo['money']) {
                if ( $payinfo['status'] == 0 ) {
                    session("pay_verify", true);
                    $check = $this->pay($info);
                    if ($check !== false) {
                        M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))
                                ->setField(array('update_time' => time(), 'status' => 1));
                    } else {
                        \Think\Log::write('修改状态失败:'.var_export($info,true),'ERR','', C('LOG_PATH').date('y_m_d').'_payment.log');
                    }
                }

                if (I('get.method') == "return") {
                    redirect(U('Home/payment/pay_success'));
                } else {
                    $pay->notifySuccess();
                }
            } else {
                \Think\Log::write(var_export($notify,true),'ERR','', C('LOG_PATH').date('y_m_d').'_payment.log');
                $data['payment_status_id'] = 2;
                $data['payment_buyer_email'] = $notify['buyer_email'];
                $data['payment_buyer_id'] = $notify['buyer_id'];
                $data['payment_date'] = time();
                $data['date_updated'] = time();
                $Order->setOrderPaymentStatus($notify['out_trade_no'], $data);
                $this->error("支付失败！");
            }
        } else {
            E("Access Denied");
        }
    }

    public function pay_success () {
        $this->display();
    }

}