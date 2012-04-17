<?php

require_once "ACFUN_init.php";

$style = new Style();

class Style {

    private $css_list = array();

    public function print_css($page_name, $print = false) {
        $str = "<style type=\"text/css\">\n";
        $str .= "@import url(\"" . CSS_PATH . '/global_style.css' . "\");\n";
        $str .= "@import url(\"" . CSS_PATH . '/' . $page_name . "_style.css\");\n";
        if (isset($this->css_list[$page_name])) {
            foreach ($this->css_list[$page_name] as $css_file) {
                $str .= "@import url(\"" . $css_file . "\");\n";
            }
        }
        $str .= "</style>\n";

        if ($print) {
            printf($str);
        } else {
            return $str;
        }
    }

    function __construct() {
		$this->css_list['wiki'][] = CSS_PATH.'/jquery-ui-1.8.16.custom.css';
    }

}

