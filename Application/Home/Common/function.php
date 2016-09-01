<?php
function setPage($count, $limit, $currUrl=''){
    $page  = new \Think\Page($count,$limit);
    $page->currUrl = $currUrl;
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $page->setConfig('header','<li><span class="rows">共 %TOTAL_ROW% 条记录</span></li>');
    return $page;
}

function urlRedirect($url) {
    echo '<script>location.href="'.$url.'"</script>';
}

/**
 * @param $id
 * @return bool
 */
function setLastView($id) {
    $lastView = cookie('lastView');
    if (!empty($lastView)) {
        $lastView = unserialize($lastView);
    } else {
        $lastView = array();
    }
    array_unshift($lastView,$id);
    $lastView = array_unique($lastView);
    $lastView = array_slice($lastView,0,3);
    cookie('lastView', serialize($lastView));
}

/**
 * @return array
 */
function getLastView() {
    $lastView = cookie('lastView');
    if (!empty($lastView)) {
        $lastView = unserialize($lastView);
    } else {
        $lastView = array();
    }
    return $lastView;
}

?>