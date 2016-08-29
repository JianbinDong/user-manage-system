var url = $('#modal').find('form').data('url');
var checkUrl = $('#modal').find('form').data('check-url');

$('#modal').find('form').validate({
    onkeyup :false,
    rules: {
        'name': {
            required: true,
            remote: {
                url: checkUrl,
                type: "get",
                dataType: "json",
                data: {
                    name: function() {
                        return $(".department-name").val();
                    }
                }
            }
        }
    },
    messages: {
        'name': {
            required: '部门名称不能为空',
            remote: '该部门名称已经存在，请重新输入'
        }
    },
    submitHandler:function(form){
        var data = $('form').serialize();
        $.post(url, data, function(){
            location.reload();
        });
    }
});