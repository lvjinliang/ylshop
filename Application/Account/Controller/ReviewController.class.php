<?php
namespace Account\Controller;
use Think\Controller;
class ReviewController extends CommonController {
    private $error = array();

    public function index() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '待评价', 'href' => 'javascript:void(0)');
        $data = array();
        $filter = array();
        $Review = D('Review');
        $count = $Review->getNotReviewTotal();
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $filter['order'] = 'o.date_added DESC, o.id DESC';
        $data['reviewList'] = $Review->getNotReviewList($filter);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }

    public function had_review () {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '已评价', 'href' => 'javascript:void(0)');
        $data = array();
        $filter = array();
        $Review = D('Review');
        $count = $Review->getReviewTotal($filter);
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $filter['order'] = 'r.date_added DESC, r.id DESC';
        $data['reviewList'] = $Review->getReviewList($filter);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }

    public function add_review () {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '商品评价', 'href' => 'javascript:void(0)');
        $data = array();
        $order_no = I('request.order_no');
        $goods_id = I('request.goods_id');
        $product_sn = I('request.product_sn');
        $score = I('request.score');
        $content = I('request.content');
        $data['order_no'] = $order_no;
        $data['goods_id'] = $goods_id;
        $data['product_sn'] = $product_sn;
        $data['score'] = empty($score)?5:$score;
        $data['content'] = $content;
        $data['success'] = 0;
        if (empty($order_no) || empty($goods_id)) {
            $data['msg'] = '没有订单号和商品id不能评价,返回<a href="'.U('review/index').'">待评价列表</a>';
        } else {
            $Review = D('Review');
            if (!$Review->checkIsMyBuy($order_no, $goods_id, $product_sn) ) {
                //判断是否购买过
                $data['msg'] = '您末购买该商品不能评价,返回<a href="'.U('review/index').'">待评价列表</a>';
            } else {
                if ($Review->checkHadReview($order_no, $goods_id, $product_sn)) {
                    //判断是否评价过
                    $data['msg'] = '该商品已评价不能评价,返回<a href="'.U('review/index').'">待评价列表</a>';
                } else {
                    if (IS_POST && $this->validate_form()) {
                        $resutl = $Review->addReview(I('post.'));
                        if ($resutl) {
                            $data['success'] = 1;
                            $data['msg'] = '评价成功感谢您的评价,返回<a href="'.U('review/index').'">待评价列表</a>';
                        } else {
                            $data['msg'] = '服务器异常，请稍后评价.<a href="'.U('review/index').'">待评价列表</a>';
                        }
                    }
                }
            }
        }



        $this->assign('error', $this->error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display(T('Account@review/add_review'));
    }

    public function validate_form () {
        if (!checkRequire(I('post.score'))) {
            $this->error['score'] = '请评分';
        } else {
            if ( !is_numeric(I('post.score')) || I('post.score')>5 || I('post.score')<1 ) {
                $this->error['score'] = '请评一到五分';
            }
        }



        $content = I('post.content');
        $contentLength = mb_strlen($content,'utf8');
        if (empty($content) || $contentLength<15 || $contentLength>200 ) {
            $this->error['content'] = '评价内容在15个字以上';
        }
        return (empty($this->error)) ? true : false;
    }









}