define(function () {
    return ''
        + '<tr data-key="1">'
        + ''
        +     '<td class="items-select-id">'
        + ''
        +     '</td>'
        + ''
        +     '<td class="select-items">'
        + ''
        +         '<select class="form-control select2 item_list" name="item_ist">'
        + ''
        +             '<option value="0" >请输入项目名称</option>'
        + ''
        +             '{{each list}}'
        + ''
        +             '<option value="{{$value.id}}" >{{$value.id}}-{{$value.item_name}}</option>'
        + ''
        +             '{{/each}}'
        + ''
        +         '</select>                        '
        + ''
        +         '<input type="hidden" class="checkitemid" value="" name="item_id[]">'
        +         '<input type="hidden" class="deleted" value=2 name="deleted[]" >'
        +         '<input type="hidden" class="new-record" value=1 name="newRecord[]">'
        +         '<input type="hidden"  name="unionId[]">'
        + ''
        +     '</td>'
        + ''
        +     '<td class="item-english_name"></td>'
        + ''
        +     '<td class="item-unit"></td>'
        + ''
        +     '<td class="item-ref"></td>'
        + ''
        +     '<td>                                        '
        + ''
        +         '<div class="form-group">'
        + ''
        +             '<a href="javascript:void(0);" class="btn-from-delete-add btn clinic-delete">'
        + ''
        +                 '<i class="fa fa-minus"></i>'
        + ''
        +             '</a>'
        + ''
        +             '<a href="javascript:void(0);" class="btn-from-delete-add btn clinic-add" style="display: inline-block;">'
        + ''
        +                 '<i class="fa fa-plus"></i>'
        + ''
        +             '</a>'
        + ''
        +         '</div>'
        + ''
        +     '</td>'
        + ''
        + '</tr>';
});