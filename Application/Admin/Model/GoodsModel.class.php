<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GoodsModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function'),
        array('promote_start_date', 'changeTime', 3, 'callback'),
        array('promote_end_date', 'changeTime', 3, 'callback'),
    );

    public function changeTime() {
        $date = func_get_arg(0);
        if (!empty($date)) {
            $date = strtotime($date);
        } else {
            $date = '';
        }
        return $date;
    }

    public function addGoods($data) {
        $goods_id = $this->add();
        if ($goods_id) {
            //添加分类
            if (!empty($data['category_id'])) {
                $GoodsCategory = D('GoodsCategory');
                $dataCategory = array();
                foreach ($data['category_id'] as $key => $val) {
                    $dataCategory[$key]['goods_id'] = $goods_id;
                    $dataCategory[$key]['category_id'] = $val;
                    if ($val == $data['is_primary']) {
                        $dataCategory[$key]['is_primary'] = 1;
                    } else {
                        $dataCategory[$key]['is_primary'] = 0;
                    }
                }
                $GoodsCategory->addAll($dataCategory);
            }

            //添加商品属性
            if (!empty($data['attr_id'])) {
                $GoodsAttr = D('GoodsAttr');
                $dataAttr = array();
                $attrValue = '';
                $i = 0;
                foreach ($data['attr_id'] as $key => $val) {
                    if (!empty($val)) {
                        foreach ($val as $k => $v) {
                            if(!empty($v)) {
                                $attrValue .= $v.',';
                                $dataAttr[$i]['attr_value'] = $v;
                                $dataAttr[$i]['attr_id'] = $key;
                                if (isset($data['attr_price'][$key][$k])) {
                                    $dataAttr[$i]['attr_price'] = $data['attr_price'][$key][$k];
                                } else {
                                    $dataAttr[$i]['attr_price'] = 0;
                                }
                                $dataAttr[$i]['goods_id'] = $goods_id;
                                $i++;
                            }
                        }
                    }

                }
                $GoodsAttr->addAll($dataAttr);
            }

            //添加推荐位
            if (!empty($data['position'])) {
                $PositionGoods = D('PositionGoods');
                $dataPosition = array();
                foreach ($data['position'] as $key => $val) {
                    $dataPosition[$key]['goods_id'] = $goods_id;
                    $dataPosition[$key]['position_id'] = $val;
                }
                $PositionGoods->addAll($dataPosition);
            }

            //添加商品相册
            if (!empty($data['image'])) {
                $GoodsGallery = D('GoodsGallery');
                $dataGallery = array();
                foreach ($data['image'] as $key => $val) {
                    $dataGallery[$key]['goods_id'] = $goods_id;
                    $dataGallery[$key]['url'] = $val;
                    $dataGallery[$key]['title'] = $data['image_title'][$key];
                    $dataGallery[$key]['sort'] = $data['image_sort'][$key];
                }
                $GoodsGallery->addAll($dataGallery);
            }

            //生成搜索
            $brandName = D('Brand')->getNameById( $data['brand_id']);
            $keywords = $data['name'].' ';
            $keywords .= str_replace(',',' ', preg_replace('/\s/','',implode(',', $data['category_name']))).' ';
            $keywords .= $brandName.' ';
            $keywords .= str_replace(',', ' ' , $attrValue).' ';
            $keywords .= str_replace(array(',','|'), ' ' , $data['keywords']);
            $sql = "REPLACE INTO __SEARCH__ SET
                    goods_id='{$goods_id}',
                    keywords='{$keywords}'";
            $Search = M('Search');
            $Search->execute($sql);
        }
        return $goods_id;
    }

    public function saveGoods($data) {
        $result = $this->save();

        if ($result !== false) {
            //添加分类
            $GoodsCategory = D('GoodsCategory');
            $GoodsCategory->where(array('goods_id' => $data['id']))->delete();
            if (!empty($data['category_id'])) {
                $dataCategory = array();
                foreach ($data['category_id'] as $key => $val) {
                    $dataCategory[$key]['goods_id'] = $data['id'];
                    $dataCategory[$key]['category_id'] = $val;
                    if ($val == $data['is_primary']) {
                        $dataCategory[$key]['is_primary'] = 1;
                    } else {
                        $dataCategory[$key]['is_primary'] = 0;
                    }
                }
                $GoodsCategory->addAll($dataCategory);
            }

            //添加商品属性
            $GoodsAttr = D('GoodsAttr');
            $GoodsAttr->where(array('goods_id' => $data['id']))->delete();
            if (!empty($data['attr_id'])) {
                $dataAttr = array();
                $i = 0;
                $attrValue = '';
                foreach ($data['attr_id'] as $key => $val) {
                    if (!empty($val)) {
                        foreach ($val as $k => $v) {
                            if(!empty($v)) {
                                $attrValue .= $v.',';
                                $dataAttr[$i]['attr_id'] = $key;
                                $dataAttr[$i]['attr_value'] = $v;
                                if (isset($data['attr_price'][$key][$k])) {
                                    $dataAttr[$i]['attr_price'] = $data['attr_price'][$key][$k];
                                } else {
                                    $dataAttr[$i]['attr_price'] = 0;
                                }
                                $dataAttr[$i]['goods_id'] = $data['id'];
                                $i++;
                            }
                        }
                    }
                }
                $GoodsAttr->addAll($dataAttr);
            }

            //添加推荐位
            $PositionGoods = D('PositionGoods');
            $PositionGoods->where(array('goods_id' => $data['id']))->delete();
            if (!empty($data['position'])) {
                $dataPosition = array();
                foreach ($data['position'] as $key => $val) {
                    $dataPosition[$key]['goods_id'] = $data['id'];
                    $dataPosition[$key]['position_id'] = $val;
                }
                $PositionGoods->addAll($dataPosition);
            }

            //添加商品相册
            $GoodsGallery = D('GoodsGallery');
            $GoodsGallery->where(array('goods_id' => $data['id']))->delete();
            if (!empty($data['image'])) {
                $dataGallery = array();
                foreach ($data['image'] as $key => $val) {
                    $dataGallery[$key]['goods_id'] = $data['id'];
                    $dataGallery[$key]['url'] = $val;
                    $dataGallery[$key]['title'] = $data['image_title'][$key];
                    $dataGallery[$key]['sort'] = $data['image_sort'][$key];
                }
                $GoodsGallery->addAll($dataGallery);
            }
            //生成搜索
            $brandName = D('Brand')->getNameById( $data['brand_id']);
            $keywords = $data['name'].' ';
            $keywords .= str_replace(',',' ', preg_replace('/\s/','',implode(',', $data['category_name']))).' ';
            $keywords .= $brandName.' ';
            $keywords .= str_replace(',', ' ' , $attrValue).' ';
            $keywords .= str_replace(array(',','|'), ' ' , $data['keywords']);
            $sql = "REPLACE INTO __SEARCH__ SET
                    goods_id='{$data['id']}',
                    keywords='{$keywords}'";
            $Search = M('Search');
            $Search->execute($sql);
        }
        return $result;
    }

    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->limit(0, 1)->find();
        if (!empty($data['promote_start_date'])) {
            $data['promote_start_date'] = date('Y-m-d', $data['promote_start_date']);
        }
        if (!empty($data['promote_end_date'])) {
            $data['promote_end_date'] = date('Y-m-d', $data['promote_end_date']);
        }
        //分类
        $GoodsCategory = D('GoodsCategory');
        $category = $GoodsCategory->getCategoryByGoodsId($id);
        $data['category_id'] = $category['category_ids'];
        $data['is_primary'] = $category['is_primary'];
        $data['category_name'] = $category['category_name'];
        //推荐位
        $PositionGoods = D('PositionGoods');
        $data['position'] = $PositionGoods->getPositionByGoodsId($id);
        //相册
        $GoodsGallery = D('GoodsGallery');
        $gallerys = $GoodsGallery->getGalleryByGoodsId($id);
        $data['image'] = $gallerys['url'];
        $data['image_title'] = $gallerys['title'];
        $data['image_sort'] = $gallerys['sort'];
        return $data;
    }



    public function getLists($filter = array()) {
        //设置分页
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;

        //设置排序
        $order = isset($filter['order']) ? $filter['order'] : array();
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->field('g.*, GROUP_CONCAT(p.name) position_name,c.name category_name, b.name brand_name')
                    ->alias('g')
                    ->join("LEFT JOIN __GOODS_CATEGORY__ gc ON g.id=gc.goods_id AND gc.is_primary=1")
                    ->join("LEFT JOIN __CATEGORY__ c ON gc.category_id=c.id")
                    ->join("LEFT JOIN __POSITION_GOODS__ pg ON g.id=pg.goods_id")
                    ->join("LEFT JOIN __POSITION__ p ON pg.position_id = p.id")
                    ->join("LEFT JOIN __BRAND__ b ON g.brand_id=b.id")
                    ->where($filter)
                    ->group('g.id')
                    ->order($order)
                    ->limit($start, $limit)
                    ->select();
        $goodsIds = array();
        foreach ($result as $val) {
            $goodsIds[] = $val['id'];
        }
        if (!empty($goodsIds)) {
            $isAttrsPrice = $this->isAttrPriceByGoodsIds($goodsIds);
            foreach ($result as $key => $val) {
                if (in_array($val['id'], $isAttrsPrice)) {
                    $result[$key]['isAttrPrice'] = true;
                } else {
                    $result[$key]['isAttrPrice'] = false;
                }
            }
        }
        return $result;
    }

    public function getSearchKeysLists($filter = array()) {
        //设置分页
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;

        //设置排序
        $order = isset($filter['order']) ? $filter['order'] : array();
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->field('g.id, g.name,g.keywords, GROUP_CONCAT(ga.attr_value) attr_value,c.name category_name, b.name brand_name')
            ->alias('g')
            ->join("LEFT JOIN __GOODS_CATEGORY__ gc ON g.id=gc.goods_id AND gc.is_primary=1")
            ->join("LEFT JOIN __CATEGORY__ c ON gc.category_id=c.id")
            ->join("LEFT JOIN __BRAND__ b ON g.brand_id=b.id")
            ->join("LEFT JOIN __GOODS_ATTR__ ga ON g.id=ga.goods_id")
            ->where($filter)
            ->group('g.id')
            ->order($order)
            ->limit($start, $limit)
            ->select();
        $goodsIds = array();
        foreach ($result as $val) {
            $goodsIds[] = $val['id'];
        }

        return $result;
    }

    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $subSql = $this->field('g.id')
            ->alias('g')
            ->join("LEFT JOIN __GOODS_CATEGORY__ gc ON g.id=gc.goods_id AND gc.is_primary=1")
            ->join("LEFT JOIN __CATEGORY__ c ON gc.category_id=c.id")
            ->join("LEFT JOIN __POSITION_GOODS__ pg ON g.id=pg.goods_id")
            ->join("LEFT JOIN __POSITION__ p ON pg.position_id = p.id")
            ->join("LEFT JOIN __BRAND__ b ON g.brand_id=b.id")
            ->where($filter)
            ->group('g.id')
            ->select(false);
        $sql = "SELECT count(*) total  FROM (" . $subSql . ") A ";
        $resutl = $this->query($sql);

        return $resutl[0]['total'];
    }

    //获取商品是否是可选属性商品
    public function isAttrPriceByGoodsIds($goodsIds) {
        if (is_array($goodsIds)) {
            $goodsIds = implode(',', $goodsIds);
        }
        $sql = "SELECT ga.goods_id
                FROM __PREFIX__goods_attr ga
                LEFT JOIN __PREFIX__attribute a
                ON ga.attr_id=a.id
                WHERE ga.goods_id in({$goodsIds}) AND a.type=2
                GROUP BY ga.goods_id";
        $data = $this->query($sql);
        $goodsIds = array();
        foreach ($data as $val) {
            $goodsIds[] = $val['goods_id'];
        }
        return $goodsIds;
    }

    public function getProductsByGoodsId($goods_id) {
        $sql = "SELECT * FROM __PREFIX__products WHERE goods_id='{$goods_id}'";
        $data = $this->query($sql);
        /*$products = array();
        print_r($data);
        foreach($data as $key => $val ) {
            $products[$val['attr_ids']][$val['attr_value']] = $val;
        }*/
        return $data;
    }

    public function getAttrPriceByGoodsId($goodsId) {
        $sql = "SELECT ga.goods_id,a.name attr_name,ga.attr_id,ga.attr_value
                FROM __PREFIX__goods_attr ga
                INNER JOIN __PREFIX__attribute a
                ON ga.attr_id=a.id
                WHERE ga.goods_id = '{$goodsId}' AND a.type=2
                ORDER BY a.sort DESC, a.id DESC";
        $data = $this->query($sql);
        $products = array();

        foreach ($data as $key => $val) {
            $products['title'][$val['attr_id']]['attr_name'] = $val['attr_name'];
            $products['value'][$val['attr_id']][] = $val['attr_value'];
        }
        $attr_ids = array_keys($products['title']);
        $products['value'] = $this->zuhe($products['value']);
        $products['value'] = $products['value'][0];

        foreach ($products['value'] as $key => $val) {
            $values = explode(',', $val);
            $value = array();
            foreach ($values as $k => $v) {
                $value[$attr_ids[$k]] = $v;
            }
            $products['value'][$key] = $value;
        }
        return $products;

    }

    public function addProduct($data) {
        $json = array('success'=>0, 'msg'=>'');
        $Products = D('Products');
        $Products->where(array('goods_id' => $data['goods_id']))->delete();
        $dataProducts = array();
        foreach ($data['attr_ids'] as $key => $val) {
            $dataProducts[$key]['attr_ids'] = $val;
            $dataProducts[$key]['attr_value'] = $data['attr_value'][$key];
            $dataProducts[$key]['product_sn'] = $data['product_sn'][$key];
            $dataProducts[$key]['product_number'] = $data['product_number'][$key];
            $dataProducts[$key]['goods_id'] = $data['goods_id'];
            if(!$this->checkProductSnUnique($data['product_sn'][$key]) ) {
                $json['msg'] = '属性货号'.$data['product_sn'][$key].'已存在';
                return $json;
            }
        }
        $result = $Products->addAll($dataProducts);
        if($result) {
            $json['success'] = 1;
            $json['msg'] = '添加成功';
        } else {
            $json['msg'] = '添加失败';
        }
        return $json;
    }

    public function checkProductSnUnique($product_sn) {
        $condition['product_sn'] = $product_sn;
        $Products = D('Products');
        $result = $Products->where($condition)->count('product_sn');

        return empty($result)?true:false;
    }

    /**
     * @param $arr 二组数组
     * @return array
     */
    public function zuhe($arr) {
        if (count($arr) >= 2) {
            $tmparr = array();
            $arr1 = array_shift($arr);
            $arr2 = array_shift($arr);
            foreach ($arr1 as $k1 => $v1) {
                foreach ($arr2 as $k2 => $v2) {
                    $tmparr[] = $v1 . ',' . $v2;
                }
            }
            array_unshift($arr, $tmparr);
            $arr = $this->zuhe($arr);
        } else {
            return array_values($arr);
        }
        return  array_values($arr);
    }


}


?>