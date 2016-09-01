jQuery(function ($) {
    $(document).ready(function () {
        if ($('nav#menu').size() > 0) {
            $('nav#menu').stickUp();
        }

        if ($('header #cart').size() > 0) {
            $('header #cart').stickUp();
        }

    });
});

$(function () {
    subMenu();
    setAddressContentHight();
    setGoodsCaptionHight();
    if($("footer").offset().top+$("footer").height()<$(window).height()) {
        $("footer").offset({top:$(window).height()-$("footer").height()})
    }
    $(window).resize(function () {
        if($("footer").offset().top+$("footer").height()<$(window).height()) {
            $("footer").offset({top:$(window).height()-$("footer").height()})
        }
        subMenu();
        if ($(".address .addr-content").size() > 0) {
            setAddressContentHight();
        }
        setGoodsCaptionHight();
    });


    //商品详情页选择属性
    $(".goods-page").on("click", ".attr .goods-attrs", function () {
        $(this).parent().find(".goods-attrs").removeClass("goods-attrs-on");
        $(this).addClass("goods-attrs-on");
        var attr_ids = [];
        var attr_values = [];
        $(".attr .goods-attrs-on").each(function (i, field) {
            attr_ids.push($(field).attr('attr-id'));
            attr_values.push($(field).html());
        });
        attr_ids = attr_ids.join(',');
        attr_values = attr_values.join(',');
        var id = $("input[name='id']").val();
        if ($(".goods-page .caption .add-cart").size() > 0) {
            $(".goods-page .caption .add-cart").removeClass("btn-enbuy").addClass("btn-disbuy");
        }
        $.ajax({
            type: "POST",
            url: $("input[name='change_goods_attr_url']").val(),
            data: "id=" + id + "&attr_ids=" + attr_ids + "&attr_values=" + attr_values,
            dataType: 'json',
            success: function (json) {
                if (json.success == 1) {
                    $(".caption .price label").html("￥" + (parseFloat(json.data['attrPrice']) + parseFloat($("input[name='price']").val())));
                    if ($("input[name='or_price']").size() > 0) {
                        $(".caption .price span").html("￥" + (parseFloat(json.data['attrPrice']) + parseFloat($("input[name='or_price']").val())));
                    }
                    if ($(".goods-page .caption .info span.product_sn").size() > 0) {
                        $(".goods-page .caption .info span.product_sn").html(json.data['productSn']);
                    }
                    $(".goods-page .caption .add-cart").attr("data-product_sn", json.data['productSn']);
                    $(".info li span.number").html(json.data['productNumber']);
                    if ($(".goods-page .caption .add-cart").size() > 0 && json.data['productNumber'] > 0) {
                        $(".goods-page .caption .add-cart").removeClass("btn-disbuy").addClass("btn-enbuy");
                    }
                }

            }
        });
    });

    $("body").on("click", ".review-text .page li a", function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: "get",
            url : url,
            dataType : 'html',
            success : function (html){
               $("#review .review-text").html(html);
            }

        });
    });

    /**
     * 加入购物车
     */
    $("body").on("click", ".btn-enbuy.add-cart", function (event) {
        if ($(".goods-page .caption .add-cart").size() > 0) {
            $(".goods-page .caption .add-cart").removeClass("btn-enbuy").addClass("btn-disbuy");
        }
        var tmp = new Array();
        var goods = new Object();
        var data = new Object();

        goods.number = $(this).parent().find("input.number").val();
        goods.goods_id = $(this).attr("data-goods_id");
        goods.product_sn = $(this).attr("data-product_sn");
        tmp.push(goods);
        data.goods = tmp;
        var url = $(this).attr("data-add_cart_url");
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (json) {
                if (json.success == 0) {
                    $("#dialog>p").html(json.msg);
                    $("#dialog").dialog({
                        autoOpen: true,
                        title: '提示',
                        resizable: true,
                        modal: true,
                        buttons: {
                            '确定': function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                } else {
                    $("header #cart .cart-number").html(json.total);
                    if ($("#cart").size() > 0 && $(".image .swiper-slide-active img").size() > 0) {
                        var offset = $("#cart").offset();
                        var start = $(".image .swiper-slide-active img").offset();
                        var img = $(".image .swiper-slide-active img").attr('src');
                        var flyer = $('<img class="u-flyer" src="' + img + '">');
                        flyer.fly({
                            start: {
                                left: start.left + $(window).scrollLeft() + $(".image .swiper-slide-active img").width() / 2,
                                top: start.top - $(window).scrollTop() + $(".image .swiper-slide-active img").height() / 2
                            },
                            end: {
                                left: offset.left + $(window).scrollLeft(),
                                top: offset.top - $(window).scrollTop(),
                                width: 0,
                                height: 0
                            },
                            onEnd: function () {
                                this.destory();
                            }
                        });
                    }
                }
                if ($(".goods-page .caption .add-cart").size() > 0) {
                    $(".goods-page .caption .add-cart").removeClass("btn-disbuy").addClass("btn-enbuy");
                }
            }
        });
    });


    /**
     * 修改购物车
     */
    $("body").on("click", ".update-cart.btn-enbuy", function (event) {
        _this = $(this);
        _this.removeClass("btn-enbuy").addClass("btn-disbuy");

        var tmp = new Array();
        var goods = new Object();
        var data = new Object();
        goods.number = $(this).parent().find("input.number").val();
        goods.goods_id = $(this).attr("data-goods_id");
        goods.product_sn = $(this).attr("data-product_sn");
        tmp.push(goods);
        data.goods = tmp;
        //是否选择了买该商品
        var selectTmp = new Array();
        $("input[name='cart_select[]']:checked").each(function (i, n) {
            var selectGoods = new Object();
            selectGoods.goods_id = $(n).attr("data-goods_id");
            selectGoods.product_sn = $(n).attr("data-product_sn");
            selectTmp.push(selectGoods);
            selectGoods = null;
        });
        data.selectGoods = selectTmp;

        var url = $(this).attr("data-update_cart_url");
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function (json) {
                if (json.success == 0) {
                    $("#dialog>p").html(json.msg);
                    $("#dialog").dialog({
                        autoOpen: true,
                        title: '提示',
                        resizable: true,
                        modal: true,
                        buttons: {
                            '确定': function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                } else {
                    $(".cart-page .cart-list").html(json.html);
                }
                _this.removeClass("btn-disbuy").addClass("btn-enbuy");
            }
        });
    });

    /**
     * 选择购
     */
    $("body").on("click", "input[name='cart_select[]']", function () {
        var selectTmp = new Array();
        var data = new Object();
        $("input[name='cart_select[]']:checked").each(function (i, n) {
            var selectGoods = new Object();
            selectGoods.goods_id = $(n).attr("data-goods_id");
            selectGoods.product_sn = $(n).attr("data-product_sn");
            selectTmp.push(selectGoods);
            selectGoods = null;
        });
        data.selectGoods = selectTmp;
        var url = $(this).attr("data-select_cart_url");
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'html',
            success: function (html) {
                $(".cart-page .cart-list").html(html);
            }
        });
    });

    /**
     * 删除购物车
     */
    $("body").on("click", ".remove-cart.enable-remove", function (event) {
        $(this).removeClass("enable-remove");
        var tmp = new Array();
        var goods = new Object();
        var data = new Object();
        goods.goods_id = $(this).attr("data-goods_id");
        goods.product_sn = $(this).attr("data-product_sn");
        tmp.push(goods);
        data.goods = tmp;
        //是否选择了买该商品
        var selectTmp = new Array();
        $("input[name='cart_select[]']:checked").each(function (i, n) {
            var selectGoods = new Object();
            selectGoods.goods_id = $(n).attr("data-goods_id");
            selectGoods.product_sn = $(n).attr("data-product_sn");
            selectTmp.push(selectGoods);
            selectGoods = null;
        });
        data.selectGoods = selectTmp;

        var url = $(this).attr("data-remove_cart_url");
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'html',
            success: function (html) {
                $(".cart-page .cart-list").html(html);
            }
        });
    });


    /**
     * 改更购买数量
     */
    $("body").on("blur", ".product input.number", function () {
        var pattern = /^[1-9][0-9]?$/;
        _this = $(this);
        if (!$(this).val().match(pattern)) {
            $("#dialog>p").html('请写入正确的数量');
            $("#dialog").dialog({
                autoOpen: true,
                title: '提示',
                resizable: true,
                modal: true,
                buttons: {
                    '确定': function () {
                        $(this).dialog("close");
                        _this.focus();
                    }
                }
            });
        }
    });

    /**
     * 结算
     */
    $("body").on("click", ".checkout.btn-checkout", function (event) {
        $(this).removeClass("btn-checkout").addClass("btn-discheckout");
        var url = $("input[name='get_checkout_url']").val();
        window.location.href = url;
    });


    /**
     * 添加新地址
     */
    $("body").on("click", ".btn-address.enable", function () {
        var thisNtn = $(this);
        thisNtn.removeClass("enable").addClass("disable");

        var url = $(this).attr("data-url");

        $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            success: function (html) {
                $("#dialog>div").html(html);
                $("#dialog").dialog({
                    autoOpen: true,
                    width: "75%",
                    title: '添加地址',
                    resizable: true,
                    modal: true,
                    buttons: {
                        '确定': function () {
                            var _this = $(this);
                            var postUrl = $("#addressForm").attr("data-url");
                            $.ajax({
                                type: "POST",
                                url: postUrl,
                                data: $("#addressForm").serialize(),
                                dataType: "html",
                                success: function (html) {
                                    if (html == 1) {
                                        var addressUrl = $("#addressForm").attr("data-getAddress-url");
                                        $.get(addressUrl, function (html) {
                                            $(".checkout-page .address").html(html)
                                        }, 'html');
                                        _this.dialog("close");
                                    } else {
                                        $("#dialog>div").empty();
                                        $("#dialog>div").html(html);
                                    }

                                }
                            });
                        },
                        '取消': function () {
                            $(this).dialog("close");
                        }
                    }
                });
                thisNtn.removeClass("disable").addClass("enable");
            }
        })

    });
    /**
     * 更改地址
     */
    $("body").on("click", ".btn-update-address.enable", function (event) {
        event.stopPropagation();
        var thisNtn = $(this);
        thisNtn.removeClass("enable").addClass("disable");
        var url = $(this).attr("data-url");
        $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            success: function (html) {
                $("#dialog>div").html(html);
                $("#dialog").dialog({
                    autoOpen: true,
                    width: "75%",
                    title: '更改地址',
                    resizable: true,
                    modal: true,
                    buttons: {
                        '确定': function () {
                            var _this = $(this);
                            var postUrl = $("#addressForm").attr("data-url");
                            $.ajax({
                                type: "POST",
                                url: postUrl,
                                data: $("#addressForm").serialize(),
                                dataType: "html",
                                success: function (html) {
                                    if (html == 1) {
                                        var addressUrl = $("#addressForm").attr("data-getAddress-url");
                                        $.get(addressUrl, function (html) {
                                            $(".checkout-page .address").html(html);
                                        }, 'html');
                                        _this.dialog("close");
                                    } else {
                                        $("#dialog>div").empty();
                                        $("#dialog>div").html(html);
                                    }
                                }
                            });
                        },
                        '取消': function () {
                            $(this).dialog("close");
                        }
                    }
                });
                thisNtn.removeClass("disable").addClass("enable");
            }
        })

    });

    /**
     * 删除地址
     */
    $("body").on("click", ".btn-delete-address.enable", function (event) {
        event.stopPropagation();
        var thisNtn = $(this);
        thisNtn.removeClass("enable").addClass("disable");
        var url = $(this).attr("data-url");
        var addressUrl = $(this).attr("data-getAddress-url");
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function (json) {
                if (json.success == 1) {
                    $.get(addressUrl, function (html) {
                        $(".checkout-page .address").html(html);
                    }, 'html');
                } else {
                    $("#dialog>div").html(json.msg);
                    $("#dialog").dialog({
                        autoOpen: true,
                        title: '提示',
                        resizable: true,
                        modal: true,
                        buttons: {
                            '确定': function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                    thisNtn.removeClass("disable").addClass("enable");
                }
            }
        });
    });

    /**
     * 设成默认地址
     */
    $("body").on("click", ".btn-set-default-address.enable", function (event) {
        event.stopPropagation();
        var thisNtn = $(this);
        thisNtn.removeClass("enable").addClass("disable");
        var url = $(this).attr("data-url");
        var addressUrl = $(this).attr("data-getAddress-url");
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function (json) {
                if (json.success == 1) {
                    $.get(addressUrl, function (html) {
                        $(".checkout-page .address").html(html);
                    }, 'html');
                } else {
                    $("#dialog>div").html(json.msg);
                    $("#dialog").dialog({
                        autoOpen: true,
                        title: '提示',
                        resizable: true,
                        modal: true,
                        buttons: {
                            '确定': function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                    thisNtn.removeClass("disable").addClass("enable");
                }
            }
        });
    });

    $("body").on("click", ".addr-content input[name='address_id']", function (event) {
        getCheckoutContent();
    });

    $("body").on("click", ".checkout-page .shipping .shipping-content", function (event) {
        $("input[name='shipping_code']").val($(this).attr("data"));
        getCheckoutContent();
    });

    $("body").on("click", ".checkout-page .payment .payment-content", function (event) {
        $("input[name='payment_code']").val($(this).attr("data"));
        getCheckoutContent();
    });

    $("body").on("change", ".checkout-page select[name='coupon']", function (event) {
        getCheckoutContent();
    });

    //使用积分
    $("body").on("blur", ".payment input[name='pay_integral']", function () {
        var preg = /^[1-9]?\d*$/;
        if (preg.test($(this).val())) {
            getCheckoutContent();
        } else {
            $("#dialog>div").html("请输入正确的积分");
            $("#dialog").dialog({
                autoOpen: true,
                title: '提示',
                resizable: true,
                modal: true,
                buttons: {
                    '确定': function () {
                        $(this).dialog("close");
                    }
                }
            });
        }
    });

    //使用余额
    $("body").on("blur", ".payment input[name='money']", function () {

        if (!isNaN($(this).val()) && parseFloat($(this).val()) >= 0 || $(this).val() == '') {
            getCheckoutContent();
        } else {
            $("#dialog>div").html("请输入正确的金额");
            $("#dialog").dialog({
                autoOpen: true,
                title: '提示',
                resizable: true,
                modal: true,
                buttons: {
                    '确定': function () {
                        $(this).dialog("close");
                    }
                }
            });
        }
    });

    /**
     * 下单
     */
    $("body").on("click", ".order.btn-checkout", function (event) {
        $(this).removeClass("btn-checkout").addClass("btn-discheckout");
        var url = $("input[name='get_order_url']").val();
        var data = getCheckoutParam();
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function (json) {
                if (json.success==1) {
                    window.location.href = json.jumpUrl;
                } else {
                    $(".checkout-content").html(json.html);
                    if(json.msg !='') {
                        $(".error-info .help-block").html(json.msg);
                        $(".error-info").show();
                    }
                }
            }
        });
    });


    $("body").on("change", "select#province", function () {
        if ($(this).val() != "") {
            var url = $(this).attr("data-url");
            $.ajax({
                url: url,
                type: "POST",
                data: "id=" + $(this).val(),
                dataType: "html",
                success: function (html) {
                    $("select#city").html(html);
                    $("select#district").html("<option value='' class='default'>--请选择区县--</option>");

                }
            });
        } else {
            $("select#city").html("<option value='' class='default'>--请选择市--</option>");
            $("select#district").html("<option value='' class='default'>--请选择区县--</option>");
        }
    });

    $("body").on("change", "select#city", function () {
        if ($(this).val() != "") {
            var url = $(this).attr("data-url");
            $.ajax({
                url: url,
                type: "POST",
                data: "id=" + $(this).val(),
                dataType: "html",
                success: function (html) {
                    $("select#district").html(html);
                }
            });
        } else {
            $("select#district").html("<option value='' class='default'>--请选择区县--</option>");
        }
    });

    //选择发票信息
    $("body").on("click", "input[name='invoice_status']", function () {
        if ($("input[name='invoice_status']:checked").val() == 1) {
            $(".invoice .invoice_body").show();
        } else {
            $(".invoice .invoice_body").hide();
        }
    });

    //评分
    $("body").on("click", ".start .solid-start span", function (){
        var emptyWidth = $(".start .empty-start").width();
        var solidWidth = ($(this).index()+1)/5 * emptyWidth;
        $(".start .solid-start").width(solidWidth);
        $("input[name='score']").val($(this).index()+1);
    });

    $("body").on("click", ".start .empty-start span", function (){
        var emptyWidth = $(".start .empty-start").width();
        var solidWidth = ($(this).index()+1)/5 * emptyWidth;
        $(".start .solid-start").width(solidWidth);
        $("input[name='score']").val($(this).index()+1);
    });

    //退货申请，确认收货及取消订单
    $("body").on("click", "a.return_order,a.cancel_order,a.confirm_order", function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        if ($(this).hasClass('return_order')) {
            $("#dialog>div").html('确认退货');
        } else if ($(this).hasClass('cancel_order')) {
            $("#dialog>div").html('确认取消订单');
        } else if ($(this).hasClass('confirm_order')) {
            $("#dialog>div").html('确认收货');
        }

        $("#dialog").dialog({
            autoOpen: true,
            title: '提示',
            resizable: true,
            modal: true,
            buttons: {
                '确定': function () {
                    $(this).dialog("close");
                    $.ajax({
                        type : "GET",
                        url : url,
                        dataType : 'json',
                        success : function (json) {
                            if (json.success==1) {
                                window.location.reload();
                            } else {
                                $("#dialog>div").html(json.msg);
                                $("#dialog").dialog({
                                    autoOpen: true,
                                    title: '提示',
                                    resizable: true,
                                    modal: true,
                                    buttons: {
                                        '确定': function () {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            }

                        }
                    });
                },
                '取消': function () {
                    $(this).dialog("close");
                }
            }
        });

    });

    //修改支付方式
    $("body").on("click", "span.other-payment", function(e) {
        if ($("tr.payment").is(":hidden")) {
            $("tr.payment").show();
        } else {
            $("tr.payment").hide();
        }
    });
    $("body").on("click", ".checkout-success-page .payment .payment-content", function (event) {
        var payment_code = $(this).attr("data");
        var id = $(this).attr("data-order");
        var url = $("input[name='change_payment']").val();
        $.ajax({
            type : 'POST',
            url  : url,
            data : {'id':id,'payment_code':payment_code},
            dataType : 'json',
            success : function (json) {
               if (json.success==1) {
                   $(".success-content").html(json.msg);
               } else {
                   $(".checkout-success-page .error-msg").html(json.msg);
               }
            }
        });
    });

    //立即支付
    $("body").on("click", ".btn.paynow", function(){
        $(this).removeClass('paynow');
        $("#dialog>div").html("正在支付……，请勿关闭");
        $("#dialog").dialog({
            autoOpen: true,
            title: '提示',
            resizable: true,
            modal: true,
            buttons: {
                '完成支付': function () {
                    window.location.reload();
                },
                '支付失败': function () {
                    window.location.reload();
                }
            }
        });
    });


    $("body").on("click", ".search-btn", function(){
        $key = $("input.search-input").val();
        window.location.href = $(this).attr('data')+"/search/"+$key;
    });

});


function subMenu() {
    if ($(window).width() >= 768) {
        $("#menu .main-menu>li ul.sub-menu").hide();
        $("#menu .main-menu>li").on("mouseover", function () {
            if ($(this).find("ul.sub-menu").size() > 0) {
                $(this).find("ul.sub-menu").show();
            }
        });
        $("#menu .main-menu>li").on("mouseout", function () {
            if ($(this).find("ul.sub-menu").size() > 0) {
                $(this).find("ul.sub-menu").hide();
            }
        });
    } else {
        $("#menu .main-menu>li ul.sub-menu").show();
        $("#menu .main-menu>li").off("mouseover");
        $("#menu .main-menu>li").off("mouseout");
    }
}

function setAddressContentHight() {
    if ($(".address .addr-content").size() > 0) {
        var maxHeight = 0;
        var contentHeight
        var addrBdHeight = $(".address .addr-content .addr-bd").css("margin-bottom").replace("px", "");
        $(".address .addr-content").each(function () {
            contentHeight = $(this).find(".addr-hd").outerHeight() + $(this).find(".addr-bd").outerHeight() + $(this).find(".addr-toolbar").outerHeight() + parseInt(addrBdHeight);
            if (contentHeight > maxHeight) {
                maxHeight = contentHeight;
            }
        });
        $(".address .addr-content").height(maxHeight);
    }
}

function setGoodsCaptionHight() {
    if ($(".checkout-page .goods .caption").size() > 0) {
        var maxHeight = 0;
        var contentHeight;
        $(".checkout-page .goods .caption").each(function () {
            contentHeight = $(this).outerHeight();
            if (contentHeight > maxHeight) {
                maxHeight = contentHeight;
            }
        });
        $(".checkout-page .goods .caption").height(maxHeight);
    }
}

function changeVerify(o) {
    o.src = $(o).attr("data") + "/" + Math.random();
}

function getCheckoutContent() {
    var url = $("input[name='get_content_url']").val();
    var data = getCheckoutParam();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (json) {
            $(".checkout-content").html(json.html);
        }
    });
}


