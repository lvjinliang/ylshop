<?php
namespace Home\Event;
use Think\Controller;

class GoodsEvent extends Controller {

    public function getGoods($data, $colLg=3, $colMd=3, $colSm=6, $colXs=12) {
        $this->assign('data', $data);
        $this->assign('colLg', $colLg);
        $this->assign('colMd', $colMd);
        $this->assign('colSm', $colSm);
        $this->assign('colXs', $colXs);
        return $this->fetch('Widget:goods');
    }
}