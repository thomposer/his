
define(function (require) {
    var template = require('template');
    var common = require('js/lib/common');
    var checkTpl = require('tpl/outpatient/check.tpl');
    var cureTpl = require('tpl/outpatient/cure.tpl');
    var _self;
    var isInTemplateMenu = false;
    var isInCureTemplateMenu = false;
    var isInCheckTemplateMenu = false;
    var tree = [];
    var cureTree = [];
    var checkTree = [];
    var choseTemplate = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            
            // _self.initTemplateMenul();
            
            
            $('body').on('click','.inspect-template-desc',function(){
                _self.formatRecipeTemplateMenu();
                _self.initTemplateMenul();
            });
            $('body').on('click','.check-template-desc',function(){
                _self.formatCheckTemplateMenu();
                _self.initCheckTemplateMenul();
            });
            $('body').on('click','.cure-template-desc',function(){
                _self.formatCureTemplateMenu();
                _self.initCureTemplateMenul();
            });

        },
        formatRecipeTemplateMenu: function () {
            var prevType = -1;
            var prevTypeID = -1;
            var treeMenu = [];
            for (var i = 0; i < inspectTemplateMenu.length; i++) {
                var template = inspectTemplateMenu[i];
                var nodeL1 = {};
                nodeL1.text = htmlEncodeByRegExp(recipeTemplateType[template.type]);
                nodeL1.nodes = [];
                nodeL1.selectable = false;

                var nodeL2 = {};
                nodeL2.text = htmlEncodeByRegExp(template.template_name);
                // nodeL2.text =  template.recipe_type_template_name ;
                nodeL2.nodes = [];
                nodeL2.selectable = false;

                var nodeL3 = {};
                nodeL3.text = htmlEncodeByRegExp(template.name);
                nodeL3.tags = [template.id];

                if (template.type != prevType || i == 0) {
                    treeMenu.push(nodeL1);
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);

                } else if (template.template_type_id != prevTypeID) {
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);
                }

                var indexL2 = treeMenu[treeMenu.length - 1]['nodes'].length - 1;
                treeMenu[treeMenu.length - 1]['nodes'][indexL2]['nodes'].push(nodeL3);
                prevType = template.type;
                prevTypeID = template.template_type_id;
            }
            tree = treeMenu;
        },
        initTemplateMenul: function () {//生成检验医嘱模板menu  可获取检验医嘱的项目
            $('#inspect-template-select').treeview({
                data: tree,
                levels: 3,
                highlightSelected: false,
                showBorder: false,
                expandIcon: "glyphicon glyphicon-chevron-right",
                collapseIcon: "glyphicon glyphicon-chevron-down",
                onNodeSelected: function (event, data) {
                    $('#inspect-template-select').hide();
                    $('#inspect-template-select').treeview('unselectNode', [data.nodeId, {silent: true}]);
                    var inspectTemplateId = data.tags[0];
                    _self.addInspectTemplate(inspectTemplateId);
                }
            });

            $('body').on('click', function () {
                if (!isInTemplateMenu) {
                    $('#inspect-template-select').hide();
                }
            });

            $("#inspect-template-select").mouseover(function () {
                isInTemplateMenu = true;
            });

            $("#inspect-template-select").mouseout(function () {
                isInTemplateMenu = false;
            });


            $('.inspect-template-desc').unbind().bind('click', function (event) {
                event.stopPropagation();
                if ($('#inspect-template-select').is(":hidden")) {
                    if (tree.length > 0) {
                        $('#inspect-template-select').show();
                    } else {
                        showInfo('暂无模板', '180px', 1);
                    }
                } else {
                    $('#inspect-template-select').hide();
                }
            });

            $('#inspect-template-select').hide();

        },
        addInspectTemplate: function (id) {
            $.ajax({
                type: 'post',
                url: getInspectTemplateInfo,
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function (json) {
                    if (json.data != undefined) {
                        var templateVal='';
                        //检查是否所有检验医嘱都有关联项目
                        var inspectFlag=1;
                        var inspectList = json.data.inspectList;
                        json = json.data.inspectTemplateInfo;
                        for(var i=0;i<json.length;i++){
                             templateVal = json[i];
                            if (inspectList[templateVal.clinic_inspect_id] != undefined) {
                                if(!main.checkInspect(inspectList[templateVal.clinic_inspect_id])){
                                    inspectFlag=0;
                                    break;
                                }
                            }
                        }
                        if(inspectFlag) {
                            for (var i = 0; i < json.length; i++) {
                                templateVal = json[i];
                                if (inspectList[templateVal.clinic_inspect_id] != undefined) {
                                    main.addCheck(inspectList[templateVal.clinic_inspect_id], inspectList[templateVal.clinic_inspect_id]['name'], 'InspectRecord[inspect_id][]', 'InspectRecord[deleted][]', 'inspect-list', '.inspect-form tbody', 1);
                                    
                                }
                            }
                        }else{
                            showInfo('实验室检查没有关联检验项目','250px',2);
                        }
                    }

                },
                error: function () {
                    showInfo('获取检验模板失败', '180px', 2);
                },
            });
        },
        formatCureTemplateMenu: function () {
            var prevType = -1;
            var prevTypeID = -1;
            var treeMenu = [];
            for (var i = 0; i < cureTemplateMenu.length; i++) {
                var template = cureTemplateMenu[i];
                var nodeL1 = {};
                nodeL1.text = htmlEncodeByRegExp(recipeTemplateType[template.type]);
                nodeL1.nodes = [];
                nodeL1.selectable = false;

                var nodeL2 = {};
                nodeL2.text = htmlEncodeByRegExp(template.template_name);
                // nodeL2.text =  template.recipe_type_template_name ;
                nodeL2.nodes = [];
                nodeL2.selectable = false;

                var nodeL3 = {};
                nodeL3.text = htmlEncodeByRegExp(template.name);
                nodeL3.tags = [template.id];

                if (template.type != prevType || i == 0) {
                    treeMenu.push(nodeL1);
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);

                } else if (template.template_type_id != prevTypeID) {
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);
                }

                var indexL2 = treeMenu[treeMenu.length - 1]['nodes'].length - 1;
                treeMenu[treeMenu.length - 1]['nodes'][indexL2]['nodes'].push(nodeL3);
                prevType = template.type;
                prevTypeID = template.template_type_id;
            }
            cureTree = treeMenu;
        },
        initCureTemplateMenul: function () {//生成治疗医嘱模板menu  可获取治疗医嘱的项目
            $('#cure-template-select').treeview({
                data: cureTree,
                levels: 3,
                highlightSelected: false,
                showBorder: false,
                expandIcon: "glyphicon glyphicon-chevron-right",
                collapseIcon: "glyphicon glyphicon-chevron-down",
                onNodeSelected: function (event, data) {
                    $('#cure-template-select').hide();
                    $('#cure-template-select').treeview('unselectNode', [data.nodeId, {silent: true}]);
                    var cureTemplateId = data.tags[0];
                    _self.addCureTemplate(cureTemplateId);
                }
            });

            $('body').on('click', function () {
                if (!isInCureTemplateMenu) {
                    $('#cure-template-select').hide();
                }
            });

            $("#cure-template-select").mouseover(function () {
                isInCureTemplateMenu = true;
            });

            $("#cure-template-select").mouseout(function () {
                isInCureTemplateMenu = false;
            });


            $('.cure-template-desc').unbind().bind('click', function (event) {
                event.stopPropagation();
                if ($('#cure-template-select').is(":hidden")) {
                    if (cureTree.length > 0) {
                        $('#cure-template-select').show();
                    } else {
                        showInfo('暂无模板', '180px', 1);
                    }
                } else {
                    $('#cure-template-select').hide();
                }
            });

            $('#cure-template-select').hide();

        },
        addCureTemplate: function (id) {
            $.ajax({
                type: 'post',
                url: getCureTemplateInfo,
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function (json) {
                	var cureList = json.data.cureList;
                	var json = json.data.cureTemplateInfo;
                    if (json != undefined) {
                        for (var i = 0; i < json.length; i++) {
                            var templateVal = json[i];
                            if (cureList[templateVal.clinic_cure_id] != undefined) {
                                main.addCure(cureList[templateVal.clinic_cure_id], cureList[templateVal.clinic_cure_id]['name'],cureList[templateVal.clinic_cure_id]['price'], cureList[templateVal.clinic_cure_id]['unit'],templateVal.time,templateVal.description);
                            }
                        }
                    }
                },
                error: function () {
                    showInfo('获取治疗模板失败', '180px', 2);
                },
            });
        },
        formatCheckTemplateMenu: function () {
            var prevType = -1;
            var prevTypeID = -1;
            var treeMenu = [];
            for (var i = 0; i < checkTemplateMenu.length; i++) {
                var template = checkTemplateMenu[i];
                var nodeL1 = {};
                nodeL1.text = htmlEncodeByRegExp(recipeTemplateType[template.type]);
                nodeL1.nodes = [];
                nodeL1.selectable = false;

                var nodeL2 = {};
                nodeL2.text = htmlEncodeByRegExp(template.template_name);
                // nodeL2.text =  template.recipe_type_template_name ;
                nodeL2.nodes = [];
                nodeL2.selectable = false;

                var nodeL3 = {};
                nodeL3.text = htmlEncodeByRegExp(template.name);
                nodeL3.tags = [template.id];

                if (template.type != prevType || i == 0) {
                    treeMenu.push(nodeL1);
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);

                } else if (template.template_type_id != prevTypeID) {
                    treeMenu[treeMenu.length - 1]['nodes'].push(nodeL2);
                }

                var indexL2 = treeMenu[treeMenu.length - 1]['nodes'].length - 1;
                treeMenu[treeMenu.length - 1]['nodes'][indexL2]['nodes'].push(nodeL3);
                prevType = template.type;
                prevTypeID = template.template_type_id;
            }
            checkTree = treeMenu;
            //console.log(checkTree);

        },
        initCheckTemplateMenul: function () {//生成检查医嘱模板menu  可获取检查医嘱的项目
            $('#check-template-select').treeview({
                data: checkTree,
                levels: 3,
                highlightSelected: false,
                showBorder: false,
                expandIcon: "glyphicon glyphicon-chevron-right",
                collapseIcon: "glyphicon glyphicon-chevron-down",
                onNodeSelected: function (event, data) {
                    $('#check-template-select').hide();
                    $('#check-template-select').treeview('unselectNode', [data.nodeId, {silent: true}]);
                    var checkTemplateId = data.tags[0];
                    _self.addCheckTemplate(checkTemplateId);
                }
            });

            $('body').on('click', function () {
                if (!isInCheckTemplateMenu) {
                    $('#check-template-select').hide();
                }
            });

            $("#check-template-select").mouseover(function () {
                isInCheckTemplateMenu = true;
            });

            $("#check-template-select").mouseout(function () {
                isInCheckTemplateMenu = false;
            });


            $('.check-template-desc').unbind().bind('click', function (event) {
                event.stopPropagation();
                if ($('#check-template-select').is(":hidden")) {
                    if (checkTree.length > 0) {
                        $('#check-template-select').show();
                    } else {
                        showInfo('暂无模板', '180px', 1);
                    }
                } else {
                    $('#check-template-select').hide();
                }
            });

            $('#check-template-select').hide();

        },
        addCheckTemplate: function (id) {
            $.ajax({
                type: 'post',
                url: getCheckTemplateInfo,
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function (json) {
                    if (json != undefined && json instanceof Array) {
                        for (var i = 0; i < json.length; i++) {
                            var templateVal = json[i];
                            main.addCheck(templateVal,templateVal['name'], 'CheckRecord[check_id][]', 'CheckRecord[deleted][]', 'check-list', '.check-form tbody', 2);
                        }
                    }

                },
                error: function () {
                    showInfo('获取检验模板失败', '180px', 2);
                },
            });
        },
    };
    return choseTemplate;
})