function getCheckoutParam() {
    var data = "";
    var address_id = $("input[name='address_id']:checked").val();
    var shipping_code = $("input[name='shipping_code']").val();
    var payment_code = $("input[name='payment_code']").val();
    var coupon =typeof($("select[name='coupon']").val())!= 'undefined' ? $("select[name='coupon']").val() : '';
    var comment = $("input[name='comment']").val();
    data = "address_id=" + address_id + "&shipping_code=" + shipping_code + "&payment_code=" + payment_code+"&comment="+comment+"&coupon="+coupon;

    //积分
    var pay_integral = $("input[name='pay_integral']").val();
    if (typeof(pay_integral) != 'undefined' && pay_integral != '' && !isNaN(pay_integral)) {
        data += "&pay_integral=" + parseInt(pay_integral);
    }
    //余额
    var money = $("input[name='money']").val();
    if (typeof(money) != 'undefined' && money != '' && !isNaN(money)) {
        data += "&money=" + parseFloat(money).toFixed(2);
    }

    var invoice_status = $("input[name='invoice_status']:checked").val();
    data = data + "&invoice_status="+invoice_status;
    if (invoice_status == 1 ){
        var invoice_title = $("input[name='invoice_title']").val();
        var invoice_type = $("select[name='invoice_type']").val();
        data = data+"&invoice_title="+invoice_title+"&invoice_type="+invoice_type;
    }
    return data;
}


//兼容手机on的click事件
;
(function () {
    var isTouch = ('ontouchend' in document.documentElement) ? 'touchend' : 'click', _on = $.fn.on;
    $.fn.on = function () {
        arguments[0] = (arguments[0] === 'click') ? isTouch : arguments[0];
        return _on.apply(this, arguments);
    };
})();
