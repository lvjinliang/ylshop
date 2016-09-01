<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends CommonController {
    protected $error = array();
    public $Goods = '';

    public function _initialize() {
        parent::_initialize();
        $this->Goods = D('Goods');
    }

    public function index() {
        $this->setTitle('商品列表');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品列表','href'=>U('goods/index'));
        $filter['g.is_delete'] = 0;
        if(I('get.name')) {
            $filter['g.name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }
        if(I('get.category_id')) {
            $filter['gc.category_id'] = array('eq', I('get.category_id'));
            $search['category_id'] = I('get.category_id');
        } else {
            $search['category_id'] = '';
        }
        if(I('get.brand_id')) {
            $filter['g.brand_id'] = array('eq', I('get.brand_id'));
            $search['brand_id'] = I('get.brand_id');
        } else {
            $search['brand_id'] = '';
        }
        if(I('get.position_id')) {
            $filter['pg.position_id'] = array('eq', I('get.position_id'));
            $search['position_id'] = I('get.position_id');
        } else {
            $search['position_id'] = '';
        }
        //商品分类
        $Category = D('Category');
        $Brand = D('Brand');
        $Position = D('Position');
        $data['categorys'] = $Category->getCategoryLevel();
        //品牌
        $data['brands'] = $Brand->getAll();
        //推荐位
        $data['positions'] = $Position->getAll();

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
        $this->assign('data', $data);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加商品');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Goods->create();
            if ($this->Goods->addGoods(I('post.'))) {
                session('success','新增成功');
                redirect(U('goods/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Goods->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            if(!empty($this->error)) {
                $this->assign('errorInfo', '请填写完商品信息');
            }
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑商品');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('goods/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Goods->create();
            if ($this->Goods->saveGoods(I('post.'))!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Goods->getDbError());
                $this->form();
            }
        } else {
            $id = I('get.id');
            if (empty($id)) {
                session('error','非法操作');
                redirect($redirect);
            }
            if(!empty($this->error)) {
                $this->assign('errorInfo', '请填写完商品信息');
            }
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function validate_form() {
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '商品名不能为空';
        } else {
            if (!$this->Goods->checkUnique('name', I('post.name'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['name'] = '商品名已存在';
            }
        }
        if (!checkRequire(I('post.goods_sn'))) {
            $this->error['goods_sn'] = '商品货号不能为空';
        } else {
            if (!$this->Goods->checkUnique('goods_sn', I('post.goods_sn'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['goods_sn'] = '商品货号已存在';
            }
        }
        $category_ids = I('post.category_id');
        if (empty($category_ids)) {
            $this->error['category_id'] = '请选择商品分类';
        }
        $brand_id = I('post.brand_id');
        if (empty($brand_id)) {
            $this->error['brand_id'] = '请选择商品品牌';
        }
        $goods_type_id = I('post.goods_type_id');
        if (empty($goods_type_id)) {
            $this->error['goods_type_id'] = '请选择商品类型';
        }
        $suppliers_id = I('post.suppliers_id');
        if (empty($suppliers_id)) {
            $this->error['suppliers_id'] = '请选择商品供应商';
        }

        if (!is_numeric(I('post.price'))) {
            $this->error['price'] = '价格为数字';
        }

        if (!is_numeric(I('post.give_integral'))) {
            $this->error['give_integral'] = '赠送积分数为整型';
        }

        if (!is_numeric(I('post.integral'))) {
            $this->error['integral'] = '积分购买金额为整型';
        }

        $promote_price = I('post.promote_price');
        if(!empty($promote_price)){
            if (!is_numeric($promote_price)) {
                $this->error['promote_price'] = '促销价格为数字';
            }
        }

        if (!checkNumber(I('post.number'), 0, 5)) {
            $this->error['number'] = '库存为数值';
        }

        if (!checkNumber(I('post.warn_number'), 0, 5)) {
            $this->error['warn_number'] = '警告库存为数值';
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
            if( $this->Goods->where(array('id'=>array('in',$id)))
                            ->save(array('is_delete'=>'1'))!==false) {
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
        $attribute = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
            $data['thumb'] = empty($data['thumb'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['thumb'];
            if(!isset($data['position'])) {
                $data['position'] = array();
            }
            if(!isset($data['is_primary'])) {
                $data['is_primary'] = '';
            }
            $data['id'] =isset($data['id'])?$data['id']:'';
            $attribute['attr_id'] = array();
            $attribute['attr_price'] = array();
            if(isset($data['attr_id'])) {
                $attribute['attr_id'] = $data['attr_id'];
            }
            if(isset($data['attr_price'])) {
                $attribute['attr_price'] = $data['attr_price'];
            }
            if(!isset($data['category_id'])) {
                $data['category_id'] = array();
            }

            if(!isset($data['image'])) {
                $data['image'] = array();
            }

        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->Goods->getDataById($id);
                $data['thumb'] = empty($data['thumb'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['thumb'];
                //$data['position'] =
            } else {
                $GoodsFields = $this->Goods->getDbFields();
                foreach($GoodsFields as $val){
                    $data[$val] = '';
                }
                $data['give_integral'] = '-1';
                $data['integral'] = '-1';
                $data['position'] = array();
                $data['thumb'] = PUBLIC_PATH.'Admin/images/no_image-100x100.png';
                $data['category_id'] = array();
                $data['image'] = array();
            }
        }
        $data['attribute_html'] = '';

        if(!empty($data['goods_type_id'])) {
            $data['attribute_html'] = $this->get_attr($data['id'],$data['goods_type_id'],$attribute);
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品列表','href'=>U('goods/index'));
        $Category = D('Category');
        $Brand = D('Brand');
        $Suppliers = D('Suppliers');
        $GoodsType = D('GoodsType');
        $Position = D('Position');
        //商品分类
        $data['categorys'] = $Category->getCategoryLevel();
        //品牌
        $data['brands'] = $Brand->getAll();
        //供应商
        $data['suppliers'] = $Suppliers->getAll();
        //商品类型
        $data['goodsTypes'] = $GoodsType->getAll();
        //推荐位
        $data['positions'] = $Position->getAll();

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('goods/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }

    public function product_list() {
        $goods_id = I('get.id');
        $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('goods/index');
        if (empty($goods_id)) {
            session('error','非法操作');
            redirect($redirect);
        }
        $data['goods_id'] = $goods_id;
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品列表','href'=>U('goods/index'));
        $data['attrPrice'] = $this->Goods->getAttrPriceByGoodsId($goods_id);
        $products = $this->Goods->getProductsByGoodsId($goods_id);
        foreach($data['attrPrice']['value'] as $key=>$val){
            $keys = array_keys($val);
            $values = array_values($val);
            $data['attrPrice']['attr_ids'][$key] = implode(',', $keys);
            $data['attrPrice']['attr_value'][$key] = implode(',', $values);
            foreach($products as $k => $v){
                $keyIntersect = array_intersect($keys, explode(',', $v['attr_ids']));
                $valIntersect = array_intersect($values, explode(',', $v['attr_value']));
                if(count($keyIntersect)==count($keys) && count($valIntersect)==count($values)) {
                    $data['attrPrice']['product_sn'][$key] = $v['product_sn'];
                    $data['attrPrice']['product_number'][$key] = $v['product_number'];
                    break;
                } else {
                    $data['attrPrice']['product_sn'][$key] = '';
                    $data['attrPrice']['product_number'][$key] = '';
                }
            }
        }

        $this->assign('redirect', $redirect);
        $this->assign('data', $data);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display();
    }

    public function add_product() {
        $json = array('success'=>0,'msg'=>'');
        $product_sn = I('post.product_sn');
        $product_number = I('post.product_number');
        foreach( $product_sn as $key => $val ){
            if(empty($val)) {
                $json['msg'] = '请完善属性货号';
                $this->ajaxReturn($json);
                exit();
            }
        }
        if(count(array_unique($product_sn)) !== count($product_sn)){
            $json['msg'] = '属性货号重复';
            $this->ajaxReturn($json);
        }
        foreach( $product_number as $key => $val ){
            if (!checkNumber($val, 0, 5)) {
                $json['msg'] = '库存为数字';
                $this->ajaxReturn($json);
                exit();
            }
        }
        $json = $this->Goods->addProduct(I('post.'));
        $this->ajaxReturn($json);
        exit();
    }

    //获取属性
    public function ajax_get_attr($goodsId='', $goodsTypeId='') {
        $goodsId = $goodsId?$goodsId:I('post.goodsId');
        $goodsTypeId = $goodsTypeId?$goodsTypeId:I('post.goodsTypeId');
        echo $this->get_attr($goodsId, $goodsTypeId );
    }

    //获取属性
    private function get_attr($goodsId='', $goodsTypeId='',$attribute=array()) {
        $data = array();
        if($goodsTypeId){
            $Attr = D('Attribute');
            $data['allAttr'] = $Attr->getAttrByTypeId($goodsTypeId);
            $data['allAttrValue'] = array();
            if(!empty($attribute)) {
                foreach($attribute['attr_id'] as $key=>$val) {
                    foreach($val as $k => $v) {
                        $data['allAttrValue'][$key][$k] = array(
                            'attr_value'=>$v,
                            'attr_price'=>$attribute['attr_price'][$key][$k]
                        );
                    }
                }
            } else {
                foreach($data['allAttr'] as $val) {
                    $data['allAttrValue'][$val['id']][] = array('attr_value'=>'','attr_price'=>'');
                }
                if($goodsId) {
                    $GoodsAttr = D('GoodsAttr');
                    $data['goodsAttrValue'] = $GoodsAttr->getAttrValueByGoodsId($goodsId,$goodsTypeId);

                    if(!empty($data['goodsAttrValue'])){
                        foreach($data['allAttrValue'] as $key => $val) {
                            if(isset($data['goodsAttrValue'][$key])){
                                $data['allAttrValue'][$key] = $data['goodsAttrValue'][$key];
                            }
                        }
                    }


                }
            }

            $this->assign('data', $data);
            return $this->fetch('ajax_get_attr');
        } else {
            return '';
        }
    }
}