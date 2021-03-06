define(function () {
    return ''
        + '<div class="hide">'
        + '<div class="chargePrint{{outTradeNo}}">'
        +     '<div class="my-show rebate-print" style="page-break-after: always;">'
        + ''
        +         '<div class="rebate-foot-bottom">'
        + ''
        + '{{if spotConfig.logo_img}}'
        + ''
        +      '<img class="clinic-img" src="{{cdnHost}}{{spotConfig.logo_img}}" onerror="javascript:this.src=\'/public/img/charge/img_click_moren.png\'"alt="">'
        + ''
        + '{{/if}}'
        + ''
        +             '<p class="rebate-date fr">{{spotConfig.pub_tel}}</p>'
        + ''
        +             '<div class="children-sign">儿科</div>'
        + ''
        +         '</div>'
        + ''
        +         '<span class="clearfix"></span>'
        + ''
        +     '<div class = \'title rebate-title add-margin-bottom-20\' style = "font-size:16px; margin-top:-50px">{{spotConfig[\'spot_name\']}}</div>'

        +         '<p class="title rebate-title add-margin-bottom-20">收费清单</p>'
        + ''
        +         '<div style="min-height: 600px;" class="print-main-contnet">'
        + ''
        +             '<p class="title small-title-third">病历号：{{userInfo.patient_number}}</p>'
        + ''
        +             '<div class="fill-info">'
        + ''
        +                 '<div class="patient-user">'
        + ''
        +                     '<div class="font-0px">'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value">{{userInfo.username}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">性别</span><span class="column-value">{{userInfo.sex}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">年龄</span><span style="width: 70%;" class="column-value">{{userInfo.birthday}}</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                     '<div class="line-margin-top font-0px">'
        + ''
        +                             '<div class="total-column-three-part"><span class="column-name">出生日期</span><span class="column-value" style="width:58%;">{{userInfo.birth}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">TEL</span><span  class="column-value">{{userInfo.iphone}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">接诊医生</span><span style="width: 58%;" class="column-value">'
        +                                          '{{doctorName}}'
        +                                    '</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +             '<div class="charge-block-margin-top fill-info font-3rem">'
        + ''
        +                 '<div class="title small-title-third">收费明细</div>'
        + ''
        +                 '<div id="w1" class="grid-view table-responsive charge-table-first inspect-table">'
        + ''
        +                     '<table class="table charge-table font-3rem inspect-table">'
        + ''
        +                         '<thead>'
        + ''
        +                         '<tr>'
        + ''
        +                             '<th style="width:20%;" class="col-sm-8 col-md-8">收费项(名称)</th>'
        + ''
        +                             '<th style="width:3%;" class="col-sm-1 col-md-1 ">单位</th>'
        + ''
        +                             '<th  style="width:7%;" class="col-sm-1 col-md-1 tr">单价(元)</th>'
        + ''
        +                             '<th  style="width:5%;" class="col-sm-1 col-md-1 tr">数量</th>'
        + ''
        +                             '<th  style="width:10%;" class="col-sm-1 col-md-1 tr">优惠金额(元)</th>'
        + ''
        +                             '<th  style="width:10%;" class="col-sm-1 col-md-1 tr">折后金额(元)</th>'
        + ''
        +                         '</tr>'
        + ''
        +                         '</thead>'
        + ''
        +                         '<tbody>'
        + ''
        +                         '{{each inspectReportDataProvider as value i}}'
        + ''
        +                     '{{if printWay == 1 && value[\'type\'] == typeList[\'material\']}}'
        +                     ''
        +                     '{{else}}'
        +                         '<tr>'
        + ''
        +                             '<td class="col-sm-8 col-md-8">{{value[\'name\']}}</td>'
        + ''
        +                             '<td>{{value[\'unit\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'unit_price\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'num\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'discount_price_unit_total\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'rest\']}}</td>'
        + ''
        +                         '</tr>'
        +                      '{{/if}}'
        + ''
        +                         '{{/each}}'
        + ''
        +                         '</tbody>'
        + ''
        +                     '</table>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +             '<div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">'
        + ''
        +                 '<div class="title small-title-third">收费项目</div>'
        + ''
        +                 '<div class="grid-view table-responsive ">'
        + ''
        +                     '<table class="table font-3rem charge-table inspect-table">'
        + ''
        +                         '<thead>'
        + ''
        +                         '<tr>'
        + ''
        +                             '<th style="width:44%">项目名称</th>'
        + ''
        +                             '<th style="width:13%">折后金额(元)</th>'
        + ''
        +                             '<th style="width:45%"></th>'
        + ''
        +                         '</tr>'
        + ''
        +                         '</thead>'
        + ''
        +                         '<tbody>'
        + ''
        +                         '{{if chargeType.inspectType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>实验室检查费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.inspectType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '{{if chargeType.checkType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>影像学检查费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.checkType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '{{if chargeType.recipeType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>处方费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.recipeType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '{{if chargeType.cureType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>治疗费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.cureType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '{{if chargeType.priceType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>诊疗费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.priceType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '{{if printWay != 1 && chargeType.materialType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>其他费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.materialType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '</tbody>'
        + ''
        +                     '</table>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        + ''
        + ''
        + ''
        +         '<div style="position: relative;min-height: 70px;" class="rebate-foot font-0px">'
        + ''
        +             '<div class="foot-left rebate-type" style="width: 75%;position:absolute;bottom:0px;">'
        + ''
        +             '</div>'
        + ''
        +             '<div class="display-inline-block fr">'
        + ''
        +              '<div class="code-part font-5rem display-inline-block tc">'
        + ''
        +                   '<div style="width:60px;" id="pay_code" class="add-padding-bottom"><img /></div>'
        + ''
        +                   '<span id="notice_words"></span>'
        + ''
        +              '</div>'
        + ''
        +               '<div class="display-inline-block">'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '应收费用：'
        + ''
        +                         '&nbsp;'
        + ''
        +                      '{{if printWay == 1}}'
        +                         '{{chargeType.otherTotal}}元'
        +                      '{{else}}' 
        +                         '{{chargeType.total}}元'
        +                      '{{/if}}'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '优惠金额：'
        + ''
        +                         '&nbsp;'
        + ''
        +                      '{{if printWay == 1}}'
        +                         '{{chargeType.otherDiscount}}元'
        +                      '{{else}}' 
        +                         '{{chargeType.totalDiscount}}元'
        +                      '{{/if}}'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '实际应付：'
        + ''
        +                         '&nbsp;'
        +                      '{{if printWay == 1}}'
        +                         '{{chargeType.otherRest}}元'
        +                      '{{else}}' 
        +                         '{{chargeType.totalRest}}元'
        +                      '{{/if}}'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +               '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        + ''
        + ''
        + ''
        + ''
        + ''
        +         '<div class="fill-info-buttom">'
        + ''
        +             '<div class="rebate-foot-bottom-second fl">'
        + ''
        +                 '<div class="tow-line-buttom fl">'
        + ''
        +                     '<div class="line-margin-top font-0px">'
        + ''
        +                         '<p class="font-3rem charge-bottom width-75">收费员：</p>'
        + ''
        +                         '<p class="font-3rem charge-bottom">日期：</p>'
        + ''
        +                     '</div>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        +           '<span class="clearfix"></span>'
        + ''
        +     '</div>'
        +'{{if printWay}}'
        +     '<div class="my-show rebate-print" style="page-break-after: always;">'//打印非药品类
        + ''
        +         '<div class="rebate-foot-bottom">'
        + ''
        + '{{if soptInfo.icon_url}}'
        + ''
        +      '<img class="clinic-img" src="{{cdnHost}}{{soptInfo.icon_url}}" onerror="javascript:this.src=\'/public/img/charge/img_click_moren.png\'"alt="">'
        + ''
        + '{{/if}}'
        + ''
        +             '<p class="rebate-date fr">{{soptInfo.telephone}}</p>'
        + ''
        +             '<div class="children-sign">儿科</div>'
        + ''
        +         '</div>'
        + ''
        +         '<span class="clearfix"></span>'
        + ''
        +         '<p class="title rebate-title add-margin-bottom-20">收费清单</p>'
        + ''
        +         '<div style="min-height: 600px;" class="print-main-contnet">'
        + ''
        +             '<p class="title small-title-third">病历号：{{userInfo.patient_number}}</p>'
        + ''
        +             '<div class="fill-info">'
        + ''
        +                 '<div class="patient-user">'
        + ''
        +                     '<div class="font-0px">'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value">{{userInfo.username}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">性别</span><span class="column-value">{{userInfo.sex}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">年龄</span><span style="width: 70%;" class="column-value">{{userInfo.birthday}}</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                     '<div class="line-margin-top font-0px">'
        + ''
        +                             '<div class="total-column-three-part"><span class="column-name">出生日期</span><span class="column-value" style="width:58%;">{{userInfo.birth}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">TEL</span><span  class="column-value">{{userInfo.iphone}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">接诊医生</span><span style="width: 58%;" class="column-value">'
        +                                          '{{doctorName}}'
        +                                    '</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +             '<div class="charge-block-margin-top fill-info font-3rem">'
        + ''
        +                 '<div class="title small-title-third">收费明细</div>'
        + ''
        +                 '<div id="w1" class="grid-view table-responsive charge-table-first inspect-table">'
        + ''
        +                     '<table class="table charge-table font-3rem inspect-table">'
        + ''
        +                         '<thead>'
        + ''
        +                         '<tr>'
        + ''
        +                             '<th style="width:20%;" class="col-sm-8 col-md-8">收费项(名称)</th>'
        + ''
        +                             '<th style="width:3%;" class="col-sm-1 col-md-1 ">单位</th>'
        + ''
        +                             '<th  style="width:7%;" class="col-sm-1 col-md-1 tr">单价(元)</th>'
        + ''
        +                             '<th  style="width:5%;" class="col-sm-1 col-md-1 tr">数量</th>'
        + ''
        +                             '<th  style="width:10%;" class="col-sm-1 col-md-1 tr">优惠金额(元)</th>'
        + ''
        +                             '<th  style="width:10%;" class="col-sm-1 col-md-1 tr">折后金额(元)</th>'
        + ''
        +                         '</tr>'
        + ''
        +                         '</thead>'
        + ''
        +                         '<tbody>'
        + ''
        +                         '{{each inspectReportDataProvider as value i}}'
        + ''
        +                         '{{if value[\'type\'] == typeList[\'material\']}}'
        +                         '<tr>'
        + ''
        +                             '<td class="col-sm-8 col-md-8">{{value[\'name\']}}</td>'
        + ''
        +                             '<td>{{value[\'unit\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'unit_price\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'num\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'discount_price_unit_total\']}}</td>'
        + ''
        +                             '<td class="tr">{{value[\'rest\']}}</td>'
        + ''
        +                         '</tr>'
        +                         '{{/if}}'
        + ''
        +                         '{{/each}}'
        + ''
        +                         '</tbody>'
        + ''
        +                     '</table>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +             '<div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">'
        + ''
        +                 '<div class="title small-title-third">收费项目</div>'
        + ''
        +                 '<div class="grid-view table-responsive ">'
        + ''
        +                     '<table class="table font-3rem charge-table inspect-table">'
        + ''
        +                         '<thead>'
        + ''
        +                         '<tr>'
        + ''
        +                             '<th style="width:44%">项目名称</th>'
        + ''
        +                             '<th style="width:13%">折后金额(元)</th>'
        + ''
        +                             '<th style="width:45%"></th>'
        + ''
        +                         '</tr>'
        + ''
        +                         '</thead>'
        + ''
        +                         '<tbody>'
        + ''
        +                         '{{if chargeType.materialType != ""}}'
        + ''
        +                         '<tr data-key="2">'
        + ''
        +                             '<td>其他费用</td>'
        + ''
        +                             '<td class="tr">{{chargeType.materialType}}</td>'
        + ''
        +                             '<td></td>'
        + ''
        +                         '</tr>'
        + ''
        +                         '{{/if}}'
        + ''
        +                         '</tbody>'
        + ''
        +                     '</table>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        + ''
        + ''
        + ''
        +         '<div style="position: relative;min-height: 70px;" class="rebate-foot font-0px">'
        + ''
        +             '<div class="foot-left rebate-type" style="width: 75%;position:absolute;bottom:0px;">'
        + ''
        +             '</div>'
        + ''
        +             '<div class="display-inline-block fr">'
        + ''
        +              '<div class="code-part font-5rem display-inline-block tc">'
        + ''
        +                   '<div style="width:60px;" id="pay_code" class="add-padding-bottom"><img /></div>'
        + ''
        +                   '<span id="notice_words"></span>'
        + ''
        +              '</div>'
        + ''
        +               '<div class="display-inline-block">'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '应收费用：'
        + ''
        +                         '&nbsp;'
        + ''
        +                         '{{chargeType.materialTotal}}元'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '优惠金额：'
        + ''
        +                         '&nbsp;'
        + ''
        +                         '{{chargeType.materialDiscount}}元'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +                 '<div class="font-16px foot-left add-margin-bottom-5">'
        + ''
        +                     '<label>'
        + ''
        +                         '实际应付：'
        + ''
        +                         '&nbsp;'
        +			'{{chargeType.materialRest}}元'
        + ''
        +                     '</label>'
        + ''
        +                 '</div>'
        + ''
        +               '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        + ''
        + ''
        + ''
        + ''
        + ''
        +         '<div class="fill-info-buttom">'
        + ''
        +             '<div class="rebate-foot-bottom-second fl">'
        + ''
        +                 '<div class="tow-line-buttom fl">'
        + ''
        +                     '<div class="line-margin-top font-0px">'
        + ''
        +                         '<p class="font-3rem charge-bottom width-75">收费员：</p>'
        + ''
        +                         '<p class="font-3rem charge-bottom">日期：</p>'
        + ''
        +                     '</div>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        +           '<span class="clearfix"></span>'
        + ''
        +     '</div>'
        + '</div>'
        + '{{/if}}'
        + '</div>';
});