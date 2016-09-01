<?php
namespace Admin\Controller;

use Think\Controller;

class AreaController extends CommonController {

    protected $error = array();
    public $Area = '';

    public function _initialize() {
        parent::_initialize();
        $this->Area = D('Area');
    }

    public function index() {
        //查询条件
        $this->setTitle('区域管理');
        $filter = array();
        $search = array();
        $pid = I('get.pid')?I('get.pid'):0;

        //获取层级
        $allData = $this->Area->getAll();
        $root = getRoot($allData,$pid);
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'区域管理','href'=>U('area/index'));
        foreach( $root as $key => $val ){
            $breadcrumbs[] = array('title'=>$val['name'], 'href'=>U('area/index',array('pid'=>$val['id'])));
        }
        $filter['a.pid'] = array('eq', $pid);
        if(I('get.name')) {
            $filter['a.name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
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
        $count = $this->Area->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show       = $page->adminShow();// 分页显示输出
        $lists = $this->Area->getLists($filter);

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
        $this->setTitle('添加区域');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Area->create();
            if ($this->Area->add()) {
                session('success','新增成功');
                redirect(U('area/index',array('pid'=>I('post.pid'))));
            } else {
                $this->assign('errorInfo', $this->Area->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑区域');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('area/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            //$redirect = I('post.redirect')?I('post.redirect'):U('area/index');
            $this->Area->create();
            if ($this->Area->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Area->getDbError());
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
            $this->error['name'] = '区域名不能为空';
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
            $allArea = $this->Area->getAll();
            $sonArea = array();

            foreach(explode(',', $id) as $val) {
                $sonArea  = array_merge($sonArea, getLevel($allArea, 2, $val));
            }
            $ids = array();
            foreach($sonArea as $k=>$v){
                $ids[] = $v['id'];
            }
            $ids = implode(',',$ids);
            if($this->Area->where(array('id'=>array('in',$ids)))->delete()!==false) {
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
                $data = $this->Area->getDataById($id);
            } else {
                $data['name'] = '';
                $data['pid'] = I('get.pid')?I('get.pid'):0;
                $data['status'] = '1';
                $data['sort'] = '0';
            }
        }
        $data['p_name'] = $data['pid']?$this->Area->getPnameByPid($data['pid']):'顶级区域';
        //获取层级
        $allData = $this->Area->getAll();
        $root = getRoot($allData,$data['pid']);
        $data['type'] = count($root);

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'区域管理','href'=>U('area/index'));
        foreach( $root as $key => $val ){
            $breadcrumbs[] = array('title'=>$val['name'], 'href'=>U('area/index',array('pid'=>$val['id'])));
        }


        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('area/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}