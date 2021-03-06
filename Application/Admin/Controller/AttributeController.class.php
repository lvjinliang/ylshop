<?php
namespace Admin\Controller;

use Think\Controller;

class AttributeController extends CommonController {

    protected $error = array();
    public $Attribute = '';

    public function _initialize() {
        parent::_initialize();
        $this->Attribute = D('Attribute');
    }

    public function index() {
        //查询条件
        $this->setTitle('商品属性');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '商品属性', 'href' => U('Attribute/index'));
        if (I('get.type_id') !== '') {
            $filter['a.type_id'] = array('eq', I('get.type_id'));
            $search['type_id'] = I('get.type_id');
        } else {
            $search['type_id'] = '';
        }
        $addLink = $search['type_id'] ? U('attribute/add', array('type_id' => $search['type_id'])) : U('attribute/add');

        if (I('get.name')) {
            $filter['a.name'] = array('eq', I('get.name'));
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }

        //排序
        $filter['order'] = array('sort' => 'DESC', 'id' => 'DESC');

        //分页
        $count = $this->Attribute->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show = $page->adminShow(); // 分页显示输出
        $lists = $this->Attribute->getLists($filter);

        //商品类型
        $GoodsType = D('GoodsType');
        $goodsTypes = $GoodsType->getAll();

        $success = session('?success') ? session('success') : false;
        session('success', null);
        $error = session('?error') ? session('error') : false;
        session('error', null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('goodsTypes', $goodsTypes);
        $this->assign('addLink', $addLink);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('search', $search);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加商品属性');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Attribute->create();
            if ($this->Attribute->add()) {
                session('success', '新增成功');
                redirect(U('Attribute/index', array('pid' => I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Attribute->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑商品属性');
        $redirect = I('post.redirect');
        if (!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Attribute/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Attribute->create();
            if ($this->Attribute->save() !== false) {
                session('success', '修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Attribute->getDbError());
                $this->form();
            }
        } else {
            $id = I('get.id');
            if (empty($id)) {
                session('error', '非法操作');
                redirect($redirect);
            }
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function validate_form() {
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '属性名不能为空';
        }
        if (!checkRequire(I('post.type_id'))) {
            $this->error['type_id'] = '请选择商品类型';
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
            if ($this->Attribute->where(array('id' => array('in', $id)))->delete() !== false) {
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '有关联数据不能删除';
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
                $data = $this->Attribute->getDataById($id);
            } else {
                $data['name'] = '';
                $data['type_id'] = I('get.type_id') ? I('get.type_id') : '';
                $data['type'] = '';
                $data['input_type'] = '';
                $data['sort'] = '1';
                $data['values'] = '';
                $data['index'] = '1';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '商品属性', 'href' => U('Attribute/index'));
        //商品类型
        $GoodsType = D('GoodsType');
        $goodsTypes = $GoodsType->getAll();
        $redirect = I('post.redirect');
        if (!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Attribute/index');
        }

        $this->assign('data', $data);
        $this->assign('goodsTypes', $goodsTypes);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }

    public function ajax_get_attr_by_type_id() {
        $data = array('success'=>0,'info'=>'');
        $typeId = I('get.type_id');
        if($typeId) {
            $data['success'] = 1;
            $data['info'] = $this->Attribute->getSearchAttriByTypeId($typeId);
        }
        $this->ajaxReturn($data);
    }
}