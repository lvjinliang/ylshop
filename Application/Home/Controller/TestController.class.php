<?php
/**
 * User: 良子
 * Date: 16-3-22
 */

namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {
     public function index () {
         header('Content-Type: text/html; charset=utf-8');
         $t1 = microtime(true);
         ini_set('memory_limit', '128M');
         error_reporting(E_ALL);
         vendor("Phpanalysis.phpanalysis");
         $Phpanalysis = new \Phpanalysis();
         $teststr = "小米5黑色4G";
         $Phpanalysis->SetSource($teststr);
         $Phpanalysis->StartAnalysis();
         $okresult = $Phpanalysis->GetFinallyResult();
         var_dump($okresult);
         $t2 = microtime(true);
         $endtime = sprintf('%0.4f', $t2 - $t1);
         echo $endtime;
     }
} 