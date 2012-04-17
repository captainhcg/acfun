<?php
    require_once MODULE_PATH . "/mc.class.php";

    $mckey = 'timeline;date:'.$_REQUEST['date'].';keywords:'.$_REQUEST['keywords'].';type:'.$_REQUEST['type'];
    $mc = new MC();
    $mcvalue = $mc->get($mckey);
    if( $mcvalue){
        echo $mcvalue."<div class='memcached'>memcached</div>";
    } 
    else{
        // generate the index first
        $xAxis = array();
        if( $_REQUEST['date'] == 'all' ){
            $sql = "
                 SELECT ac_date AS date
                 FROM ac_date AS ad
                 WHERE ac_date <= curdate() AND ac_date >= '2008-01-01'
                 ORDER BY ad.ac_date";
        }
        else{
            $sql = "
                SELECT ac_date AS date
                FROM ac_date AS ad
                WHERE ac_date <= curdate() AND ac_iso_year = ".ACFUN::quote($_REQUEST['date'])."
                ORDER BY ad.ac_date";
        }
        $result = ACFUN::query($sql);
        $index = 0;
        $xAxis = array();
        while($res = ACFUN::fetch($result)){
            $xAxis[$res['date']] = ++$index;
        }
        
        $groups = explode( ':', $_REQUEST['keywords'] ); 

        $colors = array( '213, 233, 90, .5', '253, 83, 83, .5', '89, 192, 191, .5', '207,126,13,.5', '255,255,0,.5', '255,0,255,.5', '0,255,255,.5');
        $series_arr = array();
        $index = 0;

        $_r = 6;
        foreach($groups as $group){
            $keywords = explode('_', $group);
            if(count($keywords) == 1 ){
                $condition = "(aa.ac_article_name LIKE '%".$keywords[0]."%' 
                            OR aa.ac_article_description LIKE '%".$keywords[0]."%' 
                            OR at.ac_tag_name = '".$keywords[0]."')";
            }
            else{
                $condition = "aa.ac_article_name LIKE '%".$keywords[0]."%' 
                            OR aa.ac_article_description LIKE '%".$keywords[0]."%' 
                            OR at.ac_tag_name = '".$keywords[0]."' ";
                for($i = 1; $i< count($keywords); $i++){
                    $condition .= " OR aa.ac_article_name LIKE '%".$keywords[$i]."%' 
                            OR aa.ac_article_description LIKE '%".$keywords[$i]."%' 
                            OR at.ac_tag_name = '".$keywords[$i]."' ";
                }
                $condition = "(".$condition.")";
            }
                            

            // $condition = 'ac_article_category != 13 AND aa.ac_article_view < 1000000 ';
            // then fetch the data
            if( $_REQUEST['date'] == 'all' ){
                $sql = "
                    SELECT DISTINCT aa.ac_article_link AS link, aa.ac_article_name AS name, aa.ac_article_date AS date,   
                        aa.ac_article_view AS view
                    FROM ac_article AS aa
                    INNER JOIN ac_tag AS at ON aa.ac_article_id = at.ac_article_id
                    WHERE 1 AND aa.ac_article_date >= '2008-01-01' AND ".$condition."
                    ORDER BY view DESC";
            }
            else{
                $sql = "
                    SELECT DISTINCT aa.ac_article_link AS link, aa.ac_article_name AS name, aa.ac_article_date AS date,
                        aa.ac_article_view AS view
                    FROM ac_article AS aa
                    INNER JOIN ac_tag AS at ON aa.ac_article_id = at.ac_article_id
                    INNER JOIN ac_date AS ad ON ad.ac_date = ac_article_date 
                    WHERE ad.ac_iso_year = ".ACFUN::quote($_REQUEST['date'])." AND ".$condition."
                    ORDER BY view DESC";
            }
            if($_REQUEST['limit']){
                $sql .= " LIMIT ".$_REQUEST['limit'];
            }
            $post_arr = array();
            $result = ACFUN::query($sql);
            mb_internal_encoding("UTF-8");
            while($res = ACFUN::fetch($result)){
                $xValue = $xAxis[$res['date']];
                if(mb_strlen($res['name']) > 12){
                    $len = ceil(mb_strlen($res['name'])/2);
                    $res['name'] = mb_substr($res['name'], 0 , $len).'<br>'.mb_substr($res['name'], $len);
                }
                $post_arr[] = "{date:'".$res['date']."',name: '".addslashes($res['name'])."',link:'".$res['link']."',x:".$xValue.",y:".$res['view']."}";
            }
            $series_arr[] = "{name:'".str_replace('_', '/', $group)."',color:'rgba(".$colors[$index++].")',data:[".implode(",", $post_arr)."]}";

            if(count($post_arr) >= 2000 && $_r > 2){
                $_r = 2;
            }
            else if(count($post_arr) >= 1000 && $_r > 4){
                $_r = 4;
            }
        }
        $series_str = implode(',', $series_arr);
        
        $title_str = "关键词: ".str_replace('_', '/', str_replace(':', ' vs ', $_REQUEST['keywords']));
        $tooltip_str = "";
        $output = "<script type='text/javascript'>";
        $output .= "
            chart = new Highcharts.Chart({
              chart: {
                 renderTo: 'timeline_chart',
                 zoomType: 'xy', 
                 type: 'scatter',
                 startOnTick: true,
                 endOnTick: true,
                 marginRight: 40,
                 marginLeft: 80,
                 marginTop: 50,
                 marginBottom: 30,
              },
              title: {
                 y: 20, 
                 text: '".$title_str."'
              },
              subtitle: {
                 y: 40,
                 text: '',
              },
              xAxis: {
                 min: 1,
                 labels: {
                    enabled: false
                 }
              },
              yAxis: { 
                 type: 'logarithmic',
                 showFirstLabel: false,
                 title: {
                    text: null
                 },
              },
              tooltip: {
                 formatter: function() {
                    return this.point.name+'<br>'+this.point.date+'<br>'+this.point.y+'点击';
                 }
              },      
              plotOptions: {
                series: {
                    marker: {
                        radius: ".$_r.",
                        symbol: 'circle'
                    },
                    turboThreshold: 10000,
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                window.open('http://www.acfun.tv/v/'+this.options.link, '_newtab');
                            }
                        }
                    }
                },
            },
              legend:{
                    backgroundColor: '#111',
                    borderWidth: 1,
                    y: -20
              },
              series: [
                ".$series_str."
              ],
           });
            ";
        $output .= "</script>";
        $mc->set($mckey, $output);
        echo $output;
    }
?>
