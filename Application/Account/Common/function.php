<?php
function setPage($count, $limit){
    $page  = new \Think\Page($count,$limit);
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $page->setConfig('header','<li><span class="rows">共 %TOTAL_ROW% 条记录</span></li>');
    return $page;
}

function urlRedirect($url) {
    echo '<script>location.href="'.$url.'"</script>';
}

?>