<?php

/**
  класс модуля
 * */

namespace Nyos\mod;

if (!defined('IN_NYOS_PROJECT'))
    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JobBuh {

    public static $cash = [];

    public static function getChecksMinusPlus($db, $date_start, $date_fin, $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp') {
        //echo '<br/>' . $date_start . ', ' . $date_fin;

        if (isset(self::$cash['ChecksMinusPlus'][$date_start][$date_fin]))
            return self::$cash['ChecksMinusPlus'][$date_start][$date_fin];

        $return = [];

        $jobmans = \Nyos\mod\items::getItemsSimple($db, $module_jobman);
//\f\pa($jobmans,2,'','$jobmans');

        $checks = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout', 'show');
//\f\pa($checki);

        $dt_start = date('Y-m-d 07:00:01', strtotime($date_start));
        $dt_fin = date('Y-m-d 03:00:01', strtotime($date_fin ?? $dt_start) + 3600 * 24);
        $mod_jobman = $module_jobman;

        foreach ($checks['data'] as $k => $v) {

            //\f\pa($v);
            //break;
            // echo '<br/>if ('.$v['dop']['start'].' >= '.$dt_start.' && '.$v['dop']['start'].' <= '.$dt_fin.') { ';

            if ($v['dop']['start'] >= $dt_start && $v['dop']['start'] <= $dt_fin) {

                //echo '<br/>++++++++++++++';

                $v['dop']['id_check'] = $v['id'];
                $v['dop']['type'] = 'checks';
                $v['dop']['date'] = substr($v['dop']['start'], 0, 10);

                $return['items'][$v['dop']['jobman']][] = $v['dop'];

                $return['fio'][$v['dop']['jobman']] = $jobmans['data'][$v['dop']['jobman']]['head'];
            }
        }

        // \f\pa($return['items'],2,'','items');

        $m = \Nyos\mod\items::getItemsSimple($db, '072.vzuscaniya');

        $d_start = substr($dt_start, 0, 9);
        $d_fin = substr($dt_fin, 0, 9);

        foreach ($m['data'] as $k => $v) {

            if ($v['dop']['date_now'] >= $d_start && $v['dop']['date_now'] <= $d_fin) {

                $v['dop']['type'] = 'minus';
                $v['dop']['date'] = $v['dop']['date_now'];
                $return['items'][$v['dop']['jobman']][] = $v['dop'];
            }
        }
        //unset($m);

        $plusa = \Nyos\mod\items::getItemsSimple($db, '072.plus');
        foreach ($plusa['data'] as $k => $v) {

            if (isset($v['dop']['date']) && $v['dop']['date'] >= $d_start && $v['dop']['date'] <= $d_fin) {
                $v['dop']['type'] = 'plus';
                $v['dop']['date'] = $v['dop']['date_now'];
                $return['items'][$v['dop']['jobman']][] = $v['dop'];
            }
        }
        //unset($plusa);
        // \f\pa($return['items'],2,'','items');

        foreach ($return['items'] as $jm => $v) {

            usort($return['items'][$jm], "\\f\\sort_ar_date");
        }

        // \f\pa($return,2,'','$return');
        
        return self::$cash['ChecksMinusPlus'][$date_start][$date_fin] = $return;
    }

}
