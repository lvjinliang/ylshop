<?php
/**
 * User: 良子
 * Date: 15-11-28
 */
namespace Home\Behavior;
use Think\Behavior;
class CheckLangBehavior extends Behavior{
    public function run(&$param){
         dump($param);
    }

} 