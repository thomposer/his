<?php

namespace app\modules\manage\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\modules\spot\models\Spot;
use yii\db\Query;
use app\modules\user\models\UserSpot;
use yii\helpers\Url;
use app\common\Common;
/**
 * 公众号管理平台入口，提供站点选择
 * @author zesonliu
 *
 */
class DefaultController extends Controller
{
    public $manager;
    public function init(){
        parent::init();
        $this->manager = \yii::$app->authManager;
        
    }
    public function behaviors() {
	  
		return [

			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					// 允许认证用户
					[
						'allow' => true,
						'roles' => ['@'],
					],
					// 默认禁止其他用户
				],
			],
		];
	}
    
    public function actionIndex()
    {
        $data['spot'] = '';
        $where = '';
        $list = '';
        $spotList = '';
        $cache = Yii::$app->cache;
        $userInfo = Yii::$app->user->identity;
        $userId = $userInfo->id;
        if(isset($_COOKIE['spotId'])){
            return $this->redirect(Url::to(['@manageIndex']));
        }
        //获取该机构下用户所属所有诊所列表，并缓存
        $parentSpotId = $_COOKIE['parentSpotId'];
        if(!$parentSpotId){
            Common::logout();
            return $this->redirect(Url::to(['@userIndexLogin']));
        }
        $list = (new Spot())->getCacheSpotList();
        $spotInfo = Spot::find()->select(['id','spot','spot_name','icon_url'])->where(['id' => $parentSpotId])->asArray()->one();
        if(!$list){
            //若没有机构没有诊所，则直接进入机构
            $expireTime = Yii::getAlias('@loginSessionExpireTime');
            if($_COOKIE['rememberMe'] == 1){
                $expireTime = Yii::getAlias('@loginCookieExpireTime');
            }
            setcookie('spotId',$parentSpotId,time()+$expireTime,'/',null,null);//诊所id
            $cacheSuffix = $parentSpotId.$userInfo->id;
            
            $cache->set(Yii::getAlias('@parentSpotCode').$cacheSuffix,$spotInfo['spot'],$expireTime);//机构代码
            $cache->set(Yii::getAlias('@parentSpotName').$cacheSuffix,$spotInfo['spot_name'],$expireTime);//机构名称
            
            $cache->set(Yii::getAlias('@spot').$cacheSuffix,$spotInfo['spot'],$expireTime);//诊所代码
            $cache->set(Yii::getAlias('@spotName').$cacheSuffix,$spotInfo['spot_name'],$expireTime);//诊所名称
            $cache->set(Yii::getAlias('@spotIcon').$cacheSuffix, $spotInfo['icon_url'],$expireTime);//诊所logo
            
            
            $this->redirect(Url::to(['@manageIndex']));
            return;
        }
        setcookie('spotId','',Yii::getAlias('@loginCookieExpireTime'),'/',null,null);//诊所id
        
    	return $this->render('index', [ 'list' => $list,'spotInfo' => $spotInfo,'username' => $userInfo->username]);
    }
    
}
