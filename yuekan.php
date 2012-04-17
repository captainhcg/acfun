<?php
        require_once "ACFUN_init.php";
?>
<html>
<head>
<script type="text/javascript" language="javascript" src="js/DataTables-1.9.0/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="js/DataTables-1.9.0/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                // $('#result').dataTable({"sPaginationType": "full_numbers", "iDisplayLength": 25});
        } );
</script>
<style>
 *, input{font-size:13px; font-family: Arial}
 a{text-decoration: none};
</style>
<style type="text/css">
    @import "js/DataTables-1.9.0/media/css/demo_page.css";
    @import "js/DataTables-1.9.0/media/css/demo_table.css";       
</style>
</head>
<body>
<div style="width: 800px; margin: auto; margin-top: 20px">
    <form action="yuekan.html" method="get">
        <table style="margin: autoi; padding: 2px;">
            <tr height="24px"><td><label for='start'>开始（yyyy-mm-dd）</label></td>
            <td style="padding-right:10px"><input id="start" name="start" type="text" size=10 value="<?php echo isset($_GET['start'])?$_GET['start']:date('Y-m-d'); ?>">
            <td><label for='end'>结束（yyyy-mm-dd）</label></td>
            <td style="padding-right:10px"><input id="end" name="end" type="text" size=10 value="<?php echo isset($_GET['end'])?$_GET['end']:date('Y-m-d'); ?>">
            <td style="padding-right:10px"><label for='category'>分区 </label></td><td><select id="category" name="category">
                <option value="0" <?php if(isset($_GET['category']) && $_GET['category'] == 0) echo "selected='selected'"?>>所有</option>
                <option value="1" <?php if(isset($_GET['category']) && $_GET['category'] == 1) echo "selected='selected'"?>>动画</option>
                <option value="8" <?php if(isset($_GET['category']) && $_GET['category'] == 8) echo "selected='selected'"?>>音乐</option>
                <option value="9" <?php if(isset($_GET['category']) && $_GET['category'] == 9) echo "selected='selected'"?>>游戏</option>
                <option value="13" <?php if(isset($_GET['category']) && $_GET['category'] == 13) echo "selected='selected'"?>>文章</option>
                <option value="14" <?php if(isset($_GET['category']) && $_GET['category'] == 14) echo "selected='selected'"?>>短影</option>
                <option value="10" <?php if(isset($_GET['category']) && $_GET['category'] == 10) echo "selected='selected'"?>>娱乐</option>
                <option value="7" <?php if(isset($_GET['category']) && $_GET['category'] == 7) echo "selected='selected'"?>>番剧</option>
            </select>
            <td><input type="submit" value="查询"></td></tr>
        </table>
    </form>
    <?php if(isset($_GET['start']) && isset($_GET['end']) && isset($_GET['category']))?>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="result" width="100%">
                <thead>
                        <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th width="70px">Date</th>
                                <th>View</th>
                                <th>Comment</th>
                                <th>Stow</th>
                        </tr>
                </thead>
                <tbody>
                <?php 
                        $sql = "SELECT * FROM ac_article WHERE ac_article_die < 2 AND ac_article_date >=".ACFUN::quote($_GET['start'])." AND ac_article_date<=".ACFUN::quote($_GET['end']);
                        if($_GET['category'] != 0)
                                $sql .= " AND ac_article_category = ".ACFUN::quote($_GET['category']);
                        $sql .= " ORDER BY ac_article_date ASC";
                        $res = ACFUN::query($sql);
                        while($item = ACFUN::fetch($res)){
                                echo "<tr>";
                                echo "<td>".$item{'ac_article_number'}."</td>";
                                echo "<td><a href='http://www.acfun.tv/v/".$item{'ac_article_link'}."/' target='_blank'>".$item{'ac_article_name'}."</td>";
                                echo "<td>".$item{'ac_author_name'}."</td>";
                                echo "<td>".$item{'ac_article_category'}."</td>";
                                echo "<td>".$item{'ac_article_date'}."</td>";
                                echo "<td>".$item{'ac_article_view'}."</td>";
                                echo "<td>".$item{'ac_article_comment'}."</td>";
                                echo "<td>".$item{'ac_article_stow'}."</td>";
                                echo "</tr>";
                        }
                ?>
                </tbody>
        </table>
    <?php ?>
</div>
</body>
</html>
