<?php

//递归获取栏目
if (!function_exists('getLevel')) {
    /*
     * $arr 传入数组
     * $type 返回类型 1:children,2:排序
     * $id 从哪层id工始
     * $lev 层级 1：可选择$id自身
     * $primaryKey 主键键值
     * $parentId 父ID键值
     * return 数组
     * */
    function getLevel($arr, $type = 1, $id = 0, $lev = 1, $primaryKey = 'id', $parentId = 'pid') {
        $levels = array();
        foreach ($arr as $k => $v) {
            if ($lev == 1 && $v[$primaryKey] == $id) {
                $v['lev'] = $lev;
                $levels[] = $v;
                $lev++;
            }
            if ($v[$parentId] == $id) {
                $v['lev'] = $lev;
                if ($type == 1) {
                    $v['children'] = getLevel($arr, $type, $v[$primaryKey], $lev + 1, $primaryKey, $parentId);
                }
                $levels[] = $v;
                if ($type == 2) {
                    $levels = array_merge($levels, getLevel($arr, $type, $v[$primaryKey], $lev + 1, $primaryKey, $parentId));
                }
            }
        }
        return $levels;
    }
}

if (!function_exists('getRoot')) {
    function getRoot($arr, $id){
        $root = array();
        foreach($arr as $v){
            if($v['id']== $id){
                if($v['pid']>0){
                    $root = array_merge($root, getRoot($arr, $v['pid']));
                }
                $root[] = $v;
            }
        }
        return $root;
    }
}


/******************数据验证***********************/

// 验证手机号
function checkTel($tel) {
    return preg_match('/^1(3|4|5|8|7)\d{9}$/', $tel);
}

//验证电话
function checkTelHome($tel_home) {
    return preg_match("/^([0-9]{3,4}(-)?)?[0-9]{6,8}((-)?[0-9]{2,5})?$/", $tel_home);
}

// 验证邮箱
function checkEmail($email) {
    $mode = "/^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$/";
    return preg_match($mode, $email);
}

/**
 * @param $reg 正则表达式
 * @param $val 验证的值
 */
function checkFormat($reg, $val) {
    return preg_match($reg, $val);
}

function checkConfirm($str1, $str2) {
    return $str1 === $str2 ? true : false;
}

/**
 * @function checkRequire 检测必填
 * @param string $value 字段名
 * @return bool {false:'空',true:'非空'}
 *
 */

function checkRequire($value) {
    if (!empty($value)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @function checkNumber 检测是否为数字
 * @param string $value 值
 * @param string $min 最小长度
 * @param string $max 最大长度 (为空或零则不限长)
 * @return bool {false:'非数值',true:'数值'}
 *
 */
function checkNumber($value, $min = 0, $max = '') {
    if (empty($max)) {
        $mode = '/\d*/isU';
    } else {
        $mode = '/^\d{' . $min . ',' . $max . '}$/isU';
    }
    return preg_match($mode, $value) ? true : false;
}
//验证验证码
function check_verify($code, $id = '') {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}







?>