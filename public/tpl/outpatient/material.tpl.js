define(function () {
    return ''
        + '<tr>'
        + ''
        + '\t<input type="hidden" class = "materialId" name="MaterialRecord[material_id][]" value="{{id}}">'
        + ''
        + '\t<td>{{list.name}}</td>'
        + ''
        + '\t<td>{{list.specification}}</td>'
        + ''
        + '\t<td>{{list.price}}</td>'
        + ''
        + '\t\t<td><input type="text" class="form-control" name="MaterialRecord[num][]" value=""></td>'
        + ''
        + '\t<td>{{list.unit}}</td>'
        + ''
        + '\t\t<td><input type="text" class="form-control" name="MaterialRecord[remark][]" value="{{list.remark}}"></td>'
        + ''
        + '\t<td>{{if list.attribute == 2}}{{totalNum}}{{else}}--{{/if}}</td>'
        + ''
        + '\t<td class="op-group" style="display:table-cell"><input type="hidden" class="form-control" name="MaterialRecord[deleted][]" value=""><img src = "{{baseUrl}}/public/img/common/delete.png"></td>'
        + ''
        + '</tr>';
});