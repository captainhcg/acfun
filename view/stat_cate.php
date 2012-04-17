<?php
    require_once MODULE_PATH . "/mc.class.php";

    $mckey = 'type:'.$_REQUEST['type'].';date:'.$_REQUEST['date'];
    $mc = new MC();
    $mcvalue = $mc->get($mckey);
    if($mcvalue){
        echo $mcvalue."<div class='memcached'>memcached</div>";
    } 
    else{
        $sql = "SELECT ac_category_tid, ac_category_name FROM ac_category";
        $result = ACFUN::query($sql);
        $cate_ids = array();
        $cate_arr = array();
        $cate_names = array();
        while($res = ACFUN::fetch($result)){
            $cate_ids[] = $res['ac_category_tid'];
            $cate_names[$res['ac_category_tid']] = $res['ac_category_name'];
            $cate_arr[$res['ac_category_tid']] = array();
        }

        // generate the index first
        $xAxis = array();
        if( $_REQUEST['date'] == 'all' ){
            $sql = "
                 SELECT ad.ac_date_year AS year, ad.ac_date_month AS month
                 FROM ac_date AS ad
                 WHERE ac_date < curdate()
                 GROUP BY ad.ac_date_year, ad.ac_date_month";
        }
        else{
            $sql = "
                SELECT ad.ac_iso_year AS year, ad.ac_iso_week AS week
                FROM ac_date AS ad
                WHERE ac_date < curdate() AND ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                GROUP BY ad.ac_iso_year, ad.ac_iso_week";
        }
        $result = ACFUN::query($sql);
        if( $_REQUEST['date'] == 'all' ){
            while($res = ACFUN::fetch($result)){
                $xAxis[] = $res['year'].'年'.$res['month'].'月';
            }
        }
        else{
            while($res = ACFUN::fetch($result)){
                $xAxis[] = $res['year'].'年第'.$res['week'].'周';
            }
        }

        // then fetch the data
        if( $_REQUEST['date'] == 'all' ){
            $sql = "
                SELECT ac_article_category as cate, ad.ac_date_year AS year, ad.ac_date_month AS month, 
                    COUNT( * ) AS post, SUM( aa.ac_article_view ) AS view, SUM( aa.ac_article_comment ) AS comment
                FROM ac_article AS aa
                INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date 
                GROUP BY aa.ac_article_category, ad.ac_date_year, ad.ac_date_month";
        }
        else{
            $sql = "
                SELECT ac_article_category as cate, ad.ac_iso_year AS year, ad.ac_iso_week AS week, 
                    COUNT( * ) AS post, SUM( aa.ac_article_view ) AS view, SUM( aa.ac_article_comment ) AS comment
                FROM ac_article AS aa
                INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date 
                WHERE ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                GROUP BY aa.ac_article_category, ad.ac_iso_year, ad.ac_iso_week";
        }
        $result = ACFUN::query($sql);
        if( $_REQUEST['date'] == 'all' ){
            while($res = ACFUN::fetch($result)){
                if(in_array($res['cate'], $cate_ids)){
                    $cate_arr[$res['cate']][$res['year'].'年'.$res['month'].'月']['view'] = $res['view'];
                    $cate_arr[$res['cate']][$res['year'].'年'.$res['month'].'月']['post'] = $res['post'];
                    $cate_arr[$res['cate']][$res['year'].'年'.$res['month'].'月']['comment'] = $res['comment'];
                }
            }
        }
        else{
             while($res = ACFUN::fetch($result)){
                if(in_array($res['cate'], $cate_ids)){
                    $cate_arr[$res['cate']][$res['year'].'年第'.$res['week'].'周']['view'] = $res['view'];
                    $cate_arr[$res['cate']][$res['year'].'年第'.$res['week'].'周']['post'] = $res['post'];
                    $cate_arr[$res['cate']][$res['year'].'年第'.$res['week'].'周']['comment'] = $res['comment'];
                }
            }
        }

        $series = array();
        foreach($cate_arr as $cid => $cate){
            $tmp_arr = array();
            foreach($xAxis as $x){
                if(isset($cate[$x])){
                    if($_REQUEST['type'] == 'cate_view'){
                        $tmp_arr[] = $cate[$x]['view'];
                    }
                    else if($_REQUEST['type'] == 'cate_post'){
                        $tmp_arr[] = $cate[$x]['post'];
                    }
                    else if($_REQUEST['type'] == 'cate_comment'){
                        $tmp_arr[] = $cate[$x]['comment'];
                    }
                }
                else{
                    $tmp_arr[] = 0;
                }
            }
            $series[$cid] = "name: '".$cate_names[$cid]. "', data: [". implode(',', $tmp_arr) ."]";
        }
        $series_str = '[{'.implode("},{", $series).'}]';
        
        $xAxis_str = "'".implode("','", $xAxis)."'";

        $title_str = "";
        if($_REQUEST['type'] == 'cate_view' ){
            $title_str = "ACFUN点击量分类对比";
            $tooltip_str = "this.x+'<br>'+this.series.name+'区点击量'+ (this.y/10000) + '万次'";
        }
        else if($_REQUEST['type'] == 'cate_post'){
            $title_str = "ACFUN投稿数分类对比";
            $tooltip_str = "this.x+'<br>'+this.series.name+'区共有'+ this.y + '篇投稿'";
        }
        else if($_REQUEST['type'] == 'cate_comment'){
            $title_str = "ACFUN评论数分类对比";
            $tooltip_str = "this.x+'<br>'+this.series.name+'区共有'+ this.y + '条评论'";
        }
        $output = "<script type='text/javascript'>";
        $output .= "
            chart = new Highcharts.Chart({
              chart: {
                 renderTo: 'stat_chart',
                 zoomType: 'x',
                 type: 'area', 
                 right: 60
              },
              title: {
                 text: '".$title_str."'
              },
              subtitle: {
                 text: 'Source: acfun.tv'
              },
              xAxis: [{
                 categories: [".$xAxis_str."],
                 labels: {
                    enabled: false
                 }
              }],
              yAxis: { // UP主数量
                 min: 0,
                 title: {
                    text: 'Percent'
                 },
              },
              tooltip: {
                 formatter: function() {
                    return ".$tooltip_str.";
                 }
              },      
                plotOptions: {
                    area: {
                        stacking: 'percent',
                        lineColor: '#ffffff',
                        lineWidth: 1,
                        marker: {
                            symbol: 'circle',
                            radius: 4,
                            lineWidth: 1,
                            lineColor: '#ffffff'
                        }
                    }
                },
              series: ".$series_str."
           });
            ";
        $output .= "</script>";
        $mc->set($mckey, $output);
        echo $output;
    }
?>
