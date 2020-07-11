<?php

ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

if ($_SERVER['HTTP_HOST'] == 'photo.uralweb.info' || $_SERVER['HTTP_HOST'] == 'yapdomik.uralweb.info' || $_SERVER['HTTP_HOST'] == 'a2.uralweb.info' || $_SERVER['HTTP_HOST'] == 'adomik.uralweb.info'
) {
    date_default_timezone_set("Asia/Omsk");
} else {
    date_default_timezone_set("Asia/Yekaterinburg");
}

define('IN_NYOS_PROJECT', true);

header("Access-Control-Allow-Origin: *");

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require( $_SERVER['DOCUMENT_ROOT'] . '/all/ajax.start.php' );

$input = json_decode(file_get_contents('php://input'), true);

if (!empty($input) && empty($_REQUEST))
    $_REQUEST = $input;






if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'scan_new_datafile') {

    scanNewData($db);
    //cron_scan_new_datafile();
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'send-pay-good') {

    // \f\pa($_REQUEST);
//    [val-summ] => 1842
//    [val-checkin] => 20969
//    [show_on_click] => res20969
//    [resto] => res20969    
    //\f\pa($_SESSION);

    $in = array(
        'checkin' => $_REQUEST['val-checkin'],
        'summa' => $_REQUEST['val-summa'],
        'pay_buh' => 'da',
        'pay_buh_who' => $_SESSION['now_user_di']['id'],
        'pay_buh_start' => date('Y-m-d H:i:s')
    );

    \Nyos\mod\items::addNewSimple($db, '075.buh_oplats', $in);

    \f\end2('отмечено', true);
} 
/**
 * универсальный переходник для запуска функций из vue
 */
elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'startFunctionClass') {

    if (!empty($_REQUEST['getDateMonth'])) {
        $date_start = date('Y-m-01', strtotime($_REQUEST['getDateMonth']));
        $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));
    }

    try {

        if (!empty($_REQUEST['function']) && $_REQUEST['function'] == 'getMonthOperationOnSp') {
            $return = \Nyos\mod\JobBuh::getMonthOperationOnSp($db, $_REQUEST);
        }

        $return['request'] = ( $_REQUEST ?? [] );
        \f\end2($_REQUEST['action'] . ' ' . $_REQUEST['function'], true, $return);
        
    } catch (\Exception $ex) {

        echo $ex->getTraceAsString();

        \f\end2($_REQUEST['action'] . ' ' . $_REQUEST['function'], false, [
            'excaption' => $ex,
            'in' => ( $_REQUEST ?? [] )
        ]);
    }

    \f\end2($_REQUEST['action'] . ' ' . $_REQUEST['function'] . ' что то пошло не так #' . __LINE__, false, [
        'excaption' => $ex,
        'in' => ( $_REQUEST ?? [] )
    ]);
}













// проверяем секрет
if (
        (
        isset($_REQUEST['id']{0}) && isset($_REQUEST['s']{5}) &&
        \Nyos\nyos::checkSecret($_REQUEST['s'], $_REQUEST['id']) === true
        ) ||
        (
        isset($_REQUEST['module']{0}) &&
        isset($_REQUEST['dop_name']{0}) &&
        isset($_REQUEST['item_id']{0}) &&
        isset($_REQUEST['s']{5}) &&
        \Nyos\nyos::checkSecret($_REQUEST['s'], $_REQUEST['module'] . $_REQUEST['dop_name'] . $_REQUEST['item_id']) === true
        )
) {
    
}
//
else {
    \f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору ', false);
}

