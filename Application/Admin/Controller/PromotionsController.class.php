<?php
namespace Admin\Controller;

use Think\Controller;

class PromotionsController extends CommonController {

    protected $error = array();
    public $Promotions = '';

    public function _initialize() {
        parent::_initialize();
        $this->Promotions = D('Promotions');
    }

    public function index() {
        $this->setTitle('优惠活动');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'优惠活动','href'=>U('promotions/index'));
        if(I('get.name')) {
            $filter['name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }

        //排序
        $filter['order'] = array( 'sort'=>'DESC', 'id'=>'DESC');

        //分页
        $count = $this->Promotions->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->Promotions->getLists($filter);
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

    public function add() {
        $this->setTitle('添加优惠活动');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Promotions->create();
            if ($this->Promotions->add()) {
                session('success','新增成功');
                redirect(U('promotions/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Promotions->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑优惠活动');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('promotions/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Promotions->create();
            if ($this->Promotions->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Promotions->getDbError());
                $this->form();
            }
        } else {
            $id = I('get.id');
            if (empty($id)) {
                session('error','非法操作');
                redirect($redirect);
            }
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function validate_form() {
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '活动名不能为空';
        }else {
            if (!$this->Promotions->checkUnique('name', I('post.name'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['name'] = '活动名已存在';
            }
        }
        if (!is_numeric(I('post.discount')) || I('post.discount')<0) {
            $this->error['discount'] = '折扣为数字';
        }
        if (!is_numeric(I('post.min_amount')) || I('post.min_amount')<0) {
            $this->error['min_amount'] = '最低消费为数字';
        }
        if (!checkNumber(I('post.sort'), 0, 5)) {
            $this->error['sort'] = '排序为数值';
        }
        return (empty($this->error)) ? true : false;
    }

    public function delete() {
        $json = array('success'=>1,'msg'=>'');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            if($this->Promotions->where(array('id'=>array('in',$id)))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '删除失败';
            }
        }
        $this->ajaxReturn($json);
    }

    public function form() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->Promotions->getDataById($id);
            } else {
                $data['name'] = '';
                $data['type'] = 1;
                $data['d_type'] = 1;
                $data['discount'] = 0;
                $data['start_time'] = '';
                $data['end_time'] = '';
                $data['account_rank'] = array();
                $data['min_amount'] = 0;
                $data['act_range'] = '';
                $data['act_rule'] = '';
                $data['gift_rule'] = '';
                $data['sort'] = 0;
                $data['status'] = 1;
            }
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'优惠活动','href'=>U('promotions/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('promotions/index');
        }
        $AccountRank = D('AccountRank');
        $data['account_ranks'] = $AccountRank->getAll();
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}