
define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.defaultType();
            _self.formatCaseTemplate();
            _self.saveForm();
        },
        //病例模板相关
        defaultType: function () {
            $("input[name='CaseTemplate[type]'][value=1]").attr({'disabled': true});
            var templateText = $('#triageinfo-template').find("option:selected").text();
            var templateId = $('#triageinfo-template').val();
            var lableVal = $('#triageinfo-template').find("option:selected").parent().attr('label');
            var templateType = lableVal == '我的模板' ? 1 : 2;
            if (templateType == 2) {
                $("input[name='CaseTemplate[saveType]'][value=2]").attr({'checked': true});
                $("input[name='CaseTemplate[saveType]'][value=1]").attr({'disabled': true});
            }
            $('#casetemplate-savetype').change(function () {
                var thisVal = $('input[name="CaseTemplate[saveType]"]:checked').val()
                if (thisVal == 1) {
                    $('#casetemplate-name').val(templateText);
                } else {
                    $('#casetemplate-name').val('');
                }
            });
            $('#casetemplate-caseid').val(templateId ? templateId : 0);

        },
        formatCaseTemplate: function () {
            var chiefcomplaint = $('#chiefcomplaint').val();
            var historypresent = $('#historypresent').val();
            var pasthistory = $('#pasthistory').val();
            var personalhistory = $('#personalhistory').val();
            var genetichistory = $('#genetichistory').val();
            var physical_examination = $('#physical_examination').val();
            var cure_idea = $('#cure_idea').val();
            var pastDragHistoryText = $('#triageinforelation-pastdraghistory').val();
            var followUp = $('#triageinforelation-followup').val();
            $('#casetemplate-chiefcomplaint').val(chiefcomplaint);
            $('#casetemplate-historypresent').val(historypresent);
            $('#casetemplate-pasthistory').val(pasthistory);
            $('#casetemplate-personalhistory').val(personalhistory);
            $('#casetemplate-genetichistory').val(genetichistory);
            $('#casetemplate-physical_examination').val(physical_examination);
            $('#casetemplate-cure_idea').val(cure_idea);
            $('#casetemplate-pastdraghistory').val(pastDragHistoryText);
            $('#casetemplate-followup').val(followUp);

        },
        saveForm: function () {
            $('#caseTemplateForm').yiiAjaxForm({
                beforeSend: function () {
                },
                complete: function () {

                },
                success: function (data) {

                    if (data.errorCode == 0) {
                        showInfo(data.msg, '180px');
                    } else {
                        showInfo(data.msg, '180px', 2);
                    }
                    if (data.errorCode == 0) {
                        $('#ajaxCrudModal').modal('hide');
                    }
                },
            });
        }

    };
    return main;
})
