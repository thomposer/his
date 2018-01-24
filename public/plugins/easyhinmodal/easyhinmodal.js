define(function(require) {
	 var template = require('template');
	 var easyhinModalTpl = require('tpl/easyhinModal.tpl');
function easyhinModal(instanceOptions){
	var defaults={
		"id" : "myModals",
		"title" : "提示",
		"desc" : "确定操作？",
		"confirmText" : "确定",
		"cancleText" : "取消",
		"extender" : "",
		"confirmCb" : function(){},
		"showCb" : function(){}
	}
	var options = $.extend({}, defaults,instanceOptions),
		t = this,
		$outContent = $("#"+options.id+"Modal"),
		$modal;
	t.show = function (){
		$modal.modal("show");
        $("body").addClass('modal-open-appointment-time-config');
		options.showCb();
	}
	t.hide = function (){
		$modal.modal("hide");
	}
	var render = function(){
		var easyhinModalTplStr = template.compile(easyhinModalTpl)({
			modalid:options.id,
			title:options.title,
			desc:options.desc,
			size : size,
			confirmText:options.confirmText,
			cancleText : options.cancleText,
			extender:options.extender,
			//list: json.data
		});
		if(!$outContent[0]){
			$("<div id='"+options.id+"Modal'></div>").appendTo("body");
			$outContent = $("#"+options.id+"Modal");
		}
			$outContent.html(easyhinModalTplStr);
			$modal = $("#"+options.id);
			
			$modal.find(".btn-confirm").click(function(){
				options.confirmCb();
			});
	}()
	t.thisModal = $modal;
    t.thisModal.on('hidden.bs.modal', function () {
        $("body").removeClass('modal-open-appointment-time-config');
    });
    t.thisModal.css({"overflow-y":"auto"});
}
	return easyhinModal;
});
