<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

function statusToText($code) {
    $text = $code ? '开启':'关闭';
    return $text;
}

function userStatusToText($code) {
    $text = $code ? '正常':'冻结';
    return $text;
}

function validatedStatusToText($code) {
    $text = $code ? '激活':'末激活';
    return $text;
}

function isTopToText($code) {
    $text = $code ? '置顶':'末置顶';
    return $text;
}

function reviewStatusToText($code) {
    switch($code) {
        case 0:
            $text = '未审核';
            break;
        case 1:
            $text = '审核通过';
            break;
        case 2:
            $text = '审核未通过';
            break;
        default:
            $text = '';
    }

    return $text;
}

function specialRankToText($code) {
    $text = $code ? '特殊会员组':'普通会员组';
    return $text;
}

function sexToText($code) {
    $text = $code==1 ? '男':'女';
    return $text;
}

function isShowToText($code) {
    $text = $code ? '是':'否';
    return $text;
}

function codToText($code) {
    $text = $code ? '支持':'不支持';
    return $text;
}

function isOnSaleToText($code) {
    $text = $code ? '是':'否';
    return $text;
}

function inputTypeTotext($code) {
    switch($code) {
        case 1:
            $text = '手工录入';
            break;
        case 2:
            $text = '从列表中选择';
            break;
        default :
            $text = '';
            break;
    }

    return $text;
}

function promotionTypeToText($code){
    switch($code) {
        case 1:
            $text = '全场促销';
            break;
        case 2:
            $text = '部份促销';
            break;
        case 3:
            $text = '多份促销';
            break;
        case 4:
            $text = '组合促销';
            break;
        case 5:
            $text = '买赠促销';
            break;
        default :
            $text = '';
            break;

    }
    return $text;
}

function discountTypeToText($code) {
    switch($code) {
        case 1:
            $text = '固定金额';
            break;
        case 2:
            $text = '百分比';
            break;
        default :
            $text = '';
            break;
    }

    return $text;
}

function discountShow($discount, $d_type) {
    $show = '';
    if($d_type==1){
        $show = '优惠'.$discount.'元';
    } elseif($d_type==2){
        $discount = round($discount/10,1);
        $show = $discount.'折';
    }
    return $show;
}

function setPage($count, $limit){
    $page  = new \Think\Page($count,$limit);
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $page->setConfig('header','<li><span class="rows">共 %TOTAL_ROW% 条记录</span></li>');
    return $page;
}

?>