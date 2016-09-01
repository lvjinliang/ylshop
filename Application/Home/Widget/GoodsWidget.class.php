<?php
/**
 * User: 良子
 * Date: 16-2-29
 */
namespace Home\Widget;
use Think\Controller;
class GoodsWidget extends Controller {
    private $Goods;
    public function _initialize() {
        $this->Goods = D('Goods');
    }
    /**
     * @param author 良子
     * @param int $position_id 推荐位id
     * @param int $size 取多少数据
     * @param int $colLg 每行12格，大屏占格数 >=1200
     * @param int $colMd 每行12格，中屏占格数 >=992
     * @param int $colSm 每行12格，小屏占格数 >=768
     * @param int $colXs 每行12格，超小屏占格数 <768
     */
    public function getGoodsByPositionId($position_id, $size = 4, $colLg=3, $colMd=3, $colSm=6, $colXs=12) {
        $goodIds = $this->Goods->getGoodsIdByPositionId($position_id, $size);
        $this->getGoodsByIds($goodIds, $colLg, $colMd, $colSm, $colXs );

    }

    /**
     * @param author 良子
     * @param $ids array();
     * @param int $colLg 每行12格，大屏占格数 >=1200
     * @param int $colMd 每行12格，中屏占格数 >=992
     * @param int $colSm 每行12格，小屏占格数 >=768
     * @param int $colXs 每行12格，超小屏占格数 <768
     */
    private function getGoodsByIds($ids, $colLg=3, $colMd=3, $colSm=6, $colXs=12) {
        $data = $this->Goods->getGoodsByIds($ids);
        $this->assign('data', $data);
        $this->assign('colLg', $colLg);
        $this->assign('colMd', $colMd);
        $this->assign('colSm', $colSm);
        $this->assign('colXs', $colXs);
        $this->display(T('Home@Widget:goods'));
    }


    public function get_last_view() {
        $lastGoodsIds =  getLastView();
        $data = $this->Goods->getGoodsByIds($lastGoodsIds);
        $this->assign('data', $data);
        $this->display(T('Home@Widget:last_view_goods'));
    }




}
?>