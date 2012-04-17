<?php

require_once "ACFUN_init.php";

$frame = new Frame();

class Frame {

    public function print_frame($page_name = "default", $print = false) {
        global $js;
        global $style;

        $str = "<!DOCTYPE html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n".
                "<link rel=\"shortcut icon\" href=\"images/logo.ico\" type=\"image/x-icon\" />\n".
                $style->print_css($page_name).
                $js->print_js($page_name).
                "<title>王爷千古。丞相万岁!</title>\n</head>\n". 
                "<body>\n\t<div id='container'>\n";

        if ($print) {
            echo $str;
            require_once VIEW_PATH . '/nav_bar.php';
            echo "\t\t<div id='content'>\t";
        } else {
            return $str;
        }
    }

    public function print_bottom($print = false){
        if ($print) {
            printf("</div></div></body></html>");
        }
        else{
            return "</div></div></body></html>";
        }
    }
    function __construct() {
        
    }

}

