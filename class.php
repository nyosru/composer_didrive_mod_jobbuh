<?php

/**
  класс модуля
 * */

namespace Nyos\mod;

if (!defined('IN_NYOS_PROJECT'))
    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JobBuh {

    public static $cash = [];
    public static $cash_var_oborot_day = 'oborot_day_';

    /**
     * массив для оборот за день
     * @var type 
     */
    public static $cash_ar__sp_date_oborot = [];

    /**
     * массив для сумм за месяц
     * @var type 
     */
    public static $cash_ar__sp_date_monthoborot = [];
    
    public static $ar_salaris_sp_dolgn_date = [];

    /**
     * получаем массив по датам когда кто сколько получает
     * на выходе только должности что катят под вход параметры
     * creatAutoBonusMonth 200615
     * @param type $db
     * @param int $sp
     * @param int $dolgn
     * @param string $date
     * @return type
     */
    public static function getSalarisNow($db, $sp = null, $dolgn = null, $date = null) {

        // \f\pa(__FUNCTION__);

        if (empty($sp) || empty($dolgn) || empty($date))
            return false;

        //salarys
        if (1 == 1 && empty(self::$ar_salaris_sp_dolgn_date)) {

            $salaris0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_salary, 'show', 'date_asc');
            $salaris0_kolvo = sizeof($salaris0);

            foreach ($salaris0 as $k => $v) {
                if (!empty($v['sale_point']) && !empty($v['dolgnost']) && !empty($v['date']))
                    self::$ar_salaris_sp_dolgn_date[$v['sale_point']][$v['dolgnost']][$v['date']][] = $v;
            }

            unset($salaris0);
        }

        $salary = [];

        if (!isset(self::$ar_salaris_sp_dolgn_date[$sp][$dolgn]))
            return false;

        foreach (self::$ar_salaris_sp_dolgn_date[$sp][$dolgn] as $k => $v) {
            if ($k <= $date)
                $salary = $v;
        }

        if (sizeof($salary) > 0) {

            $uslovie_oborot_month = false;
            $uslovie_oborot_day = false;

            foreach ($salary as $v) {
                if (!empty($v['oborot_sp_last_monht_bolee'])) {
                    $uslovie_oborot_month = true;
                }
                if (!empty($v['pay_from_day_oborot_bolee'])) {
                    $uslovie_oborot_day = true;
                }
            }

            // если есть условия по обороту месяца на зп
            // ищем 
            if ($uslovie_oborot_month === true) {

                $oborot = \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date);

                foreach ($salary as $v) {
                    if (!empty($v['oborot_sp_last_monht_bolee']) && $v['oborot_sp_last_monht_bolee'] <= $oborot) {
                        return $v;
                    }
                }

                foreach ($salary as $v) {
                    if (empty($v['oborot_sp_last_monht_bolee'])) {
                        return $v;
                    }
                }
            } elseif ($uslovie_oborot_day === true) {

                if (empty(\Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date]))
                    \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date);

                foreach ($salary as $v) {
                    if (
                            !empty($v['pay_from_day_oborot_bolee']) &&
                            $v['pay_from_day_oborot_bolee'] <= ( \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date] ?? 0 )) {
                        // \f\pa($v);
                        $v['oborot_day_now'] = \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date] ?? 0;
                        return $v;
                    }
                }

                foreach ($salary as $v) {
                    if (empty($v['pay_from_day_oborot_bolee'])) {
                        // \f\pa($v);
                        $v['oborot_day_now'] = \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date] ?? 0;
                        return $v;
                    }
                }
            }

            foreach ($salary as $v) {
                return $v;
            }
        }

        return $salary;
    }

    public static function newGetSmensFullMonth($db, $user, $date) {

        try {

            $date_start = date('Y-m-01', strtotime($date));
            $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

            $dt_start = date('Y-m-01 07:00:00', strtotime($date_start));
            $dt_finish = date('Y-m-d 02:00:00', strtotime($date_finish . ' +1 day'));

            $sql = 'SELECT '
                    . ' c.* , '
                    . ' job_in.date job_date '
                    . ' ,'
                    . ' job_in.sale_point job_sp '
                    . ' ,'
                    . ' job_in.dolgnost job_dolgn '
                    . ' ,'
                    . ' job_in.date_finish job_finish '
                    . ' ,'
                    . ' spec1.sale_point spec1_sp '
                    . ' ,'
                    . ' spec1.dolgnost spec1_dolgn '
                    . PHP_EOL
// текущая тп                    
                    . ' , ( CASE 
                        WHEN c.sale_point > 0 THEN c.sale_point
                        WHEN spec1.sale_point > 0 THEN spec1.sale_point '
                    . ' WHEN job_in.sale_point > 0 THEN job_in.sale_point '
                    . ' ELSE NULL 
                        END ) as now_sale_point '
                    . PHP_EOL
// текущая должность
                    . ' , ( CASE '
                    . ' WHEN c.sale_point > 0 AND c.sale_point = job_in.sale_point THEN job_in.dolgnost '
                    . ' WHEN c.sale_point > 0 AND c.sale_point = spec1.sale_point THEN spec1.dolgnost '
                    . ' WHEN spec1.sale_point > 0 AND spec1.dolgnost > 0 THEN spec1.dolgnost '
                    . ' WHEN job_in.sale_point > 0 AND job_in.dolgnost > 0 THEN job_in.dolgnost '
                    . ' ELSE NULL 
                        END ) as now_dolgnost '
                    . PHP_EOL
                    . ' FROM '
                    . ' `mod_050_chekin_checkout` as `c`  '

