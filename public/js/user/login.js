/**
 * 
 */

define(function(require){
	var icheck = require('plugins/iCheck/icheck.min');
	var template = require('public/js/lib/template');
	var explorerNoticeTpl = require('public/tpl/explorerNotice.tpl');
	var explorerBetaNoticeTpl = require('public/tpl/explorerBetaNotice.tpl');
	var main = {
			init : function(){
				this.bindEvent();
				this.explorerNotice();
			},
			bindEvent : function(){
				 $('input:checkbox').iCheck({
				      checkboxClass: 'icheckbox_flat-blue',
				     
				      increaseArea: '20%' // optional
				    });
				 
				 main.loginFocus();
				 
				 if(csrf == ''){
					 location.reload(); 
				 }
			},
			explorerNotice: function(){
                if(window.location.host == 'beta.his.easyhin.com'){
                    var explorerBetaNotice = template.compile(explorerBetaNoticeTpl)({
                    });
                    $('body').prepend(explorerBetaNotice);
                }
				if(window.navigator.userAgent.indexOf("Chrome") == -1 || window.navigator.userAgent.indexOf("Edge") !== -1 || (window.navigator.userProfile+'')=='null' ||  window.navigator.userAgent.indexOf("QQBrowser") !== -1){
					if (window.navigator.userAgent.indexOf("Window") > -1) {
						var downloadUrl = "http://s.easyhin.com/tools/WindowChromeStandalone_55.0.2883.87_Setup.exe";

					} else {
						var downloadUrl = "http://s.easyhin.com/tools/MacGoogleChrome.dmg";

					}
					var explorerNotice = template.compile(explorerNoticeTpl)({
						baseUrl: baseUrl,
						downloadUrl: downloadUrl
					});
					$('body').prepend(explorerNotice);

				}


			},
			loginFocus : function(){
				$('#loginform-email').focus(function(){
				  $(".icon-user").css('background-image', 'url(../../public/img/common/icon_username_press.png)');
				});
				$('#loginform-email').blur(function(){
				  $(".icon-user").css('background-image', 'url(../../public/img/common/icon_username.png)');
				});
				$('#loginform-password').focus(function(){
				  $(".icon-pwd").css('background-image', 'url(../../public/img/common/icon_password_press.png)');
				});
				$('#loginform-password').blur(function(){
				  $(".icon-pwd").css('background-image', 'url(../../public/img/common/icon_password_default.png)');
				});
				$('#loginform-spot').focus(function(){
				  $(".icon-adr").css('background-image', 'url(../../public/img/common/icon_mechanism_press.png)');
				});
				$('#loginform-spot').blur(function(){
				  $(".icon-adr").css('background-image', 'url(../../public/img/common/icon_password.png)');
				});
			}
	};
	
	return main;
})