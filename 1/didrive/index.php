<?php

if (isset($_REQUEST['new_buh']{0})) {
    $_SESSION['new_buh'] = $_REQUEST['new_buh'];
    \f\redirect('/', 'i.didrive.php?level=' . $_GET['level']);
}




if ( 1 == 2 && isset($_SESSION['new_buh']) && $_SESSION['new_buh'] == 2 ) {

    if (empty($vv['dihead']))
        $vv['dihead'] = '';

    echo '<div style="position: fixed; bottom: 100px; right: 50px; width: 350px;" >';

    $dirs_for_scan = [
//        __DIR__ . DS . 'dist' . DS . 'assets' . DS . 'css' . DS => '/vendor/didrive_mod/jobdesc/1/didrive/dist/assets/css/',
//        __DIR__ . DS . 'dist' . DS . 'assets' . DS . 'js' . DS => '/vendor/didrive_mod/jobdesc/1/didrive/dist/assets/js/',
//        __DIR__ . DS . 'dist' . DS . 'css' . DS => '/vendor/didrive_mod/jobdesc/1/didrive/dist/css/',
//        __DIR__ . DS . 'dist' . DS . 'js' . DS => '/vendor/didrive_mod/jobdesc/1/didrive/dist/js/',
        __DIR__ . DS . 'vue' . DS . 'dist' . DS . 'css' . DS => '/vendor/didrive_mod/job_buh/1/didrive/vue/dist/css/',
        __DIR__ . DS . 'vue' . DS . 'dist' . DS . 'js' . DS => '/vendor/didrive_mod/job_buh/1/didrive/vue/dist/js/',
    ];

    foreach ($dirs_for_scan as $d => $dir_local) {

// echo '<br/>#'.__LINE__.' '.__DIR__;
        if (is_dir($d)) {

            $list_f = scandir($d);
            foreach ($list_f as $v) {

                if (!isset($v{5}))
                    continue;

                // echo '<br/>#' . __LINE__ . ' ++1++ ' . $v;

                if (strpos($v, '.css') !== false && strpos($v, 'app.') !== false) {
                    echo '<br/>#' . __LINE__ . ' ' . $v;
                    $vv['dihead'] .= '<link href="' . $dir_local . $v . '" rel="stylesheet">';
                }

                if (strpos($v, '.js') !== false && ( strpos($v, 'app.') !== false || strpos($v, 'chunk') !== false )) {
                    echo '<br/>#' . __LINE__ . ' ' . $v;
                    $vv['in_body_end'][] = '<script type="text/javascript" defer="defer" src="' . $dir_local . $v . '"></script>';
                }

            }

        }
    }
//// $vv['dihead'] .= '<link href="/assets/css/app.640f582b232504c57832.css" rel="stylesheet">';
//
//    if (is_dir(__DIR__ . DS . 'dist' . DS . 'assets' . DS . 'js' . DS)) {
//        $list_f = scandir(__DIR__ . DS . 'dist' . DS . 'assets' . DS . 'js' . DS);
//        foreach ($list_f as $v) {
//            if (strpos($v, '.js') !== false && (strpos($v, 'app.') !== false || strpos($v, 'vendors.') !== false)) {
//                // echo '<br/>#' . __LINE__ . ' ' . $v;
//                $vv['in_body_end'][] = '<script type="text/javascript" defer="defer" src="/vendor/didrive_mod/jobdesc/1/didrive/dist/assets/js/' . $v . '"></script>';
//            }
//        }
//    }
// $vv['in_body_end'][] = '<script type="text/javascript" src="/assets/js/vendors.f2e79c865ce2172cb7cb.js"></script>';
// $vv['in_body_end'][] = '<script type="text/javascript" src="/assets/js/app.caac25aa43296c4b8c6d.js"></script>';

    echo '</div>';
}







if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'print') {

    $tpl_print_end = \f\like_tpl('body.print', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);

// echo '<br/>#'.__LINE__.' '.$tpl_print_end;
} else {

    $vv['in_body_end'][] = '<script defer="defer" src="' . DS . 'vendor' . DS . 'didrive' . DS . 'base' . DS . 'js.lib' . DS . 'jquery.ba-throttle-debounce.min.js"></script>';

    $vv['krohi'] = [];
    $vv['krohi'][1] = array(
        'text' => $vv['now_level']['name'],
        'uri' => $vv['now_level']['cfg.level']
    );

    $vv['tpl_body'] = \f\like_tpl('body', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);
    
}



//$vv['in_body_end'][] = '<script defer="defer" src="' . DS . 'vendor' . DS . 'didrive' . DS . 'base' . DS . 'js.lib' . DS . 'jquery.ba-throttle-debounce.min.js"></script>';





