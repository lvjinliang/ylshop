<?php
namespace Admin\Controller;

use Think\Controller;

class ReportSiteController extends CommonController {

    private $DayCountVisit = '';

    public function _initialize() {
        parent::_initialize();
        $this->DayCountVisit = D('DayCountVisit');
    }

    public function index() {
        //查询条件
        $this->setTitle('站点统计');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'站点统计','href'=>U('report_site/index'));


        if(I('get.start_date')) {
            $startDate = I('get.start_date');
            $search['start_date'] = I('get.start_date');
        } else {
            $startDate = date('Y-m-d', strtotime('-7 day'));
            $search['start_date'] = $startDate;
        }
        if(I('get.end_date')) {
            $endDate = I('get.end_date');
            $search['end_date'] = I('get.end_date');
        } else {
            $endDate = date('Y-m-d', strtotime('-1 day'));
            $search['end_date'] = $endDate;
        }


        $filter['date'] = array(
                              array('egt',$startDate),
                              array('elt',$endDate)
                          );
        //排序
        $filter['order'] = array( 'date'=>'asc');
        $type = I('get.type') ;
        if(empty($type)) {
            //分页
            $count = $this->DayCountVisit->getTotal($filter);
            $page = setPage($count, C('ADMIN_PAGE_SIZE'));
            $filter['start'] = $page->firstRow;
            $filter['limit'] = $page->listRows;
            $show  = $page->adminShow();// 分页显示输出
            $lists = $this->DayCountVisit->getLists($filter);
        } elseif ($type=='download') {
            $lists = $this->DayCountVisit->getLists($filter,$type='report');
            $xlsName  = "站点统计";
            $xlsCell  = array(
                array('date','日期'),
                array('type','平台'),
                array('pv','PV'),
                array('uv','UV'),
                array('ipv','IPV'),
            );
            vendor("Excel.excel");
            $Excel = new \Excel();
            $Excel->write($xlsName,$xlsCell, $lists);
            exit();
        }
        $downLoadParam = $search;
        $downLoadParam['type'] = 'download';

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('search', $search);
        $this->assign('lists', $lists);
        $this->assign('downloadUrl', U('report_site/index',$downLoadParam));
        $this->display();
    }


}