// ищем что за основная точка продаж
                    . ' LEFT JOIN `mod_jobman_send_on_sp` job_in ON job_in.jobman = c.jobman AND job_in.date <= DATE( c.`start` ) '

// тащим спец назначение
                    . PHP_EOL
                    . ' LEFT JOIN `mod_050_job_in_sp` spec1 ON spec1.jobman = c.jobman AND spec1.date = DATE( c.`start` ) '
                    . PHP_EOL
                    . ' WHERE '
                    . PHP_EOL . ' c.`start` BETWEEN :dt_start AND :dt_finish '
                    . ( $user == 'all' ? '' : PHP_EOL . ' AND `c`.`jobman` = :user ' )
                    . PHP_EOL . ' AND c.status = \'show\' '
                    . ' ORDER BY c.start ASC, job_in.date DESC '
                    . ';';
            $ff = $db->prepare($sql);

            $vars = [];

            if ($user != 'all')
                $vars[':user'] = $user;

            $vars[':dt_start'] = $dt_start;
            $vars[':dt_finish'] = $dt_finish;

            $ff->execute($vars);

            $return = [];
            $skip = [];
            while ($res = $ff->fetch()) {

                if (isset($skip[$res['id']]))
                    continue;

                $res['date'] = date('Y-m-d', strtotime($res['start'] . ' -3 hour'));

                if (!empty($res['spec1_sp']) && !empty($res['spec1_dolgn'])) {
                    $res['money'] = self::getSalarisNow($db, $res['spec1_sp'], $res['spec1_dolgn'], $res['date']);
                } else {
                    $res['money'] = self::getSalarisNow($db, $res['job_sp'], $res['job_dolgn'], $res['date']);
                }

                $res['today_hours'] = $res['hour_on_job_hand'] ?? $res['hour_on_job'] ?? '';

                if (!empty($res['money']['ocenka-hour-base']) && !empty($res['today_hours'])) {
                    $res['salary_hour'] = $res['money']['ocenka-hour-base'];
                    $res['summa'] = round($res['salary_hour'] * $res['today_hours'], 1);
                } else {

                    $res['today_ocenka'] = (!empty($res['ocenka']) ? $res['ocenka'] : (!empty($res['ocenka_auto']) ? $res['ocenka_auto'] : '' ) );

                    if (!empty($res['money']['ocenka-hour-' . $res['today_ocenka']]) && !empty($res['today_hours'])) {
                        $res['salary_hour'] = $res['money']['ocenka-hour-' . $res['today_ocenka']];
                        $res['summa'] = round($res['salary_hour'] * $res['today_hours'], 1);
                    }
                }
                $skip[$res['id']] = 1;
                $return[$res['id']] = $res;
            }
        } catch (Exception $exc) {
            \f\pa($exc);
        }
        return \f\end3('ок', true, $return);
    }

    /**
     * кто в какой должности работал в укаанный период и 1 должность до него
     * @param type $db
     * @param type $op
     * @return type
     */
    public static function getMonthOperationOnSp($db, $op = ['sp' => '', 'date' => '']) {

        // \f\pa($op);
        // throw new \Exception('нет даты');

        if (empty($op['date']))
            throw new \Exception('нет даты');

        if (!empty($op['date'])) {
            $date_start = date('Y-m-01', strtotime($op['date']));
            // $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));
            $date_finish = date('Y-m-d', strtotime($op['date']));
        }

        /**
         * кто где работает
         */
        $where_job__ar_sp_jm = [];
        $where_job__ar_jm_sp = [];

        \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start . ' -6 month')), $date_finish];
        $send_jm0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_man_job_on_sp);
        usort($send_jm0, "\\f\\sort_ar_date");
        foreach ($send_jm0 as $k => $v) {

            // если дата меньше даты старта .. переписываем весь массив
            if ($v['date'] < $date_start) {

                // если финиш этой работы ранее даты старта .. то удаляем если есть
                if (!empty($v['date_finish']) && $v['date_finish'] < $date_start) {

                    if (isset($send_jm[$v['jobman']]))
                        unset($send_jm[$v['jobman']]);
                }
                // если финиш в нашем промежутке, то добавляем эту смену 
                else {
                    $send_jm[$v['jobman']] = [$v['date'] => [0 => $v]];
                    $where_job__ar_sp_jm[$v['sale_point']][$v['jobman']] = 1;
                    $where_job__ar_sp_jm[$v['jobman']][$v['sale_point']] = 1;
                }
            }
            // если дата больше даты старта .. добавляем запись в массив
            else {
                $send_jm[$v['jobman']][$v['date']][] = $v;
                $where_job__ar_sp_jm[$v['sale_point']][$v['jobman']] = 1;
                $where_job__ar_sp_jm[$v['jobman']][$v['sale_point']] = 1;
            }
        }
        // unset($send_jm0);

        \Nyos\mod\items::$between_date['date'] = [$date_start, $date_finish];
        // $send_jm['spec'] =
        $send_jm0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_spec_jobday);
        // usort($send_jm0, "\\f\\sort_ar_date"); 
        foreach ($send_jm0 as $k => $v) {
            if (empty($v['jobman']) || empty($v['date']))
                continue;
            $v['type'] = 'spec';
            $send_jm[$v['jobman']][$v['date']][] = $v;
            if (!empty($v['sale_point'])) {
                $where_job__ar_sp_jm[$v['sale_point']][$v['jobman']] = 1;
                $where_job__ar_sp_jm[$v['jobman']][$v['sale_point']] = 1;
            }
        }
        unset($send_jm0);



        if (!empty($op['sp']) && isset($where_job__ar_sp_jm[$op['sp']])) {

            $sends_on_job__ar_jm_date_ar = [];

            foreach ($where_job__ar_sp_jm[$op['sp']] as $user => $v) {
                if (isset($send_jm[$user]))
                    $sends_on_job__ar_jm_date_ar[$user] = $send_jm[$user];
            }

            \Nyos\mod\items::$search['id'] = array_keys($sends_on_job__ar_jm_date_ar);
            $names = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_jobman);

            $fio = [];
            foreach ($names as $k => $v) {
                $fio[$v['id']] = ( $v['lastName'] ?? '-' ) . ' ' . ( $v['firstName'] ?? '-' ) . ' ' . ( $v['middleName'] ?? '-' );
            }

            \Nyos\mod\items::$search['jobman'] = array_keys($sends_on_job__ar_jm_date_ar);
            \Nyos\mod\items::$between_datetime['start'] = [$date_start . ' 03:00:00', date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'))];
            // \Nyos\mod\items::$show_sql = true;
            $cheki0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);
            $cheki_jm_ar = [];
            foreach ($cheki0 as $k => $v) {
                $v['date'] = substr($v['start'], 0, 10);
                $v['type'] = 'check';

                // \Nyos\mod\JobDesc::a_whyPositionJobManNow($db, $user, $date_now);
                $v['salary'] = \Nyos\mod\JobDesc::whatJobDate($db, $user, $v['date']);

                // $a = check
                // $a['salary-now']


                $v['salaryNow'] = [];

                if ($v['salary']['norm']['dolgnost']) {
                    $v['salaryNow'] = \Nyos\mod\JobDesc::getSalarisNow($db, $op['sp'], $v['salary']['norm']['dolgnost'], $v['date']);
                }

                $v['salaryPay'] = \Nyos\mod\JobDesc::calcSizePay($v, $v['salaryNow']);

                // $v['salaryPay'] = \Nyos\mod\JobDesc::calcSummaDay($v);

                $cheki_jm_ar[$v['jobman']][] = $v;
                // echo '<br/>'.( $v['start'] ?? 0 );
            }
            unset($cheki0);

            // bonus
            if (1 == 1) {
                \Nyos\mod\items::$search['jobman'] = array_keys($sends_on_job__ar_jm_date_ar);
                \Nyos\mod\items::$between_date['date_now'] = [$date_start, $date_finish];
                $b0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_bonus);
                // $cheki_jm_ar = [];
                foreach ($b0 as $k => $v) {
                    $v['date'] = $v['date_now'];
                    $v['type'] = 'bonus';
                    $cheki_jm_ar[$v['jobman']][] = $v;
                    // $cheki_jm_ar[$v['jobman']][]=$v;
                }
                unset($b0);
            }

            // minus
            if (1 == 1) {
                \Nyos\mod\items::$search['jobman'] = array_keys($sends_on_job__ar_jm_date_ar);
                \Nyos\mod\items::$between_date['date_now'] = [$date_start, $date_finish];
                $m0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_minus);
                // $cheki_jm_ar = [];
                foreach ($m0 as $k => $v) {
                    $v['date'] = $v['date_now'];
                    $v['type'] = 'minus';
                    $cheki_jm_ar[$v['jobman']][$v['id']] = $v;
                }
                unset($m0);
            }

            foreach ($cheki_jm_ar as $k => $v) {
                usort($cheki_jm_ar[$k], "\\f\\sort_ar_date");
            }

            return [
                'fio' => $fio,
                'job_on' => $sends_on_job__ar_jm_date_ar,
                'cheki' => $cheki_jm_ar,
                'date_start' => $date_start,
                'date_finish' => $date_finish,
                    // 'where_sp' => $where_job__ar_sp_jm[$op['sp']]
            ];
        }

        return \f\end3('получили список работ сотрудников', true, [
            'fio' => $fio,
            'jobsmens' => $send_jm,
            'job_on_sp_jm' => $where_job__ar_sp_jm,
            'job_on_jm_sp' => $where_job__ar_sp_jm
        ]);

        // $return = self::getChecksMinusPlus($db, $date_start, $date_finish, $op['sp']);

        return \f\end3('ок', true, [
            're' => ( $return ?? [] ),
            'fio' => $fio,
            'fio_names' => $names,
            'user' => \Nyos\mod\JobDesc::$WhereJobMans['data']['ar_jm']]
        );
    }

    /**
     * получаем сумму оборота за месяц по точке продаж
     * @param type $db
     * @param type $sp
     * @param type $str_date
     * @param type $module_sp
     * @param type $module_oborot
     * @return type
     */
    public static function getOborotSpDay($db, $sp, $date) {

        // self::$cash_ar__sp_date_oborot = [];

        if (isset(self::$cash_ar__sp_date_oborot[$sp][$date]))
            return self::$cash_ar__sp_date_oborot[$sp][$date];
        // echo '<br/>'.$sp.' '.$date;

        $date_start = date('Y-m-01', strtotime($date));
        $date_finish = date('Y-m-d', strtotime($date . ' +1 month -1 day'));

        $sql = 'SELECT sale_point, date, '
                // дневной оборот
                . ' ( CASE '
                . ' WHEN oborot.oborot_hand IS NOT NULL THEN oborot.oborot_hand '
                . ' WHEN oborot.oborot_server IS NOT NULL THEN oborot.oborot_server '
                . ' ELSE NULL 
                    END ) as oborot '
                . ' FROM mod_sale_point_oborot oborot WHERE sale_point = :sp 
                    AND date BETWEEN :date_start AND :date_finish '
                . ' ;';
        $ff = $db->prepare($sql);

        $ar_for_sql = [
            ':sp' => $sp,
            ':date_start' => $date_start,
            ':date_finish' => $date_finish
        ];
        $ff->execute($ar_for_sql);

        while ($r = $ff->fetch()) {
            self::$cash_ar__sp_date_oborot[$r['sale_point']][$r['date']] = $r['oborot'];
        }

        if (isset(self::$cash_ar__sp_date_oborot[$sp][$date]))
            return self::$cash_ar__sp_date_oborot[$sp][$date];

        return false;

