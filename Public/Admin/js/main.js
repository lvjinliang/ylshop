$(function () {
    //左则高度
    resizeSidebar();
    $(window).resize(function () {
        resizeSidebar();
    });
    if(getCookie("show_sidebar")==="show"){
        showSidebar("#sidebar .show_sidebar");
    }
    var onorientationchange = "onorientationchange" in window ? "orientationchange" : "";

    //是否显左则菜单
    $("#sidebar .show_sidebar").click(function (event) {
        event.stopPropagation();
        showSidebar(this);
    });

    //左则菜单
    $("#sidebar .submenu>li").each(function(){
        if($(this).find("li.active").size()>0) {
            $(this).removeClass("off").addClass("active");
            $(this).find(".ssmenu").show();
        }
    });

    $("#sidebar .menu>li").each(function(){
        if($(this).find("li.active").size()>0) {
            $(this).removeClass("off").addClass("active");
            if(!$("#sidebar .menu").hasClass("menu_float_right")){
                $(this).find(".submenu").show();
            }
        }
    });


    $("body").on("click", "#sidebar .menu>li>a", function(event){
        event.stopPropagation();
        $("#sidebar .menu>li").removeClass("active").addClass("off");
        $("#sidebar .menu>li").not($(this).parent()).find(".submenu").hide();
        $(this).parent().addClass("active");
        if ($(this).parent().find(".submenu").size() > 0) {
            if ($(this).parent().find(".submenu").is(":hidden")) {
                $(this).parent().find(".submenu").show();
            } else {
                $(this).parent().find(".submenu").hide();
            }
        }
    });

    $("body").on("mouseover", "#sidebar .menu_float_right>li", function(){
        //$("#sidebar .menu_float_right>li").removeClass("active").addClass("off");
        $("#sidebar .menu_float_right>li").not($(this)).find(".submenu").hide();
        //$(this).addClass("active");
        if ($(this).find(".submenu").size() > 0) {
            $(this).find(".submenu").show();
        }
    });
    $("body").on("mouseout", "#sidebar .menu_float_right>li", function(){
        if ($(this).find(".submenu").size() > 0) {
            $(this).find(".submenu").hide();
        }
    });

    //左侧三级菜单
    $("body").on("click", "#sidebar .submenu>li>a", function(event){
        event.stopPropagation();
        /*$("#sidebar .submenu>li").removeClass("active").addClass("off");
        $(this).parent().addClass("active");*/
        if ($(this).parent().find(".ssmenu").size() > 0) {
            if ($(this).parent().find(".ssmenu").is(":hidden")) {
                $(this).parent().find(".ssmenu").show();
            } else {
                $(this).parent().find(".ssmenu").hide();
            }
        }
    });

    /*if(!onorientationchange) {
     $("#sidebar").on("mouseover", ".menu_float_right>li", function () {
     $(this).find(".submenu").show();
     });
     $("#sidebar").on("mouseout", ".menu_float_right>li", function () {
     $(this).find(".submenu").hide();
     });
     } else {
     $("#sidebar").on("touchstart", ".menu_float_right>li .menu_icon ", function () {
     if($(this).parent().parent().find(".submenu").size()>0){
     if($(this).parent().parent().find(".submenu").is(":visible")){
     $(this).parent().parent().find(".submenu").hide();
     } else {
     $(this).parent().parent().find(".submenu").show();
     }
     }
     });


     }*/

    //全选
    if ($("input:checkbox[name='checkall']").size() > 0) {
        if ($("input:checkbox[name='check']").size() > 0) {
            if ($("input:checkbox[name='checkall']").is(":checked")) {
                $("input:checkbox[name='check']").prop('checked', true);
            } else {
                $("input:checkbox[name='check']").prop('checked', false);
            }
        }
    }

    $("input:checkbox[name='checkall']").click(function () {
        if ($(this).is(":checked")) {
            $("input:checkbox[name='check']").prop('checked', true);
        } else {
            $("input:checkbox[name='check']").prop('checked', false);
        }
    });

    $("#dialog").dialog({
        autoOpen: false,
        title: '提示',
        resizable: true,
        modal: true
    });

    //图片上传组件
    $("span.uploadImg").each(function () {
        var obj = 'upload_img_' + $(this).find("textarea").attr("data");
        _this = $(this);
        $(this).find("a.add").click(function () {
            var imageDialog = eval(obj + ".getDialog('insertimage')");
            imageDialog.open();
        });

        $(this).find("a.remove").click(function () {
            _this.find("img").attr("src", "/Public/Admin/images/no_image-100x100.png");
            _this.find("textarea").html("/Public/Admin/images/no_image-100x100.png");
            _this.find("input").val("/Public/Admin/images/no_image-100x100.png");
        });
    });

    //访问授权
    $("dt.top_access").each(function () {
        $(this).find("input").click(function () {
            if ($(this).prop("checked")) {
                $(this).parents(".top_access").parent().find(".sub_access input").prop('checked', true);
            } else {
                $(this).parents(".top_access").parent().find(".sub_access input").prop('checked', false);
            }
        });
    });
    //二级
    $(".sub_access>div.checkbox input").each(function () {
        $(this).click(function () {
             if($(this).prop("checked")) {
                 $(this).parents(".sub_access").find(".ss_access input").prop('checked', true);
                 $(this).parents("dl").find("dt.top_access input").prop('checked',true);
             } else {
                 $(this).parents(".sub_access").find(".ss_access input").prop('checked', false);

             }
        });
    });
    //三级
    $(".sub_access>.ss_access input").each(function(){
        $(this).click(function(){
            if($(this).prop("checked")){
                $(this).parents("dl").find("dt.top_access input").prop('checked',true);
                $(this).parents(".sub_access").find("div.checkbox input").prop('checked',true);
            }
        });
    });

    $(".admin_form ul.nav-tabs>li").mouseup(function(){
        setTimeout(function(){
            resizeSidebar();
        }, 10);
    });


    //区域联动
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


    $("body").on("click", "a#order-cancel", function(e) {
        e.preventDefault();
        var _this = $(this);
        $("#dialog>p").html('确认取消订单');
        $("#dialog").dialog({
            autoOpen: true,
            buttons: {
                '确定': function () {
                    var __this = $(this);
                    var url = _this.attr("href");
                    $.ajax({
                        type : "GET",
                        url : url,
                        dataType : 'json',
                        success : function (json) {
                            if (json.success==1) {
                                var contentUrl = $("input[name='contentUrl']").val();
                                $.ajax({
                                    type : "GET",
                                    url : contentUrl+'?'+window.location.search,
                                    dataType : 'html',
                                    success : function (html){
                                        $('.admin_list').html(html);
                                    }
                                });

                            } else {
                                $(".error-msg").html(json.msg);
                                $("#confirm-error").show();
                            }
                            __this.dialog("close");
                        }
                    });
                },
                '取消': function () {
                    $(this).dialog("close");
                }
            }
        });

    });

});


