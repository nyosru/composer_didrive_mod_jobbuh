$(document).ready(function () { // вся мaгия пoслe зaгрузки стрaницы

    $('body').on('click', '.send-pay-good', function (event) {

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

        console.log($vars['resto']);

        console.log($uri_query);
        //$(this).html("тут список");
        var $th = $(this);

        $.ajax({

            xurl: "/sites/yadom_admin/module/000.index/ajax.php",
            xurl: "/vendor/didrive_mod/jobdesc/1/didrive/ajax.php",
            url: "/vendor/didrive_mod/job_buh/1/didrive/ajax.php",
            data: "action=send-pay-good" + $uri_query,
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
