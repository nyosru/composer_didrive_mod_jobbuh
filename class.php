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

        $oborots = \Nyos\mod\items::getItemsSimple($db, $module_oborot);
        // \f\pa($oborots);
        // $r = [];

        foreach ($oborots['data'] as $k => $v) {
            if (isset($v['dop']['sale_point']) && $v['dop']['sale_point'] == $sp_id && $v['dop']['date'] >= $d_start && $v['dop']['date'] <= $d_finish) {
                if (!empty($v['dop']['oborot_server'])) {

                    self::$cash['month'][$sp_id][$d_month] += $v['dop']['oborot_server'];

                    // $r[ $d_start .' + '. $d_finish .' ++ '. $v['dop']['date'] .' - '. $v['dop']['sale_point'] ] = $v['dop']['oborot_server'];
                }
            }
        }

        // return $r;
//        $d_now = $d_start;
//
//        while ($d_now <= $d_finish) {
//
//            echo ' - ' . $d_now;
//            $d_now = date('Y-m-d', strtotime($d_now . ' +1 day'));
//
//            self::$cash['month'][$d_month] += 10;
//        }

        return self::$cash['month'][$sp_id][$d_month];
        // return $d_start.' + '.$d_finish;
        // echo '<br/>';
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

        $dt_start = date('Y-m-d 07:00:01', strtotime($date_start));
        $dt_fin = date('Y-m-d 03:00:01', strtotime($date_fin ?? $dt_start) + 3600 * 24);

        $d1 = substr($dt_start, 0, 10);
        // echo '||'.$d1.'||';
//        $ww = \Nyos\mod\JobDesc::whereJobmansOnSp( $db, null, substr($dt_start,0,10), substr($dt_fin,0,10) );
//        \f\pa($ww,2,'','\Nyos\mod\JobDesc::whereJobmansOnSp');
        // $e = \Nyos\mod\JobDesc::compileSalarysJobmans($db, substr($dt_start,0,10) );
        //\Nyos\mod\JobDesc::getOborotSp($db, $sp, $date)

        $salarises = \Nyos\mod\JobDesc::getSalarisPeriod($db, $date_start, $date_fin);
        // compileSalarysJobmans($db, substr($dt_start,0,10) );
        // \f\pa($salarises, 2, '', '$salarises');
        // \f\pa($salarises, '', '', '$salarises');

        $return = [
            // тащим список назначений на работу в точке продаж в период времени
            'jobman_on' => \Nyos\mod\JobDesc::getJobmansOnTime1910($db, $dt_start, $dt_fin)
                // ,
                // ,'where_job' => \Nyos\mod\JobDesc::whereJobmansOnSp($db, null, $dt_start, $dt_fin)
                // ,
        ];

        // \f\pa($return,2,'','$return');

        $where_job = \Nyos\mod\JobDesc::whereJobmansPeriod($db, $dt_start, $dt_fin);
        // \f\pa($where_job,2,'','$where_job');

        $jobmans = \Nyos\mod\items::getItemsSimple($db, $module_jobman);
        //\f\pa($jobmans,2,'','$jobmans');

        $checks = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout', 'show');
        //\f\pa($checki);

        $mod_jobman = $module_jobman;

