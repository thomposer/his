define(function(require){
    var common = require('js/lib/common');
	var main = {
			init : function(){
                main.bindEvent();
                //main.setTableWidth();
			},
            bindEvent : function(){
            },
        setTableWidth:function(){
            $("#data-report-table-width").width("200%");
            $("#data-report-table-width").css("max-width","200%");
        }
    };
    return main;
});
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
        return r[2];
    return '';
}