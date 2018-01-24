define(function () {
    return ''
        + '<div class="row allergy-line">'
        +   '<div class="col-sm-3 select-allery">'
        +       '<div class="form-group field-allergyoutpatient-type has-success">'
        +           '<select id="allergyoutpatient-type" class="form-control" name="allergyOutpatient[type][]" aria-invalid="false">'
        +               '<option value="">请选择过敏类型</option>'
        +               '<option value="1">药物过敏</option>'
        +               '<option value="2">食物过敏</option>'
        +               '<option value="3">其它过敏</option>'
        +           '</select>'

        +           '<div class="help-block"></div>'
        +       '</div>'
        +   '</div>'
        +   '<div class="col-sm-4 allery-input">'
        +       '<div class="form-group field-allergyoutpatient-allergy_content">'
        +           '<input type="text" id="allergyoutpatient-allergy_content" class="form-control" name="allergyOutpatient[allergy_content][]" maxlength="255" placeholder="请填写引起过敏的食物或者物品的名称">'
        +           '<div class="help-block"></div>'
        +       '</div>'
        +   '</div>'
        +   '<div class="col-sm-3" style = "width:22%;padding-left:0px;padding-right:0;margin-top: 5px;">'
        +       '<div class="form-group field-allergyoutpatient-allergy_degree">'
        +           '<input type="hidden" name="allergyOutpatient[allergy_degree][{{key}}]" value="">'
        +           '<div id="allergyoutpatient-allergy_degree">'
        +               '<label><input type="radio" name="allergyOutpatient[allergy_degree][{{key}}]" value="1"> 确认过敏 </label>'
        +               '<label><input type="radio" name="allergyOutpatient[allergy_degree][{{key}}]" value="2"> 疑似过敏 </label>'
        +           '</div>'
        +           '<div class="help-block"></div>'
        +       '</div>'
        +   '</div>'
        +   '<div class="col-sm-2">'
        +       '<a href="javascript:void(0);" class="btn-from-delete-add btn allergy-delete" style="display: inline-block;">'
        +           '<i class="fa fa-minus"></i>'
        +       '</a>'
        +       '<a href="javascript:void(0);" class="btn-from-delete-add btn allergy-add" style="display: inline-block;" data-key="{{key}}">'
        +           '<i class="fa fa-plus"></i>'
        +       '</a>'
        +   '</div>'
        +'</div>';
});