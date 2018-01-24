<?php

namespace app\modules\manage\controllers;

use app\modules\patient\models\Patient;
use Yii;
use yii\db\Query;
use app\common\Common;
use app\modules\spot\models\Spot;
use yii\filters\VerbFilter;
use app\common\Crop;
use app\modules\user\models\User;
use app\modules\make_appointment\models\AppointmentConfig;
use yii\helpers\Json;
use app\modules\make_appointment\models\Appointment;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot\models\SpotConfig;
use app\modules\patient\models\PatientRecord;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * 站点通用功能，包括获取站点信息，登出系统
 * @author 张震宇
 *
 */
class SitesController extends Controller
{

    public $userInfo;
    public $manager;

    public function init() {
        $this->userInfo = Yii::$app->user->identity;
        $this->manager = Yii::$app->authManager;
        parent::init();
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                    'upload' => ['post'],
                    'appointment-time' => ['post']
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        date_default_timezone_set('Asia/Shanghai');
        return parent::beforeAction($action);
    }

    
	/**
	 * 将站点信息存储到session中，并跳转到站点首页
	 * @return Ambigous <string, string>
	 */
	public function actionIndex() {    
		$id = Yii::$app->request->post('id');
		$defaultSpot = Yii::$app->request->post('default_spot',0);
                $defaultSpot = ($defaultSpot === 'on' ? $id : $defaultSpot);
		$url = Yii::getAlias('@manageIndex');
		$expireTime = Yii::getAlias('@loginSessionExpireTime');
		if(isset($_COOKIE['rememberMe'])){
		    $expireTime = Yii::getAlias('@loginCookieExpireTime');
		}
		    
		// 无ID则跳转回站点选择页面
		if (!$id) {
        	$message = '未有任何诊所存在！';
        	Common::showInfo($message);
		}
		$session = Yii::$app->session;
		
		$userId = $this->userInfo->id;
		$query = new Query();
		$query->from(Spot::tableName())->select(['spot','spot_name','parent_spot','icon_url','telephone']);
		$query->where('id = :id',[':id' => $id]);
		$curSpot = $query->one();
		if($curSpot['parent_spot'] != 0){
		    $command = Yii::$app->db->createCommand('update '.User::tableName().' set default_spot = :default_spot where id = :id');
		    $command->bindValue(':default_spot',$defaultSpot);
		    $command->bindValue(':id',$this->userInfo->id);
            $command->execute();
            setcookie('defaultSpotId',$defaultSpot,Yii::getAlias('@loginCookieExpireTime'),'/',null,null);
		}
		//若机构有变更，同步更新机构cookie信息
// 		if($curSpot['parent_spot'] != 0 && $curSpot['parent_spot'] != $_COOKIE['parentSpotId']){
// 		    $parentSpotInfo = Spot::find()->select(['id','spot','spot_name'])->where(['id' => $curSpot['parent_spot']])->asArray()->one();
// 		    setcookie('parentSpotId',$parentSpotInfo['id'],$expireTime,'/',null,null,true);;//机构id
// 		    setcookie('parentSpotCode',$parentSpotInfo['spot'],$expireTime,'/',null,null,true);;//机构代码
// 		    setcookie('parentSpotName',$parentSpotInfo['spot_name'],$expireTime,'/',null,null,true);;//机构名称
// 		}
        $parentSpotInfo = Spot::find()->select(['id','spot','spot_name'])->where(['id' => $curSpot['parent_spot']])->asArray()->one();
        $cacheSuffix = $id.$this->userInfo->id;
		setcookie('spotId',$id,time()+$expireTime,'/',null,null);//诊所id
		setcookie('parentSpotId',$parentSpotInfo['id'],time()+$expireTime,'/',null,null);//机构id
		$cache = Yii::$app->cache;
		$cache->set(Yii::getAlias('@spot').$cacheSuffix, $curSpot['spot'],$expireTime);//诊所代码
		$cache->set(Yii::getAlias('@spotName').$cacheSuffix, $curSpot['spot_name'],$expireTime);//诊所名称
		$cache->set(Yii::getAlias('@spotIcon').$cacheSuffix, $curSpot['icon_url'],$expireTime);//诊所图标
		
		$cache->set(Yii::getAlias('@parentSpotCode').$cacheSuffix, $parentSpotInfo['spot'],$expireTime);//机构代码
		$cache->set(Yii::getAlias('@parentSpotName').$cacheSuffix, $parentSpotInfo['spot_name'],$expireTime);//机构名称
// 		setcookie('spotIcon',$curSpot['icon_url'],$expireTime,'/',null,null,true);//诊所图标
// 		setcookie('spotName',$curSpot['spot_name'],$expireTime,'/',null,null,true);//诊所名称
// 		setcookie('spotTelephone',$curSpot['telephone'],$expireTime,'/',null,null,true);;//诊所电话号码
        $session->set('currentSpot','');    
		$this->redirect([$url]);
		
	}
	/**
	 * 上传图片公共action
	 */
	public function actionUpload(){

	    $avatar_src = Yii::$app->request->post('avatar_src');
	    $avatar_data = Yii::$app->request->post('avatar_data');
		$patientId = Yii::$app->request->post('patientId');
		$dstImgWidth = Yii::$app->request->post('imgWidth')?Yii::$app->request->post('imgWidth'):220;
		$dstImgHeight = Yii::$app->request->post('imgHeight')?Yii::$app->request->post('imgHeight'):220;
	    $avatar_file = $_FILES['avatar_file'];
	    $crop = new Crop($avatar_src, $avatar_data, $avatar_file,$dstImgWidth,$dstImgHeight);
	    $response = array(
	        'state'  => 200,
	        'message' => $crop -> getMsg(),
	        'result' => $crop -> getResult(),
	        'ret' => $crop->uploadCdn()
	    );
	    if($patientId){//若有患者id，则为病历库上传头像
			$patientModel = Patient::findOne($patientId);
			if($patientModel){
				$patientModel->head_img = $crop -> getResult();
				$patientModel->save();
			}
		}
	    echo json_encode($response);
	}
	
	/**
	 * 获取科室可预约时间
	 */
	public function actionAppointmentTime(){
	    $result['success'] = true;
	    
	    $id = Yii::$app->request->post('id');//二级科室id
	    $type = Yii::$app->request->post('type');//预约类型
	    if (!in_array($type, [1,2,3,4])){
	        $result['success'] = false;
	        $result['errorCode'] = 1001;
	        $result['msg'] = '参数错误';
	        return Json::encode($result);
	    }
	   $appList = Appointment::getDepartmentTime($id, $type);
	   $result['data'] = $appList;

	    return Json::encode($result);
	}
}
