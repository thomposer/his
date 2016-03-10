<?php

namespace app\modules\manage\controllers;

use Yii;
use yii\filters\AccessControl;
use app\common\base\AutoLoginFilter;
use yii\web\Controller;
use app\modules\spot\models\Spot;
use yii\helpers\VarDumper;
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
			// 自动OA登录过滤器
//  			'autologin' => [
//  				'class' => AutoLoginFilter::className(),
//  			],
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
	public function beforeAction($action){
	    
	    return parent::beforeAction($action);
	    
	}
    
    public function actionIndex()
    {
        $data = '';
        $where = '';
        $list = '';
        $userId = Yii::$app->user->identity->user_id;
        $allowSpot = $this->manager->getAssignments($userId);
        $systemRole = Yii::getAlias('@systemRole');
        if($allowSpot){
            foreach ($allowSpot as $v){
               switch (true){
                   case $v->roleName === $systemRole : 
                       $where = 1;
                       break;
//                    case strstr($v->roleName, $spotPrefix) != false :
//                        $data['spot'][] = trim(str_replace($spotPrefix, '', $v->roleName));
//                        break;     
                   default:
                       $spotName = explode('_', $v->roleName)[0];
                       $data['spot'][$spotName] = $spotName;
                       break;
                       
               }
            }
        }
        $where = $where?$where:$data;
        if($where){
            $list = Spot::find()
    		->select(['id', 'spot', 'spot_name'])
    		->where($where)
    		->asArray()
    		->all();
        }
        $session = Yii::$app->session;
    	$session->set('spot_id','');//站点id
		$session->set('spot','');//站点简称
		$session->set('spot_name','');//站点名称
		return $this->render('index', [ 'list' => $list ]);
    }
    public function actionMessage(){
        
       return $this->render('message');
    }
}