//require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/sql.start.php');
//require( $_SERVER['DOCUMENT_ROOT'] . '/0.site/0.cfg.start.php');
//require( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'class' . DS . 'mysql.php' );
//require( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'db.connector.php' );
// vue тащим разные функции
//
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'send-pay-form') {

    $in = $_REQUEST;
    // $in['date'] = date( 'Y-m-d', $_SERVER['REQUEST_TIME'] );

    if (!empty($_SESSION['now_user_di']['id']))
        $in['pay_buh_who'] = $_SESSION['now_user_di']['id'];

    $in['pay_buh_dt'] = date('Y-m-d H:I:s', $_SERVER['REQUEST_TIME']);

    $e = \Nyos\mod\items::addNewSimple($db, '075.buh_oplats', $in);

    // \f\end2('sdf',false, $_SESSION );
    \f\end2('Оплата ' . $in['summa'] . ' зафиксирована', true, $e);
    \f\end2('sdf', false, $e);
}
//
elseif (isset($_POST['action']) && $_POST['action'] == 'show_info_strings') {


    require_once '../../../../all/ajax.start.php';
    require_once dirname(__FILE__) . '/../class.php';

    // \Nyos\mod\items::getItems( $db, $folder )

    \f\end2('окей', true, array('data' => 'новый статус ' . 'val'));
}
/**
 * изменение инфы в главном итемс
 */
//
elseif (isset($_POST['action']) && $_POST['action'] == 'edit_pole') {

//    require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );
//    require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'txt.2.php' );
// $_SESSION['status1'] = true;
// $status = '';

    $e = array('id' => (int) $_POST['id']);
    $e1 = array($_POST['pole'] => $_POST['val']);
//    \f\pa($e);
//    \f\pa($e1);
//    exit;

    \f\db\db_edit2($db, 'mitems', $e, $e1);

//$table = 'mitems';
//    $polya = \f\db\pole_list($db, $table);
//    \f\pa($polya);
//    
//$table = 'mitems_dop';    
//    $polya = \f\db\pole_list($db, $table);
//    \f\pa($polya);
    //$folder = \Nyos\nyos::getFolder($db);
// папка для кеша данных
    //$dir_for_cash = $_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $folder . '/';
    $dir_for_cash = DR . dir_site;

    $list_cash = scandir($dir_for_cash);
    foreach ($list_cash as $k => $v) {
        if (strpos($v, 'cash.items.') !== false) {
            unlink($dir_for_cash . $v);
        }
    }

// f\end2( 'новый статус ' . $status);
    f\end2('новый статус ' . $_POST['val']);
}
/**
 * изменение инфы в дополнительных итемсах
 */
// edit dop поле
elseif (isset($_POST['action']) && $_POST['action'] == 'edit_dop_pole') {

//    require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );
//    require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'txt.2.php' );
// $_SESSION['status1'] = true;
// $status = '';

    $e = array('id' => (int) $_POST['item_id']);

    $ff = $db->prepare('DELETE FROM `mitems-dops` WHERE `id_item` = :id_item AND `name` = :name ');
    $ff->execute(
            array(
                ':id_item' => $_POST['item_id'],
                ':name' => $_POST['dop_name']
            )
    );


    if (isset($_POST['new_val']{0})) {
        $ff = $db->prepare('INSERT INTO `mitems-dops` ( `id_item`, `name`, `value` ) values ( :id, :name , :val ) ');
        $ff->execute(array(
            ':id' => $_POST['item_id'],
            ':name' => $_POST['dop_name'],
            ':val' => $_POST['new_val'],
        ));
    }

    $dir_for_cash = DR . dir_site;

    $list_cash = scandir($dir_for_cash);
    foreach ($list_cash as $k => $v) {
        if (strpos($v, 'cash.items.') !== false) {
            unlink($dir_for_cash . $v);
        }
    }

// f\end2( 'новый статус ' . $status);
    f\end2('ок');
}
/**
 * получаем перменные 1 записи
 */ elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'show') {

    if (isset($_REQUEST['module']{1})) {

        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );
        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'txt.2.php' );

