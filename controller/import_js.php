<?php

require_once "ACFUN_init.php";

$js = new Js();

class Js {

    private $js_list = array();

    public function print_js($page_name, $print = false) {
        $str = "<script type='text/javascript' src='" . JS_PATH . "/jquery.min.js'></script>\n";
        $str .= "<script type='text/javascript' src='" . JS_PATH . "/ga.js'></script>\n";
        $str .= "<script type='text/javascript' src='" . JS_PATH . "/global_js.js'></script>\n";
        $str .= "<script type='text/javascript' src='" . JS_PATH . '/' . $page_name . "_js.js'></script>\n";
        if (isset($this->js_list[$page_name])) {
            foreach ($this->js_list[$page_name] as $js_file) {
                $str .= "<script type='text/javascript' src='$js_file'></script>\n";
            }
        }

        if ($print) {
            printf($str);
        } else {
            return $str;
        }
    }

    function __construct() {
        $this->js_list['up'][] = JS_PATH.'/jquery.tagcanvas.min.js'; 
        $this->js_list['wiki'][] = JS_PATH.'/jquery-ui-1.8.16.custom.min.js';
        $this->js_list['stat'][] = JS_PATH.'/Highcharts/js/highcharts.js';
        $this->js_list['stat'][] = JS_PATH.'/Highcharts/js/modules/exporting.js';
        $this->js_list['stat'][] = JS_PATH.'/Highcharts/js/themes/gray.js';

        $this->js_list['timeline'][] = JS_PATH.'/Highcharts/js/highcharts.js';
        $this->js_list['timeline'][] = JS_PATH.'/Highcharts/js/modules/exporting.js';
                $this->js_list['timeline'][] = JS_PATH.'/Highcharts/js/themes/gray.js';
    }

}

