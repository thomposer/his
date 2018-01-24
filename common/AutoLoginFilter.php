<?php

namespace app\common;

use Yii;
use yii\base\ActionFilter;
use app\lib\tof\OABridge;
use app\modules\user\models\MultiUser;

/**
 * 登录过滤器，实现OA登录逻辑
 * @author zesonliu
 *
 */
class AutoLoginFilter extends ActionFilter {

	/**
	 * (non-PHPdoc)
	 * @see \yii\base\ActionFilter::beforeAction()
	 */
	public function beforeAction($action) {
		
		$loginSuccess = true;
		
		// 如果用户为访客，先判断是否为OA登录
		if (Yii::$app->user->isGuest) {
		     
 			if ($this->isLogin()) {
				$loginSuccess = $this->tryLogin();
			} elseif ($this->isQQLogin()) {
				$loginSuccess = $this->tryQQLogin();
			} else {
				$this->redirectLogin();
				$loginSuccess = false;
			}
		}
		
		return $loginSuccess;
	}
	
	/**
	 * @return boolean
	 */
	private function isLogin() {
	    
		//if (!empty($_GET['ticket']) || !empty($_COOKIE['TCOA_TICKET'])) {
	       return true;
	}
	
	/**
	 * 只要用户带上ticket则尝试用ticket来进行登录
	 * @return boolean
	 */
	private function tryLogin() {
		$oaBridge = new OABridge();
		
		$response = $oaBridge->getUserInfo();
		if ($response) {
			// session时间为一个月
			Yii::$app->user->login(MultiUser::getUserByOA($response->Data), 3600*24*30);
			return true;
		} else {
			// 获取用户的信息失败
			$this->redirectLogin();
			return false;
		}
	}

	private function isQQLogin() {
	    if(strpos(Yii::$app->request->serverName, Yii::getAlias('@GzhossQQ'))){
	        return true;
	    }
		return false;
	}
		
	private function tryQQLogin() {
	    $uin = isset($_COOKIE['uin'])?$_COOKIE['uin']:null;
	    $p_uin = isset($_COOKIE['p_uin'])?$_COOKIE['p_uin']:null;
	    if($uin != null || $p_uin != null){
	       $qq = ltrim($uin?$uin:$p_uin,'o0');	       	       
	       Yii::$app->user->login(MultiUser::getUserByQQ($qq),3600*24);
	       return true;
	    }
		 $this->redirectLogin();
		 return false;
	}
	
	/**
	 * 默认重定向到oa登录页面
	 */
	private function redirectLogin() {
		$request = Yii::$app->getRequest();
		$response = Yii::$app->getResponse();
		
		$redirectUrl = urlencode($request->absoluteUrl);
		
 		if ($this->isQQLogin()) {
			$response->redirect(['/user/login/index','url' => $redirectUrl]);
		} else {
			$response->redirect(OABridge::$OA_LOGIN_URL . '?url=' . $redirectUrl);
		}
	}
	
	
}
