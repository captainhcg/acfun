<?php
    echo "<script>highlight_menu('hot', '".$_REQUEST['type']."');</script>";
    if(!isset($_REQUEST['type'])||$_REQUEST['type'] == 'week'){
        include CACHE_PATH."/hot_week.cache";
        $_REQUEST['type'] = 'week';
    }
    else if($_REQUEST['type'] == 'all'){
        include CACHE_PATH."/hot_all.cache";
    }
    else if($_REQUEST['type'] == 'year'){
        include CACHE_PATH."/hot_year.cache";
    }  
    else if($_REQUEST['type'] == 'month'){
        include CACHE_PATH."/hot_month.cache";
    }
    else{
        include CACHE_PATH."/hot_week.cache";
    }
?>
