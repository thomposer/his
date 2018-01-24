/* 
 * time: 2017-7-19 14:37:59.
 * author : yu.li.
 */
define(function (require) {
    var template = require('template');
    var jqprint = require('js/lib/jquery.jqprint-0.3');
    var inspectApplicationPrintTpl = require('tpl/inspect/inspectApplicationPrint.tpl');
    var inspectprinkTpl = require('tpl/inspectprink.tpl');
    var recipeprinkTpl = require('tpl/recipeprink.tpl');
    var cureprinkTpl = require('tpl/cureprink.tpl');
    var common = require('js/lib/common');
    var recipePrint = require('js/outpatient/recipePrint');
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            _self.applicationPrint();//实验室检查申请单打印
            _self.inspectReportPrint();
            _self.recipePrint();
            _self.curePrint();
        },
        applicationPrint: function () {
            //实验室检查申请打印
            $('body').on('click', '.btn-inspect-application-print', function (e) {
                e.preventDefault();
                var record_id = $(this).attr('record_id');
                var id = $(this).attr('id');
                $.ajax({
                    type: 'post',
                    url: inspectApplicationPrintUrl,
                    data: {
                        'inspect_id': id,
                        'record_id': record_id,
                        'need_filter': 1,
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json['errorCode'] == 1001) {
                            showInfo(json['msg'], '180px', 2);
                            return;
                        }
                        var spotInfo = json['spotInfo'];
                        var triageInfo = json['triageInfo'];
                        var recipeInfo = json['recipeInfo'];
                        var inspectApplication = json['inspectApplication'];
                        var inspectTotalPrice = json['inspectTotalPrice'];
                        var inspectTime = json['inspectTime'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var inspectApplicationPrint = template.compile(inspectApplicationPrintTpl)({
                            triageInfo: triageInfo,
                            spotInfo: spotInfo,
                            recipeInfo: recipeInfo,
                            inspectApplication: inspectApplication,
                            inspectTotalPrice: inspectTotalPrice,
                            inspectTime: inspectTime,
                            record_id: record_id,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#inspect-application-print').html(inspectApplicationPrint);
                        $('#inspect-application-print' + record_id + 'myshow').jqprint();
                    },
                    error: function () {
                    },
                });
            });
        },
        //检验报告接口
        inspectReportPrint: function () {
            //实验室检查申请打印
            $('body').on('click', '.btn-inspect-report-print', function (e) {
                e.preventDefault();
                var record_id = $(this).attr('record_id');
                var id = $(this).attr('id');
                $.ajax({
                    type: 'post',
                    url: inspectReportPrintUrl,
                    data: {
                        record_id: record_id,
                        id: id
                    },
                    dataType: 'json',
                    success: function (json) {
                        var spotInfo = json['spotInfo'];
                        var inspectRepiceInfo = json['inspectRepiceInfo'];
                        var inspectInfo = json['inspectInfo'];
                        var triageInfo = json['triageInfo'];
                        var firstCheck = json['firstCheck'];
                        var allergy = json['allergy'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var prinkInspectReportInfoModel = template.compile(inspectprinkTpl)({
                            spotInfo: spotInfo,
                            triageInfo: triageInfo,
                            repiceInfo: inspectRepiceInfo,
                            inspectReportDataProvider: inspectInfo,
                            firstCheck: firstCheck,
                            allergy: allergy,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });
                        $('#inspect-application-print').html(prinkInspectReportInfoModel);
                        $('#inspect-application-print .my-show').jqprint();
                    },
                    error: function () {
                    },
                });
            });

        },
        //处方打印
        recipePrint: function () {
            $('body').on('click', '.btn-nurse-recipe-print', function (e) {
                //console.log('recipe-print');
                e.preventDefault();
                var record_id = $(this).attr('record_id');
                var recipe_id = [$(this).attr('id')];
                var filterType = $(this).attr('data-type');
                var options = {
                    getCureRecord: getCureRecord,
                    getStatusOtherDesc: getStatusOtherDesc,
                    cdnHost: cdnHost,
                    baseUrl: baseUrl,
                    filterType: filterType,
                };
                recipePrint.print(record_id, recipe_id, recipePrintUrl, options);
            });

        },
        curePrint: function () {
            $('body').on('click', '.btn-nurse-cure-print', function (e) {
                e.preventDefault();
                var record_id = $(this).attr('record_id');
                var id = $(this).attr('id');
                $.ajax({
                    type: 'post',
                    url: curePrintUrl,
                    data: {
                        record_id: record_id,
                        id: id
                    },
                    dataType: 'json',
                    success: function (json) {
                        var soptInfo = json['soptInfo'];
                        var recipeRecordDataProvider = json['recipeRecordDataProvider'];
                        var cureRepiceInfo = json['cureRepiceInfo'];
                        var pirntCureRecordInfo = json['pirntCureRecordInfo'];
                        var totalPrice = json['totalPrice'];
                        var triageInfo = json['triageInfo'];
                        var firstCheck = json['firstCheck'];
                        var allergy = json['allergy'];
                        var spotConfig = json['spotConfig'];
                        var logo_img = '';
                        if (spotConfig.logo_shape == 1) {
                            logo_img = "clinic-img"
                        } else {
                            logo_img = "clinic-img-long"
                        }
                        var prinkCureRecordInfoModel = template.compile(cureprinkTpl)({
                            soptInfo: soptInfo,
                            triageInfo: triageInfo,
                            repiceInfo: cureRepiceInfo,
                            pirntCureRecordInfo: pirntCureRecordInfo,
                            record_id: record_id,
                            totalPrice: totalPrice,
                            firstCheck: firstCheck,
                            allergy: allergy,
                            cdnHost: cdnHost,
                            baseUrl: baseUrl,
                            spotConfig: spotConfig,
                            logo_img: logo_img,
                        });

                        $('#cure-print').html(prinkCureRecordInfoModel);
                        $('#' + record_id + 'cure-myshow').jqprint();
                    },
                    error: function () {
                    },
                });
            });

        }
    };
    return main;
});

