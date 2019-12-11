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

    return \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_fin, $sp_on );
    
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
    
    $d = \Nyos\mod\items::getItemsSimple($db, '075.buh_oplats' );
    
    $r = [];
    
    foreach( $d['data'] as $k => $v ){
        if( isset($v['dop']['checkin']) ){
        $r[$v['dop']['checkin']][] = $v['dop'];
        }
    }
    
    return $r;
    
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('job_buh__getSalarisNow', function ( $db, $sp, $dolgn, $date, $ocenka ) {
// echo '$function = new Twig_SimpleFunction(\'job_buh__getSalarisNow\', function ( $db, '.$sp.', '.$dolgn.', '.$date.', '.$ocenka.' )';

    echo '<br/>return \Nyos\mod\JobDesc::getSalarisNow( $db, '.$sp.', '.$dolgn.', '.$date.', '.$ocenka.' );';
    return \Nyos\mod\JobDesc::getSalarisNow( $db, $sp, $dolgn, $date, $ocenka );
    
});
$twig->addFunction($function);