//        echo '<br/>';
//        echo $dt_start.' > '.$dt_fin;
//        echo '<br/>';
        //echo '<br/>' . __FILE__ . ' [' . __LINE__ . ']';

        foreach ($checks['data'] as $k => $v) {

            // пропускаем если сотрудника нет в списке допущенных по времени
            if (!isset($v['dop']['jobman']))
                continue;

            if (!isset($return['jobman_on'][$v['dop']['jobman']]))
                continue;

            //\f\pa($v,2,'','$v');
            //break;
            // echo '<br/>if ('.$v['dop']['start'].' >= '.$dt_start.' && '.$v['dop']['start'].' <= '.$dt_fin.') { ';

            if ($v['dop']['start'] >= $dt_start && $v['dop']['start'] <= $dt_fin) {
                
            } else {
                continue;
            }

//                echo '<br/>++++';
//                echo '<br/>';
//                echo $dt_start;

            $v['dop']['id_check'] = $v['id'];
            $v['dop']['type'] = 'check';
            $v['dop']['date'] = substr($v['dop']['start'], 0, 10);

            if (isset($where_job[$v['dop']['date']][$v['dop']['jobman']]))
                $v['dop']['now_job'] = $where_job[$v['dop']['date']][$v['dop']['jobman']];

            // какой уровень оплаты
            if (!empty($v['dop']['now_job']['sale_point']) && !empty($v['dop']['now_job']['dolgnost']) && !empty($v['dop']['date'])) {

                $v['dop']['oborot_sp_month'] = \Nyos\mod\JobBuh::getOborotSpMonth($db, $v['dop']['now_job']['sale_point'], $v['dop']['date']);
                // $v['dop']['oborot_sp_month'] = 100000;

                // echo '<hr>'.$v['dop']['date'];
                // ищем текущее значение

                $v['dop']['salary-now'] = \Nyos\mod\JobDesc::getSalaryJobman($db, $v['dop']['now_job']['sale_point'], $v['dop']['now_job']['dolgnost'], $v['dop']['date']);

                // \f\pa($v['dop'], 2, '', '$v-dop');
                // \f\pa($v['dop']['salary-now'], 2, '', '$v-dop-salary');
                // вычисляем сколько денег за смену
                $summa = \Nyos\mod\JobDesc::calcSummaDay($v['dop']);

                if (!empty($summa))
                    $v['dop']['summa'] = $summa;
            }

            // какой уровень оплаты 
            /*
              if (isset($salarises[$v['dop']['date']])) {
              // $v['dop']['salarys'] = $salarises[$v['dop']['date']];
              //                \f\pa($salarises[$v['dop']['date']],2,'','salaris-dop');
              }
             */

            //$v['dop']['salarys1'] = 123;

            $return[$v['dop']['jobman']]['items'][$v['dop']['date']][] = $v['dop'];
            //$return['items'][$v['dop']['jobman']][] = $v['dop'];
            // $return['fio'][$v['dop']['jobman']] = $jobmans['data'][$v['dop']['jobman']]['head'];

            if (!isset($return[$v['dop']['jobman']]['fio']))
                $return[$v['dop']['jobman']]['fio'] = $jobmans['data'][$v['dop']['jobman']]['head'];

            if (!isset($return[$v['dop']['jobman']]['user_id']))
                $return[$v['dop']['jobman']]['user_id'] = $jobmans['data'][$v['dop']['jobman']]['id'];

            if (!isset($return[$v['dop']['jobman']]['user']))
                $return[$v['dop']['jobman']]['user'] = $jobmans['data'][$v['dop']['jobman']];

            // \f\pa($v['dop']);
        }

        // \f\pa($return['items'],2,'','items');
//$return = [];

        /**
         * взыскания
         */
        if (1 == 1) {

            $m = \Nyos\mod\items::getItemsSimple($db, '072.vzuscaniya');

            // \f\pa($m,2,'','$minusa');
            // $d_start = substr($dt_start, 0, 9);
            // $d_fin = substr($dt_fin, 0, 9);

            foreach ($m['data'] as $k => $v) {

                // пропускаем если сотрудника нет в списке допущенных по времени
                if (!isset($return['jobman_on'][$v['dop']['jobman']]))
                    continue;


                // if ($v['dop']['date_now'] >= $d_start && $v['dop']['date_now'] <= $d_fin) {
                if ($v['dop']['date_now'] >= $dt_start && $v['dop']['date_now'] <= $dt_fin) {

                    $v['dop']['type'] = 'minus';
                    $v['dop']['date'] = $v['dop']['date_now'];

                    $return[$v['dop']['jobman']]['items'][$v['dop']['date']][] = $v['dop'];
                }
            }
            //unset($m);
        }

        /**
         * плюсы
         */
        if (1 == 1) {

            $plusa = \Nyos\mod\items::getItemsSimple($db, '072.plus');


            foreach ($plusa['data'] as $k => $v) {

                // пропускаем если сотрудника нет в списке допущенных по времени
                if (!isset($return['jobman_on'][$v['dop']['jobman']]))
                    continue;

                //\f\pa($v,2,'','$v');

                if (isset($v['dop']['date_now']) && $v['dop']['date_now'] >= $dt_start && $v['dop']['date_now'] <= $dt_fin) {

                    //\f\pa($v,2,'','$v');

                    $v['dop']['type'] = 'plus';
                    $v['dop']['date'] = $v['dop']['date_now'];

                    $return[$v['dop']['jobman']]['items'][$v['dop']['date_now']][] = $v['dop'];
                    //$return[$v['dop']['jobman']]['items'][$v['dop']['date']][] = $v['dop'];
                }
            }
            //unset($plusa);
        }

        // \f\pa($return,2,'','$return');
        //return $return;
//        foreach ($return['items'] as $jm => $v) {
//            usort($return['items'][$jm], "\\f\\sort_ar_date");
//        }

        \f\pa($return, 2, '', '$return');

        return self::$cash['ChecksMinusPlus'][$date_start][$date_fin] = $return;
    }

}
