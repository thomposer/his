<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\Spot;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use app\modules\rbac\models\PermissionForm;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\search\SpotSearch;
use yii\db\Exception;
use yii\rbac\Permission;
use yii\rbac\Role;
use app\modules\user\models\User;
use yii\db\Connection;
use app\modules\apply\models\ApplyPermissionList;
use app\modules\rbac\models\AssignmentForm;
use app\common\Common;

/**
 * SitesController implements the CRUD actions for Spot model.
 */
class SitesController extends BaseController
{
	public $status = array (
			'' => '全部',
			'0' => '未初始化',
			'1' => '已初始化' 
	);
	
	public function behaviors()
	{
		$parent = parent::behaviors();
		$current = [
			
/* 			'PageCache' => [ 
				'class' => 'yii\filters\PageCache',
				'only' => [ 
					'list' 
				],
				'duration' => 3600,
				'dependency' => [ 
					'class' => 'yii\caching\DbDependency',
					'sql' => "select count(*) from gzh_spot where user_id = '{$this->userId}'" 
				]
			] */
		];
		return ArrayHelper::merge($current, $parent);
	}
	
	/**
	 * 如果是超级管理员，则显示站点所有站点列表，若不是则显示当前站点信息
	 * 
	 * @return mixed
	 */
	public function actionIndex()
	{
		$isSuperSystem = $this->manager->checkAccess($this->userInfo->user_id, Yii::getAlias('@systemPermission'));			
		$data = null;
		// 如果是超级管理员，则显示站点所有站点列表
		if ($isSuperSystem) {
			$searchModel = new SpotSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
			$spot = Spot::find()->select([ 'spot','spot_name' ])->asArray()->all();
			
			$data = array(
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'render' => $isSuperSystem,
				'status' => $this->status,
				'spot' => $spot 
			);
			
			// 若不是则显示当前站点信息
		} else {
			$model = Spot::getSpot();
			$data = array (
				'model' => $model,
				'render' => $isSuperSystem 
			);
		}
		return $this->render('index', $data);
	}

	
	/**
	 * 查看已申请的站点列表
	 * 
	 * @return Ambigous <string, string>
	 */
	public function actionList()
	{
		$searchModel = new SpotSearch();
		$gets = Yii::$app->request->get();
		$gets ['SpotSearch']['user_id'] = $this->userInfo->user_id;
		$dataProvider = $searchModel->search($gets, $this->pageSize);
		return $this->render('list', [ 
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'renderStatus' => array (
				'' => '全部',
				'0' => '否',
				'1' => '是' 
			) 
		]);
	}
	
