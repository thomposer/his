define(function () {
    return ''
        + '<div class="row first-check-line">'
        + ''
        +   '<div class="col-sm-2 first-check-type">'
        + ''
        +     '<div class="form-group field-firstcheck-check_code_type">'
        + ''
        +       '<select id="firstcheck-check_code_type" class="first-check-left select-first-check form-control"'
        + ''
        +       'name="FirstCheck[check_code_type][]">'
        + ''
        +         '<option value="1" selected="">'
        + ''
        +           'ICD-10'
        + ''
        +         '</option>'
        + ''
        +         '<option value="2">'
        + ''
        +           '自定义'
        + ''
        +         '</option>'
        + ''
        +       '</select>'
        + ''
        +       '<div class="help-block">'
        + ''
        +       '</div>'
        + ''
        +     '</div>'
        + ''
        +   '</div>'
        + ''
        +   '<div class="col-sm-6 first-check-text">'
        + ''
        +     '<div class="first-check-right form-group">'
        + ''
        +       '<select class="CheckCodeSel select2-hidden-accessible" name="FirstCheck[check_code_id][]"'
        + ''
        +       'tabindex="-1" aria-hidden="true">'
        + ''
        +         '<option value="0">'
        + ''
        +           '请输入名称、拼音码或ICD编码进行搜索'
        + ''
        +         '</option>'
        + ''
        +       '</select>'
        + ''
//        +       '<span class="select2 select2-container select2-container--default CheckCodeSel2"'
//        + ''
//        +       'dir="ltr" style="width: 100%;">'
//        + ''
//        +         '<span class="selection">'
//        + ''
//        +           '<span class="select2-selection select2-selection--single" role="combobox"'
//        + ''
//        +           'aria-autocomplete="list" aria-haspopup="true" aria-expanded="false" tabindex="0"'
//        + ''
//        +           'aria-labelledby="select2-FirstCheck[check_code_id][]-nv-container">'
//        + ''
//        +             '<span class="select2-selection__rendered" id="select2-FirstCheck[check_code_id][]-nv-container"'
//        + ''
//        +             'title="请选择">'
//        + ''
//        +               '请选择'
//        + ''
//        +             '</span>'
//        + ''
//        +             '<span class="select2-selection__arrow" role="presentation">'
//        + ''
//        +               '<b role="presentation">'
//        + ''
//        +               '</b>'
//        + ''
//        +             '</span>'
//        + ''
//        +           '</span>'
//        + ''
//        +         '</span>'
//        + ''
//        +         '<span class="dropdown-wrapper" aria-hidden="true">'
//        + ''
//        +         '</span>'
//        + ''
//        +       '</span>'
        + ''
        +       '<div class="form-group field-firstcheck-content">'
        + ''
        +         '<input type="text" id="firstcheck-content" class="first-check-custom hide form-control"'
        + ''
        +         'name="FirstCheck[content][]" maxlength="30" placeholder="请输入">'
        + ''
        +         '<div class="help-block">'
        + ''
        +         '</div>'
        + ''
        +       '</div>'
        + '<div class="help-block"></div>'
        + ''
        +     '</div>'
        + ''
        +   '</div>'
        + ''
        +   '<div class="col-sm-2" style = "width: 140px;margin-top: 5px;">'
        + ''
        +     '<div class="form-group field-firstcheck-check_degree">'
        + ''
        +       '<input type="hidden" name="FirstCheck[check_degree][{{key}}]" value="">'
        + ''
        +       '<div id="firstcheck-check_degree" name="FirstCheck[check_degree][{{key}}]">'
        + ''
        +         '<label>'
        + ''
        +           '<input style="margin-right:3px;" type="radio" name="FirstCheck[check_degree][{{key}}]" value="1" checked>'
        + ''
        +           '确诊'
        + ''
        +         '</label>'
        + ''
        +         '<label>'
        + ''
        +           '<input style="margin-left:6px;margin-right: 3px" type="radio" name="FirstCheck[check_degree][{{key}}]" value="2">'
        + ''
        +           '疑诊'
        + ''
        +         '</label>'
        + ''
        +       '</div>'
        + ''
        +       '<div class="help-block">'
        + ''
        +       '</div>'
        + ''
        +     '</div>'
        + ''
        +   '</div>'
        + ''
        +   '<div class="col-sm-2 first-check-line-button" >'
        + ''
        +     '<a href="javascript:void(0);" class="btn-from-delete-add btn first-check-delete margin-top-0"'
        + ''
        +     'style="display: inline-block;">'
        + ''
        +       '<i class="fa fa-minus">'
        + ''
        +       '</i>'
        + ''
        +     '</a>'
        + ''
        +     '<a href="javascript:void(0);" class="btn-from-delete-add btn first-check-add margin-top-0"'
        + ''
        +     'style="display: inline-block;" data-key="{{key}}">'
        + ''
        +       '<i class="fa fa-plus">'
        + ''
        +       '</i>'
        + ''
        +     '</a>'
        + ''
        +   '</div>'
        + ''
        + '</div>';
});