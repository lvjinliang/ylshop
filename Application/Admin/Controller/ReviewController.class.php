<?php
namespace Admin\Controller;

use Think\Controller;

class ReviewController extends CommonController {

    protected $error = array();
    public $Review = '';

    public function _initialize() {
        parent::_initialize();
        $this->Review = D('Review');
    }

    public function index() {
        //查询条件
        $this->setTitle('评价管理');
        $filter = array();
        $search = array();

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'评价管理','href'=>U('review/index'));
        
        if(I('get.id')) {
            $filter['g.id'] = array('eq', I('get.id'));
            $search['id'] = I('get.id');
        } else {
            $search['id'] = '';
        }

        if(I('get.account_id')!=='') {
            $filter['r.account_id'] = array('eq', I('get.account_id'));
            $search['account_id'] = I('get.account_id');
        } else {
            $search['account_id'] = '';
        }

        if(I('get.status')!=='') {
            $filter['r.status'] = array('eq', I('get.status'));
            $search['status'] = I('get.status');
        } else {
            $search['status'] = '';
        }

        //排序
        $filter['order'] = array('r.id'=>'DESC', 'r.date_added'=>'DESC');
        //分页
        $count = $this->Review->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show       = $page->adminShow();// 分页显示输出
        $lists = $this->Review->getLists($filter);
        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        //搜索url
        $this->assign('success', $success);
        $this->assign('error', $error);

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('search', $search);

        $this->assign('show', $show);
        $this->assign('lists', $lists);
        $this->display();
    }

    /*public function add() {
        $this->setTitle('添加评价');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Review->create();
            if ($this->Review->add()) {
                session('success','新增成功');
                redirect(U('review/index',array('pid'=>I('post.pid'))));
            } else {
                $this->assign('errorInfo', $this->Review->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }*/

    public function update() {
        $this->setTitle('编辑评价');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('review/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {

            $this->Review->create();
            $this->Review->date_updated=time();
            $this->Review->reply_date=time();
            if ($this->Review->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Review->getDbError());
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
        return (empty($this->error)) ? true : false;
    }

    public function set_top() {
        $json = array('success'=>0,'href'=>'', 'text'=>'');
        $id = I('get.id');
        $is_top = I('get.is_top');
        $is_top = $is_top==0 ? 1 : 0;
        $data['id'] = $id;
        $data['is_top'] = $is_top;
        $status = $this->Review->data($data)->save();
        if ($status) {
            $json['href'] = U('review/set_top',array('id'=>$data['id'],'is_top'=>$is_top));
            $json['text'] = isTopToText($data['is_top']);
            $json['success'] = 1;
        }
        $this->ajaxReturn($json);
    }

    public function delete() {
        $json = array('success'=>1,'msg'=>'');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            if($this->Review->where(array('id'=>array('in',$id)))->delete()!==false) {
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
                $data = $this->Review->getDataById($id);
            } else {
                $data['order_no'] = '';
                $data['goods_id'] = '';
                $data['product_sn'] = '';
                $data['goods_attr'] = '';
                $data['account_id'] = '';
                $data['author'] = '';
                $data['content'] = '';
                $data['score'] = '';
                $data['status'] = '';
                $data['is_top'] = '';
                $data['status'] = '';
                $data['date_added'] = '';
                $data['date_updated'] = '';
                $data['reply_content'] = '';
                $data['reply_date'] = '';
                $data['reply_user'] = '';
            }
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'评价管理','href'=>U('review/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('review/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}