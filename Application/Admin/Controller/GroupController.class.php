<?php
namespace Admin\Controller;

use Think\Controller;

class GroupController extends CommonController {
    protected $error = array();
    public $Group = '';

    public function _initialize() {
        parent::_initialize();
        $this->Group = D('Group');
    }

    public function index() {
        //查询条件
        $this->setTitle('角色管理');
        $filter = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '角色管理', 'href' => U('group/index'));

        //排序
        $filter['order'] = array('sort' => 'DESC', 'id' => 'DESC');

        //分页
        $count = $this->Group->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show = $page->adminShow(); // 分页显示输出
        $lists = $this->Group->getLists($filter);

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加角色');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Group->create();
            if ($this->Group->add()) {
                session('success','新增成功');
                redirect(U('group/index',array('pid'=>I('post.pid'))));
            } else {
                $this->assign('errorInfo', $this->Group->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑角色');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('group/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Group->create();
            if ($this->Group->save() !== false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Group->getDbError());
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
        if (!checkRequire(I('post.title'))) {
            $this->error['title'] = '角色名不能为空';
        } else {
            if (!$this->Group->checkUnique('title', I('post.title'), array('id' => array('neq', I('post.id'))))) {
                $this->error['title'] = '角色名已存在';
            }
        }

        if (!checkNumber(I('post.sort'), 0, 5)) {
            $this->error['sort'] = '排序为数值';
        }

        return (empty($this->error)) ? true : false;
    }

    public function delete() {
        $json = array('success' => 1, 'msg' => '');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            if ($this->Group->where(array('id' => array('in', $id)))->delete() !== false) {
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
                $data = $this->Group->getDataById($id);
            } else {
                $data['title'] = '';
                $data['status'] = '1';
                $data['sort'] = '0';
                $data['description'] = '';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '角色管理', 'href' => U('group/index'));

        $redirect = I('post.redirect');
        if (!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('group/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }

    public function authuser() {
        $this->setTitle('授权成员列表');
        $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('group/index');
        $gid = I('get.gid');
        if (empty($gid)) {
            session('error','非法操作');
            redirect($redirect);
        }
        $groupUser = D('GroupUser');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '角色管理', 'href' => U('group/index'));
        if (I('get.name')) {
            $filter['au.name'] = array('like', I('get.name') . '%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }
        if (I('get.id')) {
            $filter['au.id'] = array('eq', I('get.id'));
            $search['id'] = I('get.id');
        } else {
            $search['id'] = '';
        }
        $filter['gu.group_id'] = array('eq', $gid);
        //排序
        $filter['order'] = array('au.id' => 'DESC');

        //分页
        $count = $groupUser->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show = $page->adminShow(); // 分页显示输出
        $lists = $groupUser->getLists($filter);
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

    public function authuser_add() {
        $this->setTitle('添加授权成员');
        $gid = I('get.gid');
        $uid = I('post.uid');
        $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('group/index');
        if (IS_POST && !empty($uid) && !empty($gid) && is_numeric($gid)) {
            $uid = explode(',', trim($uid));
            $data = array();
            foreach($uid as $v ){
                $data[] = array('group_id'=>$gid, 'uid'=>$v);
            }
            $json = array('success' => 1, 'msg' => '');
            $groupUser = D('GroupUser');
            foreach($data as $v){
                if ($groupUser->isAuthUser($data)) {
                    $json['success'] = 0;
                    $json['msg'] = '用户已授权';
                    $this->ajaxReturn($json);
                    exit();
                    break;
                }
            }

            $groupUser->addAll($data);
            $json['success'] = 1;
            $json['msg'] = '授权成功';
            $this->ajaxReturn($json);
            exit();
        }
        if (empty($gid)) {
            session('error','非法操作');
            redirect($redirect);
        }
        //查询条件
        $filter = array();
        $search = array();
        $adminUser = D('AdminUser');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '角色管理', 'href' => U('group/index'));
        $breadcrumbs[] = array('title' => '授权用户列表', 'href' =>U('group/authuser', array('gid'=>$gid)));
        if (I('get.name')) {
            $filter['name'] = array('like', I('get.name') . '%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }
        if (I('get.id')) {
            $filter['id'] = array('eq', I('get.id'));
            $search['id'] = I('get.id');
        } else {
            $search['id'] = '';
        }
        $filter['status'] = array('eq', 1);
        $filter['status'] = array('eq', 1);
        $filter['_string'] = " NOT EXISTS (
                                  SELECT 1 FROM " . C('DB_PREFIX') . "group_user
                                  WHERE group_id='{$gid}'
                                  AND uid =" . C('DB_PREFIX') . "admin_user.id
                               )";

        //排序
        $filter['order'] = array('id' => 'DESC');

        //分页
        $count = $adminUser->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show = $page->adminShow(); // 分页显示输出
        $lists = $adminUser->getLists($filter);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('search', $search);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function authuser_delete() {
        $json = array('success' => 1, 'msg' => '');
        $uid = I('post.uid');
        $group_id = I('get.gid');
        if (empty($uid) || empty($group_id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            $groupUser = D('GroupUser');
            $condition['uid'] = array('in', $uid);
            $condition['group_id'] = array('eq', $group_id);
            if ($groupUser->where($condition)->delete() !== false) {
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '删除失败';
            }
        }
        $this->ajaxReturn($json);
    }

    public function authaccess() {
        $this->setTitle('访问授权');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('group/index');
        }
        $gid = I('get.gid');
        if (empty($gid)) {
            session('error','非法操作');
            redirect($redirect);
        }
        $Group = D('Group');
        if(IS_POST){
            $rules = implode(',',I('post.rules'));
            $result = $Group->updateRulesById($gid, array('rules'=>$rules));
            if($result){
                session('success','授权成功');
                redirect($redirect);
            }
            exit();
        }


        $rules = $Group->getRulesById($gid);
        if (is_null($rules)) {
            session('error','要设置的管理组不存在');
            redirect($redirect);
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '角色管理', 'href' => U('group/index'));

        $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('group/index');


        $data = array();
        $data['rules'] = explode(',',$rules);
        $accessRule = D('AccessRule');
        $data['menus'] = getLevel($accessRule->getAll());

        $this->assign('data', $data);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('redirect', $redirect);
        $this->display();

    }
}