// $_SESSION['status1'] = true;
// $status = '';
// \f\db\db_edit2($db, 'mitems', array('id' => (int) $_POST['id']), array($_POST['pole'] => $_POST['val']));

        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'items' . DS . 'class.php' );
        $items = Nyos\mod\items::getItems($db, $_REQUEST['folder'], '010.tovars');

        /*
          f\pa($_REQUEST['id']);
          f\pa($_REQUEST['var']);
          f\pa($items);
         * 
         */

        if (isset($_REQUEST['var']) && isset($items['data'][$_REQUEST['id']]['dop'][$_REQUEST['var']])) {

            $r1 = $r2 = array();
            $r1[] = 'ish2/rizolin-opt.ru/images/';
            $r2[] = '/9.site/' . $_REQUEST['folder'] . '/download/img/';
            $r1[] = 'FSm.png';
            $r2[] = 'fsm.png';
            $r1[] = 'AS.png';
            $r2[] = 'as.png';

            $r1[] = 'width:64px';
            $r2[] = 'max-width:64px';
            $r1[] = 'height:64px';
            $r2[] = 'max-height:64px;padding:10px;';

            die('<style>.icof img{ max-height:64px;padding:10px;max-width:64px }</style><div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                    . '<h4 class="modal-title" id="myModalLabel">' . $items['data'][$_REQUEST['id']]['head'] . '</h4>'
                    . '</div>
                    <div class="modal-body">
                    <div style="padding:20px">' . str_replace($r1, $r2, $items['data'][$_REQUEST['id']]['dop'][$_REQUEST['var']]) . '</div></div>');
        } else {
// f\end2( 'новый статус ' . $status);
            f\end2('ок', 'ok', $items);
        }
    } else {
        f\end2('новый статус 1111111');
    }
}


f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', false);






// печать купона
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'print' && isset($_REQUEST['kupon']{0}) && is_numeric($_REQUEST['kupon']{0})) {
    
}

if (1 == 2) {

// печать купона
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'print' && isset($_REQUEST['kupon']{0}) && is_numeric($_REQUEST['kupon']{0})) {

        require( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'kupons' . DS . 'class.php' );

        $folder = Nyos\nyos::getFolder($db);
// echo $folder;

        die(Nyos\mod\kupons::showHtmlPrintKupon($db, $folder, $_REQUEST['kupon']));
    }

