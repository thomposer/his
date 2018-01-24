define(function () {
    return ''
        + '<div class = "{{parentClass}}">'
        + ''
        +     '<div class = \'check-name\'>{{if type==1}}<span title="{{itmTitle}}"  data-toggle="tooltip" data-html="true" data-placement="bottom">{{name}}</span>{{else}}{{name}}{{/if}}</div>'
        + ''
        +     '<div class = \'check-id\'><input type="hidden" class="form-control" name="{{deleted}}" value=""><input type="hidden" class="form-control" name="{{inputName}}" value="{{list}}"></div>'
        + ''
        +     '<div class="op-group" style = "display:block"><img src = "{{baseUrl}}/public/img/common/delete.png"></div>'
        + ''
        + '</div>';
});