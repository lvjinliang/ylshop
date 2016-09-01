<?php
namespace Org\Common;
class Bi {
    private $Urllog = '';

    public function __construct() {
        $this->Urllog = M('Urllog');
    }

    public function add($param) {
        /**
         * 记录url
         */
        $status = 'on'; // {on:yes,off:no}
        // 初始化
        $param = $param ? $param : array();
        if(empty($param)){
            return false;
        }

        $cururl = isset($param['cururl']) ? $param['cururl'] : '';
        $useragent = isset($param['ua']) ? $param['ua'] : '';

        $ip = isset($param['ip']) ? $param['ip'] : '';  // 169.235.24.133 美国
        // filter web baidu
        if (strpos($useragent, 'http://www.baidu.com/search/spider.html') !== false) {
            $status = 'off';
        } // filter web baidu
        else if (strpos($useragent, 'http://www.baidu.com/search/spi_der.html') !== false) {
            $status = 'off';
        } // filter wap baidu
        else if (strpos($useragent, 'spider-ads') !== false) {
            $status = 'off';
        } // filter baidu ip
        else if (strpos($ip, '61.135.190.') !== false) {
            $status = 'off';
        } // filter sogou ip
        else if (strpos($ip, '113.105.146.') !== false || strpos($ip, '183.56.167.') !== false || strpos($ip, '183.57.151.') !== false || strpos($ip, '14.18.206.') !== false || strpos($ip, '222.73.59.') !== false) {
            $status = 'off';
        }

        if ($status === 'on') {
            $ipLocation = new \Org\Net\IpLocation();
            $ipinfo = $ipLocation->getlocation($ip);

            if ($status === 'on') {
                // 添加记录
                $data = array();
                $data['type'] = isset($param['type']) ? $param['type'] : '';
                $data['sub_type'] = isset($param['sub_type']) ? $param['sub_type'] : '';
                $data['url'] = $cururl;
                $data['from'] = isset($param['sourceurl']) ? $param['sourceurl'] : '';
                $data['categor_id'] = isset($param['catid']) ? $param['catid'] : '';
                $data['goods_id'] = isset($param['id']) ? $param['id'] : '';
                $data['ip'] = $ip;
                $data['country'] = isset($ipinfo['country']) ? $ipinfo['country'] : '';
                $data['province'] = isset($ipinfo['province']) ? $ipinfo['province'] : '';
                $data['area'] = isset($ipinfo['city']) ? $ipinfo['city'] : '';
                $data['area_desc'] = isset($ipinfo['desc']) ? $ipinfo['desc'] : '';
                $data['user_agent'] = isset($param['ua']) ? $param['ua'] : '';
                $data['session_id'] = isset($param['sid']) ? $param['sid'] : '';
                $data['account_id'] = isset($param['account_id']) ? $param['account_id'] : '';
                $data['sourcecode'] = isset($param['sc']) ? $param['sc'] : '';
                $data['usersign'] = isset($param['us']) ? $param['us'] : '';
                $data['pagename'] = isset($param['pagename']) ? $param['pagename'] : '';
                $data['pagetitle'] = isset($param['pt']) ? $param['pt'] : ''; // page title
                $data['date_added'] = date('Y-m-d H:i:s');
                if (!$data['categor_id']) {
                    $data['categor_id'] = 0;
                }
                if (!$data['goods_id']) {
                    $data['goods_id'] = 0;
                }
                $this->Urllog->add($data);

            }
        }
        echo  $this->Urllog->getDbError();
    }


}