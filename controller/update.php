<?php
    require_once "ACFUN_init.php";
    $inf_arr = json_decode($_REQUEST['inf']);
    $sql = "SELECT * FROM ac_article WHERE ac_article_link = ".ACFUN::quote($inf_arr[2]);
    $result = ACFUN::query($sql);
    if(ACFUN::is_empty($result)){
        $sql = sprintf("INSERT INTO ac_article(
                ac_article_category, 
                ac_article_name, 
                ac_article_link, 
                ac_article_name, 
                ac_article_date, 
                ac_article_view, 
                ac_article_comment, 
                ac_article_created) 
                VALUE(%s, %s, %s, %s, %s, %s, %s, NOW()) ",
                    ACFUN::quote($inf_arr[0]), 
                    ACFUN::quote($inf_arr[1]), 
                    ACFUN::quote($inf_arr[2]), 
                    ACFUN::quote($inf_arr[3]), 
                    ACFUN::quote($inf_arr[4]), 
                    ACFUN::quote($inf_arr[5]), 
                    ACFUN::quote($inf_arr[6])
                );
        echo "remote: add ".$inf_arr[2];
    }
    else{
        $sql = sprintf("UPDATE ac_article SET 
                ac_article_view = %s, 
                ac_article_comment = %s 
                WHERE ac_article_link = %s",
                    ACFUN::quote($inf_arr[5]),
                    ACFUN::quote($inf_arr[6]),
                    ACFUN::quote($inf_arr[2])
                );
        echo "remote: update ".$inf_arr[2];
    }
    ACFUN::query($sql);
?>

