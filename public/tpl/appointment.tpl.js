define(function () {
    return ''
        + '\t'
        + ''
        + '\t<div class="search-block J-search-name">'
        + ''
        + '\t\t<div class="tips">请选择患者或继续新增</div>'
        + ''
        + '\t\t\t<ul>'
        + ''
        + ''
        + ''
        + '\t\t\t\t{{each list}}'
        + ''
        + '\t\t\t\t<li class="J-name-search-submit" id="{{$value.id}}">'
        + ''
        + '\t\t\t\t\t<img src="{{if $value.head_img != \'\'}}{{cdnHost}}{{$value.head_img}}{{else}}../../public/img/default.png{{/if}}" onerror="this.src=\'../../public/img/default.png\'" alt="">'
        + ''
        + '\t\t\t\t\t<span>{{$value.username}}</span>'
        + ''
        + '\t\t\t\t\t<span>'
        + ''
        + '\t\t\t\t\t{{if $value.sex == 1}}'
        + ''
        + '\t\t\t\t\t男'
        + ''
        + '\t\t\t\t\t{{else if $value.sex == 2}}'
        + ''
        + '\t\t\t\t\t女'
        + ''
        + '\t\t\t\t\t{{else if $value.sex == 3}}'
        + ''
        + '\t\t\t\t\t不祥'
        + ''
        + '\t\t\t\t\t{{else}}'
        + ''
        + '\t\t\t\t\t其他'
        + ''
        + '\t\t\t\t\t{{/if}}'
        + ''
        + '\t\t\t\t\t</span>'
        + ''
        + '\t\t\t\t\t<!-- <span>{{$value.username}}岁</span> -->'
        + ''
        + '\t\t\t\t\t<span>{{$value.iphone}}</span>'
        + ''
        + '\t\t\t\t</li>'
        + ''
        + '\t\t\t\t{{/each}}'
        + ''
        + '\t\t\t</ul>'
        + ''
        + '\t</div>';
});