<?php
    if(!isset($_REQUEST['type'])){
        $_REQUEST['type'] = 'week';
    }
    echo "<script>highlight_menu('up', '".$_REQUEST['type']."');</script>";
?>
<div id="up_sidebar" class="sidebar">
    <div id='ac_search' class="sidebar_section">
        <img src="images/metro/search.png" class="sidebar_section_icon" title="查找" />
        <div id="ac_search_title" class="sidebar_section_title">
        </div>
        <div id="ac_search_body" class="sidebar_section_body">
            <script>var init = 1;</script>
            <input type="text" id="ac_search_submit" value="SEARCH" 
                onclick="if(init == 1){$(this).val(''); init = 0}" onchange="search(this.value, 2)" onkeyup="search(this.value, 2)" >
            <div id="sidebar_search_result" style="width: 100%">
            </div>
        </div>
    </div>
    <div id='ac_tag_cloud' class="sidebar_section">
    	<img src="images/metro/note.png" class="sidebar_section_icon" title="热门标签" />
        <div id="ac_tag_cloud_title" class="sidebar_section_title">
            热门标签
        </div>
        <div id="ac_tag_cloud_body" class="sidebar_section_body">
        <?php include CACHE_PATH."/tag_cloud_".$_REQUEST['type'].".cache"; ?>
        </div>
    </div>
    <div id="ac_today" class="sidebar_section">
    	<img src="images/metro/calend.png" class="sidebar_section_icon" title="历史上的今天" />
        <div id="ac_today_title" class="sidebar_section_title">
            历史上的今天
        </div>
        <div class="sidebar_section_body" style="padding-top:40px">
        <?php include CACHE_PATH."/today.cache"; ?>
        </div>                         
    </div>
</div>
<?php
    include CACHE_PATH."/up_".$_REQUEST['type'].".cache";
?>

