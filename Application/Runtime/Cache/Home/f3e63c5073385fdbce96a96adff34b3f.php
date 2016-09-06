<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-lg-12">
    <div class="title text-center"><?php echo ($msg); ?></div>
</div>
<input name="change_payment" value="<?php echo ($changePayment); ?>" type="hidden" />
<?php if(!empty($data)): ?><div class="col-lg-12">
        <div class="info text-center table-responsive">
            <table class="table table-bordered">
                <tr>
                    <td class="text-right" width="35%">订单号：</td>
                    <td class="text-left"><?php echo ($data['order']['order_no']); ?></td>
                </tr>
                <tr>
                    <td class="text-right">支付金额：</td>
                    <td class="text-left"><span style="color:red;" ><?php echo ($data['order']['total']); ?></span></td>
                </tr>
                <tr>
                    <td class="text-right">支付方式：</td>
                    <td class="text-left">
                        <span style="color:red;" ><?php echo ($data['order']['payment_method']); ?></span>
                        <span class="other-payment">其它支付方式<span class="caret"></span></span>
                    </td>
                </tr>
                <tr class="payment" style="display:none;">
                    <td colspan="2">
                        <?php if(is_array($data['paymentList'])): $i = 0; $__LIST__ = $data['paymentList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$payment): $mod = ($i % 2 );++$i;?><div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="payment-content text-center <?php if(($payment['code']) == $data['order']['payment_code']): ?>selected<?php endif; ?>" data="<?php echo ($payment['code']); ?>" data-order="<?php echo ($data['order']['order_no']); ?>">
                                <?php echo ($payment['name']); ?>
                                 </div>
                             </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </td>

                </tr>
            </table>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="title text-center">
            <?php if(!empty($payBut['status'])): ?><a href="<?php echo ($payBut['url']); ?>" target="_blank" class="btn btn-enbuy paynow"><?php echo ($payBut['text']); ?></a>
            <?php else: ?>
                <a href="<?php echo ($payBut['url']); ?>"  class="btn btn-disbuy"><?php echo ($payBut['text']); ?></a><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="error-msg text-center text-danger"></div>
    </div><?php endif; ?>