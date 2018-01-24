define(function (require) {
    var _self;
    var template = require('template');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var firstCheckTpl = require('tpl/firstCheckTpl.tpl');
    var firstCheckMaxLen = 30;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.initSelect2();
//            _self.firstCheckBind();
            _self.addFirst();
            _self.firstCheckRecordBind();
//            _self.saveModalForm();
        },
        addCheck: function (list, name, inputName, deleted, parentClass, appendClass, type) {
            var itmTitle = '';
            var checkModel = template.compile(checkTpl)({
                list: htmlEncodeByRegExp(JSON.stringify(list)),
                name: name,
                deleted: deleted,
                inputName: inputName,
                parentClass: parentClass,
                baseUrl: baseUrl,
                type: type,
                itmTitle: itmTitle
            });
            $(appendClass).append(checkModel);
        },
        addFirst: function () {
            $('body').off('click', '.first-check-content .first-check-add').on('click', '.first-check-content .first-check-add', function () {
                var key = parseInt($(this).attr('data-key')) + 1;
                _self.appendCheckLine(key);
            });
            $('body').off('click', '.first-check-content .first-check-delete').on('click', '.first-check-content .first-check-delete', function () {
                $(this).parents('.first-check-line').remove();
                _self.dealWithSymbol();
                var len = $('.first-check-content .first-check-line').length;
                if (len == 1) {
                    $('.first-check-content .first-check-delete').hide();
                }
                $('.first-check-content .first-check-add').last().show();
            });
        },
        appendCheckLine: function (key) {
            var html = template.compile(firstCheckTpl)({
                key: key,
            });
            $('.first-check-content').append(html);//
            _self.initSelect2();
            _self.dealWithSymbol();
        },
        dealWithSymbol: function () {
            $('.first-check-content .first-check-add').hide();
            $('.first-check-content .first-check-delete').show();
            $('.first-check-content .first-check-delete').last().show();
            $('.first-check-content .first-check-add').last().show();
        },
        initSelect2: function () {
//            if ($(".CheckCodeSel").length < 1) {
//                return;
//            }
//            if ($(".CheckCodeSel").is(":hidden")) {
//                return;
//            }
            var searchData = null;
            $('.CheckCodeSel:not(.hide)').each(function () {
                var $select2 = $(this).select2({
                    language: "zh-CN",
                    selectOnBlur : true,
                    placeholder : '请输入名称、拼音码或ICD编码进行搜索',
                    minimumInputLength : 1,
            		minimumResultsForSearch : 1,
            		allowClear : false,
                    ajax: {
                        url: getCheckCodeList,
                        dataType: 'json',
            	        quietMillis: 2000,
            	        delay: 2000,
                        data: function (params) {
                            return {
                                search: params.term, // search term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.list,
                            };
                        },
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 1, //至少输入多少个字符后才会去调用ajax
                    templateResult: function (repo) { //对返回的结果进行修饰
                        searchData = $('.select2-search__field').val();
                        if(typeof(searchData) != 'undefined'){
                            searchData = searchData.toString();
                            return highLightKeywords(repo.text,$.trim(searchData));//高亮显示要搜索的数据
                        }
                        return htmlEncodeByRegExp(repo.text);
                    },
                    templateSelection: function (repo) {//对选中的结果进行修饰
                        return htmlEncodeByRegExp(repo.text);
                    },
                    width: "100%",
                });

                $select2.data('select2').$container.addClass("CheckCodeSel2");
            })

        },
        firstCheckRecordBind: function () {
            $('body').on('change', '.first-check-content .select-first-check', function () {
                if ($(this).val() == 1) {
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').removeClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').removeClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val('').addClass('hide');
                    _self.initSelect2();
                } else {
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').html('<option value="0">请选择</option>')
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val(0).addClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').addClass('hide');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.select2-container').find('.select2-selection__rendered').text('请选择');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val('');
                    $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').removeClass('hide');
                }
            });
            $('body').off('change', '.first-check-content .CheckCodeSel').on('change', '.CheckCodeSel', function (e) {
                var val = $(this).val();
                var data = $(this).select2("data")[0];
                var item = {};
                item.check_code_id = data['id'];
                item.content = data['name'];
                var n = $(this).siblings('.field-firstcheck-content').find('.first-check-custom').val(item.content);
                $(this).parents('.first-check-text').find('.help-block').last().html('');
            });
            $('body').off('focusout', '.first-check-content .first-check-custom').on('focusout', '.first-check-content .first-check-custom', function (e) {
                if ($(this).val()) {
                    $(this).parents('.first-check-text').find('.help-block').last().html('');
                }
            })
        },
        initFirstCheckBtn: function (parentObj) {
            parentObj = parentObj || '.first-check-content';
            console.log(parentObj, 'par');
            var len = $(parentObj).find('.first-check-line').length;
            console.log(len, 'len');
            if (len >= 2) {
                $(parentObj + " .first-check-delete").show();
                $(parentObj + " .first-check-add").hide();
                $(parentObj + ' .first-check-add').last().show();
            } else {
                $(parentObj + " .first-check-delete").first().hide();
                $(parentObj + ' .first-check-add').last().show();
            }
        },
        saveModalForm: function () {
            $('#first-check-weight').yiiAjaxForm({
                beforeSend: function () {
                    alert(111);
                    return false;
                    var status = 0;
                    var length = 0;
                    if ($('#recordForm input[name="OutpatientRelation[hasAllergy]"]:checked').val() == 2) {
                        status = _self.allergyValidity();
                    }

//                    $('#recordForm .first-check-list').each(function () {
//                        if ($(this).css('display') != 'none') {
//                            length = length + 1;
//                        }
//                    });
                    $('#recordForm .select-first-check').each(function () {
                        var val = $(this).val();
                        if (val == 1) {//icd-10
                            var contentId = $(this).parents('.first-check-type').siblings('.first-check-text').find('.CheckCodeSel').val();
                            console.log(contentId, 'contentId');
                            if (contentId == 0) {
                                console.log('haha');
                                $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-right').addClass('has-error').find('.help-block:last').text('初步诊断不能为空');
                                status = 1;
                            }
                        } else {
                            var contentText = $(this).parents('.first-check-type').siblings('.first-check-text').find('.first-check-custom').val();
                            console.log(contentText, 'contentId');
                            if (contentText == '') {
                                console.log('haha');
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
                    if (data.errorCode != 0) {
                        showInfo(data.msg, '200px', 2);
                    }
                },
            });
        }
    };
    return main;
});
