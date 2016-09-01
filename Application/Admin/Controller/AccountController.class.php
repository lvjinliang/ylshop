<?php
namespace Admin\Controller;

use Think\Controller;

class AccountController extends CommonController {

    protected $error = array();
    public $Account = '';

    public function _initialize() {
        parent::_initialize();
        $this->Account = D('Account');
    }

    public function index() {
        //查询条件
        $this->setTitle('会员管理');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'会员管理','href'=>U('account/index'));

        $content = $this->content();
        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);

        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('content', $content);
        $this->display();
    }

    private function content() {
        $filter = array();
        $search = array();
        if(I('get.id')) {
            $filter['id'] = array('eq', I('get.id'));
            $search['id'] = I('get.id');
        } else {
            $search['id'] = '';
        }

        if(I('get.username')) {
            $filter['username'] = array('like', I('get.username').'%');
            $search['username'] = I('get.username');
        } else {
            $search['username'] = '';
        }

        if(I('get.status')!=='') {
            $filter['status'] = array('eq', I('get.status'));
            $search['status'] = I('get.status');
        } else {
            $search['status'] = '';
        }

        if(I('get.is_validated')!=='') {
            $filter['is_validated'] = array('eq', I('get.is_validated'));
            $search['is_validated'] = I('get.is_validated');
        } else {
            $search['is_validated'] = '';
        }

        //排序
        $filter['order'] = array( 'id'=>'ASC');
        $lists = $this->Account->getLists($filter);

        $this->assign('lists', $lists);
        $this->assign('search', $search);
        return $this->fetch('Account/content');
    }

    public function ajax_get_content() {
        $content = $this->content();
        echo $content;
        exit();

    }

    public function ajax_set_value() {
        $json = array('success'=>0,'href'=>'', 'text'=>'');
        $id = I('get.id');
        $key = I('get.key');

        switch ($key) {
            case 'status':
                $value = I('get.value')==1 ? 0 : 1;
                $json['text'] = userStatusToText($value);
                break;
            case 'is_validated' :
                $value = I('get.value')==1 ? 0 : 1;
                $json['text'] = validatedStatusToText($value);
                break;

        }
        $data['id'] = $id;
        $data[$key] = $value;
        $status = $this->Account->data($data)->save();
        if ($status) {
            $json['href'] = U('account/ajax_set_value',array('id'=>$data['id'],'key'=>$key, 'value'=>$value));
            $json['success'] = 1;
        }
        $this->ajaxReturn($json);
    }

    public function add() {
        $this->setTitle('添加会员等级');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Account->create();
            if ($this->Account->add()) {
                session('success','新增成功');
                redirect(U('account/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Account->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑会员');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('account/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Account->create();
            if (empty($this->Account->password)) {
                unset($this->Account->password);
            } else {
                $this->Account->password = md5(md5($this->Account->password).$this->Account->salt);
            }
            if ($this->Account->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Account->getDbError());
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


    public function form() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->Account->getDataById($id);
            } else {
                $data['username'] = '';
                $data['email'] = '';
                $data['telephone'] = '';
                $data['home_phone'] = '';
                $data['firstname'] = '';
                $data['lastname'] = '';
                $data['money'] = '0';
                $data['status'] = '1';
                $data['is_validated'] = '1';
                $data['birthday'] = '';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'会员管理','href'=>U('account/index'));
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('account/index');
        }
        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}