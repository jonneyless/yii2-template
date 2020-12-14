<?php

/* @var $this yii\web\View */

$this->title = 'APP 支付';
?>

<form name="mbcpay_b2c">
    <?php foreach ($params as $name => $value) { ?>
        <input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
    <?php } ?>
    <input type="hidden" name="SUPPORTACCOUNTTYPE" value="3"/>
</form>

<script type="text/javascript">
    function MBC_ANDROID_PAY() {
        var orderinfo = "TXCODE=" + mbcpay_b2c.TXCODE.value + "," + "WAPVER=" + mbcpay_b2c.WAPVER.value + "," + "MERCHANTID=" + mbcpay_b2c.MERCHANTID.value + "," + "ORDERID=" + mbcpay_b2c.ORDERID.value + "," + "PAYMENT=" + mbcpay_b2c.PAYMENT.value + "," + "MAGIC=" + mbcpay_b2c.MAGIC.value + "," + "BRANCHID=" + mbcpay_b2c.BRANCHID.value + "," + "POSID=" + mbcpay_b2c.POSID.value + "," + "CURCODE=" + mbcpay_b2c.CURCODE.value + "," + "REMARK1=" + mbcpay_b2c.REMARK1.value + "," + "REMARK2=" + mbcpay_b2c.REMARK2.value + "," + "SUPPORTACCOUNTTYPE=" + mbcpay_b2c.SUPPORTACCOUNTTYPE.value;
        window.mbcpay.b2c(orderinfo);
    }

    function MBC_IOS_PAY() {
        window.location = "/mbcpay.b2c ";
    }

    function MBC_PAYINFO() {
        var orderinfo = "TXCODE=" + mbcpay_b2c.TXCODE.value + "," + "WAPVER=" + mbcpay_b2c.WAPVER.value + "," + "MERCHANTID=" + mbcpay_b2c.MERCHANTID.value + "," + "ORDERID=" + mbcpay_b2c.ORDERID.value + "," + "PAYMENT=" + mbcpay_b2c.PAYMENT.value + "," + "MAGIC=" + mbcpay_b2c.MAGIC.value + "," + "BRANCHID=" + mbcpay_b2c.BRANCHID.value + "," + "POSID=" + mbcpay_b2c.POSID.value + "," + "CURCODE=" + mbcpay_b2c.CURCODE.value + "," + "REMARK1=" + mbcpay_b2c.REMARK1.value + "," + "REMARK2=" + mbcpay_b2c.REMARK2.value + "," + "SUPPORTACCOUNTTYPE=" + mbcpay_b2c.SUPPORTACCOUNTTYPE.value;
        return "{" + orderinfo + "}";
    }
    <?php if($platform == 'Android'){ ?>
    MBC_ANDROID_PAY();
    <?php }else{ ?>
    MBC_IOS_PAY();
    <?php } ?>
</script>

<div class="box">
    <h4 class="red">如没有自动转跳，请选择对应的手机客户端版本。</h4>
    <input type="button" class="btn btn-danger" value="建行手机支付Android版" onclick="MBC_ANDROID_PAY()"/>
    <input type="button" class="btn btn-primary" value="建行手机支付IOS版" onclick="MBC_IOS_PAY()"/>
</div>