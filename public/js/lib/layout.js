/**
 * 
 */

define(function(require){
	var common = require('js/lib/jquery.cookie');
	var template = require('template');
	var explorerNoticeTpl = require('tpl/explorerNotice.tpl');
  var explorerBetaNoticeTpl = require('public/tpl/explorerBetaNotice.tpl');
  var _self;
  var minHeight;
	var main = {
			addCssFlag : 0,
			init : function(){
				_self = this;
				this.bindEvent();
				this.initLayoutSideBar();
			},
			bindEvent : function(){
				// var addCssFlag = 0;//添加css的标志
        if(window.location.host == 'beta.his.easyhin.com'){
            var explorerBetaNotice = template.compile(explorerBetaNoticeTpl)({
            });
            this.addCssFlag++;
            $('.main-header').prepend(explorerBetaNotice);
        }
				if(window.navigator.userAgent.indexOf("Chrome") == -1 || window.navigator.userAgent.indexOf("Edge") !== -1 || (window.navigator.userProfile+'')=='null' ||  window.navigator.userAgent.indexOf("QQBrowser") !== -1){
					if (window.navigator.userAgent.indexOf("Window") > -1) {
						var downloadUrl = "http://s.easyhin.com/tools/WindowChromeStandalone_55.0.2883.87_Setup.exe";
					} else {
						var downloadUrl = "http://s.easyhin.com/tools/MacGoogleChrome.dmg";
					}
					var explorerNotice = template.compile(explorerNoticeTpl)({
						baseUrl : baseUrl,
						downloadUrl : downloadUrl
					});
					this.addCssFlag++;
					$('.main-header').prepend(explorerNotice);
				}
				// 样式修改
				if(this.addCssFlag > 0){
					$('.main-sidebar').css('padding-top',this.addCssFlag*40+50+'px');
					$('.content-wrapper .content').css('padding-top',this.addCssFlag*40+20+'px');
				}
				//this.editHeight();
				$('.main-sidebar li.dropdown-submenu ul').addClass('menu');
				$('.main-sidebar li.treeview a').each(function(){
					var url = $(this).attr('href');
					if(url.indexOf(requestModuleController) === 0){
						$(this).parent('li.treeview').addClass('active');
						$(this).children('i').css('background-position-y','95%');
						$(this).parent('li.treeview').children('a').children('i').after('<i class=" circle_sanjiao"></i>');
					}
				});
				$('.main-sidebar .treeview-menu li a').each(function(){
					var url = $(this).attr('href');
					if(url.indexOf(requestModuleController) === 0){
						$(this).parent('li').addClass('active');
						$(this).parent('li').closest('li.treeview').addClass('active');
						$(this).parents('li.treeview').children('a').children('i').css('background-position-y','95%');
						$(this).children('i').removeClass('fa-circle-o');
						$(this).children('i').addClass('fa-circle');
						$(this).parent('li').children('a').children('i').after('<i class=" circle_sanjiao"></i>');

					}
				});

				$('.main-sidebar .sidebar-menu .treeview').on('click',function(){
					$('.icon_url').css('background-position-y','0');
					$(this).children().children('i').css('background-position-y','95%');
					//main.editHeight();
				});
				var msg = errorMsg?errorMsg:successMsg;
				if(msg){
					main.showInfo(msg,'200px');
				}
//				$('.check_radio').on('click',function(){
//					var spot_name = $(this).attr('spot_name');
//					main.showInfo('已将'+spot_name+'设置为默认诊所','300px');
//				});
                $('.switch-spot').on('change',function(){
                                    
                    var defaultId = $(this).data('default-id');
                    var currentId = $(this).val();
                    $("[name=defaultSpot]:checkbox").prop("checked", currentId == defaultId);
                });
                                
				$('.btn-spot').on('click',function(){
					var id = $('.switch-spot').val();
					var isDefault = $('[name=defaultSpot]:checked').val();
                    var defaultId = $('.switch-spot').data('default-id');
					var csrfToken = $('meta[name="csrf-token"]').attr("content");
                    var defaultSpot = 0;
                    if(isDefault){
                        defaultSpot = id;
                    }else if( typeof(isDefault) == 'undefined' && id != defaultId ){
                        defaultSpot = defaultId;
                    }else{
                        defaultSpot = 0;
                    }
					$.post(
						selectSpotUrl,
						{
							'id' : id,
							'default_spot' : defaultSpot,
							'_csrf' : csrfToken,
						}
					);
				});
				$('.sidebar-status').on('click',function(){
					var sidebar = $('body').attr('class');
					var sidebarType;
					if(sidebar == 'skin-blue sidebar-mini sidebar-collapse'){
						sidebar = 'skin-blue sidebar-mini';
						sidebarType = 2;
						_self.initScroll();
					}else{
						sidebar = 'skin-blue sidebar-mini sidebar-collapse';
						sidebarType = 1;
						_self.destroyScroll();
					}
					$.cookie('sidebar',sidebar,{ expires: 365, path: '/'});
					$.cookie('sidebarType',sidebarType,{ expires: 365, path: '/'});
				});
			this.pjaxAjaxTab();
			this.consoleForm();
			this.clickInspectModal();
			},
			showInfo : function(msg,width){
				   var style = 'background: rgba(74, 74, 74, 0.8); border-radius: 4px; padding: 1.9rem; font-size: 16px;min-height:60px;width: 13.5rem; position: absolute; z-index: 11000; left: 50%; margin-left: -15.75rem; margin-top: 0; top: 50%; text-align: center; color: #fff;';
			        width?style = style + "width:"+width:"";
			        var html = '<div class="flex j_center" id="show_tips_box" style="'+style+'">';
			        html += msg;
			        html += '</div>';
			        $("body").append(html);
			        window.setTimeout(function () {
			            $('#show_tips_box').remove();
			        }, 1500);
			},
			editHeight : function(){
				/* 可视区域高度控制 */
				var bodyHeight = document.body.clientHeight;
				if(parseInt(bodyHeight) > 1500){
					$('.content-wrapper').css('min-height',bodyHeight+'px');
				}else{
					$('.content-wrapper').css('min-height','1500px');
				}
			},

			pjaxAjaxTab : function(){
				// $(document).on('ready pjax:end', function(event) {
				// 				event.preventDefault();
				// 				alert(111);
				//     			$.pjax.reload({
    //                                 container:'#crud-datatable-pjax',
    //                                 url: window.location.href,
    //                             });
				// });

				  // $(document).on('click', 'a', function(event) {
				  	   
					 //   var pjax = $(this).data('pjax');
					 //   var url = $(this).attr('href');	

					 //   	if(pjax != "0"){
					 //   		event.preventDefault();
					 //   		$.pjax.reload({
      //                               container:'#crud-datatable-pjax',
      //                               url: url,
      //                           });

					 //   }
					 
					 // })
			},

			consoleForm:function(){
				jsonFormInit = $("form").serialize();
				$('body').on('click', '.second-cancel', function (event) {
					event.preventDefault();   
					var cancel_option = {
							label: "否",
							className: 'btn-default  btn-form',
						};
					var confirm_option = {
							label: "是",
							className: 'btn-cancel btn-form',
					};
					btns = {
							confirm: confirm_option,
							cancel: cancel_option,
					}
					var url = $(this).attr('href');
					var jsonFormCurr = $("form").serialize();
					if (typeof(url) == "undefined"){
						return false;
					}
					if (jsonFormCurr != jsonFormInit) {
							bootbox.confirm(
									{
										message: '是否放弃编辑?',
										title: '系统提示',
										buttons: btns,
										callback: function (confirmed) {
											if (confirmed) {
												window.location.href=url;
											} else {
												return true;
											}
										}
									}
								);
					} else {
						window.location.href=url;
					}
				});

				$('body').on('click', '.right-cancel-confirmed', function (event) {
					event.preventDefault();
					var url = $(this).attr('href');
					var jsonFormCurr = $("form").serialize();
					if (typeof(url) == "undefined"){
						return false;
					}
					
					var cancel_option = {
							label: "否",
							className: 'btn-default  btn-form',
						};
					var confirm_option = {
							label: "是",
							className: 'btn-cancel btn-form',
					};
					btns = {
							cancel: cancel_option,
							confirm: confirm_option,
					}
					if (jsonFormCurr != jsonFormInit) {
						bootbox.confirm(
								{
									message: '是否放弃编辑?',
									title: '系统提示',
									buttons: btns,
									callback: function (confirmed) {
										if (confirmed) {
											window.location.href=url;
										} else {
											return true;
										}
									}
								}
						);
					} else {
						window.location.href=url;
					}
				});

				/*window.history.pushState('',null, '');
				if (window.history && window.history.pushState) {
					$(window).on('popstate', function () {
						var url = $('.second-cancel').attr('href');
						var jsonFormCurr = $("form").serialize();
						if (typeof(url) == "undefined"){
							return false;
						}
						if (jsonFormCurr != jsonFormInit) {
							bootbox.confirm(
									{
										message: '是否放弃编辑?',
										title: '系统提示',
										buttons: btns,
										callback: function (confirmed) {
											if (confirmed) {
												window.location = url;
											} else {
												window.history.pushState('#', '123', './create.html');
												return true;
											}
										}
									}
							);
						}else{
							window.location = url;
						}
					});
				}*/

			},
			
			clickInspectModal : function(){
				if(doctorWarningCount > 0){
					$('#inspect-warn-global').click();
				}
			},
			// 初始化Scroll滚动条以及一些相关初始化处理
			initScroll:function(){
				var sidebar = document.getElementById('sidebar');
				var firefox = navigator.userAgent.indexOf('Firefox') != -1;
		    function MouseWheel(e) {
		        e = e || window.event;
		        if (e.preventDefault) e.preventDefault();
		        else e.returnValue = false;
		    }
		    function addBan(){
		    	firefox ? sidebar.addEventListener('DOMMouseScroll', MouseWheel, false) : (sidebar.onmousewheel = MouseWheel);
		    }
		    function removeBan(){
		    	firefox ? sidebar.removeEventListener('DOMMouseScroll', MouseWheel) : (sidebar.onmousewheel = null);
		    }
		    	main.fixedSideBar();
				//初始化slimScroll插件
				function setScroll(){
					scrollHeight = $(window).height()-(main.addCssFlag*40+50);
					var sidebarTop = sessionStorage.getItem('sidebarTop');
					// 注明：此处用了调用了两次slimScroll(暂行办法，待优化）
					$("#sidebar").slimScroll({
						height: scrollHeight,
						allowPageScroll: false,
						alwaysVisible: true
					});
					$("#sidebar").slimScroll({scrollBy: sidebarTop});
				}
				// setScroll();
				$('#sidebar').ready(function(){
					setScroll();
				});
				if(sidebar.clientHeight == sidebar.scrollHeight){//当不存在滚动条时 禁止滚轮事件
					addBan();
				}

				$(window).on("resize",function(){
					setScroll();
					if(sidebar.clientHeight == sidebar.scrollHeight){//当不存在滚动条时
						addBan();
					}
					if(sidebar.clientHeight < sidebar.scrollHeight){//存在滚动条 移除禁止事件
						removeBan();
					}
				});

				$('.wrapper').addClass('fixed'); //加上fixed固定布局
				$('.content-wrapper')[0].style.minHeight = minHeight + 'px'; // 恢复原来的min-height
			},
			// 销毁scroll滚动效果以及一些样式的处理
			destroyScroll : function(){
				var sidebar = document.getElementById('sidebar');
				var firefox = navigator.userAgent.indexOf('Firefox') != -1;
				$(window).off("resize"); // 1-移除resize事件
				$("#sidebar").slimScroll({destroy:true}); // 2-销毁slimScroll
				firefox ? sidebar.removeEventListener('DOMMouseScroll', function(e){ //3-移除禁止事件
						e = e || window.event;
		        if (e.preventDefault) e.preventDefault();
		        else e.returnValue = false;
				}) : (sidebar.onmousewheel = null);
				$('.wrapper').removeClass('fixed'); // 4-移除固定布局
				$('.sidebar').removeAttr('style'); // 5-移除slimScroll添加的样式

				// 6-处理二级下拉列表显示不全的问题(根据二级列表的最大列高度来加高content-wrapper的min-hegith)
				var treeMenus = $('#sidebar .sidebar-menu .treeview .treeview-menu');
				var eleCount = 0;
				treeMenus.each(function(){
					if(this.childElementCount > eleCount){
						eleCount = this.childElementCount;
					}
				});
				$('.content-wrapper')[0].style.minHeight = minHeight + eleCount*40 + 'px';
			},
			// 左侧导航固定 不跳回顶部
			fixedSideBar : function(){
				var sidebarTop = 0;
				var sideMenu;
				$(window).unload(function(){//页面卸载时调用
					sideMenu = $('#sidebar .sidebar-menu');
					sidebarTop = Math.abs(sideMenu.position().top);
					sessionStorage.setItem('sidebarTop',sidebarTop);
				});
			},
			// 初始化左侧边栏布局
			initLayoutSideBar:function(){
				minHeight = Number($('.content-wrapper')[0].style.minHeight.replace('px',''));
				var sidebarType = $.cookie('sidebarType') || 2; //默认为展开状态
				if(sidebarType == 1){ //当左侧导航是折叠状态时
					_self.destroyScroll();
				}
				if(sidebarType == 2){ //展开状态时
					_self.initScroll();
				}
			}
	};
	
	return main;
})