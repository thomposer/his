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
        +      '<img class="{{logo_img}}" src="{{cdnHost}}{{spotConfig.logo_img}}" onerror="javascript:this.src=\'/public/img/charge/img_click_moren.png\'" alt="">'
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
        +             '<p class="title small-title-third">病历号：{{chargeRecordLogList.patient_number}}</p>'
        + ''
        +             '<div class="fill-info">'
        + ''
        +                 '<div class="patient-user">'
        + ''
        +                     '<div class="font-0px">'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value">{{chargeRecordLogList.username}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">性别</span><span class="column-value">{{chargeRecordLogList.sex}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">年龄</span><span style="width: 70%;" class="column-value">{{chargeRecordLogList.birthday}}</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                     '<div class="line-margin-top font-0px">'
        + ''
        +                             '<div class="total-column-three-part"><span class="column-name">出生日期</span><span class="column-value" style="width:58%;">{{chargeRecordLogList.birth}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">TEL</span><span  class="column-value">{{chargeRecordLogList.iphone}}</span></div>'
        + ''
        +                         '<div class="total-column-three-part"><span class="column-name">接诊医生</span><span style="width: 58%;" class="column-value">'
        +                                          '{{chargeRecordLogList.doctorName}}'
        +                                    '</span></div>'
        + ''
        +                     '</div>'
        + ''
        +                 '</div>'
        + ''
        +             '</div>'
        + ''
        +   '{{if orderLogList.length > 0}}'
        +       '<div class="charge-block-margin-top fill-info font-3rem">'
        +           '<div class="title small-title-third">药品类收费明细</div>'
        +           '<div id="w1" class="grid-view table-responsive charge-table-first inspect-table">'
        +               '<table class="table charge-table font-3rem inspect-table">'
        +                   '<thead>'
        +                       '<tr>'
        +                           '<th class="col-sm-6 col-md-6" style="width:20%;">收费项 名称</th>'
        +                           '<th class="col-sm-1 col-md-1" style="width:4%;">单位</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:7%;">单价</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:5%;">数量</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">单项优惠金额</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">会员卡优惠金额</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">折后金额(元)</th>'
        +                       '</tr>'
        +                   '</thead>'
        +                   '<tbody>'
        +                       '{{each orderLogList as value}}'
        +                           '<tr>'
        +                               '<td>{{value.name}}</td>'
        +                               '<td>{{value.unit}}</td>'
        +                               '<td class="tr">{{value.unit_price}}</td>'
        +                               '<td class="tr">{{value.num}}</td>'
        +                               '<td class="total_price tr">{{if value.discount_price !=0.00 }}{{value.discount_price}}{{else}}--{{/if}}</td>'
        +                               '<td class="cardDiscountPrice total_price tr">{{if value.card_discount_price !=0.00 }}{{value.card_discount_price}}{{else}}--{{/if}}</td>'
        +                               '<td class="total_price tr">{{value.total_price}}</td>'
        +                           '</tr>'
        +                       '{{/each}}'
        +                   '</tbody>'
        +               '</table>'
        +           '</div>'

        +           '{{if packageRecord.price.name !="" }}'
        +         '<div class="fill-info" style="margin-top:-20px;margin-bottom:10px;">'
        + ''
        +             '<div class="patient-user">'
        + ''
        +                 '<div class="font-0px">'
        + ''
        +                     '<div>'
        +                           '<span class="column-name" style="width:30%;margin-left:5px;">套餐包含</span>'
        +                           '<span class="column-name" style="width:69%;">'
        +                                   '{{if packageRecord.inspect !="" }}'
        +                                   '<div>检验医嘱：'
        +                                       '{{packageRecord.inspect}}'
        +                                   '</div>'
        +                                   '{{else}}'
        +                                   '{{/if}}'
        +                                   '{{if packageRecord.check !="" }}'
        +                                   '<div>检查医嘱：'
        +                                       '{{packageRecord.check}}'
        +                                   '</div>'
        +                                   '{{else}}'
        +                                   '{{/if}}'
        +                                   '{{if packageRecord.cure !="" }}'
        +                                   '<div>治疗医嘱：'
        +                                       '{{packageRecord.cure}}'
        +                                   '</div>'
        +                                   '{{else}}'
        +                                   '{{/if}}'
        +                                   '{{if packageRecord.recipe !="" }}'
        +                                   '<div>处方医嘱：'
        +                                       '{{packageRecord.recipe}}'
        +                                   '</div>'
        +                                   '{{else}}'
        +                                   '{{/if}}'
        +                           '</span>'
        +                      '</div>'
        +                 '</div>'
        +                 '</div>'
        +             '</div>'
        +             '{{/if}}'


        +       '</div>'
        +   '{{/if}}'
        +   '{{if materialList.length > 0}}'
        +       '<div class="charge-block-margin-top fill-info font-3rem">'
        +           '<div class="title small-title-third">其它类收费明细</div>'
        +           '<div id="w1" class="grid-view table-responsive charge-table-first inspect-table">'
        +               '<table class="table charge-table font-3rem inspect-table">'
        +                   '<thead>'
        +                       '<tr>'
        +                           '<th class="col-sm-6 col-md-6" style="width:20%;">收费项 名称</th>'
        +                           '<th class="col-sm-1 col-md-1" style="width:4%;">单位</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:7%;">单价</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:5%;">数量</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">单项优惠金额</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">会员卡优惠金额</th>'
        +                           '<th class="col-sm-1 col-md-1 tr" style="width:10%;">折后金额(元)</th>'
        +                       '</tr>'
        +                   '</thead>'
        +                   '<tbody>'
        +                       '{{each materialList as value}}'
        +                           '<tr>'
        +                               '<td>{{value.name}}</td>'
        +                               '<td>{{value.unit}}</td>'
        +                               '<td class="tr">{{value.unit_price}}</td>'
        +                               '<td class="tr">{{value.num}}</td>'
        +                               '<td class="total_price tr">{{if value.discount_price !=0.00 }}{{value.discount_price}}{{else}}--{{/if}}</td>'
        +                               '<td class="cardDiscountPrice total_price tr">{{if value.card_discount_price !=0.00 }}{{value.card_discount_price}}{{else}}--{{/if}}</td>'
        +                               '<td class="total_price tr">{{value.total_price}}</td>'
        +                           '</tr>'
        +                       '{{/each}}'
        +                   '</tbody>'
        +               '</table>'
        +           '</div>'
        +       '</div>'
        +   '{{/if}}'

        +   '{{if orderLogList.length > 0}}'
        +       '<div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">'
        +           '<div class="title small-title-third">'
        +               '药品类收费项目'
        +           '</div>'
        +           '<div class="grid-view table-responsive ">'
        +               '<table class="table font-3rem charge-table inspect-table">'
        +                   '<thead>'
        +                       '<tr>'
        +                           '<th style="width: 44%;">项目名称</th>'
        +                           '<th class="tr" style="width: 20%;">折后金额(元)</th>'
        +                           '<th style="width: 36%;"></th>'
        +                       '</tr>'
        +                   '</thead>'
        +                   '<tbody>'
        +                       '{{if chargeRecordLogList.inspect_price}}'
        +                           '<tr>'
        +                               '<td>实验室检查费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.inspect_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                       '{{if chargeRecordLogList.check_price}}'
        +                           '<tr>'
        +                               '<td>影像学检查总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.check_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                       '{{if chargeRecordLogList.recipe_price}}'
        +                           '<tr>'
        +                               '<td>处方总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.recipe_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                       '{{if chargeRecordLogList.cure_price}}'
        +                           '<tr>'
        +                               '<td>治疗总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.cure_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                       '{{if chargeRecordLogList.diagnosis_price}}'
        +                           '<tr>'
        +                               '<td>诊疗总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.diagnosis_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                       '{{if chargeRecordLogList.package_price}}'
        +                           '<tr>'
        +                               '<td>医嘱套餐总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.package_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                   '</tbody>'
        +               '</table>'
        +           '</div>'
        +       '</div>'
        +   '{{/if}}'
        +   '{{if materialList.length > 0}}'
        +       '<div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">'
        +           '<div class="title small-title-third">'
        +               '其它类收费项目'
        +           '</div>'
        +           '<div class="grid-view table-responsive ">'
        +               '<table class="table font-3rem charge-table inspect-table">'
        +                   '<thead>'
        +                       '<tr>'
        +                           '<th style="width: 44%;">项目名称</th>'
        +                           '<th class="tr" style="width: 20%;">折后金额(元)</th>'
        +                           '<th style="width: 36%;"></th>'
        +                       '</tr>'
        +                   '</thead>'
        +                   '<tbody>'
        +                       '{{if chargeRecordLogList.material_price}}'
        +                           '<tr>'
        +                               '<td>其他总费用</td>'
        +                               '<td class="tr">{{chargeRecordLogList.material_price}}</td>'
        +                               '<td></td>'
        +                           '</tr>'
        +                       '{{/if}}'
        +                   '</tbody>'
        +               '</table>'
        +           '</div>'
        +       '</div>'
        +   '{{/if}}'
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
        +                         '{{chargeRecordLogList.totalPrice}}元'
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
        +                         '{{chargeRecordLogList.totalDiscount}}元'
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
        +                         '{{chargeRecordLogList.price}}元'
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
        + '</div>';
});