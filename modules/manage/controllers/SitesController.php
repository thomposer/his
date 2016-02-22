<?php

namespace app\modules\manage\controllers;

use Yii;
use app\common\base\BaseController;
use yii\db\Query;
use yii\base\ErrorException;
use app\common\Common;
/**
 * 站点通用功能，包括获取站点信息，登出系统
 * @author 张震宇
 *
 */
class SitesController extends BaseController {
	
  
    public function beforeAction($action){
        return true;
    }
    
	/**
	 * 将站点信息存储到session中，并跳转到站点首页
	 * @return Ambigous <string, string>
	 */
	public function actionIndex() {
	    if(!Yii::$app->request->isPost){
	        throw new ErrorException("非法操作");
	    }	    
		$id = Yii::$app->request->post('id');
		$url = Yii::getAlias('@manageIndex');
		
		// 无ID则跳转回站点选择页面
		if (!$id) {
        	$message = '未有任何站点存在！';
        	Common::showInfo($message);
		}
		$userId = $this->userInfo->user_id;
		$bool = $this->manager->checkAccess($userId, Yii::getAlias('@systemPermission'));
		$query = new Query();
		$query->from('gzh_spot as a')->select(['a.spot','a.spot_name']);
		$query->where('a.id = :id',[':id' => $id]);
// 		if(!$bool) {
// 		    $query->addSelect('b.status');
// 		    $query->join('left join', 'gzh_apply_permission_list as b','a.spot = b.spot');
// 		    $query->andWhere('b.user_id = :user_id', [':user_id' => $userId]);
// 		}
		$curSpot = $query->one();
		if(!$bool){
    		//若用户不是超级管理员，并且站点角色被冻结了，则提示用户联系站点管理员进行解封
    		if(!$this->manager->checkAccess($userId,Yii::getAlias('@spotPrefix').$curSpot['spot'])){
    		    Common::showMessage();
    		}
		}
		$session = Yii::$app->session;
		$session->set('spot_id',$id);//站点id
		$session->set('spot', $curSpot['spot']);//站点简称
		$session->set('spot_name', $curSpot['spot_name']);//站点名称
        $session->set('currentSpot','');
        
		return $this->redirect([$url]);
	}
	
	
	
}
