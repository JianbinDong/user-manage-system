$(function(){
    $('.change-status-show').on('click', function(){
        $.get($(this).data('url'), function(data){
            $('.modal').html(data).modal();
        });
    });

    $('body').on('click', '.change-status',function() {

        $.post($(this).data('url'), $('form').serialize(), function(data){
            $('#modal').modal('hide');
            $('#js-status-'+data.userId).remove();
            var userCount = $('#user-count').text();
            $('#user-count').text(userCount-1);
        });
    });


    $('#js-import-btn').on('click', function(){
        $.get($(this).data('url'), function(html){
            $('.modal').html(html).modal();
        });
    });

    $('#js-export-btn').on('click', function(){
        location.href = $('#js-export-btn').data('url')+'&'+$('#search-form').serialize();
    })
});