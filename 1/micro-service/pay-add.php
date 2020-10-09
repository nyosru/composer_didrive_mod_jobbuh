<?php

try {

//    if (empty($_REQUEST['date']))
//        throw new \Exception('нет даты');
//
//    if (empty($_REQUEST['user']))
//        throw new \Exception('нет пользователя');
//
//    if (empty($_REQUEST['dolgn']))
//        throw new \Exception('нет должности');

    if (isset($skip_start) && $skip_start === true) {
        
    } else {
        require_once '0start.php';
        $skip_start = false;
    }

//    $indb = array(
//        'head' => 1,
//        'jobman' => $_REQUEST['user'],
//        'sale_point' => $_POST['sp'] ?? $_REQUEST['sp'],
//        'dolgnost' => $_REQUEST['dolgn'],
//        'date' => $_REQUEST['date'],
//        
////        'start' => date('Y-m-d H:i', $start_time),
////        'fin' => date('Y-m-d H:i', $fin_time),
////        // 'hour_on_job' => \Nyos\mod\IikoChecks::calculateHoursInRange( date('Y-m-d H:i', $start_time), date('Y-m-d H:i', $fin_time)),
////        'hour_on_job' => \Nyos\mod\IikoChecks::calcHoursInSmena(date('Y-m-d H:i', $start_time), date('Y-m-d H:i', $fin_time)),
////        // 'hour_on_job' => \Nyos\mod\IikoChecks::calculateHoursInRangeUnix($start_time, $fin_time),
////        'who_add_item' => 'admin',
////        'who_add_item_id' => $_SESSION['now_user_di']['id'] ?? '',
////        'ocenka' => $_REQUEST['ocenka']
//    );
//
//    //\f\pa($indb);
//    \Nyos\mod\items::$type_module = 2;

    $indb = $_REQUEST;

    $indb['pay_buh_who'] = $_SESSION['now_user_di']['id'];
    $indb['pay_buh_dt'] = date('Y-m-d h:i:s');
    
    $e = \Nyos\mod\items::add($db, \Nyos\mod\JobDesc::$mod_buh_oplats, $indb);
    // \f\pa($e);
    
//    \f\end2( 'добавлено' );
//    
//    // $ee = file_get_contents( 'http://'.$_SERVER['HTTP_HOST'].'/i.didrive.php?level=000.job&refresh_db=sd&only=jobman_send_on_sp&show_res=no' );
//    die();
//    $ee = file_get_contents( 'http://'.$_SERVER['HTTP_HOST'].'/i.didrive.php?level=000.job&refresh_db=sd&only=050.chekin_checkout&show_res=no' );
//        
//    
//    
//    
//    
//    $ee = file_get_contents( 'http://'.$_SERVER['HTTP_HOST'].'/i.didrive.php?level=000.job&refresh_db=sd' );
//
//    \f\end2('<div class="warn" style="padding:5px;" >'
//            . '<nobr><b>смена добавлена</b>'
//            . '<br/>с ' . date('d.m.y H:i', $start_time)
//            . '<br/>до ' . date('d.m.y H:i', $fin_time)
//            . '<br/>часов на работе ' . $indb['hour_on_job']
//            . '<hr>'. $ee . '<hr>'
//            . '</nobr>'
//            . '</div>', true);

    \f\end2('не', false);
} catch (Exception $exc) {

    echo '<pre>';
    print_r($exc);
    echo '</pre>';
    // echo $exc->getTraceAsString();
}