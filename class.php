<?php

/**
  класс модуля
 * */

namespace Nyos\mod;

if (!defined('IN_NYOS_PROJECT'))
    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JobBuh {

    public static $cash = [];

    /**
     * получаем сумму оборота за месяц по точке продаж
     * @param type $db
     * @param type $sp
     * @param type $str_date
     * @param type $module_sp
     * @param type $module_oborot
     * @return type
     */
    public static function getOborotSpMonth($db, $sp_id, $str_date, $module_sp = 'sale_point', $module_oborot = 'sale_point_oborot') {

        $d_month = date('Y-m', strtotime($str_date));

        if (!empty(self::$cash['month'][$sp_id][$d_month]))
            return self::$cash['month'][$sp_id][$d_month];

        self::$cash['month'][$sp_id][$d_month] = 0;

        $d_start = $d_month . '-01';
        $d_finish = date('Y-m-d', strtotime($d_start . ' +1 month -1 day'));
        
//        \Nyos\mod\items::$where2 = ' AND ( midop.name = \'date\' AND midop.value_date '
//                . ' BETWEEN \''.date('Y-m-d',strtotime($d_start)).'\' '
//                . ' AND \''.date('Y-m-d',strtotime($d_finish)).'\' ) ';
//        $oborots = \Nyos\mod\items::getItemsSimple2($db, $module_oborot);
        
        $oborots = \Nyos\mod\items::getItemsSimple3($db, $module_oborot);
        // \f\pa($oborots);

        foreach ($oborots as $k => $v) {
            if (isset($v['sale_point']) && $v['sale_point'] == $sp_id && $v['date'] >= $d_start && $v['date'] <= $d_finish) {
                if (!empty($v['oborot_server'])) {
                    self::$cash['month'][$sp_id][$d_month] += $v['oborot_server'];
                    // $r[ $d_start .' + '. $d_finish .' ++ '. $v['dop']['date'] .' - '. $v['dop']['sale_point'] ] = $v['dop']['oborot_server'];
                }
            }
        }

        return self::$cash['month'][$sp_id][$d_month];
    }

    /**
     * выдёргиваем все плюса и минуса
     * @param type $db
     * @param type $date_start
     * @param type $date_fin
     * @param type $mod_minusa
     * @param type $mod_plus
     * @return type
     */
    public static function calcPlusMinus($db, $date_start, $date_fin, $mod_minusa = '072.vzuscaniya', $mod_plus = '072.plus') {

        $dt_start = date('Y-m-d 07:00:01', strtotime($date_start));
        $dt_fin = date('Y-m-d 03:00:01', strtotime($date_fin ?? $dt_start) + 3600 * 24);

        \Nyos\mod\items::$get_data_simple = true;
//        \Nyos\mod\items::$sql_itemsdop_add_where = '( ( midop.name = \'date_now\' AND midop.value_date >= \''.$date_start.'\' AND midop.value_date <= \''.$date_fin.'\' ) OR midop.name != \'date_now\' )';
        $m = \Nyos\mod\items::getItemsSimple($db, $mod_minusa);

//        \f\pa($m,'','','minus');

        $return = [];

        foreach ($m as $k => $v) {

            // пропускаем если 
            if (empty($v['date_now']) || ( $v['date_now'] <= $date_start || $v['date_now'] >= $date_fin ))
                continue;

            $v['type'] = 'minus';
            $return[$v['jobman']][$v['date_now']][] = $v;
        }

        // \f\pa($return,'','','ret minus');

        \Nyos\mod\items::$get_data_simple = true;
        $plusa = \Nyos\mod\items::getItemsSimple($db, $mod_plus);
        // \f\pa($plusa);

        foreach ($plusa as $k => $v) {

            // пропускаем если 
            if (empty($v['date_now']) || ( $v['date_now'] <= $date_start || $v['date_now'] >= $date_fin ))
                continue;

            $v['type'] = 'plus';
            $return[$v['jobman']][$v['date_now']][] = $v;
        }

        return $return;
    }

    /**
     * 
     * @param type $db
     * @param type $date_start
     * @param type $date_fin
     * @param type $module_jobman
     * @param type $module_sp
     * @param type $module_send_jobman_to_sp
     * @return type
     */
    public static function calcChecks($db, $date_start, $date_fin, $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp') {


        $dt_start = date('Y-m-d 07:00:01', strtotime($date_start));
        $dt_fin = date('Y-m-d 03:00:01', strtotime($date_fin ?? $dt_start) + 3600 * 24);

        $d1 = substr($dt_start, 0, 10);
        // echo '||'.$d1.'||';
//        $ww = \Nyos\mod\JobDesc::whereJobmansOnSp( $db, null, substr($dt_start,0,10), substr($dt_fin,0,10) );
//        \f\pa($ww,2,'','\Nyos\mod\JobDesc::whereJobmansOnSp');
        // $e = \Nyos\mod\JobDesc::compileSalarysJobmans($db, substr($dt_start,0,10) );
        //\Nyos\mod\JobDesc::getOborotSp($db, $sp, $date)
//        \f\pa($date_start);
//        \f\pa($date_fin);
//        $salarises = \Nyos\mod\JobDesc::getSalarisPeriod($db, $date_start, $date_fin);
        // compileSalarysJobmans($db, substr($dt_start,0,10) );
        // \f\pa($salarises, 2, '', '$salarises');
        // \f\pa($salarises, '', '', '$salarises');

        // \f\timer::start(156);
        
        $return = [
            // тащим список назначений на работу в точке продаж в период времени
            'jobman_on' => \Nyos\mod\JobDesc::getJobmansOnTime1910($db, $dt_start, $dt_fin)
                // ,
                // ,'where_job' => \Nyos\mod\JobDesc::whereJobmansOnSp($db, null, $dt_start, $dt_fin)
                // ,
        ];

        // echo '<br/>timer1:'. \f\timer::stop( 'str' , 156 );

        
        // \f\pa($return,2,'','$return');

        $where_job = \Nyos\mod\JobDesc::whereJobmansPeriod($db, $dt_start, $dt_fin);
        // \f\pa($where_job,2,'','$where_job');

        // echo '<br/>timer2:'. \f\timer::stop( 'str' , 156 );

                        
        
        \Nyos\mod\items::$get_data_simple = true;
        $jobmans = \Nyos\mod\items::getItemsSimple($db, $module_jobman);
        // \f\pa($jobmans,2,'','$jobmans');

        // echo '<br/>timer2:'. \f\timer::stop( 'str' , 156 );

        
  /*      
        \Nyos\mod\items::$get_data_simple = true;
        // $checks = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout', 'show');
        $checks = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout');
        \f\pa($checks,2,'','checks');
*/
        
        
        /*
        $ee = \Nyos\mod\items::$where2 = ' AND ( midop.name = \'start\' AND midop.value_datetime '
                . 'BETWEEN \''.date('Y-m-d',strtotime($date_start)).' 08:00:00\' AND \''.date('Y-m-d',strtotime($date_fin.' +1 day')).' 03:00:00\' ) ';
        // $ee = \Nyos\mod\items::$where2 = ' AND midop.name = \'date_now\'  ';
        
        $checks = \Nyos\mod\items::getItemsSimple2($db, '050.chekin_checkout');
        \f\pa($checks,2,'','checks');
        */
        
        
        
        
        
        
        
        //echo '<br/>timer8:'. \f\timer::start( 999 );
        
        \Nyos\mod\items::$where2dop = ' AND '
                . ' ( '
                . '  ( '
                . '  midop.name = \'start\' '
                . '  AND midop.value_datetime '
                . '  BETWEEN \''.date('Y-m-d 08:00:00',strtotime($date_start)).'\' AND \''.date('Y-m-d 03:00:00',strtotime($date_fin.' +1 day')).'\' '
                . '  ) '
                . ' OR '
                . '  midop.name != \'start\' '
                . ' ) ';
        \Nyos\mod\items::$need_polya_vars = [ 'start', 'jobman' ];
        // \f\pa(\Nyos\mod\items::$need_polya_vars);
        
        $checks = \Nyos\mod\items::getItemsSimple3($db, '050.chekin_checkout');
        //\f\pa($checks,2,'','checks');
      
        //echo '<br/>timer (getItemsSimple3($db, 050.chekin_checkout);) :'. \f\timer::stop( 'str' , 999 );
        
        // echo '<br/>timer8:'. \f\timer::stop( 'str' , 156 );
        
        //return ;
        
        $mod_jobman = $module_jobman;

//        echo '<br/>';
//        echo $dt_start.' > '.$dt_fin;
//        echo '<br/>';
        //echo '<br/>' . __FILE__ . ' [' . __LINE__ . ']';

        foreach ($checks as $k => $v) {

            // пропускаем если сотрудника нет в списке допущенных по времени
            if (!isset($v['jobman']))
                continue;

            if (!isset($return['jobman_on'][$v['jobman']]))
                continue;

//            if ($v['start'] >= $dt_start && $v['start'] <= $dt_fin) {
//                
//            } else {
//                continue;
//            }

            //\f\pa($v);
            
            $v['id_check'] = $v['id'];
            $v['type'] = 'check';
            $v['date'] = substr($v['start'], 0, 10);

            if (isset($where_job[$v['date']][$v['jobman']]))
                $v['now_job'] = $where_job[$v['date']][$v['jobman']];

            // какой уровень оплаты
            if (!empty($v['now_job']['sale_point']) && !empty($v['now_job']['dolgnost']) && !empty($v['date'])) {
                
                $v['oborot_sp_month'] = \Nyos\mod\JobBuh::getOborotSpMonth($db, $v['now_job']['sale_point'], $v['date']);
//                // ищем текущее значение
                $v['salary-now'] = \Nyos\mod\JobDesc::getSalaryJobman($db, $v['now_job']['sale_point'], $v['now_job']['dolgnost'], $v['date']);
//                //\f\pa($v['salary-now']);
                //$v['now'] = \Nyos\mod\JobDesc::calcSummaDay($v);
                $v['summa-day'] = \Nyos\mod\JobDesc::calcSummaDay($v);
                
            }

            // \f\pa($v['now'],2,'','now1');

            // какой уровень оплаты 

            //\f\pa($v);
            
            if ( !empty($v['ocenka']) && is_numeric($v['ocenka']) && !empty($v['salary-now']['ocenka-hour-' . $v['ocenka']])) {

                $v['now']['ocenka'] = $v['ocenka'];
                $v['now']['price_hour'] = $v['salary-now']['ocenka-hour-' . $v['now']['ocenka']];
            }
            //
            elseif (!empty($v['ocenka_auto']) && !empty($v['salary-now']['ocenka-hour-' . $v['ocenka_auto']])
                && is_numeric($v['ocenka_auto']) ) {

                $v['now']['ocenka'] = $v['ocenka_auto'];
                $v['now']['price_hour'] = $v['salary-now']['ocenka-hour-' . $v['now']['ocenka']];
            } else {
                
                $v['now']['ocenka'] = null;
                $v['now']['price_hour'] = null;
            }

            if (empty($v['now']['price_hour'])) {
                
                continue;
            } elseif (!empty($v['price_hour']) && !empty($v['hour_on_job'])) {
                
                $v['now']['summa'] = ceil($v['price_hour'] * $v['hour_on_job']);
            }

            $return[$v['jobman']]['items'][$v['date']][] = $v;

            if (!isset($return[$v['jobman']]['fio'])) {
                $return[$v['jobman']]['fio'] = $jobmans[$v['jobman']]['_head'];

                if (!isset($return['sort_people'][$jobmans[$v['jobman']]['_head']]))
                    $return['sort_people'][$jobmans[$v['jobman']]['_head']] = $v['jobman'];
            }

            if (!isset($return[$v['jobman']]['user_id']))
                $return[$v['jobman']]['user_id'] = $jobmans[$v['jobman']]['_id'];

            if ( isset($return[$v['jobman']]['user']))
                unset($return[$v['jobman']]['user']);
//            if (!isset($return[$v['jobman']]['user']))
//                $return[$v['jobman']]['user'] = $jobmans[$v['jobman']];

            //\f\pa($v,2,'','$v');
        }

        
        // echo '<br/>timer '.__FUNCTION__.' end '. \f\timer::stop( 'str' , 156 );
        //return ;
        
        
        
        
        
        // \f\pa($return['items'],2,'','items');
//$return = [];

        return $return;
    }

    /**
     * получаем чеки минусы и плюсы за определённый период
     * @param type $db
     * @param type $date_start
     * @param type $date_fin
     * @param type $module_jobman
     * @param type $module_sp
     * @param type $module_send_jobman_to_sp
     * @return type
     */
    public static function getChecksMinusPlus($db, $date_start, $date_fin, $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp') {
        
        //echo '<br/>' . $date_start . ', ' . $date_fin;

        if (isset(self::$cash['ChecksMinusPlus'][$date_start][$date_fin]))
            return self::$cash['ChecksMinusPlus'][$date_start][$date_fin];
        
        $return = self::calcChecks($db, $date_start, $date_fin);
        // \f\pa($return,4);

        usort($return, "\\f\\sort_ar_str_fio");

        $return['plus_minus'] = self::calcPlusMinus($db, $date_start, $date_fin);
        // \f\pa($ret, '', '', 'calcPlusMinus');

        $return['days'] = [];

        for ($w = 0; $w <= 32; $w++) {

            $dd = date('Y-m-d', strtotime($date_start . ' +' . $w . ' day'));

            if ($dd > $date_fin)
                break;

            $return['days'][$dd] = 1;

        }

        return self::$cash['ChecksMinusPlus'][$date_start][$date_fin] = $return;
        
    }

}
