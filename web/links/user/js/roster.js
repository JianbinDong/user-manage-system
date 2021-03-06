$('#roster-form').validate({
    rules: {
        'trueName': "required",
        'gender': "required",
        'number': "required",
        'native': "required",
        'phone': "required",
        'bornTime': "required",
        'QQ': "required",
        'constellation': "required",
        'hobby': "required",
        'favoriteMusic': "required",
        'favoriteMovie': "required",
        'favoriteSport': "required",
        'favoriteFood': "required",
        'favoritePlace': "required",
        'dream': "required",
    },
    messages: {
        'trueName': "请输入姓名",
        'gender': "请选择性别",
        'native': "请输入籍贯",
        'phone': "请输入手机",
        'bornTime': "请输入生日",
        'QQ': "请输入QQ",
        'constellation': "请输入星座",
        'hobby': "请输入爱好",
        'favoriteMusic': "请输入喜欢的音乐",
        'favoriteMovie': "请输入喜欢的电影",
        'favoriteSport': "请输入喜欢的运动",
        'favoriteFood': "请输入喜欢的食物",
        'favoritePlace': "请输入喜欢的地方",
        'dream': "请输入梦想",
    }

});

$('#bornTime').datetimepicker({
   viewMode: 'years',
   format: 'YYYY-MM-DD'
});