//<input type='hidden' name='get_cupon' value='da' />
    elseif (isset($_REQUEST['get_cupon']) && $_REQUEST['get_cupon'] == 'da') {

        require( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'kupons' . DS . 'class.php' );
        require( $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/txt.2.php' );

        $get = $_REQUEST;

        $get['phone'] = f\translit($get['phone'], 'cifr');
        $get['kupon'] = $get['id'];
        $get['email'] = trim(strtolower($get['email']));

        $res = Nyos\mod\kupons::addPoluchatel($db, $get);

        if (isset($_COOKIE['fio']{0}) && $_COOKIE['fio'] != $get['fio'])
            setcookie("fio", $get['fio'], time() + 24 * 31 * 3600, '/');

        if (isset($_COOKIE['tel']{0}) && $_COOKIE['tel'] != $get['phone'])
            setcookie("tel", $get['phone'], time() + 24 * 31 * 3600, '/');

        if (isset($_COOKIE['email']{0}) && $_COOKIE['email'] != $get['email'])
            setcookie("email", $get['email'], time() + 24 * 31 * 3600, '/');

        setcookie("cupon" . $get['kupon'], $res['number_kupon'], time() + 24 * 31 * 3600, '/');

        if ($_REQUEST['id'] == 2) {
            f\end2('<h3>Добро пожаловать</h3>'
                    . '<Br/>'
                    . '<p>Регистрация проведена успешно</p>'
                    . '<Br/>'
                    . '<Br/>'
                    , 'ok');
        } else {
// f\pa($res);
            f\end2('<h3>Липон получен !<br/><br/>№' . $res['number_kupon'] . '</h3>'
                    . '<Br/>'
                    . '<p>Сообщите номер липона в магазине и воспользуйтесь скидкой!</p>'
                    . '<Br/>'
                    . '<Br/>'
                    , 'ok', array('number_kupon' => $res['number_kupon'])
            );
        }
    }

// получение купона по новому (сразу по кнопе)
    elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_cupon17711') {

// echo '<pre>'; print_r($_COOKIE); echo '</pre>';    exit;

        $vname = 'fio';
        if (isset($_REQUEST[$vname]{0})) {
            $$vname = $_REQUEST[$vname];
        } elseif (isset($_COOKIE[$vname]{0})) {
            $$vname = $_COOKIE[$vname];
        }

        $vname = 'tel';
        if (isset($_REQUEST[$vname]{0})) {
            $$vname = $_REQUEST[$vname];
        } elseif (isset($_COOKIE[$vname]{0})) {
            $$vname = $_COOKIE[$vname];
        }

        $vname = 'email';
        if (isset($_REQUEST[$vname]{0})) {
            $$vname = $_REQUEST[$vname];
        } elseif (isset($_COOKIE[$vname]{0})) {
            $$vname = $_COOKIE[$vname];
        }

        $vname = 'kupon';
        if (isset($_REQUEST[$vname]{0})) {
            $$vname = $_REQUEST[$vname];
        }

        if (
                isset($fio{0}) &&
                isset($tel{0}) &&
                isset($email{0}) &&
                isset($kupon{0})
        ) {

            require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'kupons' . DS . 'class.php' );
            require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/txt.2.php' );

            $get['fio'] = $fio;
            $get['phone'] = f\translit($tel, 'cifr');
            $get['kupon'] = $kupon;
            $get['email'] = trim(strtolower($email));

//получаем менюшку
            if (1 == 1) {
                $folder = Nyos\nyos::getFolder($db);
                $mnu = Nyos\nyos::creat_menu($folder);
// f\pa($mnu);

                foreach ($mnu[1] as $k => $v) {
//f\pa($v);
                    if ($v['type'] == 'kupons') {
                        $get['now_level'] = $v;
                        break;
                    }
                }
            }

//f\pa($get);

            $res = Nyos\mod\kupons::addPoluchatel($db, $get, $folder);
// f\pa($res);

            if ($res['status'] == 'error') {
                f\end2($res['html'], 'error', array('line' => __LINE__));
            }

// echo '<pre>'; print_r($res); echo '</pre>';

            foreach ($_COOKIE as $k => $v) {
                if ($k == 'fio' || $k == 'tel' || $k == 'email')
                    setcookie($k, $v, time() + 24 * 31 * 3600, '/');
            }

//setcookie("cupon" . $get['kupon'], $res['number_kupon'], time() + 24 * 31 * 3600, '/');

            if (isset($res['number_kupon']{0})) {

// отправяляем письмо сданными липона пользователю
// $vars = Nyos\mod\kupons::getItem($folder, $db, $res['number_kupon'], null);

                setcookie("cupon" . $kupon, $res['number_kupon'], time() + 24 * 31 * 3600, '/');

//f\pa($vars);

                foreach ($_COOKIE as $k => $v) {
                    if ($k == 'fio' || $k == 'tel' || $k == 'email')
                        $vars[$k] = $v;
                }

                /*
                  require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/class/mail.2.php' );
                  //require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/smarty.php' );
                  // Nyos\mod\mailpost::$body = f\compileSmarty( 'lipon_get_lipon.smarty.htm', $vars, $_SERVER['DOCUMENT_ROOT'].DS.'template-mail' );
                  Nyos\mod\mailpost::$sendpulse_id = $_ss['sendpulse_id'];
                  Nyos\mod\mailpost::$sendpulse_id = '1';
                  Nyos\mod\mailpost::$sendpulse_key = $_ss['sendpulse_key'];
                  Nyos\mod\mailpost::sendNow($db, $_ss['mail_from'], $email, ( isset($_ss['mail_head_newcupon']{2}) ? $_ss['mail_head_newcupon'] : 'Получен купон'), 'lipon_get_lipon.smarty.htm', $vars);
                 */

// sleep(3);
// f\pa($res);
                f\end2('<h3>Липон получен !<br/><br/>№' . $res['number_kupon'] . '</h3>'
                        . '<Br/>'
                        . '<p>Сообщите номер липона в магазине и воспользуйтесь скидкой!</p>'
                        . '<Br/>'
                        . '<Br/>'
                        , 'ok', array('number_kupon' => $res['number_kupon'])
                );
            }
        } else {

//require_once($_SERVER['DOCUMENT_ROOT'] . '/0.all/f/smarty.php');
//f\end2(f\compileSmarty('ajax_form_enter.htm', array(), dirname(__FILE__) . '/../../lk/3/tpl_smarty/')
            /*
              f\end2( '1111111111111'
              , 'ok', array(
              'need_reg' => 'yes',
              'line' => __LINE__
              ));
             */

//return false;
        }

        f\end2('Нужно войти в лк или зарегистрироваться'
                . '<Br/>'
                . '<Br/>'
                , 'error', array(
            'need_reg' => 'yes',
            'line' => __LINE__
        ));
    }

