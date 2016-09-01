<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class EmailTplModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function')
    );

}


?>