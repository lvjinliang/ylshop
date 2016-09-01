<?php
/**
 * User: 良子
 * Date: 16-2-3
 */
namespace Admin\Widget;
use Think\Controller;
class CommonWidget extends Controller {
    public function leftMenu(){
        $AUTH = new \Think\Auth();
        $menuNames = $AUTH->getAuthMenuName(1,array('in','1,2'));
        $AccessRule = D('AccessRule');
        $menus = $AccessRule->getMenuByNames($menuNames);
        foreach($menus as $key => $val){
            if($val['type']==1){
                $menus[$key]['name'] = U($val['name']);
            } else {
                $menus[$key]['name'] = 'javascript:void(0)';
            }
            if( strpos($menus[$key]['name'],strtolower(__CONTROLLER__.'/'))!==false) {
                $menus[$key]['isActive'] = 1;
            } else {
                $menus[$key]['isActive'] = 0;
            }
        }
        $menus = getLevel($menus);
        $username = session('user.name');
        $this->assign('username',$username);
        $this->assign('menus',$menus);
        $this->display('Widget:left_menu');
    }

}
?>