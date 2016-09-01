<?php
namespace Admin\Controller;

use Think\Controller;

class MenuController extends CommonController {

    protected $error = array();
    public $accessRule = '';

    public function _initialize() {
        parent::_initialize();
        $this->accessRule = D('AccessRule');
    }

    public function index() {
        //查询条件
        $this->setTitle('菜单管理');
        $filter = array();
        $search = array();
        $pid = I('get.pid')?I('get.pid'):0;

        //获取层级
        $allData = $this->accessRule->getAll();
        $root = getRoot($allData,$pid);
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'菜单管理','href'=>U('menu/index'));
        foreach( $root as $key => $val ){
            $breadcrumbs[] = array('title'=>$val['title'], 'href'=>U('menu/index',array('pid'=>$val['id'])));
        }

        $filter['a.pid'] = array('eq', $pid);
        if(I('get.title')) {
            $filter['a.title'] = array('like', I('get.title').'%');
            $search['title'] = I('get.title');
        } else {
            $search['title'] = '';
        }

        if(I('get.status')!=='') {
            $filter['a.status'] = array('eq', I('get.status'));
            $search['status'] = I('get.status');
        } else {
            $search['status'] = '';
        }

        //排序
        $filter['order'] = array('a.sort'=>'DESC', 'a.id'=>'DESC');
        //分页
        $count = $this->accessRule->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show       = $page->adminShow();// 分页显示输出
        $lists = $this->accessRule->getLists($filter);

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        //搜索url
        $searchUrl = __ACTION__.'/pid/'.$pid;
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('pid', $pid);
        $this->assign('level', count($root));
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('search', $search);
        $this->assign('searchUrl', $searchUrl);
        $this->assign('show', $show);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加菜单');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->accessRule->create();
            if ($this->accessRule->add()) {
                session('success','新增成功');
                redirect(U('menu/index',array('pid'=>I('post.pid'))));
            } else {
                $this->assign('errorInfo', $this->accessRule->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑菜单');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('menu/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            //$redirect = I('post.redirect')?I('post.redirect'):U('menu/index');
            $this->accessRule->create();
            if ($this->accessRule->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->accessRule->getDbError());
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
            $this->error['title'] = '标题不能为空';
        } else {
            if (!$this->accessRule->checkUnique('title', I('post.title'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['title'] = '标题名已存在';
            }
        }

        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '规则名不能为空';
        } else {
            if (!$this->accessRule->checkUnique('name', I('post.name'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['name'] = '规则名已存在';
            }
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
            $allRules = $this->accessRule->getAll();
            $sonRulse = array();

            foreach(explode(',', $id) as $val) {
                $sonRulse = array_merge($sonRulse, getLevel($allRules, 2, $val));
            }
            $ids = array();
            foreach($sonRulse as $k=>$v){
                $ids[] = $v['id'];
            }
            $ids = implode(',',$ids);
            if($this->accessRule->where(array('id'=>array('in',$ids)))->delete()!==false) {
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
                $data = $this->accessRule->getDataById($id);
            } else {
                $data['title'] = '';
                $data['type'] = 1;
                $data['name'] = '';
                $data['pid'] = I('get.pid')?I('get.pid'):0;
                $data['is_show'] = '1';
                $data['status'] = '1';
                $data['sort'] = '0';
                $data['condition'] = '';
            }
        }
        $data['p_title'] = $data['pid']?$this->accessRule->getPtitleByPid($data['pid']):'顶级菜单';
        //获取层级
        $allData = $this->accessRule->getAll();
        $root = getRoot($allData,$data['pid']);

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'菜单管理','href'=>U('menu/index'));
        foreach( $root as $key => $val ){
            $breadcrumbs[] = array('title'=>$val['title'], 'href'=>U('menu/index',array('pid'=>$val['id'])));
        }


        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('menu/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}