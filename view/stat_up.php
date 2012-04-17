<?php
    require_once MODULE_PATH . "/mc.class.php";

    $mckey = 'type:up;date:'.$_REQUEST['date'];
    $mc = new MC();
    $mcvalue = $mc->get($mckey);
    if($mcvalue){
        echo $mcvalue."<div class='memcached'>memcached</div>";
    } 
    else{
        $output = "";
        if( $_REQUEST['date'] == 'all'){
            $sql = "
                SELECT ad.ac_date_year, ad.ac_date_month, COUNT(DISTINCT ac_author_id) as author, COUNT( * ) AS post
                FROM ac_article AS aa
                INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date
                GROUP BY ad.ac_date_year, ad.ac_date_month";
        }
        else{
            $sql = "
                SELECT ad.ac_iso_year, ad.ac_iso_week, COUNT(DISTINCT ac_author_id) as author, COUNT( * ) AS post
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
        $author_arr = array();
        $post_arr = array();
        $per_arr = array();
        foreach($data as $item){
            if( $_REQUEST['date'] == 'all'){
                $category[$item['ac_date_year'].'年'.$item['ac_date_month'].'月'] = $item;
            }
            else{
                $category[$item['ac_iso_year'].'年第'.$item['ac_iso_week'].'周'] = $item;
            }
            $author_arr[] = $item['author']; 
            $post_arr[] = $item['post']; 
        }
        $cate_str = "'".implode("','", array_keys($category))."'";
        $author_str = implode(",", $author_arr);
        $post_str = implode(",", $post_arr);
        
        if( $_REQUEST['date'] == 'all'){
            $sql = "
                SELECT T1.ac_date_year, T1.ac_date_month, COUNT(*) AS new
                FROM ac_date AS T1 
                INNER JOIN 
                (SELECT `ac_author_id` , MIN( `ac_article_date` ) AS first
                FROM `ac_article`
                GROUP BY `ac_author_id` ) AS T2 ON T1.ac_date = T2.first
                GROUP BY T1.ac_date_year, T1.ac_date_month ";
        }
        else{
            $sql = "
                SELECT T1.ac_iso_year, T1.ac_iso_week, COUNT(*) AS new
                FROM ac_date AS T1 
                RIGHT JOIN 
                (SELECT `ac_author_id` , MIN( `ac_article_date` ) AS first
                FROM `ac_article`
                GROUP BY `ac_author_id` ) AS T2 ON T1.ac_date = T2.first
                WHERE T1.ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                GROUP BY T1.ac_iso_year, T1.ac_iso_week";
        }
        ACFUN::query($sql);
        $result = ACFUN::query($sql);
        $new_author = array();
        while($item = ACFUN::fetch($result)){
            if( $_REQUEST['date'] == 'all'){
                $new_author[$item['ac_date_year'].'年'.$item['ac_date_month'].'月'] = $item['new'];
            }
            else{
                $new_author[$item['ac_iso_year'].'年第'.$item['ac_iso_week'].'周'] = $item['new'];
            }
        }
        $new_arr =array();
        foreach($category as $key => $value){
            if(!isset($new_author[$key])){
                $new_arr[] = 0;
                $per_arr[] = 0;
            }
            else{
                 $new_arr[] = $new_author[$key];
                 $per_arr[] = number_format(100*$new_author[$key]/$value['author'],2);
            }
        }
        $new_author_str = implode(",", $new_arr);
        $per_str = implode(",", $per_arr);
        $output .= "<script type='text/javascript'>";
        $output .= "
            chart = new Highcharts.Chart({
              chart: {
                 renderTo: 'stat_chart',
                 alignTicks: false,
                 zoomType: 'x',
                 right: 60
              },
              title: {
                 text: 'ACFUN UP主趋势'
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
              yAxis: [{ // UP主数量
                 labels: {
                    formatter: function() {
                       return this.value+'人';
                    },
                    style: {
                       color: '#DB843D'
                    }
                 },
                 min: 0,
                 title: {
                    text: null
                 },
                 showFirstLabel: false
              },{ // 比率
                 title: {
                    text: null
                 },
                 min: 0,
                 max: 100,
                 showFirstLabel: false,
                 labels: {
                    formatter: function() {
                       return this.value+'%';
                    },
                    style: {
                       color: '#4572A7'
                    }
                 },
                 opposite: true
              }],        
              tooltip: {
                 formatter: function() {
                    if(this.series.name == '活跃的UP主'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '人';
                    }
                    if(this.series.name == '新UP主比例'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '%';
                    }
                    if(this.series.name == '处女作'){
                        return this.series.name+'<br>'+this.x +': '+ this.y + '件';
                    }                
                 }
              },      
              series: [{
                 name: '活跃的UP主',
                 color: '#DB843D',
                 type: 'spline',
                 data: [".$author_str."]      
              
              },{
                 name: '处女作',
                 color: '#AA4643',
                 type: 'spline',
                 data: [".$new_author_str."]
              },{
                 name: '新UP主比例',
                 color: '#4572A7',
                 type: 'spline',
                 yAxis: 1,
                 data: [".$per_str."]
              }, ]
           });
            ";
        $output .= "</script>";
        $mc->set($mckey, $output);
        echo $output;
    }
?>
