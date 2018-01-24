define(function(require){
    var common = require('js/lib/common');
    var _self;
    var main={
        init:function(){
            _self = this;
            this.defaultTotal();//默认总成本价
            this.calculate(); //显示总成本价
        },
        /*
         默认成本价
         */
        defaultTotal: function () {
            var EleTbody=$('.stock-info').children("tbody");
            var EleList=EleTbody.children("tr");
            _self.printTotal(EleList)
        },
        /*
         成本合计
         */
        calculate:function(){
            $('body').on('focusout','.stock-inbound-focusout',function(){
                var EleParent=$(this).parent().parent().parent();
                var EleList=EleParent.children("tr");
                _self.printTotal(EleList);
            });
        },
        printTotal : function(EleList){
            var flag=1;//用来判断是否全部输入
            var total_price=0;//总价格
            var ifLength=EleList.length; //实际长度（有可能叉掉）
            if(!view) {
                for (var i = 0; i < EleList.length; i++) {
                    var tdArr = EleList.eq(i).find('td');
                    var total_num = tdArr.find('.total_num-focusout').val();
                    var default_price = tdArr.find('.default_price-focusout').val();
                    var delete_value=tdArr.find('.stock-inbound-delete').val();
                    if(delete_value){
                        ifLength=ifLength-1;
                        continue;
                    }
                    if (!total_num || !default_price) {
                        flag = 0;
                        break;
                    }

                    total_price = add(total_price,mul(total_num ,default_price));
                }
            }else{
                for (var i = 0; i < EleList.length; i++) {
                    var tdArr = EleList.eq(i).find('td');
                    var total_num = tdArr.siblings('.total_num-focusout').html();
                    var default_price = tdArr.siblings('.default_price-focusout').html();
                    default_price=toDecimal2(parseFloat(default_price.substring(1,default_price.length)));
                    if (!total_num || !default_price) {
                        flag = 0;
                        break;
                    }
                    total_price = add(total_price,mul(total_num ,default_price));
                }

            }
            total_price=toDecimal2(total_price);
            if(flag&&ifLength&&total_price!='NaN'){
                $('.stock-total-pre-num').html(total_price);
            }else{
                $('.stock-total-pre-num').html('');
            }

        },
    };

    return main;
})