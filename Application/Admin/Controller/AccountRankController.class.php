<?php
namespace Admin\Controller;

use Think\Controller;

class AccountRankController extends CommonController {

    protected $error = array();
    public $Account = '';
    public $AccountRank = '';

    public function _initialize() {
        parent::_initialize();
        $this->AccountRank = D('AccountRank');
    }

    public function index() {
        //查询条件
        $this->setTitle('会员等级');
        $filter = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'会员等级','href'=>U('account_rank/index'));

        //排序
        $filter['order'] = array( 'sort'=>'DESC', 'id'=>'DESC');
        $lists = $this->AccountRank->getLists($filter);
        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加会员等级');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->AccountRank->create();
            if ($this->AccountRank->add()) {
                session('success','新增成功');
                redirect(U('account_rank/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->AccountRank->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑会员等级');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('account_rank/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->AccountRank->create();
            if ($this->AccountRank->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->AccountRank->getDbError());
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
            $this->error['name'] = '会员等级名不能为空';
        }
        if (!checkNumber(I('post.sort'), 0, 5)) {
            $this->error['sort'] = '排序为数值';
        }
        if (!checkNumber(I('post.min_points'), 0, 8)) {
            $this->error['min_points'] = '积分下限为数值';
        }
        if (!checkNumber(I('post.max_points'), 0, 8)) {
            $this->error['max_points'] = '积分上限为数值';
        }
        if (!checkNumber(I('post.discount'), 0, 3) || (int)I('post.discount')>100) {
            $this->error['discount'] = '折扣为0-100';
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
            if($this->AccountRank->where(array('id'=>array('in',$id)))->delete()!==false){
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
                $data = $this->AccountRank->getDataById($id);
            } else {
                $data['name'] = '';
                $data['min_points'] = '';
                $data['max_points'] = '';
                $data['discount'] = '';
                $data['show_price'] = '';
                $data['special_rank'] = '';
                $data['sort'] = '1';
                $data['status'] = '1';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'会员等级','href'=>U('account_rank/index'));
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('account_rank/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}