<?php

/**
  определение функций для TWIG
 */
//creatSecret
// $function = new Twig_SimpleFunction('creatSecret', function ( string $text ) {
//    return \Nyos\Nyos::creatSecret($text);
// });
// $twig->addFunction($function);


$function = new Twig_SimpleFunction('job_buh__getChecksMinusPlus', function ( $db, $date_start, $date_fin, $sp_on = 'all', $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp' ) {

    return \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_fin, $sp_on);
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('job_buh__jobman_on_sp', function ( $db, string $date_start, string $date_finish ) {

    return \Nyos\mod\JobDesc::getSetupJobmanOnSp($db, $date_start, $date_finish);
});
$twig->addFunction($function);


/**
 * получаем сотрудников кто работал в указанном промежутке
 */
$function = new Twig_SimpleFunction('job_buh__getJobmansOnTime', function ( $db, string $date_start, string $date_finish ) {

    return \Nyos\mod\JobDesc::getJobmansOnTime($db, $date_start, $date_finish);
});
$twig->addFunction($function);


/**
 * получаем список платежей по чекинам
 */
$function = new Twig_SimpleFunction('job_buh__get_payed', function ( $db, string $date_start, string $date_finish ) {
    
//    echo '<br/>'.__FILE__.' #'.__LINE__;
    
    // \Nyos\mod\items::$show_sql = true;
//    \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` midop ON '
//            . ' midop.name = \'date\' '
//            . ' AND midop.id_item = mi.id '
//            . ' AND midop.value_date >= \'' . date('Y-m-d', strtotime($date_start)) . '\' '
//            . ' AND midop.value_date <= \'' . date('Y-m-d', strtotime($date_finish)) . '\' '
//    ;
    $d = \Nyos\mod\items::getItemsSimple3($db, '075.buh_oplats');

    // \f\pa($d,'','','d');
    
    $r = [ 
//        'checkins' => [], 
//        'payed' => [], 
//        'other' => []
        ];

    foreach ($d as $k => $v) {

        if (isset($v['checkin'])) {
            $r['checkins'][$v['checkin']][] = $v;
        } elseif (isset($v['date']) && isset($v['user_id']) ) {
            $r['payed'][$v['user_id']][$v['date']][] = $v;
        } else {
            $r['other'][] = $v;
        }
    }

    return $r;
});
$twig->addFunction($function);










$function = new Twig_SimpleFunction('job_buh__getSalarisNow', function ( $db, $sp, $dolgn, $date, $ocenka ) {
// echo '$function = new Twig_SimpleFunction(\'job_buh__getSalarisNow\', function ( $db, '.$sp.', '.$dolgn.', '.$date.', '.$ocenka.' )';

    echo '<br/>return \Nyos\mod\JobDesc::getSalarisNow( $db, ' . $sp . ', ' . $dolgn . ', ' . $date . ', ' . $ocenka . ' );';
    return \Nyos\mod\JobDesc::getSalarisNow($db, $sp, $dolgn, $date, $ocenka);
});
$twig->addFunction($function);