function resizeSidebar() {
    var main_height = $("#main-content").height();
    var side_height = $("#sidebar").height();
    var area_height = $(window).height() - $("#header").height() - $("#footer").height();
    var max_height = Math.max(main_height, side_height, area_height);
    if (max_height == area_height) {
        $("#sidebar").height(area_height);
    }
    if ($("#sidebar ul.menu>li>a>span.text").is(":hidden")) {
        $("#main-content").outerWidth($(window).width() - 35 - $("#sidebar .show_sidebar").outerWidth());
    } else {
        $("#main-content").outerWidth($(window).width() - 235);
    }

}

function showSidebar(o) {
    if ($(o).find("span").hasClass("glyphicon-menu-left")) {
        delCookie("show_sidebar");
        setCookie("show_sidebar","hide");
        $(o).find("span").removeClass("glyphicon-menu-left").addClass("glyphicon-menu-right");
        $("#sidebar .submenu").hide();
        $("#sidebar .menu").addClass("menu_float_right");
        $("#main-content").outerWidth($(window).width() - 35 - $(o).outerWidth());
        $("#sidebar").width($(o).outerWidth());
        $("#sidebar .left_username").hide();
    } else if ($(o).find("span").hasClass("glyphicon-menu-right")) {
        delCookie("show_sidebar");
        setCookie("show_sidebar","show");
        $(o).find("span").removeClass("glyphicon-menu-right").addClass("glyphicon-menu-left");
        $("#main-content").outerWidth($(window).width() - 235);
        $("#sidebar").width(200);
        $("#sidebar .menu").removeClass("menu_float_right");
        $("#sidebar .left_username").show();
    }
}
/**
 * @function 删除选中属性
 * @param url 处理的URL
 * @param attr 删除的属性
 */

function deleteSelect(url, attr, msg) {
    var ids = new Array();
    $("input:checkbox[name='check']:checked").each(function () {
        ids.push($(this).val());
    });
    if (ids.length < 1) {
        $("#dialog>p").html("请选择需要删除的项");
        $("#dialog").dialog({
            autoOpen: true,
            buttons: {
                '确定': function () {
                    $(this).dialog("close");
                }
            }
        });
        return false;
    }

    attr = attr ? attr : 'id';
    msg = msg ? msg : '确认删除所选项';

    $("#dialog>p").html(msg);
    $("#dialog").dialog({
        autoOpen: true,
        buttons: {
            '确定': function () {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: attr + '=' + ids.join(','),
                    dataType: 'json',
                    success: function (json) {
                        $("#dialog").dialog("close");
                        $("#dialog>p").html(json.msg);
                        $("#dialog").dialog({
                            autoOpen: true,
                            buttons: {
                                '确定': function () {
                                    $("#dialog").dialog("close");
                                    if(json.success==1){
                                        window.location.reload();
                                    }
                                }
                            }
                        });
                    }
                });
            },
            '取消': function () {
                $(this).dialog("close");
            }
        }
    });
}

/**
 * @function 删除记录
 * @param url 处理的URL
 * @param attr 删除的属性
 */

