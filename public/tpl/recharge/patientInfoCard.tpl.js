define(function () {
    return ''
        + '\t'
        + ''
        + '\t<div class="search-block J-search-name">'
        + ''
        + '\t\t<div class="tips">请选择</div>'
        + ''
        + '\t\t\t<ul>'
        + ''
        + ''
        + ''
        + '\t\t\t\t{{each list}}'
        + ''
        + '\t\t\t\t<li class="J-name-search-submit membership-card-li" data-patientId = "{{$value.id}}" data-iphone="{{$value.iphone}}" data-username="{{$value.username}}" data-birthday="{{$value.birthday}}" data-sex="{{$value.sexText}}"  data-name="{{$value.name}}" >'
        + ''
        + '\t\t\t\t\t<img src="{{if $value.head_img != \'\'}}{{cdnHost}}{{$value.head_img}}{{else}}{{baseUrl}}/public/img/default.png{{/if}}" onerror="this.src=\'{{baseUrl}}/public/img/default.png\'" alt="">'
        + ''
        + '\t\t\t\t\t<div class="J-search-info">'
        + ''
        + '\t\t\t\t\t\t\t<span>{{$value.username}}（{{$value.birthday}} {{$value.sexText}}）</span>'
        + '\t\t\t\t\t\t\t<div></div>'

        + ''
        + '\t\t\t\t\t\t\t<span>{{$value.iphone}}</span>'
        + ''
        + '\t\t\t\t\t\t\t<div style="clear: both"></div>'
        + ''
        + '\t\t\t\t\t</div>'
        + ''
        + '\t\t\t\t</li>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</ul>'
        + ''
        + '\t</div>';
});