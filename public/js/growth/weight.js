/**
 *
 */

define(function(require){
	var echarts = require('public/plugins/chartjs/echarts.min');
	var growthPrintTpl = require('tpl/GrowthPrint.tpl');
	var jqprint = require('js/lib/jquery.jqprint-0.3');
	var template = require('template');
	var main = {
			init : function(){
				this.bindEvent();
			},
			bindEvent : function(){
                minMonth = JSON.parse(minMonth);
				maxMonth = JSON.parse(maxMonth);
				//百分率p值
                                patientInfo = JSON.parse(patientInfo);
                                var id = '';
                                yAxisWeightTh = JSON.parse(yAxisWeightTh);
                                for(var i = 0;i < weightTh.length;i++){
                                    id = 'weightMain-'+i;
                                    main.setWeight(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientWeight,weightTh[i],yAxisWeightTh[i]);
                                }
                                
                                yAxisHeightTh = JSON.parse(yAxisHeightTh);
                                for(var i = 0;i < heightTh.length;i++){
                                    id = 'heightMain-'+i;
                                    main.setHeight(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientHeightcm,heightTh[i],yAxisHeightTh[i]);
                                }
                                
                                //渲染头围曲线
                                yAxisHeadCircumferenceTh = JSON.parse(yAxisHeadCircumferenceTh);
                                for(var i = 0;i < headCircumferenceTh.length;i++){
                                    id = 'headCircumference-'+i;
                                    main.setHeadCircumference(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientHeadCircumference,headCircumferenceTh[i],yAxisHeadCircumferenceTh[i]);
                                }
                                
                                //渲染BMI曲线
                                yAxisBmiTh = JSON.parse(yAxisBmiTh);
                                for(var i = 0;i < bmith.length;i++){
                                    id = 'bmi-'+i;
                                    main.setBmi(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientBmi,bmith[i],yAxisBmiTh[i]);
                                }
				
				//z值
                                yAxisWeightSd = JSON.parse(yAxisWeightSd);
                                for(var i = 0;i < weightSd.length;i++){
                                    id = 'weightMainZscore-'+i;
                                    main.setZscoreWeight(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientWeightZscore,weightSd[i],yAxisWeightSd[i]);
                                }
                                
                                yAxisHeightSd = JSON.parse(yAxisHeightSd);
                                for(var i = 0;i < heightSd.length;i++){
                                    id = 'heightMainZscore-'+i;
                                    main.setZscoreHeight(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientHeightcmZscore,heightSd[i],yAxisHeightSd[i]);
                                }
                                
                                yAxisHeadCircumferenceSd = JSON.parse(yAxisHeadCircumferenceSd);
                                for(var i = 0;i < headCircumferenceSd.length;i++){
                                    id = 'headCircumferenceZscore-'+i;
                                    main.setZscoreHeadCircumference(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientHeadCircumferenceZscore,headCircumferenceSd[i],yAxisHeadCircumferenceSd[i]);
                                }
                                
                                yAxisBmiSd = JSON.parse(yAxisBmiSd);
                                for(var i = 0;i < bmiSd.length;i++){
                                    id = 'bmiZscore-'+i;
                                    main.setZscoreBmi(id,minMonth[i],parseInt(maxMonth[i])+10,parseInt(i)+1,patientInfo[i].patientBmiZscore,bmiSd[i],yAxisBmiSd[i]);
                                }
				
				$('body').on('click','.echarts-growth',function(){
					var id = $(this).data('id');
                                        if(id == 'headCircum-line'){
                                            $(this).addClass('active');
                                            var otherSiblings = $(this).siblings('span.echarts-growth');
                                            otherSiblings.removeClass('active');
                                            var otherId = otherSiblings.find('a').attr('class')
                                            $('.'+id).hide();
                                            if(otherId == 'headCircumference'){
                                                $('#headCircumferenceZscore-0').show();
                                            }else{
                                                $('#headCircumference-0').show();
                                            }
                                            return ;
                                        }
                                        var data_id = '';
                                        
					$(this).addClass('active');
                                        $(this).attr('data-value',1);
					var otherSiblings = $(this).siblings('span.echarts-growth');
					otherSiblings.removeClass('active');
                                        otherSiblings.attr('data-value',0);
                                        
					var parentSiblings = $(this).parent().siblings('.pull-left');
                                        parentSiblings.find('span').each(function(){
                                            if($(this).attr('data-value') == 1){
                                                data_id = $(this).find('a').attr('class');
                                            }
                                        });
                                        
                                        $('.' + id).hide();
                                        var data = $(this).find('a').attr('class');
                                        if(data_id == 'age0'){
                                            $('#' + data + '-0').show();
                                        }else{
                                            $('#' + data + '-1').show();
                                        }
                                        
                                        
				});
                                
                                $('body').on('click','.echarts-age',function(){
					var id = $(this).data('id');
                                        var data_id = '';
					$(this).addClass('active');
                                        $(this).attr('data-value',1);
					var otherSiblings = $(this).siblings('span.echarts-age');
					otherSiblings.removeClass('active');
                                        otherSiblings.attr('data-value',0);
                                        
					var parentSiblings = $(this).parent().siblings('.pull-right');
                                        parentSiblings.find('span').each(function(){
                                            if($(this).attr('data-value') == 1){
                                                data_id = $(this).find('a').attr('class');
                                            }
                                        });
                                        
                                        $('.' + id).hide();
                                        if($(this).find('a').attr('class') == 'age0'){
                                            $('#' + data_id + '-0').show();
                                        }else{
                                            $('#' + data_id + '-1').show();
                                        }
                                        
//                                        $('.'+id).hide();
            });
            $('.print').unbind('click').click(function () {
                main.printGrowth();
            });
        },
        tooltip: function () {
            return {
                trigger: 'axis',
//					trigger : 'item',
//			        triggerOn : 'click', 
                axisPointer: {
                    show: true,
                    type: 'line',
//			            type : 'cross',
                    lineStyle: {
                        type: 'dashed',
                        width: 1
                    }
                },
//			        formatter: '{b0}: {c0}<br />111{b1}: {c1}'
            };
        },
        toolbox: function () {
            return {
                show: true,
                feature: {
                    saveAsImage: {show: false}
                }
            };
        },
        color: function () {//百分率／p值的颜色
            return ['#55657D', "#FF1C1C", "#DCAB2D", "#00AF3D", "#DCAB2D", '#FF1C1C'];
        },
        getZscoreColor: function () {//z值的颜色
            return ['#55657D', '#55657D', "#FF1C1C", "#DCAB2D", "#00AF3D", "#DCAB2D", '#FF1C1C', '#55657D'];
        },
        getOneXaxis: function (minMonth, maxMonth, name, nameLocation, interval, length, axisTickShow, axisLabelShow) {
            return {
                type: 'value',
                min: minMonth,
                max: maxMonth,
                name: name,//x轴名称
                nameLocation: nameLocation,
                nameTextStyle: {
                    fontSize: 12
                },
                nameGap: 30,
                interval: interval,
                axisLine: {onZero: false},
                splitLine: {
                    show: false,
                    interval: 'auto',
                },
                axisTick: {
                    show: axisTickShow,
                    alignWithLabel: false,
                    interval: 'auto',
                    inside: true,
                    length: length,
                },
                axisLabel: {
                    formatter: '{value}',
                    show: axisLabelShow,
                    interval: 'auto',
                    inside: false,
                    rotate: 0,
                    margin: 8,
                    formatter: null,
                },
            };
        },
        getTwoXaxis: function (minMonth, maxMonth, name, interval, length, position, show) {
            return {
                name: name,
                type: 'value',
                min: minMonth,
                max: maxMonth,
                interval: interval,
                position: position,
                axisLine: {onZero: false},
                splitLine: {
                    show: false,
                    interval: 'auto',
                },
                axisTick: {
                    show: true,
                    alignWithLabel: false,
                    interval: 'auto',
                    inside: true,
                    length: length,
                },
                axisLabel: {
                    formatter: '{value} ',
                    show: show,
                    interval: 'auto',
                    inside: false,
                    rotate: 0,
                    margin: 8,
                    formatter: null,
                },
            };
        },
        getOneYaxis: function (name, min, max, nameLocation, position, interval, length, show) {
            return {
                type: 'value',
                name: name,
                min: min,
                max: max,
                interval: interval,
                nameLocation: nameLocation,
                position: position,
                axisLine: {onZero: false},
                nameTextStyle: {
                    fontSize: 12
                },
                nameGap: 30,
                splitLine: {
                    show: false,
                    interval: 'auto',
                },
                axisTick: {
                    show: true,
                    alignWithLabel: false,
                    interval: 'auto',
                    inside: true,
                    length: length,
                },
                axisLabel: {
                    formatter: '{value} ',
                    show: show,
                    interval: 'auto',
                    inside: false,
                    rotate: 0,
                    margin: 8,
                    formatter: null,
                },
            };
        },
        getTwoYaxis: function (name, min, max, interval, length, position) {
            return {
                type: 'value',
                position: position,
                min: min,
                max: max,
                interval: interval,
                axisLine: {onZero: false},
                name: name,
                splitLine: {
                    show: false,
                    interval: 'auto',
                },

                axisTick: {
                    show: true,
                    alignWithLabel: false,
                    interval: 'auto',
                    inside: true,
                    length: length,
                },
                axisLabel: {

                    formatter: '{value} ',
                    show: false,
                    interval: 'auto',
                    inside: false,
                    rotate: 0,
                    margin: 8,
                    formatter: null,
                },
            };
        },
        getSeries: function (name, data, maxMonth, yAxisBmiTh3, formatter) {
            return {
                name: name,
                type: 'line',
                data: data,
                symbol: 'none',
                smooth: true,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                markPoint: {
                    data: [
                        {
                            xAxis: maxMonth - 8,
                            yAxis: yAxisBmiTh3,
                            symbolSize: 1,
                            label: {
                                normal: {
                                    position: 'center',
                                    formatter: formatter
                                }
                            }

                        }
                    ]
                }
            };
        },
        setBmi: function (name, minMonth, maxMonth, yearsSex, patientBmi, bmith, yAxisBmiTh) {

            var echarts_w4 = echarts.init(document.getElementById(name));
            var min = yearsSex == 1 ? 9 : 12;
            var max = yearsSex == 1 ? 22 : 30;
            var oneInterval = yearsSex == 1 ? 1 : 2;
            var twoInterval = yearsSex == 1 ? 0.2 : 0.5;
            var showTicks = yearsSex == 1 ? true : false;
            echarts_w4.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.color(),

                calculable: true,
                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//					             main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					             main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					             main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),

                ],

                yAxis: [
                    main.getOneYaxis('BMI(kg/m²)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),
//					            main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//					            main.getTwoYaxis('',min,max,twoInterval,5,'right'),
                ],
                series: [
                    {
                        name: '[月龄,BMI指数,P值]',
                        type: 'line',
                        data: patientBmi,
                        smooth: true,
                    },
                    main.getSeries('97th', bmith.th97, maxMonth, yAxisBmiTh.Th97, '97th'),
                    main.getSeries('85th', bmith.th85, maxMonth, yAxisBmiTh.Th85, '85th'),
                    main.getSeries('50th', bmith.th50, maxMonth, yAxisBmiTh.Th50, '50th'),
                    main.getSeries('15th', bmith.th15, maxMonth, yAxisBmiTh.Th15, '15th'),
                    main.getSeries('3rd', bmith.th3, maxMonth, yAxisBmiTh.Th3, '3rd'),
                ]
            });
            $(window).resize(function () {
                echarts_w4.resize()
            });
        },
        setHeadCircumference: function (name, minMonth, maxMonth, yearsSex, patientHeadCircumference, headCircumferenceTh, yAxisHeadCircumferenceTh) {
            if (yearsSex == 2) {
                $('#headCircumference').hide();
                return;
            }
            var showTicks = yearsSex == 1 ? true : false;
            var echarts_w3 = echarts.init(document.getElementById(name));
            echarts_w3.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.color(),
                calculable: true,
                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),

                ],

                yAxis: [
                    main.getOneYaxis('头围(cm)', 30, 56, 'middle', 'left', 2, 10, true),
                    main.getTwoYaxis('', 30, 56, 0.5, 5, 'left'),

//							main.getOneYaxis('',30,56,'middle','right',2,10,false),
//							main.getTwoYaxis('',30,56,0.5,5,'right'),

                ],
                series: [
                    {
                        name: '[月龄,头围,P值]',
                        type: 'line',
                        data: patientHeadCircumference,
                        smooth: true,
                    },
                    main.getSeries('97th', headCircumferenceTh.th97, maxMonth, yAxisHeadCircumferenceTh.Th97, '97th'),
                    main.getSeries('85th', headCircumferenceTh.th85, maxMonth, yAxisHeadCircumferenceTh.Th85, '85th'),
                    main.getSeries('50th', headCircumferenceTh.th50, maxMonth, yAxisHeadCircumferenceTh.Th50, '50th'),
                    main.getSeries('15th', headCircumferenceTh.th15, maxMonth, yAxisHeadCircumferenceTh.Th15, '15th'),
                    main.getSeries('3rd', headCircumferenceTh.th3, maxMonth, yAxisHeadCircumferenceTh.Th3, '3rd'),

                ]
            });
            $(window).resize(function () {
                echarts_w3.resize()
            });
        },
        setHeight: function (name, minMonth, maxMonth, yearsSex, patientHeightcm, heightTh, yAxisHeightTh) {
//				if(patientHeightcm.length == 0){
//					$('#heightMain').hide();
//					return;
//				}
            if (yearsSex == 1) {
                var min = 40;
                var max = 125;
                var oneInterval = 5;
                var twoInterval = 1;
            } else {
                var min = 90;
                var max = 200;
                var oneInterval = 10;
                var twoInterval = 5;

            }
            var showTicks = yearsSex == 1 ? true : false;
            var echarts_w2 = echarts.init(document.getElementById(name));
            echarts_w2.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.color(),
                calculable: true,

                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),

                ],

                yAxis: [
                    main.getOneYaxis('身高(cm)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),

//								main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//								main.getTwoYaxis('',min,max,twoInterval,5,'right'),   

                ],
                series: [
                    {
                        name: '[月龄,身高,P值]',
                        type: 'line',
                        data: patientHeightcm,
                        smooth: true,
                    },
                    main.getSeries('97th', heightTh.th97, maxMonth, yAxisHeightTh.Th97, '97th'),
                    main.getSeries('85th', heightTh.th85, maxMonth, yAxisHeightTh.Th85, '85th'),
                    main.getSeries('50th', heightTh.th50, maxMonth, yAxisHeightTh.Th50, '50th'),
                    main.getSeries('15th', heightTh.th15, maxMonth, yAxisHeightTh.Th15, '15th'),
                    main.getSeries('3rd', heightTh.th3, maxMonth, yAxisHeightTh.Th3, '3rd'),
                ]
            });
            $(window).resize(function () {
                echarts_w2.resize()
            });
        },
        setWeight: function (name, minMonth, maxMonth, yearsSex, weight, weightTh, yAxisWeightTh) {
            if (yearsSex == 1) {
                var min = 0;
                var max = 28;
                var oneInterval = 2;
                var twoInterval = 1;
            } else {
                var min = 10;
                var max = 50;
                var oneInterval = 5;
                var twoInterval = 1;
                maxMonth = 132;
            }
            var showTicks = yearsSex == 1 ? true : false;
            var echarts_w1 = echarts.init(document.getElementById(name));
            echarts_w1.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.color(),
                calculable: true,
                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//							main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//				             main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//				             main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),
                ],

                yAxis: [
                    main.getOneYaxis('体重(kg)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),

//							main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//							main.getTwoYaxis('',min,max,twoInterval,5,'right'),   

                ],
                series: [{
                    name: '[月龄,体重,P值]',
                    type: 'line',
                    smooth: true,
                    data: weight,
                    sampling: 'average',

                },
                    main.getSeries('97th', weightTh.th97, maxMonth, yAxisWeightTh.Th97, '97th'),
                    main.getSeries('85th', weightTh.th85, maxMonth, yAxisWeightTh.Th85, '85th'),
                    main.getSeries('50th', weightTh.th50, maxMonth, yAxisWeightTh.Th50, '50th'),
                    main.getSeries('15th', weightTh.th15, maxMonth, yAxisWeightTh.Th15, '15th'),
                    main.getSeries('3rd', weightTh.th3, maxMonth, yAxisWeightTh.Th3, '3rd'),
                ]
            });
            $(window).resize(function () {
                echarts_w1.resize()
            });
        },
        //z值--身高
        setZscoreHeight: function (name, minMonth, maxMonth, yearsSex, patientHeightcmZscore, heightSd, yAxisHeightSd) {
            if (yearsSex == 1) {
                var min = 40;
                var max = 126;
                var oneInterval = 5;
                var twoInterval = 1;
            } else {
                var min = 90;
                var max = 200;
                var oneInterval = 10;
                var twoInterval = 5;

            }
            var showTicks = yearsSex == 1 ? true : false;
            var echartsHeight = echarts.init(document.getElementById(name));
            echartsHeight.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.getZscoreColor(),
                calculable: true,

                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),
                ],

                yAxis: [
                    main.getOneYaxis('身高(cm)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),

//								main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//								main.getTwoYaxis('',min,max,twoInterval,5,'right'),   

                ],
                series: [
                    {
                        name: '[月龄,身高,Z值]',
                        type: 'line',
//							    data: [[30,50],[10,40]],
                        data: patientHeightcmZscore,
                        smooth: true,
                    },
                    main.getSeries('+3', heightSd.Sd3, maxMonth, yAxisHeightSd.Sd3, '+3'),
                    main.getSeries('+2', heightSd.Sd2, maxMonth, yAxisHeightSd.Sd2, '+2'),
                    main.getSeries('+1', heightSd.Sd1, maxMonth, yAxisHeightSd.Sd1, '+1'),
                    main.getSeries(' 0', heightSd.Sd0, maxMonth, yAxisHeightSd.Sd0, ' 0'),
                    main.getSeries(' -1', heightSd.Sd1neg, maxMonth, yAxisHeightSd.Sd1neg, ' -1'),
                    main.getSeries(' -2', heightSd.Sd2neg, maxMonth, yAxisHeightSd.Sd2neg, ' -2'),
                    main.getSeries(' -3', heightSd.Sd3neg, maxMonth, yAxisHeightSd.Sd3neg, ' -3'),
                ]
            });
            $(window).resize(function () {
                echartsHeight.resize()
            });
        },
        //z值--体重
        setZscoreWeight: function (name, minMonth, maxMonth, yearsSex, patientWeightZscore, weightSd, yAxisWeightSd) {
            if (yearsSex == 1) {
                var min = 0;
                var max = 30;
                var oneInterval = 2;
                var twoInterval = 1;
            } else {
                var min = 10;
                var max = 60;
                var oneInterval = 5;
                var twoInterval = 1;
                maxMonth = 132;

            }
            var showTicks = yearsSex == 1 ? true : false;
            var echartsWeight = echarts.init(document.getElementById(name));
            echartsWeight.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.getZscoreColor(),
                calculable: true,

                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),
                ],

                yAxis: [
                    main.getOneYaxis('体重(kg)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),

//								main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//								main.getTwoYaxis('',min,max,twoInterval,5,'right'),   

                ],
                series: [
                    {
                        name: '[月龄,体重,Z值]',
                        type: 'line',
                        data: patientWeightZscore,
                        smooth: true,
                    },
                    main.getSeries('+3', weightSd.Sd3, maxMonth, yAxisWeightSd.Sd3, '+3'),
                    main.getSeries('+2', weightSd.Sd2, maxMonth, yAxisWeightSd.Sd2, '+2'),
                    main.getSeries('+1', weightSd.Sd1, maxMonth, yAxisWeightSd.Sd1, '+1'),
                    main.getSeries(' 0', weightSd.Sd0, maxMonth, yAxisWeightSd.Sd0, ' 0'),
                    main.getSeries(' -1', weightSd.Sd1neg, maxMonth, yAxisWeightSd.Sd1neg, ' -1'),
                    main.getSeries(' -2', weightSd.Sd2neg, maxMonth, yAxisWeightSd.Sd2neg, ' -2'),
                    main.getSeries(' -3', weightSd.Sd3neg, maxMonth, yAxisWeightSd.Sd3neg, ' -3'),
                ]
            });
            $(window).resize(function () {
                echartsWeight.resize()
            });
        },
        //z值--头围
        setZscoreHeadCircumference: function (name, minMonth, maxMonth, yearsSex, patientHeadCircumferenceZscore, headCircumferenceSd, yAxisHeadCircumferenceSd) {
            if (yearsSex == 2) {
                $('#headCircumferenceZscore').hide();
                return;
            }
            var showTicks = yearsSex == 1 ? true : false;
            var echartsHeadCircumferenceZscore = echarts.init(document.getElementById(name));
            echartsHeadCircumferenceZscore.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.getZscoreColor(),
                calculable: true,

                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					            main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),
                ],

                yAxis: [
                    main.getOneYaxis('头围(cm)', 30, 56, 'middle', 'left', 2, 10, true),
                    main.getTwoYaxis('', 30, 56, 0.5, 5, 'left'),

//								main.getOneYaxis('',30,56,'middle','right',2,10,false),
//								main.getTwoYaxis('',30,56,0.5,5,'right'),   

                ],
                series: [
                    {
                        name: '[月龄,头围,Z值]',
                        type: 'line',
                        data: patientHeadCircumferenceZscore,
                        smooth: true,
                    },
                    main.getSeries('+3', headCircumferenceSd.Sd3, maxMonth, yAxisHeadCircumferenceSd.Sd3, '+3'),
                    main.getSeries('+2', headCircumferenceSd.Sd2, maxMonth, yAxisHeadCircumferenceSd.Sd2, '+2'),
                    main.getSeries('+1', headCircumferenceSd.Sd1, maxMonth, yAxisHeadCircumferenceSd.Sd1, '+1'),
                    main.getSeries(' 0', headCircumferenceSd.Sd0, maxMonth, yAxisHeadCircumferenceSd.Sd0, ' 0'),
                    main.getSeries(' -1', headCircumferenceSd.Sd1neg, maxMonth, yAxisHeadCircumferenceSd.Sd1neg, ' -1'),
                    main.getSeries(' -2', headCircumferenceSd.Sd2neg, maxMonth, yAxisHeadCircumferenceSd.Sd2neg, ' -2'),
                    main.getSeries(' -3', headCircumferenceSd.Sd3neg, maxMonth, yAxisHeadCircumferenceSd.Sd3neg, ' -3'),
                ]
            });
            $(window).resize(function () {
                echartsHeadCircumferenceZscore.resize()
            });
        },
        //z值--BMI
        setZscoreBmi: function (name, minMonth, maxMonth, yearsSex, patientBmiZscore, bmiSd, yAxisBmiSd) {
            var min = yearsSex == 1 ? 9 : 10;
            var max = yearsSex == 1 ? 24 : 38;
            var oneInterval = yearsSex == 1 ? 1 : 2;
            var twoInterval = yearsSex == 1 ? 0.2 : 0.5;
            var showTicks = yearsSex == 1 ? true : false;
            var echartsBmiZscore = echarts.init(document.getElementById(name));
            echartsBmiZscore.setOption({

                tooltip: main.tooltip(),
                toolbox: main.toolbox(),
                color: main.getZscoreColor(),
                calculable: true,

                xAxis: [
                    main.getOneXaxis(minMonth, maxMonth, '年龄（以月计算）', 'middle', 12, 10, true, true),
                    main.getTwoXaxis(minMonth, maxMonth, '年龄(月)', 2, 5, 'bottom', showTicks),
                    main.getTwoXaxis(minMonth, maxMonth, '', 1, 3, 'bottom', false),

//								main.getOneXaxis(minMonth,maxMonth,'','top',12,10,true,false),
//					             main.getTwoXaxis(minMonth,maxMonth,'',2,5,'top',false),
//					             main.getTwoXaxis(minMonth,maxMonth,'',1,3,'top',false),
                ],

                yAxis: [
                    main.getOneYaxis('BMI(kg/m²)', min, max, 'middle', 'left', oneInterval, 10, true),
                    main.getTwoYaxis('', min, max, twoInterval, 5, 'left'),

//								main.getOneYaxis('',min,max,'middle','right',oneInterval,10,false),
//								main.getTwoYaxis('',min,max,twoInterval,5,'right'), 

                ],
                series: [
                    {
                        name: '[月龄,BMI指数,Z值]',
                        type: 'line',
//							    data: [[30,10],[10,12]],
                        data: patientBmiZscore,
                        smooth: true,
                    },
                    main.getSeries('+3', bmiSd.Sd3, maxMonth, yAxisBmiSd.Sd3, '+3'),
                    main.getSeries('+2', bmiSd.Sd2, maxMonth, yAxisBmiSd.Sd2, '+2'),
                    main.getSeries('+1', bmiSd.Sd1, maxMonth, yAxisBmiSd.Sd1, '+1'),
                    main.getSeries(' 0', bmiSd.Sd0, maxMonth, yAxisBmiSd.Sd0, ' 0'),
                    main.getSeries(' -1', bmiSd.Sd1neg, maxMonth, yAxisBmiSd.Sd1neg, ' -1'),
                    main.getSeries(' -2', bmiSd.Sd2neg, maxMonth, yAxisBmiSd.Sd2neg, ' -2'),
                    main.getSeries(' -3', bmiSd.Sd3neg, maxMonth, yAxisBmiSd.Sd3neg, ' -3'),
                ]
            });
            $(window).resize(function () {
                echartsBmiZscore.resize()
            });
        },
        /**
         * 生长曲线打印
         * @author JeanneWu
         */
        printGrowth: function () {
            //找到tab对应的active区块
            var tabActiveDom = $('.growth-cruve').find('.outpatient-form').find('.active').find('a').attr('href');

            //拼接canvas父节点
            var ageValue = $(tabActiveDom).find('.select-age').find('.active').attr('age');
            var typeValue = $(tabActiveDom).find('.select-type').find('.active').find('a').attr('class');
       
            //针对头围没有年龄切换做容错
            var ageValue = ageValue ? ageValue : '0';

            //对表头显示处理
            var ageTitle = (ageValue == '0')?'0-5':'5-19';
            var typeTitle = $(tabActiveDom).find('.select-type').find('.active').attr('name');
            var compareItem = $('.growth-cruve').find('.outpatient-form').find('.active').find('a').html().toString();

            var canvas = $('#' + typeValue + '-' + ageValue).find('canvas');
            var logo_img = '';
            if(spotConfig.logo_shape == 1){
                logo_img = "clinic-img"
            }else{
                logo_img = "clinic-img-long"
            }
            console.log(spotConfig);
            var prinkRecipeRecordInfoModel = template.compile(growthPrintTpl)({
                spotInfo: spotInfo,
                cdnHost: cdnHost,
                baseUrl: baseUrl,
                triageInfo: growthTriageInfo,
                ageTitle:ageTitle,
                typeTitle:typeTitle,
                diagnosisTime:diagnosisTime,
                compareItemY:compareItem.split('-')[0],
                compareItemX:compareItem.split('-')[1],
                spotConfig : spotConfig,
                logo_img : logo_img,
            });
            $('#growth_print').html(prinkRecipeRecordInfoModel);
            $('#growthImg').html(canvas);

            window.print();
            $('#' + typeValue + '-' + ageValue).children(':first').html($('#growthImg').find('canvas'));


        }


    };

    return main;
})