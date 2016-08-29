$('.add-department-modal').on('click',function(){
    var url = $(this).data('url');
    $.get(url, function(res){
        $('#modal').html(res).modal();
    });
});

$('.del-department').on('click', function(){
    var url = $(this).data('url');
    if (confirm('确认删除')) {
    $.post(url, function(){
        location.reload();
    });
    }
});

$('.edit-department-modal').on('click',function(res){
    var url = $(this).data('url');
    $.get(url, function(res){
        $('#modal').html(res).modal();
    });
});

$('#verify-number').on('click', function(){
    $.post($(this).data('url'), function(){
        location.reload();
    })
})

