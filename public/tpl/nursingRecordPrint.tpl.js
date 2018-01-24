define(function () {
    return ''
        + '<div class="my-show-new" id="nursing-record-{{record_id}}">'
        + ''
        + '\t<div class="rebate-foot-bottom">'
        + ''
        + '\t\t\t{{if spotConfig[\'logo_img\']}}'
        + ''
        + '\t\t\t\t<img class="{{logo_img}}" src="{{cdnHost}}{{spotConfig[\'logo_img\']}}" onerror="javascript:this.src=\'{{baseUrl}}/public/img/charge/img_click_moren.png\'" />      '
        + ''
        + '\t\t\t{{/if}}        '
        + ''
        + '\t\t\t\t<p class="rebate-date">       '
        + ''
        + '\t\t\t\t{{if spotConfig[\'pub_tel\']}}       '
        + ''
        + '\t\t\t\tTel:{{spotConfig[\'pub_tel\']}}        '
        + ''
        + '\t\t\t\t{{/if}}       '
        + ''
        + '\t\t\t\t</p>       '
        + ''
        + '\t\t\t\t <span class="clearfix"></span>       '
        + ''
        + '\t\t\t   <div class="children-sign" >儿科</div>       '
        + ''
        + '\t</div>'
        + ''
        +     '<span class="clearfix"></span>'
        + ''
        +     '<div class = \'title rebate-title add-margin-bottom-20\' style = "font-size:16px; margin-top:-50px">{{spotConfig[\'spot_name\']}}</div>'
        +     '<p class="title rebate-title add-margin-bottom-20">护理记录</p>'
        + ''
        +     '<div style="min-height: 850px;" class="print-main-contnet">'
        + ''
        +         '<p class="add-padding-bottom-8 font-5rem small-title-third margin-bottom-0">病历号：{{triageInfo[\'patient_number\']}}</p>'
        + ''
        + '\t\t'
        +         '<div class="fill-info">'
        + ''
        +             '<div class="add-margin-bottom-8">'
        + ''
        +             '<div class="rebate-write-new"><span class="column-name">姓名&nbsp;</span><div class="column-value">{{triageInfo[\'username\']}}</div></div>'
        + ''
        +             '<div class="tc rebate-write-new"><span class="column-name">性别&nbsp;</span><div class="column-value">{{triageInfo[\'receive_sex\']}}</div></div>'
        + ''
        +             '<div class="tr rebate-write-new"><span class="column-name">年龄&nbsp;</span><div class="column-value">{{triageInfo[\'birthday\']}}</div></div>'
        + ''
        +             '</div>'
        + ''
        +             '<div class="add-margin-bottom-8">'
        + ''
        +             '<div class="rebate-write-new"><span class="column-name">出生日期&nbsp;</span><div style="width:57%;" class="column-value">{{triageInfo[\'birth\']}}</div></div>'
        + ''
        +             '<div class="tc rebate-write-new"><span class="column-name">TEL&nbsp;</span><div style="width:71%;" class="column-value">{{triageInfo[\'iphone\']}}</div></div>'
        + ''
        +             '<div class="tr rebate-write-new"><span class="column-name">服务类型&nbsp;</span><div style="width:58%;" class="column-value">{{triageInfo[\'receive_type\']}}</div></div>'
        + ''
        +             '</div>'
        + ''
        +             '<div>'
        + ''
        +             '<div class="rebate-write-new"><span class="column-name">就诊科室&nbsp;</span><div class="column-value">{{basicInfo[\'departmentName\']}}</div></div>'
        + ''
        +             '<div class="tc rebate-write-new"><span class="column-name">门诊号&nbsp;</span><div style="width:63%;" class="column-value">{{triageInfo[\'case_id\']}}</div></div>'
        + ''
        +             '<div class="rebate-write-new"><span class="column-name"></span><div class=""></div></div>'
        + ''
        +             '</div>'
        + ''
        +         '</div>'
        + ''
        + '\t\t'
        + ''
        +       '<div class="add-margin-top-30" style="padding:0px;margin-right:0px;">\t\t '
        + ''
        +             '<div class="rebate-write-new"><span class="column-name">就诊方式：&nbsp;</span><div style="width:53%;" class="column-value no-bottom-border">{{basicInfo[\'treatment\']}}</div></div>'
        + ''
        +             '<div class=""><div class="column-name">疼痛评估：&nbsp;</div><div style="vertical-align: top;"  class="column-value no-bottom-border">{{#basicInfo[\'pain_score\']}}</div></div>'
        + ''
        +             '<div class=""><div class="column-name">跌倒评估：&nbsp;</div><div style="vertical-align: top;" class="column-value no-bottom-border">{{#basicInfo[\'fall_score\']}}</div></div>'
        + ''
        +        '</div>'
        + ''
        + '\t\t'
        + ''
        + '\t\t<div class="add-margin-top-30">'
        + ''
        +             '<p class=\'add-padding-bottom-8 font-5rem small-title-third margin-bottom-0\'>体征测量</p>'
        + ''
        +         '</div>'
        + ''
        + '\t\t<div class="font-clear add-margin-bottom-5">\t\t '
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">身高：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'heightcm\']}}</div></div>'
        + ''
        + '\t\t\t'
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">体重：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'weightkg\']}}</div></div>'
        + ''
        + '\t\t\t '
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">头围：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'head_circumference\']}}</div></div>'
        + ''
        + '\t\t\t'
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">BMI：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'bmi\']}}</div></div>'
        + ''
        +         '</div>'
        + ''
        + '\t\t<div class="font-clear">\t\t '
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">体温：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'temperature\']}}</div></div>'
        + ''
        + '\t\t\t'
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">呼吸：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'breathing\']}}</div></div>'
        + ''
        + '\t\t\t '
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">脉搏：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'pulse\']}}</div></div>'
        + ''
        + '\t\t\t'
        + ''
        + '\t\t\t<div class="total-column"><span class="column-name">血压：&nbsp;</span><div class="column-value no-bottom-border">{{physicalInfo[\'bloodPressure\']}}</div></div>'
        + ''
        +         '</div>'
        + ''
        + '\t\t'
        + ''
        + '\t\t<div class="add-margin-top-30">'
        + ''
        +             '<p class=\'add-padding-bottom font-5rem small-title-third margin-bottom-0\'>评估 / 治疗 / 观察记录</p>'
        + ''
        +         '</div>'
        + ''
        + '\t\t<div>'
        + ''
        + '\t\t\t<table class="table nursinng-print-table">'
        + ''
        + '\t\t\t\t<thead>'
        + ''
        + '\t\t\t\t\t<tr>'
        + ''
        + '\t\t\t\t\t\t<th style="width:20%;">护理项目</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:15%;">执行人</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:20%;">执行时间</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:45%;">执行内容</th>'
        + ''
        + '\t\t\t\t\t</tr>'
        + ''
        + '\t\t\t\t</thead>'
        + ''
        + '\t\t\t\t\t '
        + ''
        + '\t\t\t\t<tbody>'
        + ''
        + '\t\t\t\t\t{{each nursingRecord as value}}'
        + ''
        + '\t\t\t\t\t\t<tr>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:20%;">{{value.name}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:15%;">{{value.executor}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:20%;word-break:normal;">{{value.execute_time}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:45%;">{{value.content}}</td>'
        + ''
        + '\t\t\t\t\t\t</tr>'
        + ''
        + '\t\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t\t</tbody>'
        + ''
        + '\t\t\t</table>'
        + ''
        + '\t\t</div>'
        + ''
        + '\t\t'
        + ''
        + '\t\t<div class="add-margin-top-30">'
        + ''
        +             '<p class=\'add-padding-bottom font-5rem small-title-third margin-bottom-0\'>健康教育</p>'
        + ''
        +         '</div>'
        + ''
        + '\t\t<div>'
        + ''
        + '\t\t\t<table class="table nursinng-print-table">'
        + ''
        + '\t\t\t\t<thead>'
        + ''
        + '\t\t\t\t\t<tr>'
        + ''
        + '\t\t\t\t\t\t<th style="width:30%;">宣教内容</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:14%;">宣教对象</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:18%;">宣教方式</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:18%;">接受障碍</th>'
        + ''
        + '\t\t\t\t\t\t<th style="width:20%;">接受能力</th>'
        + ''
        + '\t\t\t\t\t</tr>'
        + ''
        + '\t\t\t\t</thead>'
        + ''
        + '\t\t\t\t\t '
        + ''
        + '\t\t\t\t<tbody>'
        + ''
        + '\t\t\t\t\t{{each healthEducation as value}}'
        + ''
        + '\t\t\t\t\t\t<tr>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:30%;">{{value.education_content}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:14%;">{{value.education_object}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:18%;">{{value.education_method}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:18%;">{{value.accept_barrier}}</td>'
        + ''
        + '\t\t\t\t\t\t\t<td style="width:20%;">{{value.accept_ability}}</td>'
        + ''
        + '\t\t\t\t\t\t</tr>'
        + ''
        + '\t\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t\t</tbody>'
        + ''
        + '\t\t\t</table>'
        + ''
        + '\t\t</div>'
        + ''
        +     '</div>'
        + ''
        + '\t<div class="fill-info-buttom">     '
        + ''
        +         '<div class="double-underline"></div>      '
        + ''
        +         '<p class="rebate-write fl margin-bottom-0">记录人：</p>'
        + ''
        +         ''
        + ''
        +         '<p class="width-30 fr margin-bottom-0">日期：{{triageInfo[\'diagnosis_time\']}}</p>      '
        + ''
        +         '<span class="clearfix"></span>      '
        + ''
        +         '</div>     '
        + ''
        +         '<span class="clearfix"></span>      '
        + ''
        +     '</div>'
        + ''
        + '</div>';
});