//<input type='hidden' name='get_cupon' value='da' />
    elseif (isset($_REQUEST['reg']) && $_REQUEST['reg'] == 'da') {

        require( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'kupons' . DS . 'class.php' );
        require( $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/txt.2.php' );

        $get = $_REQUEST;

// $get['kupon'] = $get['id'];
        $get['name'] = $get['fio'];
        $get['mail'] = trim(strtolower($get['email']));
        $get['phone'] = f\translit($get['phone'], 'cifr');
        $get['pass'] = Nyos\nyos::creat_pass(5, 2);

// $res = Nyos\mod\kupons::addPoluchatel($db, $get);

        setcookie("fio", $get['fio'], $_SERVER['REQUEST_TIME'] + 24 * 31 * 3600, '/');
        setcookie("tel", $get['phone'], $_SERVER['REQUEST_TIME'] + 24 * 31 * 3600, '/');
        setcookie("email", $get['mail'], $_SERVER['REQUEST_TIME'] + 24 * 31 * 3600, '/');

// setcookie("cupon" . $get['kupon'], $get['number_kupon'], $_SERVER['REQUEST_TIME'] + 24 * 31 * 3600, '/');
// шлём майл, шаблон такой-то
// $get['send_reg_mail'] = 'kupon_reg';

        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.site' . DS . 'exe' . DS . 'lk' . DS . 'class.php' );
        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );

        /*
         * $indb['reg_mail_head'] - тема письма о регистрации,
         * $indb['reg_mail_template'] - шаблон письма о регистрации
         * $indb['reg_mail_from_mail'] - майл отправителя
         * $indb['reg_mail_sendpulse_id'] - id sendpulse api
         * $indb['reg_mail_sendpulse_key'] - key sendpulse api
         */


        require_once( DirAll . 'class' . DS . 'nyos.2.php' );
        $now = Nyos\nyos::domain($db, $_SERVER['HTTP_HOST']);

        require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . $now['folder'] . DS . 'index.php' );

        foreach ($_ss as $k => $v) {
            if (!isset($get[$k]))
                $get[$k] = $v;
        }

        $get['head'] = 'Регистрация';
        $ee = Nyos\mod\lk::regUser($db, $now['folder'], $get, 'array');

        if (isset($ee['reg_mail_sendpulse_id']))
            unset($ee['reg_mail_sendpulse_id']);

        if (isset($ee['reg_mail_sendpulse_key']))
            unset($ee['reg_mail_sendpulse_key']);

        if ($ee['status'] == 'ok') {
            f\end2($ee['html'], 'ok', $ee);
        } else {
            f\end2($ee['html'], $ee['status'], $ee);
        }
    }

// удалить город
    elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del_city') {

//$status = '';
        $db->sql_query('UPDATE `g_city` SET `show` = \'no\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
// $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

        f\end2('Город удалён');
    }

// удаляем каталог в дидрайве
    elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del1') {

//$status = '';
        $db->sql_query('UPDATE `gm_katalogi` SET `status` = \'hide\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
// $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

        f\end2('Каталог удалён');
    }
//
    elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del_item') {

        $db->sql_query('UPDATE `mpeticii_item` SET `status` = \'cancel\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
// $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

        f\end2('Петиция удалёна');
    }
//
    elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activated') {

        $db->sql_query('UPDATE `gm_katalogi` SET `status` = \'show\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
// $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

        f\end2('Восстановлен');
    }
    /**
     * удаление каталога совсем
     */ elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del2') {

        $db->sql_query('DELETE FROM `gm_katalogi` WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1;');

        f\end2('Каталог удалён совсем');
    }
}

f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', 'error');
exit;
