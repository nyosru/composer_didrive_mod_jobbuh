<?php

if (isset($skip_start) && $skip_start === true) {
    
} else {
    require_once '0start.php';
    $skip_start = false;
}

// \f\pa($_REQUEST);

try {

    if (empty($_REQUEST['sale_point']) || empty($_REQUEST['date']) || empty($_REQUEST['jobman'])) {
        throw new \Exception('не хватает данных');
    }

    $module = '005_money_buh_head_sp';

    $sql = 'UPDATE `mod_' . \f\translit($module, 'uri2') . '` SET status = \'delete\' WHERE sale_point = :sp AND jobman=:jm AND date=:d ;';
    $ff = $db->prepare($sql);

    $for_sql = [
        ':sp' => $_REQUEST['sale_point'],
        ':d' => $_REQUEST['date'],
        ':jm' => $_REQUEST['jobman'],
    ];
    $ff->execute($for_sql);

    \Nyos\mod\items::add($db, $module, $_REQUEST);

    \f\end2( 'готово', true );
    
} catch (Exception $exc) {

    // echo '<pre>'; print_r($exc); echo '</pre>';
    // echo $exc->getTraceAsString();
    
    \nyos\Msg::sendTelegramm( __FILE__.PHP_EOL.'произошла ошибка '. $exc->getMessage() , null, 2);
    \f\end2( 'произошла ошибка '. $exc->getMessage() , false);
}