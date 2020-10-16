<?php

// получаем список точек продаж

\Nyos\mod\items::$sql_select_vars = ['head', 'id'];
$vv['sps'] = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_sale_point, 'show', 'id_id');
//\f\pa($vv['sps']);
// получаем список главных точек продаж (по дате конца периода отслеживаем)

if (!empty($_GET['d_fin'])) {
    \Nyos\mod\items::$sql_select_vars = ['jobman', 'sale_point'];
    \Nyos\mod\items::$search['date'] = $_GET['d_fin'];

    $t = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_buh_head_sp);
    $vv['sps_heads__jm_sp'] = [];

    foreach ($t as $k => $v) {
        if (!empty($v['jobman']) && !empty($v['sale_point']))
            $vv['sps_heads__jm_sp'][$v['jobman']] = $v['sale_point'];
    }

    //unset($t);
    
    
//    echo '-------';
//    \f\pa($vv['sps_heads__jm_sp']);
//    \f\pa($t);
//    echo '++++++++';

}


if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'print') {

    $tpl_print_end = \f\like_tpl('body.2007.print', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);

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