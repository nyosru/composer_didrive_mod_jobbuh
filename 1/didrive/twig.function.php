<?php

/**
  определение функций для TWIG
 */
//creatSecret
// $function = new Twig_SimpleFunction('creatSecret', function ( string $text ) {
//    return \Nyos\Nyos::creatSecret($text);
// });
// $twig->addFunction($function);

// creatSecret
 $function = new Twig_SimpleFunction('jobdesc__newGetSmensFullMonth', function ( $db, $user, $date ) {
    
     $res = \Nyos\mod\JobDesc::newGetSmensFullMonth($db, $user, $date);
     // \f\pa($res);
     
     return $res;
 });
 $twig->addFunction($function);

 $function = new Twig_SimpleFunction('jobdesc__newGetPaysDopFullMonth', function ( $db, $user, $date ) {
    
     $res = \Nyos\mod\JobDesc::newGetPaysDopFullMonth($db, $user, $date);
     // \f\pa($res);
     
     return $res;
 });
 $twig->addFunction($function);

$function = new Twig_SimpleFunction('get_buh_PM_cfg', function () {

    $return = [];

    //echo '<div style="text-align:left;" >';
    //\f\pa(\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]);

    for ($w = 1; $w <= 20; $w++) {

        $t = 'plus';
        if (!empty(\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w]) && !empty(\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w . 'val'])) {
            $return[$t][\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w . 'val']] = \Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w];
        }

        $t = 'minus';
        if (!empty(\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w]) &&
                !empty(\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w . 'val'])) {
            $return[$t][\Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w . 'val']] = \Nyos\Nyos::$menu[\Nyos\mod\JobDesc::$mod_buh_pm]['type_' . $t]['item' . $w];
        }
    }

    return $return;
});
$twig->addFunction($function);




/**
 * получаем список главных точек за дату сотрудникам
 */
$function = new Twig_SimpleFunction('job_buh__get_head_sp', function ( $db, $date_finish ) {

    $ar__jobman_sp = \Nyos\mod\JobBuh::getHeadSps($db, $date_finish );
    
    return $ar__jobman_sp;
});
$twig->addFunction($function);




$function = new Twig_SimpleFunction('job_buh__get_buh_PM2', function ($db, $date, $user) {

    $sql = 'SELECT * FROM mod_003_money_buh_pm WHERE date = :date and jobman = :jm ';
                $ff = $db->prepare($sql);
                $sql_vars = [
                    ':date' => $date,
                    ':jm' => $user
                ];
                $ff->execute($sql_vars);

// $return = [];
                return $ff->fetchAll();
                // echo '<div style="padding:10px; border: 1px solid green;" >';
//
//                $return = $return1 = [];
//
//                while ($v = $ff->fetch()) {
//    
//    return $ar__jobman_sp;
});
$twig->addFunction($function);




