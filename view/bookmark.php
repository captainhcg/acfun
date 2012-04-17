<?php
    if(!isset($_REQUEST['type']))
        $_REQUEST['type'] = 'all';
    echo "<script>highlight_menu('bookmark', '".$_REQUEST['type']."');</script>";
    if(!isset($_REQUEST['type'])||$_REQUEST['type'] == 'all'){
        include CACHE_PATH."/bookmark_all.cache";
    }
    else if($_REQUEST['type'] == 'all'){
		include CACHE_PATH."/bookmark_all.cache";
	}
	else if($_REQUEST['type'] == 'year'){
		include CACHE_PATH."/bookmark_year.cache";
	}  
    else if($_REQUEST['type'] == 'month'){
        include CACHE_PATH."/bookmark_month.cache";
    }
    else{
        include CACHE_PATH."/bookmark_week.cache";
    }
	
?>

