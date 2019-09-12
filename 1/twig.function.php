<?php

/**
  определение функций для TWIG
 */
//creatSecret

/**
 * достаём кому сколько нужно заплатить
 */
$function = new Twig_SimpleFunction('job_buhGetGuhPay', function ( $db, $date_start, $date_fin ) {

//    echo '<br/>' . $date_start;
//    echo '<br/>' . $date_fin;

    $pays = [];

    \Nyos\mod\items::$sql_itemsdop_add_where = '
        ( midop.name != \'start\' OR
            ( 
                midop.name = \'start\' AND 
                midop.value_datetime >= date(\'' . $date_start . '\') AND 
                midop.value_datetime <= date(\'' . $date_fin . '\') 
            )
        )
        ';
    $vv['checks'] = \Nyos\mod\items::getItems($db, \Nyos\nyos::$folder_now, '050.chekin_checkout', 'show', null);
    // \f\pa($vv['checks']);
    foreach ($vv['checks']['data'] as $k => $v) {

        if (isset($v['dop']['start']) && isset($v['dop']['jobman'])) {

            $pays[$v['dop']['jobman']][date('Y-m-d', strtotime($v['dop']['start']))][] = array(
                // 'sp' => $v['dop']['sale_point'] ,
                'text' => 'рабочая смена с '.$v['dop']['start'].' по '.$v['dop']['fin'] ,
                'summa' => 1 ,
                'type' => 'smena' ,
                'payed' => ( isset($v['dop']['pay_buh']) && $v['dop']['pay_buh'] == 'da' ) ? '+' : '-' ,
                'full' => $v
                );
        }
    }


    \Nyos\mod\items::$sql_itemsdop_add_where = '
        ( midop.name != \'date_now\' OR
            ( 
                midop.name = \'date_now\' AND 
                midop.value_date >= date(\'' . $date_start . '\') AND 
                midop.value_date <= date(\'' . $date_fin . '\') 
            )
        )
        ';
    $vv['minusa'] = \Nyos\mod\items::getItems($db, \Nyos\nyos::$folder_now, '072.vzuscaniya', 'show', null);
//    \f\pa($vv['minusa']);
    foreach ($vv['minusa']['data'] as $k => $v) {

        if (isset($v['dop']['date_now']) && isset($v['dop']['jobman'])) {

            $pays[$v['dop']['jobman']][$v['dop']['date_now']][] = array(
                // 'sp' => $v['dop']['sale_point'] ,
                'text' => 'взыскание '. ( isset($v['dop']['text']) ? '( '.$v['dop']['text'].' )' : '' ) ,
                'summa' => $v['dop']['summa'] ,
                'type' => 'minus' ,
                'payed' => ( isset($v['dop']['pay_buh']) && $v['dop']['pay_buh'] == 'da' ) ? '+' : '-' ,
                'full' => $v
                );
        }
    }


    \Nyos\mod\items::$sql_itemsdop_add_where = '
        ( midop.name != \'date_now\' OR
            ( 
                midop.name = \'date_now\' AND 
                midop.value_date >= date(\'' . $date_start . '\') AND 
                midop.value_date <= date(\'' . $date_fin . '\') 
            )
        )
        ';
    $vv['plus'] = \Nyos\mod\items::getItems($db, \Nyos\nyos::$folder_now, '072.plus', 'show', null);
    // \f\pa($vv['plus']);
    foreach ($vv['plus']['data'] as $k => $v) {

        if (isset($v['dop']['date_now']) && isset($v['dop']['jobman'])) {

            $pays[$v['dop']['jobman']][$v['dop']['date_now']][] = array(
                // 'sp' => $v['dop']['sale_point'] ,
                'text' => 'премия '. ( isset($v['dop']['text']) ? '( '.$v['dop']['text'].' )' : '' ) ,
                'summa' => $v['dop']['summa'] ,
                'type' => 'plus' ,
                'payed' => ( isset($v['dop']['pay_buh']) && $v['dop']['pay_buh'] == 'da' ) ? '+' : '-' ,
                'full' => $v
                );
        }
    }

    // ksort($pays);
    
    return $pays;
    
//    return array(
//        'pays' => $pays,
//        'cheks' => $vv['checks'],
//        'minusa' => $vv['minusa'],
//        'plus' => $vv['plus']
//    );
});
$twig->addFunction($function);