//        $mod_sp = \Nyos\mod\JobDesc::$mod_sale_point ?? '';
//        $mod_oborot = \Nyos\mod\JobDesc::$mod_oborots ?? '';
//        // $module_sp = 'sale_point', $module_oborot = 'sale_point_oborot'
//        if (empty($mod_sp) || empty($mod_oborot)) {
//            throw new \Exception('не важных переменных, не подгружен класс jobdesc');
//        }
//
//        $var_cash_day = self::$cash_var_oborot_day . 'sp' . $sp . '_d' . $date;
//
//        $r = \f\Cash::getVar($var_cash_day);
//        if ($r) {
//            return $r;
//        } else {
//            self::getOborotSpMonth($db, $sp, $date);
//            return false;
//        }
    }

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

        $cash_var = \Nyos\mod\JobDesc::$mod_oborots . '_sp' . $sp_id . '_date' . $d_month;
        $cash_time = 60 * 60 * 6;

        // \f\timer_start(123);

        $kk = false;

        if (!empty($cash_var))
            $kk = \f\Cash::getVar($cash_var);

        if ($kk !== false) {
            
        } else {


            // новая версия с новыми бд
            if (1 == 1) {

                $sql = 'SELECT oborot FROM v__month_oborot WHERE date_y = :date_y AND date_m = :date_m AND sale_point = :sp LIMIT 1;';
                $ff = $db->prepare($sql);
                $ff->execute([
                    ':date_y' => date('Y', strtotime($str_date)),
                    ':date_m' => ceil(date('m', strtotime($str_date))),
                    ':sp' => $sp_id
                ]);
                $kk0 = $ff->fetch();
                $kk = $kk0['oborot'];

            }
            // новая версия с новыми бд
            elseif (2 == 1) {

                $sql = 'SELECT oborot FROM temp_oborot WHERE date_y = :date_y AND date_m = :date_m AND sale_point = :sp LIMIT 1;';
                $ff = $db->prepare($sql);
                $ff->execute([
                    ':date_y' => date('Y', strtotime($str_date)),
                    ':date_m' => ceil(date('m', strtotime($str_date))),
                    ':sp' => $sp_id
                ]);
                $kk0 = $ff->fetch();
                $kk = $kk0['oborot'];
            }
            // старая версия без новых бд
            else {
//        if (!empty(self::$cash['month'][$sp_id][$d_month]))
//            return self::$cash['month'][$sp_id][$d_month];
                // self::$cash['month'][$sp_id][$d_month] = 0;
                $kk = 0;

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
                        // if (!empty($v['oborot_server'])) {

                        if (!empty($v['oborot_hand']) && is_numeric($v['oborot_hand'])) {
                            $oborot = (int) $v['oborot_hand'];
                        } elseif (!empty($v['oborot_server']) && is_numeric($v['oborot_server'])) {
                            $oborot = (int) $v['oborot_server'];
                        } else {
                            $oborot = 0;
                        }

                        // $oborot = $v['oborot_hand'] ?? $v['oborot_server'] ?? 0;
                        // self::$cash['month'][$sp_id][$d_month] += $v['oborot_server'];
                        \f\Cash::setVar(self::$cash_var_oborot_day . 'sp' . $sp_id . '_d' . $v['date'], $oborot);
                        //$kk += $v['oborot_server'];

                        $kk += $oborot;
                        // $r[ $d_start .' + '. $d_finish .' ++ '. $v['dop']['date'] .' - '. $v['dop']['sale_point'] ] = $v['dop']['oborot_server'];
                        // }
                    }
                }
            }

            \f\Cash::setVar($cash_var, $kk, ( $cash_time ?? 0));
        }

        // echo '<br/>' . \f\timer_stop(123);
        // return self::$cash['month'][$sp_id][$d_month];
        return $kk;
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
                . '  BETWEEN \'' . date('Y-m-d 08:00:00', strtotime($date_start)) . '\' AND \'' . date('Y-m-d 03:00:00', strtotime($date_fin . ' +1 day')) . '\' '
                . '  ) '
                . ' OR '
                . '  midop.name != \'start\' '
                . ' ) ';
        \Nyos\mod\items::$need_polya_vars = ['start', 'jobman'];
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

            // \f\pa($v);

            if (!empty($v['hour_on_job_hand'])) {
                $v['hours_on_job'] = $v['hour_on_job_hand'];
            }
            //
            elseif (!empty($v['hour_on_job'])) {
                $v['hours_on_job'] = $v['hour_on_job'];
            }
            //
            else {
                $v['hours_on_job'] = 0;
            }

            // \f\pa($v['now'],2,'','now1');
            // какой уровень оплаты 
            //\f\pa($v);

            if (!empty($v['ocenka']) && is_numeric($v['ocenka']) && !empty($v['salary-now']['ocenka-hour-' . $v['ocenka']])) {

                $v['now']['ocenka'] = $v['ocenka'];
                $v['now']['price_hour'] = $v['salary-now']['ocenka-hour-' . $v['now']['ocenka']];
            }
            //
            elseif (!empty($v['ocenka_auto']) && !empty($v['salary-now']['ocenka-hour-' . $v['ocenka_auto']]) && is_numeric($v['ocenka_auto'])) {

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

            if (isset($return[$v['jobman']]['user']))
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
    public static function getChecksMinusPlus($db, $date_start, $date_fin, $sp_on = 'all', $module_jobman = '070.jobman', $module_sp = 'sale_point', $module_send_jobman_to_sp = 'jobman_send_on_sp') {

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

    public static function getHeadSps($db, $date_fin) {

        $cash_var = 'list__' . \Nyos\mod\JobDesc::$mod_buh_head_sp . '__' . $date_fin;
        // \f\timer_start(123);

        if (!empty($cash_var))
            $ar__head_sp__jobman_sp = \f\Cash::getVar($cash_var);

        if (!empty($ar__head_sp__jobman_sp)) {
            
        } else {

            // echo '<br/>' . $date_finish;
            \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                    . ' ON mid.id_item = mi.id '
                    . ' AND mid.name = \'date\' '
                    . ' AND mid.value_date = :df '
            ;
            \Nyos\mod\items::$var_ar_for_1sql[':df'] = $date_fin;
            $head_sp0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_buh_head_sp);
            // \f\pa($head_sp0);
            $ar__head_sp__jobman_sp = [];
            foreach ($head_sp0 as $k => $v) {
                $ar__head_sp__jobman_sp[$v['jobman']] = $v['sale_point'];
            }

            \f\Cash::setVar($cash_var, $ar__head_sp__jobman_sp);
        }

        // \f\pa($ar__head_sp__jobman_sp, '', '', '$ar__head_sp__jobman_sp');
        // echo \f\timer_stop(123);

        return $ar__head_sp__jobman_sp;
    }

}
