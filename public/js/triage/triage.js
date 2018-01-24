define(function (require) {
    var template = require('template');
    var cropper = require('dist/cropper.min');
    var select = require('plugins/select2/select2.full.min');
    var allergyTpl = require('tpl/allergy.tpl');
    var healthEducationTpl = require('tpl/healthEducation.tpl');
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            this.bindEvent();
        },
        bindEvent: function () {
            //modal切换
            _self = this;
//            _self.addUserModal();
//            _self.addDoctorModal();
//            _self.addRoomModal();
//            _self.chooseDoct();
            _self.choseRoom();
//            $('#btn-type-1').click(function (e) {
//                $('#myModal1 a[href="#ptab2"]').click();
//            });
            //过敏史
            _self.allergy();
            _self.addAllergy();
//            $('body').on('click', '.btn-type', function (e) {
//                e.preventDefault();
//                var type = $('#j_supplyInfo').find('.tab-pane.active').data('type') * 1;
//                _self.saveForm(type);
//            });

//            $('body').on('change', '#triageinfo-treatment_type', function () {
//                var type = $(this).val();
//                $('#triageinfo-treatment').val('');
//                if (5 == type) {
//                    $('.treatment_div').show();
//                } else {
//                    $('.treatment_div').hide();
//                }
//            });

        },
        //过敏史
        allergy: function () {
            $('body').on('click', "input[name='TriageInfo[has_allergy_type]'][value=1]", function () {
                $('#allergy-list').show();
                $('.clinic-delete').hide();
            })
            $('body').on('click', "input[name='TriageInfo[has_allergy_type]'][value=2]", function () {
                $('#allergy-list').hide();
            })
        },
        //select2选择器
        select2: function () {
//            $('body').on('mouseover', '.select2', function () {
//                $('.select2').select2();
//            });
//            $(".select3").select2();
//            $(".select4").select2();
        },
        addAllergy: function () {
            var id = this.getQueryString('id');
            var id = '';
            if (!id) {
                $('.clinic-delete').hide();
            }
            $('body').on('click', '.clinic-add', function () {

//                var shiftTime = $('.allergy-list').html();
                var shiftTime = template.compile(allergyTpl)({
                });
                console.log(shiftTime);

                $('#allergy-list').append('<div class ="allergy-list">' + shiftTime + '</div>');
                $('.clinic-add').hide();
                $('.clinic-delete').hide();
                $('.allergy-list').last().children().children('.form-group').children('a').show();

            });
            $('body').on('click', '.clinic-delete', function () {
                var len = $('.allergy-list').length;
                if (len == 2) {
                    $('.clinic-delete').hide();
                }
                $('.allergy-list').first().remove();
            });
        },
        getQueryString: function (name) {
//	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
            var reg = new RegExp("(^|&|&amp;)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.search.substr(1).match(reg);
            if (r != null)
                return r[2];
            return '';
        },
//        addUserModal: function () {
//            $('.j-modal1').click(function (e) {
//                e.preventDefault();
//                $('#myModal1').modal('show');
//                var record_id = $(this).attr('record_id');
//                $('#myModal1').attr('record_id', record_id);
//                $.ajax({
//                    cache: true,
//                    type: "POST",
//                    url: modalUrl,
//                    data: {id: record_id}, // 你的formid
//                    dataType: 'html',
//                    async: false,
//                    success: function (data, textStatus, jqXHR) {
//                        $('#myModal1').html(data);
//                    },
//                    error: function () {
//                        alert('操作失败');
//                    }
//                });
//            });
//        },
        addDoctorModal: function () {
            $('.j-modal2').click(function (e) {
                e.preventDefault();
                $('#myModal2').modal({
                    show: true,
//                    backdrop: 'static' // 禁用空白处点击关闭弹窗
                });
                var record_id = $(this).attr('record_id');
                var doctor_id = $(this).attr('doctor_id');
                $('#myModal2').attr('record_id', record_id);
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: doctorModalUrl,
                    data: {record_id: record_id, doctor_id: doctor_id}, // 你的formid
                    dataType: 'html',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        $('#myModal2').html(data);
                    },
                    error: function () {
                        alert('操作失败');
                    }
                });
            });
        },
        addRoomModal: function () {
            $('.j-modal3').click(function (e) {
                $('#myModal3').modal({
                    show: true,
//                    backdrop: 'static' // 禁用空白处点击关闭弹窗
                });
                var record_id = $(this).attr('record_id');
                $('#myModal3').attr('record_id', record_id);
                var room_id = $(this).attr('room_id');
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: roomModalUrl,
                    data: {record_id: record_id, room_id: room_id}, // 你的formid
                    dataType: 'html',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        $('#myModal3').html(data);
                    },
                    error: function () {
                        alert('操作失败');
                    }
                });
            });
        },
        saveForm: function (type) {
            var w = this;
            var record_id = $('#myModal1').attr('record_id');
//            var $form = $('#j_tabForm_' + type);
//            var data = $form.data('yiiActiveForm');
//            console.log(data);
//            if (data.validated) {
//                alert('yes');
//            } else {
//                alert('no');
//            }
//            return false;
            $.ajax({
                cache: true,
                type: "POST",
                url: infoUrl,
                data: $('#j_tabForm_' + type).serialize() + '&TriageInfo[record_id]=' + record_id, // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                    if (data.ret == 0) {
                        w.changeTab(type);
                    } else {
                        alert(data.msg);
                    }
                },
                error: function () {
                    alert('操作失败');
                }
            });

        },
        changeTab: function (type) {
            if (type == 4) {
                $('#myModal1').modal('hide');
            } else {
                $('#progressWizard').find('li.border-none').eq(type).find('a').click();
            }
        },
        chooseDoct: function () {
            $('body').on('click', '.J-chooseDoct', function (e) {
                e.preventDefault();
                var curTarget = $(e.currentTarget);
                var doctor_id = curTarget.find('input[name="doctor_id"]').val();
                var record_id = $('#j_doctorList').attr('record_id');
                var appointment_doctor = $('#j_doctorList').attr('appointment_doctor');
                //如果是按医生预约的更换医生的时候  需要二次确认
//                if (appointment_doctor != 0 && doctor_id != appointment_doctor) {
//                    _self.secondConfirm(doctor_id, record_id);
//                } else {
                _self.sendChoseDoctInfo(doctor_id, record_id);
//                }
            })
        },
        choseRoom: function () {
            $('body').off('click', '.btn-chose-room').on('click', '.btn-chose-room', function (e) {
                e.preventDefault();
                var room_id = $(this).attr('room_id');
                var record_id = $('#j_roomList').attr('record_id');
                $.ajax({
                    cache: true,
                    type: "POST",
                    url: choseRoomUrl,
                    data: {room_id: room_id, record_id: record_id}, // 你的formid
                    dataType: 'json',
                    async: false,
                    success: function (data, textStatus, jqXHR) {
                        if(data.errorCode == 1001){
                            showInfo(data.msg,'180px',2);
                        }
                        window.location.reload();//刷新当前页面.
                        $('#ajaxCrudModal').modal('hide');
                    },
                    error: function () {
                        alert('操作失败');
                    }
                });
            })
        },
        secondConfirm: function (doctor_id, record_id) {
            var confirm_option = {
                label: "是",
                className: 'btn-default btn-form',
            };
            var cancel_option = {
                label: "否",
                className: 'btn-cancel btn-form',
            };
            btns = {
                confirm: confirm_option,
                cancel: cancel_option,
            }
            bootbox.confirm(
                    {
                        message: '指定医生的预约患者，是否确定更换医生?',
                        title: '系统提示',
                        buttons: btns,
                        callback: function (confirmed) {
                            if (confirmed) {
                                _self.sendChoseDoctInfo(doctor_id, record_id)
                            } else {
                                window.location.reload();//刷新当前页面.
                                $('#ajaxCrudModal').modal('hide');
                            }
                        }
                    }
            );
        },
        sendChoseDoctInfo: function (doctor_id, record_id) {
            $.ajax({
                cache: true,
                type: "POST",
                url: choseDoctUrl,
                data: {doctor_id: doctor_id, record_id: record_id}, // 你的formid
                dataType: 'json',
                async: false,
                success: function (data, textStatus, jqXHR) {
                    window.location.reload();//刷新当前页面.
                    $('#ajaxCrudModal').modal('hide');
                },
                error: function () {
                    alert('操作失败');
                }
            });
        }

    };
    return main;
})