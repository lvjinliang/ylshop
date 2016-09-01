<?php
namespace Org\Home;
class Cart {
    private $accountObj = ''; //Account类

    public function __construct($accountObj) {
        $this->accountObj = $accountObj;
    }

    public function addCart($goods) {
        if (!empty($goods)) {
            foreach ($goods as $key => $val) {
                $goods[$key]['checked'] = 1;

            }
        }
        $cart = self::getCart();
        if (!empty($cart) && !empty($goods)) {
            $flag = true;
            foreach ($goods as $k1 => $v1) {
                foreach ($cart as $k2 => $v2) {
                    if (($v2['goods_id'] == $v1['goods_id']) && ($v2['product_sn'] == $v1['product_sn'])) {
                        $cart[$k2]['number'] = (int)$v2['number'] + (int)$v1['number'];
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push($cart, $v1);
                }
                $flag = true;
            }
        } else {
            $cart = $goods;
        }
        cookie('cart', serialize($cart), 24 * 3600 * 7);
        //登录时同步到数据库
        if ($this->accountObj->isLogin()) {
            $this->cartToDb($cart);
        }
        $number = 0;
        foreach ($cart as $val) {
            $number += $val['number'];
        }
        return $number;
    }

    public function removeCart($goods) {
        $cart = self::getCart();
        $number = 0;
        if (empty($cart)) {
            return $number;
        } else {
            foreach ($goods as $k1 => $v1) {
                foreach ($cart as $k2 => $v2) {
                    if (($v2['goods_id'] == $v1['goods_id']) && ($v2['product_sn'] == $v1['product_sn'])) {
                        unset($cart[$k2]);
                        break;
                    }
                }
            }
            foreach ($cart as $val) {
                $number += $val['number'];
            }
            cookie('cart', serialize($cart), 24 * 3600 * 7);
            //登录时同步到数据库
            if ($this->accountObj->isLogin()) {
                $this->cartToDb($cart);
            }
        }
        return $number;
    }

    public function updateCart($goods) {
        $cart = self::getCart();
        if (!empty($cart)) {
            foreach ($goods as $k1 => $v1) {
                foreach ($cart as $k2 => $v2) {
                    if (($v2['goods_id'] == $v1['goods_id']) && ($v2['product_sn'] == $v1['product_sn'])) {
                        $cart[$k2]['number'] = (int)$v1['number'];
                        break;
                    }
                }
            }
        } else {
            $cart = $goods;
        }
        cookie('cart', serialize($cart), 24 * 3600 * 7);
        //登录时同步到数据库
        if ($this->accountObj->isLogin()) {
            $this->cartToDb($cart);
        }
        $number = 0;
        foreach ($cart as $val) {
            $number += $val['number'];
        }
        return $number;
    }

    static public function emptyCart() {
        cookie('cart', null);
    }

    static public function getCartNumber() {
        $cart = cookie('cart');
        $number = 0;
        if (!empty($cart)) {
            $cart = unserialize($cart);
            foreach ($cart as $val) {
                $number += $val['number'];
            }
        }
        return $number;
    }

    static public function getCart() {
        $cart = cookie('cart');
        if (!empty($cart)) {
            $cart = unserialize($cart);
        } else {
            $cart = array();
        }
        return $cart;
    }

    private function cartToDb($cart) {
        if (!$this->accountObj->isLogin()) {
            return false;
        }
        //删除数据库
        $CartDb = M('Cart');
        $CartDb->where(array('account_id'=>$this->accountObj->accountInfo['id']))->delete();
        if (empty($cart)) {
            return false;
        }
        //同步到数据库
        foreach ($cart as $key => $val) {
            $cart[$key]['account_id'] = $this->accountObj->accountInfo['id'];
            ksort($cart[$key]);
        }
        $CartDb->addAll(array_values($cart));
    }

    public function mergeCartAndDb() {
        if (!$this->accountObj->isLogin()) {
            return false;
        }
        $CartDb = M('Cart');
        $dbCart = $CartDb->field('goods_id,product_sn,number,checked')
                         ->where(array('account_id'=>$this->accountObj->accountInfo['id']))
                         ->select();
        $cart = self::getCart();
        $flag = true;
        foreach ($dbCart as $k1 => $v1) {
            foreach ($cart as $k2 => $v2) {
                if (($v2['goods_id'] == $v1['goods_id']) && ($v2['product_sn'] == $v1['product_sn'])) {
                    //产品一样以购物车为准
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                array_push($cart, $v1);
            }
            $flag = true;
        }
        cookie('cart', serialize($cart), 24 * 3600 * 7);
        $this->cartToDb($cart);

    }

    public function updateChecked($data) {
        if(empty($data)) {
            return false;
        }
        $cart = self::getCart();
        foreach ($data as $v1) {
            foreach($cart as $k2 => $v2) {
                if ($v1['goods_id'] == $v2['goods_id'] && $v1['product_sn']==$v2['product_sn']) {
                    $cart[$k2]['checked'] = $v1['checked'];
                }
            }
        }
        cookie('cart', serialize($cart), 24 * 3600 * 7);
        //登录时同步到数据库
        if ($this->accountObj->isLogin()) {
            $this->cartToDb($cart);
        }
    }

}