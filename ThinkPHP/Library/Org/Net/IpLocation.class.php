<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Org\Net;
/**
 *  IP 地理位置查询类 修改自 CoolCode.CN
 *  由于使用UTF8编码 如果使用纯真IP地址库的话 需要对返回结果进行编码转换
 * @author    liu21st <liu21st@gmail.com>
 */
class IpLocation {
    /**
     * QQWry.Dat文件指针
     *
     * @var resource
     */
    private $fp;

    /**
     * 第一条IP记录的偏移地址
     *
     * @var int
     */
    private $firstip;

    /**
     * 最后一条IP记录的偏移地址
     *
     * @var int
     */
    private $lastip;

    /**
     * IP记录的总条数（不包含版本信息记录）
     *
     * @var int
     */
    private $totalip;

    /**
     * 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
     *
     * @param string $filename
     * @return IpLocation
     */
    public function __construct($filename = "UTFWry.dat") {
        $this->fp = 0;
        if (($this->fp      = fopen(dirname(__FILE__).'/'.$filename, 'rb')) !== false) {
            $this->firstip  = $this->getlong();
            $this->lastip   = $this->getlong();
            $this->totalip  = ($this->lastip - $this->firstip) / 7;
        }
    }

    /**
     * 返回读取的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong() {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong3() {
        //将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3).chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @access private
     * @param string $ip
     * @return string
     */
    private function packip($ip) {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval(ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     *
     * @access private
     * @param string $data
     * @return string
     */
    private function getstring($data = "") {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {        // 字符串按照C格式保存，以\0结束
            $data  .= $char;             // 将读取的字符连接到给定字符串之后
            $char   = fread($this->fp, 1);
        }
        return $data;
    }

    /**
     * 返回地区信息
     *
     * @access private
     * @return string
     */
    private function getarea() {
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 0:                     // 没有区域信息
                $area = "";
                break;
            case 1:
            case 2:                     // 标志字节为1或2，表示区域信息被重定向
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:                    // 否则，表示区域信息没有被重定向
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }

    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @access public
     * @param string $ip
     * @return array
     */
    public function getlocation($ip='') {
        if (!$this->fp) return null;            // 如果数据文件没有被正确打开，则直接返回空
		if(empty($ip)) $ip = get_client_ip();
        $location['ip'] = gethostbyname($ip);   // 将输入的域名转化为IP地址
        $ip = $this->packip($location['ip']);   // 将输入的IP地址转化为可比较的IP地址
                                                // 不合法的IP地址会被转化为255.255.255.255
        // 对分搜索
        $l = 0;                         // 搜索的下边界
        $u = $this->totalip;            // 搜索的上边界
        $findip = $this->lastip;        // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）
        while ($l <= $u) {              // 当上边界小于下边界时，查找失败
            $i = floor(($l + $u) / 2);  // 计算近似中间记录
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4));     // 获取中间记录的开始IP地址
            // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
            // 以便用于比较，后面相同。
            if ($ip < $beginip) {       // 用户的IP小于中间记录的开始IP地址时
                $u = $i - 1;            // 将搜索的上边界修改为中间记录减一
            }
            else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4));   // 获取中间记录的结束IP地址
                if ($ip > $endip) {     // 用户的IP大于中间记录的结束IP地址时
                    $l = $i + 1;        // 将搜索的下边界修改为中间记录加一
                }
                else {                  // 用户的IP在中间记录的IP范围内时
                    $findip = $this->firstip + $i * 7;
                    break;              // 则表示找到结果，退出循环
                }
            }
        }

        //获取查找到的IP地理位置信息
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong());   // 用户IP所在范围的开始地址
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong());     // 用户IP所在范围的结束地址
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 1:                     // 标志字节为1，表示国家和区域信息都被同时重定向
                $countryOffset = $this->getlong3();         // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1);    // 标志字节
                switch (ord($byte)) {
                    case 2:             // 标志字节为2，表示国家信息又被重定向
                        fseek($this->fp, $this->getlong3());
                        $location['country']    = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area']       = $this->getarea();
                        break;
                    default:            // 否则，表示国家信息没有被重定向
                        $location['country']    = $this->getstring($byte);
                        $location['area']       = $this->getarea();
                        break;
                }
                break;
            case 2:                     // 标志字节为2，表示国家信息被重定向
                fseek($this->fp, $this->getlong3());
                $location['country']    = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area']       = $this->getarea();
                break;
            default:                    // 否则，表示国家信息没有被重定向
                $location['country']    = $this->getstring($byte);
                $location['area']       = $this->getarea();
                break;
        }
        // 重置返回值
        if($location){
            $location['desc'] = isset($location['area']) ? $location['area'] : '';
            unset($location['area']);

            $getCountrys = $this->getCountrys();
            foreach($location as $key=>$val){
                //$location[$key] = iconv('GB2312', 'UTF-8',$val);
                if($key == 'country'){
                    $countrystr = str_replace('省','|',$location[$key]);
                    $countrystr = str_replace('市','|',$countrystr);
                    $countrystr = str_replace('区','|',$countrystr);
                    $strarray = explode('|',$countrystr);
                    if(strpos($location[$key],'省')>0){ // 省
                        $location[$key] = '中国';
                        $location['province'] = isset($strarray[0]) ? $strarray[0] : '';
                        $location['city'] = isset($strarray[1]) ? $strarray[1] : '';
                        $location['area'] = isset($strarray[2]) ? $strarray[2] : '';
                    } elseif(strpos($location[$key],'市')>0){ // 直辖市
                        $location[$key] = '中国';
                        $location['province'] = isset($strarray[0]) ? $strarray[0] : '';
                        $location['city'] = isset($strarray[0]) ? $strarray[0] : '';
                        $location['area'] = isset($strarray[1]) ? $strarray[1] : '';
                    } else{
                        $location[$key] = in_array($location[$key],$getCountrys) ? $location[$key] : '';
                        $location['province'] = '';
                        $location['city'] = '';
                        $location['area'] = '';
                    }
                }
            }
        }


        if (trim($location['country']) == 'CZ88.NET') {  // CZ88.NET表示没有有效信息
            $location['country'] = '未知';
        }
        if (trim($location['area']) == 'CZ88.NET') {
            $location['area'] = '';
        }
        return $location;
    }

    public function getCountrys(){
        $result = array('阿富汗','奥兰群岛','阿尔巴尼亚','阿尔及利亚','美属萨摩亚','安道尔','安哥拉','安圭拉','安提瓜和巴布达','阿根廷','亚美尼亚','阿鲁巴','澳大利亚','奥地利','阿塞拜疆','孟加拉','巴林','巴哈马','巴巴多斯','白俄罗斯','比利时','伯利兹','贝宁','百慕大','不丹','玻利维亚','波斯尼亚和黑塞哥维那','博茨瓦纳','布维岛','巴西','文莱','保加利亚','布基纳法索','布隆迪','柬埔寨','喀麦隆','加拿大','佛得角','中非','乍得','智利','圣诞岛','科科斯（基林）群岛','哥伦比亚','科摩罗','刚果（金）','刚果','库克群岛','哥斯达黎加','科特迪瓦','中国','克罗地亚','古巴','捷克','塞浦路斯','丹麦','吉布提','多米尼加','东帝汶','厄瓜多尔','埃及','赤道几内亚','厄立特里亚','爱沙尼亚','埃塞俄比亚','法罗群岛','斐济','芬兰','法国','法国大都会','法属圭亚那','法属波利尼西亚','加蓬','冈比亚','格鲁吉亚','德国','加纳','直布罗陀','希腊','格林纳达','瓜德罗普岛','关岛','危地马拉','根西岛','几内亚比绍','几内亚','圭亚那','香港 （中国）','海地','洪都拉斯','匈牙利','冰岛','印度','印度尼西亚','伊朗','伊拉克','爱尔兰','马恩岛','以色列','意大利','牙买加','日本','泽西岛','约旦','哈萨克斯坦','肯尼亚','基里巴斯','韩国','朝鲜','科威特','吉尔吉斯斯坦','老挝','拉脱维亚','黎巴嫩','莱索托','利比里亚','利比亚','列支敦士登','立陶宛','卢森堡','澳门（中国）','马其顿','马拉维','马来西亚','马达加斯加','马尔代夫','马里','马耳他','马绍尔群岛','马提尼克岛','毛里塔尼亚','毛里求斯','马约特','墨西哥','密克罗尼西亚','摩尔多瓦','摩纳哥','蒙古','黑山','蒙特塞拉特','摩洛哥','莫桑比克','缅甸','纳米比亚','瑙鲁','尼泊尔','荷兰','新喀里多尼亚','新西兰','尼加拉瓜','尼日尔','尼日利亚','纽埃','诺福克岛','挪威','阿曼','巴基斯坦','帕劳','巴勒斯坦','巴拿马','巴布亚新几内亚','巴拉圭','秘鲁','菲律宾','皮特凯恩群岛','波兰','葡萄牙','波多黎各','卡塔尔','留尼汪岛','罗马尼亚','卢旺达','俄罗斯联邦','圣赫勒拿','圣基茨和尼维斯','圣卢西亚','圣文森特和格林纳丁斯','萨尔瓦多','萨摩亚','圣马力诺','圣多美和普林西比','沙特阿拉伯','塞内加尔','塞舌尔','塞拉利昂','新加坡','塞尔维亚','斯洛伐克','斯洛文尼亚','所罗门群岛','索马里','南非','西班牙','斯里兰卡','苏丹','苏里南','斯威士兰','瑞典','瑞士','叙利亚','塔吉克斯坦','坦桑尼亚','台湾 （中国）','泰国','特立尼达和多巴哥','东帝汶','多哥','托克劳','汤加','突尼斯','土耳其','土库曼斯坦','图瓦卢','乌干达','乌克兰','阿拉伯联合酋长国','英国','美国','乌拉圭','乌兹别克斯坦','瓦努阿图','梵蒂冈','委内瑞拉','越南','瓦利斯群岛和富图纳群岛','西撒哈拉','也门','南斯拉夫','赞比亚','津巴布韦');
        return $result;
    }

    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     *
     */
    public function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }

}