	/**
	 * Creates a new Spot model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * 
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Spot();
		
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			
			// 判断当前用户是否已入库,否则入库
			$userInfo = $this->userInfo;
					
			$model->render = Spot::NO_REDNER;
			$model->user_id = $userInfo->user_id;
			$model->save();
			
			Common::showInfo('添加站点成功', 'list.html');
		}
		
		// 添加一个空白站点模板
		$defaultList = array (
			[ 
				'spot' => Yii::getAlias('@defaultSpotName'),
				'spot_name' => '空白模板' 
			] 
		);
		$spotList = Spot::find()
			->select([ 'spot','spot_name' ])
			->where([ 'render' => Spot::HAS_RENDER ])
			->orderBy([ 'id' => SORT_DESC ])
			->asArray()
			->all();
		$spotList = ArrayHelper::merge($defaultList, $spotList);
		return $this->render('create', [ 
				'model' => $model,
				'templateList' => $spotList 
		]);
	}
	
	/**
	 * Updates an existing Spot model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * 
	 * @param integer $id        	
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect([ 'index' ]);
		}
		
		$spotList = Spot::find()
			->select([ 'spot','spot_name','template' ])
			->where([ 'render' => Spot::HAS_RENDER ])
			->orderBy([ 'id' => SORT_DESC ])
			->asArray()
			->all();
		
		if ($model->render == Spot::HAS_RENDER) {
			foreach ( $spotList as $v ) {
				if ($v ['spot'] == $model->template) {
					$spotList = array (
						0 => array (
							'spot' => $model->template,
							'spot_name' => $v['spot_name'] 
						) 
					);
					break;
				}
			}
		}
		// 添加一个空白站点模板
		$defaultList = [
		    [
		        'spot' => Yii::getAlias('@defaultSpotName'),
		        'spot_name' => '空白模板'
		    ],
		    [
		        'spot' => Yii::getAlias('@superSpotName'),
		        'spot_name' => '系统模板',
		    ]
		];
		$spotList = ArrayHelper::merge($defaultList, $spotList);
		return $this->render('update', [ 
				'model' => $model,
				'templateList' => $spotList 
		]);
	}
	
	private function createDefaultApply($spot, $role)
	{
		$model = new ApplyPermissionList();
						
		$applyUser = User::find()->select(['username'])->where(['user_id' => $spot->user_id])->asArray()->one();
		
		$model->user_id = $spot->user_id;
		$model->username = $applyUser['username'];
		$model->item_name = $role->name;
		$model->item_name_description = $role->description;
		$model->spot_name = $spot->spot_name;
		$model->spot = $spot->spot;
		$model->reason = '系统默认生成';
		$model->apply_persons = $this->userInfo->username;
		$model->status = ApplyPermissionList::VERIFIED;
		$model->create_time = time();
		$model->update_time = time();
		
		$model->save();
	}
	
	public function initDefaultTemplate($targetSpot)
	{
		$rootPath = $targetSpot->template === Yii::getAlias('@defaultSpotName')?Yii::getAlias('@defaultTemplateUrl'):Yii::getAlias('@superTemplateUrl');
		$defaultPerms = include($rootPath);
		$dbTrans = Yii::$app->db->beginTransaction();
		try {
			$categoryRoleSuffix = '_roles';
			$categoryPermSuffix = '_permissions';
			//$roleType = '_roles_';
			//$permType = '_permissions_';
			
			// 初始化权限分类总目录和角色分类总目录
			$categoryRole = new Role();
			$categoryRole->name = $targetSpot->spot . $categoryRoleSuffix;
			$categoryRole->description = $targetSpot->spot_name;
			$this->manager->add($categoryRole);
			
			$categoryPerm = new Permission();
			$categoryPerm->name = $targetSpot->spot . $categoryPermSuffix;
			$categoryPerm->description = $targetSpot->spot_name;
			$this->manager->add($categoryPerm);
			
			// 初始化站点管理员用户 站点_roles_用户名称
			$systemRole = new Role();
			$systemRole->name = $this->rolePrefix . 'system';
			$systemRole->description = '站点管理员';
			$this->manager->add($systemRole);
			
			// 添加到角色分类
			$this->manager->addChild($categoryRole, $systemRole);
			// 分配站点管理员给申请的用户
			$this->manager->assign($systemRole, $targetSpot->user_id);
			
			// 初始化默认权限
			foreach ( $defaultPerms as $permName => $perms ) {
				$modulePerm = new Permission();
				$modulePerm->name = $this->permissionPrefix . $permName;
				$modulePerm->description = $perms ['categoryName'];
				$modulePerm->data = $categoryPerm->name;
				
				// 模块权限
				$this->manager->add($modulePerm);
				$this->manager->addChild($categoryPerm, $modulePerm);
				
				// 菜单权限
				foreach ( $perms ['children'] as $perm ) {
					$temp = new Permission();
					$temp->name = $targetSpot->spot . $perm ['name'];
					$temp->description = $perm ['description'];
					$temp->data = $modulePerm->name;
					
					$this->manager->add($temp);
					// 将权限分配给权限总类以及系统管理员
					$this->manager->addChild($modulePerm, $temp);
					$this->manager->addChild($systemRole, $temp);
				}
			}			
			$targetSpot->render = Spot::HAS_RENDER;
			$targetSpot->save();
			// 申请表中添加用户
			$this->createDefaultApply($targetSpot, $systemRole);
			$dbTrans->commit();
			Common::showInfo('初始化成功');
		} catch (Exception $e) {
			$dbTrans->rollBack();
			throw $e;
		}
		
		return $this->redirect(['index']);
	}
	
	/**
	 * 初始化站点权限和角色
	 * 赋予创建该站点的用户-站点管理员角色
	 * 
	 * @throws NotFoundHttpException
	 * @return string
	 */
	public function actionTemplate($id)
	{
		$targetSpot = Spot::findOne($id);
		// 非法id
		if (!$targetSpot) {
			throw new NotFoundHttpException('你所请求的站点不存在', 404);
		}
		
		$this->wxcode = $targetSpot->spot;
		$this->rolePrefix = $this->wxcode . '_roles_';
		$this->permissionPrefix = $this->wxcode . '_permissions_';		 
		$this->rootRole = $this->wxcode . '_roles';
		$this->rootPermission = $this->wxcode . '_permissions';
		// 已经初始化
		$haspermission = $this->manager->getPermission($this->rootPermission);
		if ($haspermission) {
		    $targetSpot->render = Spot::HAS_RENDER;
		    $targetSpot->save();
		    	
		    Common::showInfo('该站点权限已经初始化');
		}
		// 使用默认模板初始化,如果有同名的模板，则使用自定义的那个模板
		$isDefaultTemplate = Spot::find()->select([ 'id' ])->where(['spot' => $targetSpot->template])->one() !== null;
		if (!$isDefaultTemplate && ($targetSpot->template === Yii::getAlias('@defaultSpotName') || $targetSpot->template === Yii::getAlias('@superSpotName'))) {
			return $this->initDefaultTemplate($targetSpot);
		}
		
		$dbTrans = Yii::$app->db->beginTransaction();
		try {
			
			$categoryRoleSuffix = '_roles';
			$categoryPermSuffix = '_permissions';			
	
			// 初始化权限分类总目录和角色分类总目录
			$categoryRole = new Role();
			$categoryRole->name = $this->rootRole;
			$categoryRole->description = $targetSpot->spot_name;
			$this->manager->add($categoryRole);
				
			$categoryPerm = new Permission();
			$categoryPerm->name = $this->rootPermission;
			$categoryPerm->description = $targetSpot->spot_name;
			$this->manager->add($categoryPerm);
	
			$templateSpotName = $targetSpot->template;
			$targetSpotName = $targetSpot->spot;
			
			// 复制模板的权限
			$templatePerms = $this->manager->getChildren($templateSpotName . $categoryPermSuffix);
			foreach ($templatePerms as $perm) {
				$tempPerm = new Permission();
				// 替换前缀为目标站点
				$tempPerm->name = str_replace($templateSpotName.'_', $targetSpotName.'_', $perm->name);
				$tempPerm->description = $perm->description;
				$tempPerm->data = $categoryPerm->name;
				
				$this->manager->add($tempPerm);
				$this->manager->addChild($categoryPerm, $tempPerm);
				
				// 复制该权限下的二级权限，即url资源权限
				$subPerms = $this->manager->getChildren($perm->name);
				foreach ($subPerms as $subPerm) {
				    $subPermName = $targetSpotName.ltrim($subPerm->name,$templateSpotName);
					$tempSubPerm = $this->manager->getPermission($subPermName);
					if (!$tempSubPerm) {
						$tempSubPerm = new Permission();
						$tempSubPerm->name = $subPermName;
						$tempSubPerm->description = $subPerm->description;
						$tempSubPerm->data = $tempPerm->name;
						
						$this->manager->add($tempSubPerm);
					}
					
					$this->manager->addChild($tempPerm, $tempSubPerm);
				}
			}
			
			// 复制模板的角色
			$templateRoles = $this->manager->getChildren($templateSpotName . $categoryRoleSuffix);
			foreach ($templateRoles as $role) {
				$tempRole = new Role();
				// 替换前缀为目标站点
				$tempRole->name = str_replace($templateSpotName.'_', $targetSpotName.'_', $role->name);
				$tempRole->description = $role->description;
				$tempRole->data = $categoryRole->name;
				
				$this->manager->add($tempRole);
				$this->manager->addChild($categoryRole, $tempRole);	

				// 复制该角色下的url资源权限
				$subPerms = $this->manager->getChildren($role->name);
				foreach ($subPerms as $subPerm) {
				    
					$subPermName = $targetSpotName.ltrim($subPerm->name,$templateSpotName);			
					$tempSubPerm = $this->manager->getPermission($subPermName);
					
					$this->manager->addChild($tempRole, $tempSubPerm);
				}
			}
			
			// 判断是否有站点管理员，没有则创建一个并赋予申请者
			$systemRoleName = $this->rolePrefix . 'system';
			$systemRole = $this->manager->getRole($systemRoleName);
			if (!$systemRole) {
				$systemRole = new Role();
				$systemRole->name = $systemRoleName;
				$systemRole->description = '站点管理员';
				
				$this->manager->add($systemRole);
				
				// 将所有权限都给站点管理员
				$allPerms = $this->manager->getChildren($this->rootPermission);
				foreach ($allPerms as $perm) {
					$subPerms = $this->manager->getChildren($perm->name);
					foreach ($subPerms as $subPerm) {
					    
						$this->manager->addChild($systemRole, $subPerms);
					}					
				}
			}
			
			// 将站点管理员权限给申请者
			if (!$this->manager->getAssignment($systemRoleName, $targetSpot->user_id)) {
				$this->manager->assign($systemRole, $targetSpot->user_id);
			}
			
			$targetSpot->render = Spot::HAS_RENDER;
			$targetSpot->save();
			
			// 申请表中添加用户
			$this->createDefaultApply($targetSpot, $systemRole);			
			$dbTrans->commit();
			Common::showInfo('初始化成功');
			
		} catch (Exception $e) {
			$dbTrans->rollBack();
			throw $e;
		}
		
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the Spot model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * 
	 * @param integer $id        	
	 * @return Spot the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Spot::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('你所请求的页面不存在');
		}
	}
}
