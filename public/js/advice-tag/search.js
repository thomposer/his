

define(function (require) {
    var _self;
    var main = {
        init: function () {
            _self = this;
            this.bindEvent();
        },
        bindEvent: function () {
            //点击保存时，回填搜索内容
            $('body').off('click', '.advice-tag-search-button').on('click', '.advice-tag-search-button', function () {
                var discountTagCheckedVal =[]; 
                var discountTagCheckedText = [];
                $('input[name="AdviceTagRelation[discountTag][]"]:checked').each(function(){ 
                    discountTagCheckedVal.push($(this).val()); 
                    discountTagCheckedText.push($(this).parent('label').text());
                    
                }); 
                var commonTagCheckedVal =[]; 
                var commonTagCheckedText = [];
                $('input[name="AdviceTagRelation[commonTag][]"]:checked').each(function(){ 
                    commonTagCheckedVal.push($(this).val());
                    commonTagCheckedText.push($(this).parent('label').text()); 
                    
                }); 
                $('#commonTagChecked').val(commonTagCheckedVal);
                $('#discountTagChecked').val(discountTagCheckedVal);
                if(discountTagCheckedText.length != 0 && commonTagCheckedText.length != 0){
                    $('#search-advicetagid').val(discountTagCheckedText+','+commonTagCheckedText);
                }else{
                    $('#search-advicetagid').val(discountTagCheckedText+commonTagCheckedText);
                }
                $('#apiTagSearchUrl').attr({'href':apiTagSearchUrl+'?discountTagChecked='+discountTagCheckedVal+'&commonTagChecked='+commonTagCheckedVal});
                modal.hide();
            });


        },

    };
    return main;
})