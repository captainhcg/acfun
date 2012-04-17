<?php
    require_once "ACFUN_init.php";
    if($_SERVER['HTTP_REFERER'] == "http://www.acfun.tv/" && $_REQUEST['type'] == 'all' )
    {
        ACFUN::redirect_to("/acfun/up.html?type=week");
    }

    /* import the controller, which fetches all the components needed */
    require_once CONTROLLER_PATH . "/controller.php";

    /* draw page frame, including header, meta, javascipt ans css */
    $frame->print_frame('up', true);

    /* draw page */
    require_once VIEW_PATH . '/up.php';

    printf("</div></div></body></html>");
?>
