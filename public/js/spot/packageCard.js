define(function (require) {
    var template = require('template');
    var packageCardServiceTpl = require('tpl/card/packageCardService.tpl');
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            $('#packagecard-content').attr('placeholder', contentPlaceholder);//显示placeholder
            _self.initAddDeleteButton();
            $('body').off('click', '.package-card-form .service-delete').on('click', '.package-card-form .service-delete',function(){
                $(this).parent().parent().parent().remove();
                _self.initAddDeleteButton();
            });
            $('body').off('click', '.package-card-form .service-add').on('click', '.package-card-form .service-add', function () {
                var packageCardServiceModel = template.compile(packageCardServiceTpl)({
                    packageCardServiceList: packageCardServiceList,
                });
                $('.package-card-form .package-card-service-list').append(packageCardServiceModel);
                _self.initAddDeleteButton();
            });
            
            $('body').off('change', '.package-card-form .package-card-service-list select').on('change', '.package-card-form .package-card-service-list select', function(){
                _self.validateService();
            });
            
            $('#package-card-form').yiiAjaxForm({
                beforeSend: function () {
                    var ret = _self.validateData() && _self.validateService();
                    return ret;
                },
                complete: function () {


                },
                success: function (data) {
                    console.log(data);
                    if (data.errorCode != 0) {
                        showInfo(data.msg, '250px', 2);
                    }
                },
            });
        },
        initAddDeleteButton: function () {//按钮初始化
            $('.package-card-form .package-card-service-list .service-delete').show();//显示所有删除按钮
            $('.package-card-form .package-card-service-list .service-add').hide();//隐藏所有添加按钮
            $('.package-card-form .package-card-service-list .service-add').last().show();//显示最后一个添加按钮
            if($('.package-card-form .package-card-service-list .service-delete').length == 1){
                $('.package-card-form .package-card-service-list .service-delete').hide();//隐藏所有添加按钮
            }
        },
        validateService: function () {//验证服务类型是否重复
            var map = {};
            $('.package-card-form .package-card-service-list select').each(function () {
                var key = $(this).val();
                if (map.hasOwnProperty(key)) {
                    showInfo('服务类型不能重复','250px', 2);
                    return false;
                } else {
                    map[key] = 1;
                }
            });
            return true;
        },
        validateData: function(){//验证数据
            $('.package-card-form .package-card-service-list select').each(function () {
                if($(this).val() == ''){
                    showInfo('服务类型不能为空','250px', 2);
                    return false;
                }
            });
            var re = /^[1-9]+[0-9]*]*$/;
            $('.package-card-form .package-card-service-list input[name="PackageServiceUnion[time][]"').each(function(){
                if($(this).val() == ''){
                    showInfo('次数不能为空','250px', 2);
                    return false;
                }else if(!re.test($(this).val())){
                    showInfo('次数必须为1-999的整数','250px', 2);
                    return false;
                }else if($(this).val() < 1 || $(this).val() > 999){
                    showInfo('次数必须为1-999的整数','250px', 2);
                    return false;
                }
                return true;
            });
        }
    };
    return main;
})