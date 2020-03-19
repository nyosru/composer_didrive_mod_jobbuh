<?php

<<<<<<< HEAD
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'print') {
    $tpl_print_end = \f\like_tpl('body.print', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);
// echo '<br/>#'.__LINE__.' '.$tpl_print_end;
} else {
=======
$vv['in_body_end'][] = '<script defer="defer" src="' . DS . 'vendor' . DS . 'didrive' . DS . 'base' . DS . 'js.lib' . DS . 'jquery.ba-throttle-debounce.min.js"></script>';

$vv['krohi'] = [];
$vv['krohi'][1] = array(
    'text' => $vv['now_level']['name'],
    'uri' => $vv['now_level']['cfg.level']
);
>>>>>>> 7589ba5012cc822af1d77e0bae5b96ed696a860c

    $vv['in_body_end'][] = '<script defer="defer" src="' . DS . 'vendor' . DS . 'didrive' . DS . 'base' . DS . 'js.lib' . DS . 'jquery.ba-throttle-debounce.min.js"></script>';

    $vv['krohi'] = [];
    $vv['krohi'][1] = array(
        'text' => $vv['now_level']['name'],
        'uri' => $vv['now_level']['cfg.level']
    );

    $vv['tpl_body'] = \f\like_tpl('body', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);
}