define(function () {
    return ''
            + '{{each list}}'
            + '<div class="print-specimen">'
            + '<div class="print-label-left"> '
            + '<div class="print-specimen-text"><span class="ver-bottom specimen-t">{{$value.username}} {{$value.sex}} {{$value.birthday}} {{$value.departmentName}}</span></div>'
            + '<div><img barcode={{$value.specimen_number}} class="print-specimen-barcode barcode" id="barcode"/>'
            + '<span class="print-specimen-color text-ver">{{if $value.cuvette}}{{$value.cuvette}}{{else}}无{{/if}}</span>'
            + '</div>'
            + '<div class="print-specimen-hr"></div>'
            + '<div class="print-specimen-item"><span class="specimen-t">{{$value.inspectName}}</span></div>'
//                a += '<div class="print-specimen-item"><span class="specimen-t">大便常规培养+药敏(Stool routine culture and drug susceptib </span></div>';
            + '</div>'
            + '<div class="print-label-right">'
            + '<div class="print-specimen-type">{{if $value.specimen_type}}{{$value.specimen_type}}{{else}}　{{/if}}</div>'
            + '<div class="print-specimen-num-date">{{$value.inspect_in_time}}</div>'
            + '<div class="print-specimen-num">{{$value.specimen_number}}</div>'
            + '<div class="specimen-clearfix"></div>'
            + '</div>'
            + '</div>'
            + '{{/each}}';
});