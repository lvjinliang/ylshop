<?php
/**
 * User: 良子
 * Date: 16-05-23
 */

namespace Home\Model;
use Think\Model;

class SearchModel extends Model {

    function getTotal($key) {
        $key = htmlspecialchars(strip_tags($key),ENT_QUOTES);
        $sql = "SELECT count(*) total
                FROM __PREFIX__search s
                LEFT JOIN __PREFIX__goods g
                ON s.goods_id = g.id
                WHERE g.is_on_sale = 1
                AND g.is_alone_sale = 1
                AND MATCH(s.keywords) AGAINST('{$key}' IN BOOLEAN MODE)";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

    function getList($filter, $key) {
        $key = htmlspecialchars(strip_tags($key),ENT_QUOTES);
        $order = isset($filter['order']) ? $filter['order'] : 'g.sort DESC ,g.id DESC';
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('PAGE_SIZE');

        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);

        $sql = "SELECT g.id, g.name , g.thumb,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, null) or_price
                FROM __PREFIX__search s
                LEFT JOIN __PREFIX__goods g
                ON s.goods_id = g.id
                WHERE g.is_on_sale = 1
                AND g.is_alone_sale = 1
                AND MATCH(s.keywords) AGAINST('{$key}' IN BOOLEAN MODE)
                ORDER BY {$order}
                Limit {$start}, {$limit}";
        $data = $this->query($sql);
        return $data;
    }

}
?>