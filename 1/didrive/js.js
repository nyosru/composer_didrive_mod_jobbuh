$(document).ready(function () { // вся мaгия пoслe зaгрузки стрaницы

    /*
     $("#form").submit(function () { //устанавливаем событие отправки для формы с id=form
     
     var form_data = $(this).serialize(); //собераем все данные из формы
     $.ajax({
     type: "POST", //Метод отправки
     url: "send.php", //путь до php фаила отправителя
     data: form_data,
     beforeSend: function () { },
     success: function() {
     //код в этом блоке выполняется при успешной отправке сообщения
     alert("Ваше сообщение отпрвлено!");
     });
     
     });
     */


//alert('123');

    $('body').on('submit', '.send-pay-form', function (event) {

        var form_data = $(this).serialize(); //собераем все данные из формы

//        alert('2323');
//        return false;
//        
//        // $(this).removeClass("show_job_tab");
//        // $(this).addClass("show_job_tab2");
        var uri_query = '';
//        var $vars = [];
//

        var resto_id = '';
        var resto_id2 = '';

        $.each(this.attributes, function () {
            if (this.specified) {
                if (this.name == 'resto_id') {
                    resto_id = '#' + this.value;
                    resto_id2 = '#' + this.value + '-2' ;
                } else if (this.name.indexOf('ajax') + 1) {
//                    console.log( 1, this.name, this.value);
                    uri_query = uri_query + '&' + this.name + '=' + this.value;
                }
//                else{
//                    console.log( 2, this.name, this.value);
//                }
            }
        });


//
//                if (this.name == 'class')
//                {
//                } else {
//
//                    // console.log(this.name, this.value);
//                    $uri_query = $uri_query + '&' + this.name + '=' + this.value.replace(' ', '..');
//
//                    if (this.name == 'resto') {
//                        $vars['resto'] = '#' + this.value;
//                        console.log($vars['resto']);
//                        // alert($res_to);
//                    }
//
//                    if (this.name == 'show_on_click') {
//                        $('#' + this.value).show('slow');
//                    }
//                }
//
//            }
//
//        });
//
//        // console.log($vars['resto']);
//        //console.log($uri_query);
//        //$(this).html("тут список");

        var $th = $(this);

//        if (resto_id != '') {
//            $(resto_id).html('sadfsdf');
//        }

        // alert("action=send-pay-form&" + form_data + uri_query);
        // return false;

        $.ajax({

            url: "/vendor/didrive_mod/job_buh/1/didrive/ajax.php",
            data: "action=send-pay-form&" + form_data + uri_query,
            cache: false,
            dataType: "json",
            type: "post",

            beforeSend: function () {

                // $("#ok_but").hide();

                if (resto_id != '') {
                    $(resto_id2).html('<img src="/img/load.gif" style="width:50px;" alt="" border=0 />');
                }

            }
            ,

            success: function ($j) {


                if( $j.status == 'ok' ){
                    // console.log(1, $j);
                    $th.hide('slow');
                }
//                else{
//                    console.log(2, $j);
//                }

                if (resto_id != '') {
                    // $(resto_id).html($j['html']);
                    $(resto_id2).hide();
                    $(resto_id).append($j['html']+'<br/>');
                }
                


                // $($res_to).html($j.data);
                // $($vars['resto']).html($j.data);
                // $($vars['resto']).append($j.data);
                // alert($vars['resto']);
                // $($vars['resto']).append($j['html']);

                // $th("#main").prepend("<div id='box1'>1 блок</div>");                    
                // $th("#main").prepend("<div id='box1'>1 блок</div>");                    
                // $th.html( $j.html + '<br/><A href="">Сделать ещё заявку</a>');
                // $($res_to_id).html( $j.html + '<br/><A href="">Сделать ещё заявку</a>');

                // return true;

                /*
                 // alert($j.html);
                 if (typeof $div_show !== 'undefined') {
                 $('#' + $div_show).show();
                 }
                 */
//                $('#form_ok').hide();
//                $('#form_ok').html($j.html + '<br/><A href="">Сделать ещё заявку</a>');
//                $('#form_ok').show('slow');
//                $('#form_new').hide();
//
//                $('.list_mag').hide();
//                $('.list_mag_ok').show('slow');

            }

        });


        return false;

    });

    $('body').on('click', '.send-pay', function (event) {

        // alert('2323');
        // $(this).removeClass("show_job_tab");
        // $(this).addClass("show_job_tab2");
        var $uri_query = '';
        var $vars = [];

        $.each(this.attributes, function () {

            if (this.specified) {

                if (this.name == 'class')
                {
                } else {

                    // console.log(this.name, this.value);
                    $uri_query = $uri_query + '&' + this.name + '=' + this.value.replace(' ', '..');

                    if (this.name == 'resto') {
                        $vars['resto'] = '#' + this.value;
                        console.log($vars['resto']);
                        // alert($res_to);
                    }

                    if (this.name == 'show_on_click') {
                        $('#' + this.value).show('slow');
                    }
                }

            }

        });

        // console.log($vars['resto']);
        //console.log($uri_query);
        //$(this).html("тут список");

        var $th = $(this);

        $.ajax({

//            xurl: "/sites/yadom_admin/module/000.index/ajax.php",
//            xurl: "/vendor/didrive_mod/jobdesc/1/didrive/ajax.php",
            url: "/vendor/didrive_mod/job_buh/1/didrive/ajax.php",
            data: "action=send-pay" + $uri_query,
            cache: false,
            dataType: "json",
            type: "post",

            beforeSend: function () {
                /*
                 if (typeof $div_hide !== 'undefined') {
                 $('#' + $div_hide).hide();
                 }
                 */
                $($vars['resto']).html('<img src="/img/load.gif" style="width:50px;" alt="" border=0 />');
//                $("#ok_but_stat").show('slow');
//                $("#ok_but").hide();
            }
            ,

            success: function ($j) {

                // $($res_to).html($j.data);
                // $($vars['resto']).html($j.data);
                // $($vars['resto']).append($j.data);
                // alert($vars['resto']);
                // $($vars['resto']).append($j['html']);
                $($vars['resto']).html($j['html']);
                $th.hide('slow');

                // $th("#main").prepend("<div id='box1'>1 блок</div>");                    
                // $th("#main").prepend("<div id='box1'>1 блок</div>");                    
                // $th.html( $j.html + '<br/><A href="">Сделать ещё заявку</a>');
                // $($res_to_id).html( $j.html + '<br/><A href="">Сделать ещё заявку</a>');

                // return true;

                /*
                 // alert($j.html);
                 if (typeof $div_show !== 'undefined') {
                 $('#' + $div_show).show();
                 }
                 */
//                $('#form_ok').hide();
//                $('#form_ok').html($j.html + '<br/><A href="">Сделать ещё заявку</a>');
//                $('#form_ok').show('slow');
//                $('#form_new').hide();
//
//                $('.list_mag').hide();
//                $('.list_mag_ok').show('slow');

            }

        });


        //return false;

    });
    // else {
    // alert(i + ': ' + $(elem).text());
    // }


});
