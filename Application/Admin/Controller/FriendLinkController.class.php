<?php
namespace Admin\Controller;

use Think\Controller;

class FriendLinkController extends CommonController {

    protected $error = array();
    public $FriendLink = '';

    public function _initialize() {
        parent::_initialize();
        $this->FriendLink = D('FriendLink');
    }

    public function index() {
        //查询条件
        $this->setTitle('友情链接');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'友情链接','href'=>U('friend_link/index'));
        if(I('get.name')) {
            $filter['name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }

        //排序
        $filter['order'] = array( 'sort'=>'DESC', 'id'=>'DESC');

        //分页
        $count = $this->FriendLink->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->FriendLink->getLists($filter);

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
        $this->setTitle('添加友情链接');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->FriendLink->create();
            if ($this->FriendLink->add()) {
                session('success','新增成功');
                redirect(U('friend_link/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->FriendLink->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑友情链接');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('friend_link/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->FriendLink->create();
            if ($this->FriendLink->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->FriendLink->getDbError());
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
            $this->error['name'] = '链接名不能为空';
        }

        if (!checkRequire(I('post.url'))) {
            $this->error['url'] = '链接不能为空';
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
            if($this->FriendLink->where(array('id'=>array('in',$id)))->delete()!==false){
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
            $data['logo'] = empty($data['logo'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['logo'];
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->FriendLink->getDataById($id);
                $data['logo'] = empty($data['logo'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['logo'];
            } else {
                $data['name'] = '';
                $data['logo'] = PUBLIC_PATH.'Admin/images/no_image-100x100.png';
                $data['content'] = '';
                $data['site_url'] = '';
                $data['sort'] = '1';
                $data['status'] = '1';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'品牌管理','href'=>U('friend_link/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('friend_link/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}