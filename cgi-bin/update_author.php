#!/usr/bin/php
<?php
    include "/var/www/acfun/ACFUN_init.php";

    $sql = "SELECT * FROM ac_article WHERE ac_author_name != 'Admin' AND ac_author_id = 0 GROUP BY ac_author_name";
    $result = ACFUN::query($sql);
    while($res = ACFUN::fetch($result)){
        $sql = "SELECT * FROM ac_author WHERE ac_author_name = ".ACFUN::quote($res['ac_author_name']);
        $author_res = ACFUN::query($sql);
        if(ACFUN::is_empty($author_res))
        {
            $sql = "INSERT INTO ac_author(ac_author_name) VALUE(".ACFUN::quote($res['ac_author_name']).")";
        }
        else{
            $author = ACFUN::fetch($author_res);
            $sql = "UPDATE ac_article SET ac_author_id = ".$author['ac_author_id']." WHERE ac_author_name = ".ACFUN::quote($author['ac_author_name']);
        }
        ACFUN::query($sql);
        // echo $sql."<br>";
    }

    return;
?>
