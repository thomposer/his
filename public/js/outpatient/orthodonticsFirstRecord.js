
define(function (require) {
    var _self;
    var allergy = require('js/triage/allergy');
    var firstCheck = require('js/outpatient/firstCheck');
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
            if(dentalRecordStatus == 1 && (recordType == 6 || recordType == 7)){
            	$('#record .form-control').attr({'disabled': true});
                $('#record [type=radio]').attr({'disabled': true});
                $('[type=checkbox]').attr({'disabled': true});
                $('#record .btn-from-delete-add').hide();
                $('#record .dental-check .control-label').removeClass('dental-check-title');
                $('#record .dental-check .control-label .dentail-content-edit').hide();
                $('#record .kv-file-remove').hide();
                $('#record .input-group-btn-custom').hide();
            }else{
            	 $('#record .form-control').attr({'disabled': false});
                 $('[type=radio]').attr({'disabled': false});
                 $('[type=checkbox]').attr({'disabled': false});
                 $('#record .btn-from-delete-add').show();
                 $('#record .dental-check .control-label').addClass('dental-check-title');
                 $('#record .dental-check .control-label .dentail-content-edit').show();
                 $('#record .kv-file-remove').show();
                 $('#record .input-group-btn-custom').show();
                 allergy.initAllergyBtn();
                 firstCheck.initFirstCheckBtn();
                 _self.initInput();
            }

        },
        initInput: function(){
            _self.checkBadHabits();
            _self.checkSmileRemark();
            _self.checkAnteriorTeeth();
            _self.checkPosteriorTeeth();
            _self.checkOralFunction();
            _self.checkMandibularMovement();
            _self.checkMouthOpen();
            _self.checkLeftTemporomandibular();
            _self.checkRightTemporomandibular();
            _self.checkCoverAnteriorTeeth();
        },
        bindEvent: function () {
        	
        	_self.checkBadHabits();
        	$('body').on('click','#orthodonticsfirstrecord-bad_habits input',function(){
        		_self.checkBadHabits();
        	});
        	$('body').on('click','#orthodonticsfirstrecord-bad_habits_abnormal input',function(){
        		_self.checkBadHabits();
        	});
        	_self.checkSmileRemark();
        	$('body').on('click','#orthodonticsfirstrecordfeatures-smile input',function(){
        		_self.checkSmileRemark();
        	});
        	_self.checkAnteriorTeeth();
        	$('body').on('click','#orthodonticsfirstrecordexamination-overbite_anterior_teeth input',function(){
        		_self.checkAnteriorTeeth();
        	});
        	_self.checkPosteriorTeeth();
        	$('body').on('click','#orthodonticsfirstrecordexamination-overbite_posterior_teeth input',function(){
        		_self.checkPosteriorTeeth();
        	});
        	$('body').on('click','#orthodonticsfirstrecordexamination-overbite_anterior_teeth_abnormal input',function(){
        		_self.checkAnteriorTeeth();
        	});
        	$('body').on('click','#orthodonticsfirstrecordexamination-overbite_posterior_teeth_abnormal input',function(){
        		_self.checkPosteriorTeeth();
        	});
        	
        	_self.checkOralFunction();
        	$('body').on('click','#orthodonticsfirstrecord-oral_function',function(){
            	_self.checkOralFunction();
        	});
        	
        	_self.checkMandibularMovement();
        	$('body').on('click','#orthodonticsfirstrecord-mandibular_movement input',function(){
            	_self.checkMandibularMovement();
        	});
        	
        	_self.checkMouthOpen();
        	$('body').on('click','#orthodonticsfirstrecord-mouth_open input',function(){
            	_self.checkMouthOpen();
        	});
        	
        	_self.checkLeftTemporomandibular();
        	$('body').on('click','#orthodonticsfirstrecord-left_temporomandibular_joint input',function(){
            	_self.checkLeftTemporomandibular();
        	});
        	$('body').on('click','#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal input',function(){
            	_self.checkLeftTemporomandibular();
        	});
        	
        	_self.checkRightTemporomandibular();
        	$('body').on('click','#orthodonticsfirstrecord-right_temporomandibular_joint input',function(){
            	_self.checkRightTemporomandibular();
        	});
        	$('body').on('click','#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal input',function(){
            	_self.checkRightTemporomandibular();
        	});
        	
        	_self.checkCoverAnteriorTeeth();
        	$('body').on('click','#orthodonticsfirstrecordexamination-cover_anterior_teeth',function(){
            	_self.checkCoverAnteriorTeeth();
        	});
        	_self.checkCoverPosteriorTeeth();
        	$('body').on('click','#orthodonticsfirstrecordexamination-cover_posterior_teeth',function(){
            	_self.checkCoverPosteriorTeeth();
        	});
        	
        	$('#orthodonticsFirstRecord').yiiAjaxForm({
                beforeSend: function () {
                    var status = 0;
                    if ($('#orthodonticsFirstRecord input[name="OrthodonticsFirstRecord[hasAllergy]"]:checked').val() == 2) {
                        var parentObj = $('#orthodonticsFirstRecord').find('.allergy-form');
                        status = allergy.allergyValidity(parentObj);
                    }

                    $('#orthodonticsFirstRecord .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            if (contentId == 0) {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            if (contentText == '') {
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        }
                    });
                    if (status) {
                        return false;
                    }
                },
                complete: function () {


                },
                success: function (data) {
                    
                },
            });
            // 精确度控制
        	_self.orthodonticsFirstAccuracyControl();
        },
        orthodonticsFirstAccuracyControl : function(){// 需要精确度控制的事件绑定
            $('body').on('input','#orthodonticeHeightcm',function(e){
                accuracyControl($(this),1);
            });
            $('body').on('input','#orthodonticeWeightcm',function(e){
                accuracyControl($(this),2);
            });
        },
        checkBadHabits : function(){//不良习惯
        	var bad_habits = $('input[name="OrthodonticsFirstRecord[bad_habits]"]:checked').val();
        	if(bad_habits == 2){
        		$('#orthodonticsfirstrecord-bad_habits_abnormal').show();
        		$('#orthodonticsfirstrecord-bad_habits_abnormal_other').show();
        	}else{
        		$('#orthodonticsfirstrecord-bad_habits_abnormal').hide();
        		$('#orthodonticsfirstrecord-bad_habits_abnormal_other').hide();
                 $('#orthodonticsfirstrecord-bad_habits_abnormal_other').val('');
        	}
    		$('#orthodonticsfirstrecord-bad_habits_abnormal_other').attr({'disabled' : true});
            var emptyAbnormal = 0;
        	$.each($('input[name="OrthodonticsFirstRecord[bad_habits_abnormal][]"]:checked'),function(){
                if($(this).val() == 8){
                    emptyAbnormal = 1;
            		$('#orthodonticsfirstrecord-bad_habits_abnormal_other').attr({'disabled' : false});
                }
            });
            if(emptyAbnormal == 0){
                $('#orthodonticsfirstrecord-bad_habits_abnormal_other').val('');
            }
        },
        checkSmileRemark : function(){//颜貌---微笑-其他
        	var smile = $('input[name="OrthodonticsFirstRecordFeatures[smile]"]:checked').val();
    		$('#orthodonticsfirstrecordfeatures-smile_other').attr({'disabled':true});
        	if(smile == 3){
        		$('#orthodonticsfirstrecordfeatures-smile_other').attr({'disabled':false});
        		$('#orthodonticsfirstrecordfeatures-smile_other').focus();
        	}else{
                $('#orthodonticsfirstrecordfeatures-smile_other').val('');
            }

        },
        checkAnteriorTeeth : function(){//前牙-其他
        	var overbite_anterior_teeth = $('input[name="OrthodonticsFirstRecordExamination[overbite_anterior_teeth]"]:checked').val();
        	if(overbite_anterior_teeth == 2){
        		$('#overbite-anterior-teeth-abnormal').show();
        		$('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').show();
        	}else{
        		$('#overbite-anterior-teeth-abnormal').hide();
        		$('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').hide();
                $('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').val('');

        	}
        	
    		$('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').attr({'disabled':true});
            var emptyAbnormal = 0;
        	$.each($('input[name="OrthodonticsFirstRecordExamination[overbite_anterior_teeth_abnormal][]"]:checked'),function(){
                if($(this).val() == 3){
                    emptyAbnormal = 1;
            		$('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').attr({'disabled':false});
                }
            });
            if(emptyAbnormal == 0){
                $('#orthodonticsfirstrecordexamination-overbite_anterior_teeth_other').val('');
            }
        },
        checkPosteriorTeeth : function(){//后牙-其他
        	
        	var overbite_posterior_teeth = $('input[name="OrthodonticsFirstRecordExamination[overbite_posterior_teeth]"]:checked').val();
        	if(overbite_posterior_teeth == 2){
        		$('#overbite-posterior-teeth-abnormal').show();
        		$('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').show();
        	}else{
        		$('#overbite-posterior-teeth-abnormal').hide();
        		$('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').hide();
                $('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').val('');

        	}
        	
    		$('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').attr({'disabled':true});
            var emptyAbnormal = 0;
        	$.each($('input[name="OrthodonticsFirstRecordExamination[overbite_posterior_teeth_abnormal][]"]:checked'),function(){
                if($(this).val() == 3){
                    emptyAbnormal = 1;
            		$('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').attr({'disabled':false});
                }
            });
            if(emptyAbnormal == 0){
                $('#orthodonticsfirstrecordexamination-overbite_posterior_teeth_other').val('');
            }
        },
        checkOralFunction : function(){//口腔功能
        	var oral_function = $('input[name="OrthodonticsFirstRecord[oral_function]"]:checked').val();
        	if(oral_function == 2){
        		$('#orthodonticsfirstrecord-oral_function_abnormal').show();
        	}else{
        		$('#orthodonticsfirstrecord-oral_function_abnormal').hide();
        	}
        },
        checkMandibularMovement : function(){//下颌运动
        	var mandibular_movement = $('input[name="OrthodonticsFirstRecord[mandibular_movement]"]:checked').val();
        	if(mandibular_movement == 2){
        		$('#orthodonticsfirstrecord-mandibular_movement_abnormal').attr({'disabled':false});
        		$('#orthodonticsfirstrecord-mandibular_movement_abnormal').focus();
        	}else{
                $('#orthodonticsfirstrecord-mandibular_movement_abnormal').val('');
        		$('#orthodonticsfirstrecord-mandibular_movement_abnormal').attr({'disabled':true});
        	}
        },
        checkMouthOpen : function(){//张口度
        	var mouth_open = $('input[name="OrthodonticsFirstRecord[mouth_open]"]:checked').val();
        	if(mouth_open == 2){
        		$('#orthodonticsfirstrecord-mouth_open_abnormal').attr({'disabled':false});
        		$('#orthodonticsfirstrecord-mouth_open_abnormal').focus();
        	}else{
                $('#orthodonticsfirstrecord-mouth_open_abnormal').val('');
        		$('#orthodonticsfirstrecord-mouth_open_abnormal').attr({'disabled':true});
        	}
        },
        checkLeftTemporomandibular : function(){//颞下颌关节-左
        	
        	var left_temporomandibular_joint = $('input[name="OrthodonticsFirstRecord[left_temporomandibular_joint]"]:checked').val();
        	if(left_temporomandibular_joint == 2){
        		$('#left-temporomandibular-joint-abnormal').show();
        		$('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').show();
        	}else{
        		$('#left-temporomandibular-joint-abnormal').hide();
        		$('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').hide();
                $('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').val('');

        	}
        	
    		$('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').attr({'disabled':true});
            var emptyAbnormal = 0;
        	$.each($('input[name="OrthodonticsFirstRecord[left_temporomandibular_joint_abnormal][]"]:checked'),function(){
                if($(this).val() == 3){
                    emptyAbnormal = 1;
            		$('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').attr({'disabled':false});
                }
            });
            if(emptyAbnormal == 0){
                $('#orthodonticsfirstrecord-left_temporomandibular_joint_abnormal_other').val('');
            }
        },
        checkRightTemporomandibular : function(){
        	var right_temporomandibular_joint = $('input[name="OrthodonticsFirstRecord[right_temporomandibular_joint]"]:checked').val();
        	if(right_temporomandibular_joint == 2){
        		$('#right-temporomandibular-joint-abnormal').show();
        		$('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').show();
        	}else{
        		$('#right-temporomandibular-joint-abnormal').hide();
        		$('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').hide();
                $('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').val('');

        	}
        	
    		$('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').attr({'disabled':true});
            var emptyAbnormal = 0;
        	$.each($('input[name="OrthodonticsFirstRecord[right_temporomandibular_joint_abnormal][]"]:checked'),function(){
                if($(this).val() == 3){
                    emptyAbnormal = 1;
            		$('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').attr({'disabled':false});
                }
            });
            if (emptyAbnormal == 0) {
                $('#orthodonticsfirstrecord-right_temporomandibular_joint_abnormal_other').val('');
            }
        },
        checkCoverAnteriorTeeth : function(){//覆盖-前牙
        	var cover_anterior_teeth = $('input[name="OrthodonticsFirstRecordExamination[cover_anterior_teeth]"]:checked').val();
        	if(cover_anterior_teeth == 2){
        		$('#cover-anterior-teeth-abnormal').show();
        	}else{
        		$('#cover-anterior-teeth-abnormal').hide();
        	}
        },
        checkCoverPosteriorTeeth : function(){//覆盖-后牙
        	var cover_posterior_teeth = $('input[name="OrthodonticsFirstRecordExamination[cover_posterior_teeth]"]:checked').val();
        	if(cover_posterior_teeth == 2){
        		$('#cover-posterior-teeth-abnormal').show();
        	}else{
        		$('#cover-posterior-teeth-abnormal').hide();
        	}
        },
        
    };
    return main;
})
