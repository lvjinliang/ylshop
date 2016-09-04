<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    public function index(){
        $startDate = date('Y-m-d', strtotime('-2 day'));
        $endDate = date('Y-m-d');
        $filter['start_date'] = $startDate;
        $filter['end_date'] = $endDate;
        $filter['date'] = array(
            array('egt',$startDate),
            array('elt',$endDate)
        );
        //排序
        $filter['order'] = array( 'date'=>'DESC');

        $Order = D('Order');
        $Account = D('Account');
        $data['order'] = $Order->getOrderDateList($filter);
        $data['account'] = $Account->getAccountDateList($filter);
        $this->assign('data', $data);
        $this->display();
    }

    public function ajax_get_count_visit () {
        $startDate = date('Y-m-d', strtotime('-7 day'));
        $endDate = date('Y-m-d');
        $filter['type'] = strtoupper(I('get.type', 'WEB'));
        $filter['start_date'] = $startDate;
        $filter['end_date'] = $endDate;
        $filter['date'] = array(
            array('egt',$startDate),
            array('elt',$endDate)
        );
        //排序
        $filter['order'] = array( 'date'=>'ASC');
        $DayCountVisit = D('DayCountVisit');
        $data =  $DayCountVisit->getLists($filter);
        $output['pv'] = array_column($data, 'pv');
        $output['uv'] = array_column($data, 'uv');
        $output['ipv'] = array_column($data, 'ipv');
        $output['date'] = array_column($data, 'date');
        return $this->ajaxReturn($output);
    }

}