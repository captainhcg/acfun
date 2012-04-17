<?php
    require_once MODULE_PATH . "/mc.class.php";

    $mckey = 'type:post;date:'.$_REQUEST['date'];
    $mc = new MC();
    $mcvalue = $mc->get($mckey);
    if($mcvalue){
        echo $mcvalue."<div class='memcached'>memcached</div>";
    }
    else{
        $output = '';

        if( $_REQUEST['date'] == 'all'){
            $sql = "
                SELECT ad.ac_date_year, ad.ac_date_month, COUNT( * ) AS post, SUM( aa.ac_article_view ) AS view, SUM( aa.ac_article_comment) AS comment
                FROM ac_article AS aa
                INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date
                GROUP BY ad.ac_date_year, ad.ac_date_month";
        }
        else{
            $sql = "
                SELECT ad.ac_iso_year, ad.ac_iso_week, COUNT( * ) AS post, SUM( aa.ac_article_view ) AS view, SUM( aa.ac_article_comment) AS comment
                FROM ac_article AS aa
                INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date
                WHERE ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                GROUP BY ad.ac_iso_year, ad.ac_iso_week";        
        }
        
        ACFUN::query($sql);
        $result = ACFUN::query($sql);
        $data = array();
        while($res = ACFUN::fetch($result)){
            $data[] = $res;
        }

        $category = array();
        $view_arr = array();
        $comment_arr = array();
        foreach($data as $item){
            if( $_REQUEST['date'] == 'all'){
                $category[$item['ac_date_year'].'年'.$item['ac_date_month'].'月'] = $item;
            }
            else{
                $category[$item['ac_iso_year'].'年第'.$item['ac_iso_week'].'周'] = $item;
            }
            $view_arr[] = $item['view']/10000;
            $comment_arr[] = $item['comment']/10000;
            $post_arr[] = $item['post']; 
        }
        $cate_str = "'".implode("','", array_keys($category))."'";
        $view_str = implode(",", $view_arr);
        $post_str = implode(",", $post_arr);
        $comment_str = implode(",", $comment_arr);
        
        if( $_REQUEST['date'] == 'all'){
            $sql = "
                SELECT MAX(ac_article_number) AS max, ac_date.ac_date_year, ac_date_month
                FROM ac_article
                INNER JOIN ac_date ON
                ac_article.ac_article_date = ac_date.ac_date
                GROUP BY ac_date.ac_date_year, ac_date_month
                ";
        }
        else{
            $sql = "
                SELECT MAX(ac_article_number) AS max, ac_date.ac_iso_year, ac_iso_week
                FROM ac_article
                INNER JOIN ac_date ON
                ac_article.ac_article_date = ac_date.ac_date
                WHERE ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                GROUP BY ac_date.ac_iso_year, ac_iso_week";
        }
        ACFUN::query($sql);
        $result = ACFUN::query($sql);
        $max_post= array();
        while($item = ACFUN::fetch($result)){
            $max_post[] = $item['max']/10000;
        }
        $max_str = implode(",", $max_post);
        
        $output .= "<script type='text/javascript'>";
        $output .= "
              chart = new Highcharts.Chart({
              chart: {
                 renderTo: 'stat_chart',
                 zoomType: 'x',
                 right: 60,
                 defaultSeriesType: 'areaspline'
              },
              title: {
                 text: 'ACFUN投稿趋势'
              },
              subtitle: {
                 text: 'Source: acfun.tv'
              },        
              xAxis: [{
                 categories: [".$cate_str."],
                 labels: {
                    enabled: false
                 }
              }],
              yAxis: [
                 { // 点击
                 labels: {
                    formatter: function() {
                       return this.value+'万次';
                    },
                    style: {
                       color: '#4572A7'
                    }
                 },
                 min: 0,
                 title: {
                    text: null
                 },
                 showFirstLabel: false
              }, { // 投稿
                 title: {
                    text: null
                 },
                 min: 0,
                 showFirstLabel: false,
                 labels: {
                    formatter: function() {
                       return this.value+'件';
                    },
                    style: {
                       color: '#DB843D'
                    }
                 },
              },{ // 评论
                 title: {
                    text: null
                 },
                 min: 0,
                 showFirstLabel: false,
                 labels: {
                    formatter: function() {
                       return this.value+'万条';
                    },
                    style: {
                       color: '#AA4643'
                    }
                 },
                 opposite: true
              }],
              tooltip: {
                 formatter: function() {
                    if(this.series.name == '投稿点击'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '万次';
                    }
                    if(this.series.name == '投稿发布'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '件';
                    }
                    if(this.series.name == '用户评论'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '万条';
                    }
                    if(this.series.name == '总投稿'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '万条';
                    }                
                 }
              },         
              series: [{
                 name: '投稿点击',
                 color: '#4572A7',
                 type: 'spline',
                 data: [".$view_str."]      
              
              }, {
                 name: '投稿发布',
                 color: '#DB843D',
                 type: 'spline',
                 yAxis: 1,
                 data: [".$post_str."]
              }, {
                 name: '用户评论',
                 color: '#AA4643',
                 type: 'spline',
                 yAxis: 2,
                 data: [".$comment_str."]
              }]
           });
            ";
        $output .= "</script>";
        $mc->set($mckey, $output);
        echo $output;
    }
?>