$function = new Twig_SimpleFunction('job_buh__get_buh_PM', function ($db, $date, $user) {

    // если нет переменной то не пишем кеш            
    //$cash_var = 'jobdesc__money_mp_buh__' . \Nyos\mod\JobDesc::$mod_buh_pm . '_jm' . $user . '_date' . $date;
    // если не указали время жизни то оно бесконечно    
    // $cash_time_sec = 3600*24;
    // если есть таймер то показываем таймер выполнения
    //$show_timer = rand(0, 9999);

    if (!empty($cash_var) && !empty($cash_var)) {

        if (!empty($show_timer)) {
            echo '<br/>#' . __LINE__ . ' var ' . $cash_var;
            \f\timer_start($show_timer);
        }

        $return = \f\Cash::getVar($cash_var);
    }

    if (!empty($return)) {

        if (!empty($show_timer))
            echo '<br/>#' . __LINE__ . ' данные из кеша';

        if (isset($return[0]) && $return[0] == 'mc_skip') {
            if (!empty($show_timer))
                echo '<br/>#' . __LINE__ . ' данных нет, возвращаем null';
            unset($return);
        }
    } else {

        $return = [];

        if (!empty($show_timer))
            echo '<br/>#' . __LINE__ . ' считаем данные и пишем в кеш';

        // тут супер код делающий $return старт

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'date\' '
                . ' AND mid.value_date = :d '
                . ' INNER JOIN `mitems-dops` mid2 '
                . ' ON mid2.id_item = mi.id '
                . ' AND mid2.name = \'jobman\' '
                . ' AND mid2.value = :jm '
        ;

        \Nyos\mod\items::$var_ar_for_1sql[':jm'] = $user;

        // $ds = date('Y-m-01', strtotime($date));
        \Nyos\mod\items::$var_ar_for_1sql[':d'] = date('Y-m-01', strtotime($date));

        // echo \Nyos\mod\JobDesc::$mod_buh_pm;
        
        $ee = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_buh_pm);
        // \f\pa($ee);
        // $df = date('Y-m-d', strtotime($ds . ' +1 month -1 day'));

        foreach ($ee as $k => $v) {
            $return[$v['type_minus'] ?? $v['type_plus'] ?? '-'] = [
                'id' => $v['id']
                , 'summa' => $v['summa']
            ];
        }

        // \f\pa($return);
//    for ($er = 0; $er <= 32; $er++) {
//        $nd = date('Y-m-d', strtotime($ds . ' +' . $er . 'day'));
//
//        if ($nd > $df)
//            break;
//        
//    }
//
//    return $ee;
        // тут супер код делающий $return конец

        if (!empty($return)) {
            if (!empty($cash_var))
                \f\Cash::setVar($cash_var, $return, ( $cash_time_sec ?? 0));
        } else {
            if (!empty($cash_var))
                \f\Cash::setVar($cash_var, [0 => 'mc_skip'], ( $cash_time_sec ?? 0));
            if (!empty($show_timer))
                echo '<br/>#' . __LINE__ . ' нет данных ничего не пишем в кеш';
        }
    }

    if (!empty($show_timer))
        echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop($show_timer);

    return $return ?? [];
    //return \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_fin, $sp_on);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('job_buh__getChecksMinusPlus', function ($db, $date_start, $date_fin, $sp_on = 'all', $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp') {

    return \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_fin, $sp_on);
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('job_buh__jobman_on_sp', function ($db, string $date_start, string $date_finish) {

    return \Nyos\mod\JobDesc::getSetupJobmanOnSp($db, $date_start, $date_finish);
});
$twig->addFunction($function);


/**
 * получаем сотрудников кто работал в указанном промежутке
 */
$function = new Twig_SimpleFunction('job_buh__getJobmansOnTime', function ($db, string $date_start, string $date_finish) {

    return \Nyos\mod\JobDesc::getJobmansOnTime($db, $date_start, $date_finish);
});
$twig->addFunction($function);


/**
 * получаем список платежей по чекинам
 */
$function = new Twig_SimpleFunction('job_buh__get_payed', function ($db, string $date_start, string $date_finish) {

//    echo '<br/>'.__FILE__.' #'.__LINE__;
    // \Nyos\mod\items::$show_sql = true;
    \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` midop ON '
            . ' midop.name = \'date\' '
            . ' AND midop.id_item = mi.id '
            . ' AND midop.value_date >= \'' . date('Y-m-d', strtotime($date_start)) . '\' '
            . ' AND midop.value_date <= \'' . date('Y-m-d', strtotime($date_finish)) . '\' '
    ;
    $d = \Nyos\mod\items::getItemsSimple3($db, '075.buh_oplats', null, 'date_asc');

    // \f\pa($d,'','','d');

    $r = [
//        'checkins' => [], 
//        'payed' => [], 
//        'other' => []
    ];

    foreach ($d as $k => $v) {

        if (isset($v['checkin'])) {
            $r['checkins'][$v['checkin']][] = $v;
        } elseif (isset($v['date']) && isset($v['user_id'])) {
            $r['payed'][$v['user_id']][$v['date']][] = $v;
        } else {
            $r['other'][] = $v;
        }
    }

    return $r;
});
$twig->addFunction($function);










$function = new Twig_SimpleFunction('job_buh__getSalarisNow', function ($db, $sp, $dolgn, $date, $ocenka) {
// echo '$function = new Twig_SimpleFunction(\'job_buh__getSalarisNow\', function ( $db, '.$sp.', '.$dolgn.', '.$date.', '.$ocenka.' )';

    echo '<br/>return \Nyos\mod\JobDesc::getSalarisNow( $db, ' . $sp . ', ' . $dolgn . ', ' . $date . ', ' . $ocenka . ' );';
    return \Nyos\mod\JobDesc::getSalarisNow($db, $sp, $dolgn, $date, $ocenka);
});
$twig->addFunction($function);



