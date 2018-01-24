define(function (require) {
    doctorId = '';
    var main = {
        init: function () {
            _self = main;
            _self.bind();
            _self.notice();
        },
        bind: function () {
            $('.appointment-type-container-right').find('label').click(function () {
                doctorId = $('.notice-title').attr('doctor-id');
                type = $(this).attr('type-id');
                typeName = $(this).attr('type-name');
                _self.getDataAjax();
            });

        },
        notice:function () {
            $('.skip-url').unbind('click').click(function () {
                if(!$(this).attr('departmentId')){
                    showInfo('该医生没有关联科室，请先关联科室','400px',2);
                }
            });
        },
        getDataAjax:function () {
            $.ajax({
                url: apiAppointmentDoctorTimeList,
                data: {
                    date : datePost,
                    doctorId :doctorId,
                    doctorName : doctorName,
                    type : type

                },
                type: "post",
                dataType: "json",
                success: function (json, response) {
                    var a = '';
                    if(json.errorCode == 0){
                        for(var i=0;i<json.data.length;i++){
                            var disabledStyle = (json.data[i].selected == false)?'disabled-style' : '';
                            var contentText = (json.data[i].selected == false) ? '已预约':htmlEncodeByRegExp(typeName);
                            if(!departmentId){
                                a += '<a class="skip-url">';
                            }else if(json.data[i].selected){
                                a += '<a class="skip-url" departmentId="'+departmentId+'" href="'+makeAppointmentAppointmentCreate+'?departmentId='+departmentId+'&doctor_id='+doctorId+'&date='+datePost+' '+json.data[i].name+'&type='+json.type+'">';
                            }else {
                                a += '<a>';
                            }
                            a += '<li class="'+disabledStyle+'">';
                            a += '<span class="pull-left">'+json.data[i].name+'</span>';
                            a += '<span class="content-text pull-right">'+contentText+'</span>';

                            a += '</li>';
                            a += '</a>';

                        }
                        $('#max-appointment-body').html(a);
                        _self.notice();
                    }

                },
                error: function (x) {

                }
            });
        }

    };

    return main;
})