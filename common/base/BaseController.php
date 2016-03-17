<?php

namespace app\common\base;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\common\AutoLoginFilter;
use yii\web\NotAcceptableHttpException;
use app\modules\module\models\Title;
use app\modules\module\models\Menu;
use app\modules\spot\models\Spot;
use yii\helpers\Url;
use app\modules\behavior\models\BehaviorRecord;
use yii\helpers\Json;
use app\common\Common;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{
    public $wxcode;//当前站点
    public $rolePrefix;//当前站点角色前缀
    public $permissionPrefix;//当前站点权限分类前缀
    public $rootRole;//用户管理模块－当前站点的角色根分类
    public $rootPermission;//用户管理模块－当前站点的权限根分类
    public $layoutData;//用户菜单列表
    public $pageSize = 20;//分页大小
    public $userInfo;//用户的user_id;
    public $manager;
    
    private $readApi = array('index', 'view', 'list', 'get'); // 不做记录的动作
    
    public function init(){
        parent::init();
        $this->wxcode = Yii::$app->session->get('spot');
        $this->manager = Yii::$app->authManager;
        $this->userInfo = Yii::$app->user->identity;
    }
    
	public function behaviors() {
	    
		return [
			// 自动登录过滤器
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
	
	
	public function beforeAction($action) {
	    parent::beforeAction($action);
	    date_default_timezone_set('Asia/Shanghai');
	    $this->rolePrefix = $this->wxcode . '_roles_';
	    $this->permissionPrefix = $this->wxcode . '_permissions_';
	    
	    $this->rootRole = $this->wxcode . '_roles';
	    $this->rootPermission = $this->wxcode . '_permissions';
	    
	    //$requestUrl = \Yii::$app->controller->module->module->requestedRoute;
	    $moduleId = \Yii::$app->controller->module->id; // module的id
	    $controllerId = Yii::$app->controller->id; // controller的id
	    $requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . $action->id; // 用户访问的路径
	    
	    $this->layout = false;
	    $view = Yii::$app->view;
	    $view->params['requestModuleController'] = '/'.$moduleId . '/' . $controllerId;
	    $view->params['requestUrl'] = $requestUrl;
	   	//允许直接访问的url,无权限不需要进入站点
	    $allowUrl = [
		    Yii::getAlias('@spotSitesCreate'),
		    Yii::getAlias('@spotSitesList'),
		    Yii::getAlias('@manageSites'), // 选择站点
		    Yii::getAlias('@moduleMenuSearch'),
	    ];
	    
	    // 无权限可以访问的url
	    if (in_array($requestUrl, $allowUrl)) {
    		return parent::beforeAction($action);
    	}

     	//若站点信息失效，则直接返回站点选择界面
    	if ($this->wxcode === null || $this->wxcode === '') {
    		$url = Url::to(['@manageDefaultIndex']);
    		return $this->redirect($url);
    	}
    	    	
	    $systemPermission = Yii::getAlias('@systemPermission');
        //如果用户拥有系统管理员－systems角色，则免验证
        if ($this->manager->checkAccess($this->userInfo->user_id, $systemPermission)) {
            $this->getUserRole($systemPermission);
            return parent::beforeAction($action);
        }
       	    
	    //若有站点权限，则站点首页免验证 	   
	    if ($requestUrl === Yii::getAlias('@manageIndex')) {
	    
	        $this->getUserRole();
	        return parent::beforeAction($action);
	    }
	   	
		if (!$this->manager->checkAccess($this->userInfo->user_id, $this->wxcode . $requestUrl)) {
	        return Common::showMessage();
	    }
	    //检测当前url是否已启用
// 	    if(!Menu::checkMenu($requestUrl)){
// 	        throw new NotFoundHttpException('你所请求的页面不存在',404);
// 	    }
	     
	    $this->getUserRole();
	    return parent::beforeAction($action);
	}
	
	//获取用户当前站点所有的权限，并渲染进layout
	private function getUserRole($role = null) {
	    $datas = '';
	    $list = [];
	    $view = Yii::$app->view;
 	    if($role != null) {
 	        $datas = Title::getMenus();
 	        $list = ['role' => true];
	    } else {
	    	$userPermissions = '';
	    	$localRoleType = $this->manager->getChildren($this->rootRole);//查找当前站点的所有角色
	    	if($localRoleType){
	    		foreach ($localRoleType as $p){//逐一匹配，若用户拥有该角色，则获取该角色下的权限
	    			$accessRole = $this->manager->getAssignment($p->name, $this->userInfo->user_id);
	    			if($accessRole){
	    				$userPermissions[$accessRole->roleName] = $this->manager->getPermissionsByRole($accessRole->roleName);
	    			}
	    		}
	    	}
	    	//过滤字段
	    	foreach ($userPermissions as $v){
	    	    foreach ($v as $k){
	    	        $list[] = ltrim($k->name,$this->wxcode);//获取有权限菜单的详细信息以及所属模块
	    	    }
	    	}
	    	 
	    	$result = Menu::getParent($list);
	    	foreach ($result as $v){
	    	    $datas[$v['title_id']]['module_description'] = $v['module_description'];
	    	    $datas[$v['title_id']]['module_name'] = $v['module_name'];
	    	    unset($v['module_description']);
	    	    $datas[$v['title_id']]['children'][] = $v;
	    	}
	    	
	   }
	    $view->params['permList'] = $list;
	    $view->params['layoutData'] = $datas;
	   
	}
	
	public function afterAction($action, $result)
	{
		// 过滤不记录掉读的接口
		if (in_array($action->id, $this->readApi)) {
			return parent::afterAction($action, $result);
		}
		
		$moduleId = Yii::$app->controller->module->id;
		$controllerId = Yii::$app->controller->id;
		$requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . $action->id;
		$menu = Menu::findOne(['menu_url' => $requestUrl]);
		$module = null;
		
		if ($menu) {
			$module = $menu->getTitle()->select(['module_name'])->one();
		}
		
		$request = Yii::$app->request;
		$getData = $request->get();
		$bodyData = $request->getBodyParams();
		if ($module && (count($getData) > 0 || count($bodyData) > 0)) {
			$data = Json::encode(array(
				'GET' => $getData,
				'BODY' => $bodyData,
			));
			BehaviorRecord::log(

				$this->userInfo->user_id,
				$request->userIP,
				Yii::$app->session->get('spot'),
				$module->module_name,
				$requestUrl,
				$data
			);
		}
		
		return parent::afterAction($action, $result);
	}
	
	    
	
}
