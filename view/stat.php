<div id="stat_chart" style="width: 100%; height: 500px">
</div>
<?php
echo "<script>highlight_menu('stat', '".$_REQUEST['type']."');</script>";
if (!isset($_REQUEST['type']))
    $_REQUEST['type'] = 'post';
if (!isset($_REQUEST['date']))
    $_REQUEST['date'] = 'all';

echo '<div class="nav_link" style="text-align: center; height: 40px">';
echo '<ul class="nav_ul">';
if ($_REQUEST['date'] != 'all')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=all\'>有史以来</a></li>';
else
    echo '<li class="nav_li nav_li_selected">有史以来</li>';

if ($_REQUEST['date'] != '2008')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=2008\'>2008年</a></li>';
else
    echo '<li class="nav_li nav_li_selected">2008年</li>';

if ($_REQUEST['date'] != '2009')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=2009\'>2009年</a></li>';
else
    echo '<li class="nav_li nav_li_selected">2009年</li>';

if ($_REQUEST['date'] != '2010')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=2010\'>2010年</a></li>';
else
    echo '<li class="nav_li nav_li_selected">2010年</li>';

if ($_REQUEST['date'] != '2011')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=2011\'>2011年</a></li>';
else
    echo '<li class="nav_li nav_li_selected">2011年</li>';

if ($_REQUEST['date'] != '2012')
    echo '<li class="nav_li"><a href=\'stat.html?type=' . $_REQUEST['type'] . '&date=2012\'>2012年</a></li>';
else
    echo '<li class="nav_li nav_li_selected">2012年</li>';

echo "</ul>\n</div>";

if ($_REQUEST['type'] == 'post')
    include("stat_post.php");
else if ($_REQUEST['type'] == 'up')
    include("stat_up.php");
else if ($_REQUEST['type'] == 'cate_view' || $_REQUEST['type'] == 'cate_post' || $_REQUEST['type'] == 'cate_comment')
    include("stat_cate.php");

echo "<div><span style='color: #eeeeff; font-size: 12px'>本页面推荐使用Chrome浏览</div>";
?>
