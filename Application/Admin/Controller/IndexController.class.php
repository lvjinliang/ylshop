<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    public function index(){
        $startDate = date('Y-m-d', strtotime('-1 day'));
        $endDate = date('Y-m-d');
        $filter['start_date'] = $startDate;
        $filter['end_date'] = $endDate;
        $filter['date'] = array(
            array('egt',$startDate),
            array('elt',$endDate)
        );
        //排序
        $filter['order'] = array( 'date'=>'DESC');
        $DayCountVisit = D('DayCountVisit');
        $Order = D('Order');
        $Account = D('Account');
        $data['countVisit'] =  $DayCountVisit->getLists($filter);
        $data['order'] = $Order->getOrderDateList($filter);
        $data['account'] = $Account->getAccountDateList($filter);
        $this->assign('data', $data);
        $this->display();
    }
}