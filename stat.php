<?php
    require_once "ACFUN_init.php";

    /* import the controller, which fetches all the components needed */
    require_once CONTROLLER_PATH . "/controller.php";

    /* draw page frame, including header, meta, javascipt ans css */
    $frame->print_frame('stat', true);

    /* draw page */
    require_once VIEW_PATH . '/stat.php';

    /* draw bottom */
    $frame->print_frame(true);
?>
