<?php
namespace Admin\Controller;
use Think\Controller;

class TrashController extends CommonController {
    protected $error = array();
    public $Goods = '';

    public function _initialize() {
        parent::_initialize();
        $this->Goods = D('Goods');
    }

    public function index() {
        $this->setTitle('商品回收');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品回收','href'=>U('trash/trash'));
        $filter['g.is_delete'] = 1;
        if(I('get.name')) {
            $filter['g.name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }

        //排序
        $filter['order'] = array( 'sort'=>'DESC', 'id'=>'DESC');

        //分页
        $count = $this->Goods->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->Goods->getLists($filter);
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
        $this->display();
    }


    public function delete() {
        $json = array('success'=>1,'msg'=>'');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            $EventGoods = A('Goods', 'Event');
            if($EventGoods->delete($id)){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '删除失败';
            }
        }
        $this->ajaxReturn($json);
    }

    public function restore() {
        $json = array('success'=>1,'msg'=>'');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            if( $this->Goods->where(array('id'=>array('in',$id)))
                    ->save(array('is_delete'=>'0'))!==false) {
                $json['success'] = 1;
                $json['msg'] = '还原成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '还原失败';
            }
        }
        $this->ajaxReturn($json);
    }

}