function del(url, value, attr, msg) {
    attr = attr ? attr : 'id';
    msg = msg ? msg : '确认删除此项';
    $("#dialog>p").html(msg);
    $("#dialog").dialog({
        autoOpen: true,
        buttons: {
            '确定': function () {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: attr + '=' + value,
                    dataType: 'json',
                    success: function (json) {
                        $("#dialog").dialog("close");
                        $("#dialog>p").html(json.msg);
                        $("#dialog").dialog({
                            autoOpen: true,
                            buttons: {
                                '确定': function () {
                                    $("#dialog").dialog("close");
                                    if(json.success==1){
                                        window.location.reload();
                                    }
                                }
                            }
                        });
                    }
                });
            },
            '取消': function () {
                $(this).dialog("close");
            }
        }
    });
}

/**
 * @function 设置日历控件
 * @param obj 表单对表
 */
function setDatepicker(obj) {
    obj.datepicker({
        dateFormat: "yy-mm-dd",
        autoSize: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "关闭",
        yearRange: "1950:"
    });
    obj.datepicker($.datepicker.regional[ "zh-CN" ]);
}

function setDateRange(objStart, objEnd) {
    objStart.datepicker({
        dateFormat: "yy-mm-dd",
        autoSize: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "关闭",
        yearRange: "1950:",
        onClose: function( selectedDate ) {
            objEnd.datepicker( "option", "minDate", selectedDate );
        }
    });
    objEnd.datepicker({
        dateFormat: "yy-mm-dd",
        autoSize: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "关闭",
        yearRange: "1950:",
        onClose: function( selectedDate ) {
            objStart.datepicker( "option", "maxDate", selectedDate );
        }
    });
}

//图片上传后触发事件
function callBeforeInsertImage(id, imgInfo) {
    $("span.uploadImg").find("img." + id).attr('src', imgInfo.src);
    $("span.uploadImg").find("input[name='" + id + "']").val(imgInfo.src);
}



function addMultImage(o) {
    var imageDialog = eval(o + ".getDialog('insertimage')");
    imageDialog.open();
}

function revomeImage(o) {
    $(o).parents(".imageRow").remove();
}

//多图片上传后触发事件
function callBeforeInsertMultImage(id, imgInfo) {
    var html = '';
    var index = $("#"+id).parents(".gallery_table").find(".imageRow").size();
    if(index>0){
        index =  parseInt($("#"+id).parents(".gallery_table").find(".imageRow").last().find("td>img").attr("data-sort")) + 1;
    }
    $.each(imgInfo,function(i,n){
        html += '<tr class="imageRow">' +
            '<td class="text-left">' +
            '<img data-sort="'+(index+i)+'" src="'+ n.src+'" width="100" height="100"  />' +
            '<input type="hidden" name="image['+(index+i)+']" value="'+ n.src+'" />' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" name="image_title['+(index+i)+']" value="" />'+
            '</td>' +
            '<td class="text-right">'+
            '<input type="text" name="image_sort['+(index+i)+']" value="" />'+
            '</td>' +
            '<td><a class="btn btn-danger" onclick="revomeImage(this);">-</a></td>' +
            '</tr>';
    });

    $("#"+id).parents(".gallery_table").children("tbody").append(html);
}

//多图片上传后触发事件
function callBeforeInsertAdImage(id, imgInfo) {
    var html = '';
    var index = $("#"+id).parents(".gallery_table").find(".imageRow").size();
    if(index>0){
        index =  parseInt($("#"+id).parents(".gallery_table").find(".imageRow").last().find("td>img").attr("data-sort")) + 1;
    }
    $.each(imgInfo,function(i,n){
        html += '<tr class="imageRow">' +
            '<td class="text-left">' +
            '<img data-sort="'+(index+i)+'" src="'+ n.src+'" width="100" height="100"  />' +
            '<input type="hidden" name="image['+(index+i)+']" value="'+ n.src+'" />' +
            '</td>' +
            '<td class="text-right">' +
            '<input type="text" name="image_title['+(index+i)+']" value="" />'+
            '</td>' +
            '<td class="text-right">'+
            '<input type="text" name="image_sort['+(index+i)+']" value="" />'+
            '</td>' +
            '<td class="text-right">'+
            '<input type="text" name="image_link['+(index+i)+']" value="" />'+
            '</td>' +
            '<td><a class="btn btn-danger" onclick="revomeImage(this);">-</a></td>' +
            '</tr>';
    });

    $("#"+id).parents(".gallery_table").children("tbody").append(html);
}

//写入cookie
function setCookie(name,value, expires) {
    var second = expires||30*24*3600; //此 cookie 将被保存 30 天
    var exp = new Date();
    exp.setTime(exp.getTime() + second*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path=/";
}

///删除cookie
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

function download_excel(url) {
    console.log(url);
    location.href = url;

}

//读取cookie
function getCookie(name) {
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) {
        return unescape(arr[2]);
    }
    return null;
}

//兼容手机on的click事件
;(function () {
    var isTouch = ('ontouchstart' in document.documentElement) ? 'touchstart' : 'click', _on = $.fn.on;
    $.fn.on = function () {
        arguments[0] = (arguments[0] === 'click') ? isTouch : arguments[0];
        return _on.apply(this, arguments);
    };
})();