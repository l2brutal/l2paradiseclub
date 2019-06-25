$(function () {



    $('body').on('submit', 'form#form', function() {
        // Form
        var form = $(this);
        var button = $(".button", this);
        var data = form.serialize();
        var msg = $(".msg", this);

        $.ajax({
            type:'POST',
            url: ''+$(form).attr('action')+'',
            cache:false,
            data:data,
            dataType: 'json',

            beforeSend: function() {
                button.html('Validating...');
            },

            success :  function(response){
                if(response.code == 1) {
                    button.html(''+response.BtnName+'');
                    msg.html(''+response.msgSystem+'');
                    if(msg.hasClass('warning')) { msg.removeClass('warning'); }
                    msg.addClass('success');


                    if(response.txtDown == 1) {
                        document.location.replace('./request/acm/txt/&'+data+'');
                    }

                    if(typeof response.redirect !== 'undefined') {
                        window.location = response.redirect;
                    }

                }else {
                    button.html(''+response.BtnName+'');
                    msg.html(''+response.msgSystem+'');
                    if(msg.hasClass('success')) { msg.removeClass('success'); }


                    msg.addClass('warning');
                }
            }
        });
        return false;
    });

});

