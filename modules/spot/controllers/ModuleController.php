<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\module\models\Title;
use app\modules\module\models\Menu;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use app\modules\module\models\search\TitleSearch;
use yii\rbac\Permission;

/**
 * AdminController implements the CRUD actions for Menu model and Title model.
 */
class ModuleController extends BaseController
{
	
	private function isMenuExist($menuUrl, $parentId) {
		return Menu::find()->select(['id'])->where([ 'parent_id' => $parentId, 'menu_url' => $menuUrl])->one() !== null;
	}
    
    
       
    /**
     * Finds the Title model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Title the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
    	if (($model = Title::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('该模块不存在!');
    	}
    }
    
    /**
     * 新增某个模块的所有功能到该站点下
     */
    public function actionList() {
    	$searchModel = new TitleSearch();
    	
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
    	
     	return $this->render('list', [
    		'dataProvider' => $dataProvider,
    		'searchModel' => $searchModel,
    	]);
    }
    
    /**
     * 添加模块
     * @param 模块id
     */
    public function actionAdd($id) {
        
    	 $title = Title::findOne($id);
   	 
    	 // 获取该站点下的站点管理员角色
    	 $spotUserRole = $this->manager->getRole($this->parentRolePrefix . 'system');
    	 
    	 // 检查是否存在该站点的管理员
    	if (!$spotUserRole) {
    		throw new \Exception('该诊所未初始化，请联系管理员');
    	}
    	
    	$menus = $title->getAllMenus()
    		// 非超级管理员拥有的菜单
    		->where(['role_type' => 0])
    		->all();
    	// 添加模块权限到该站点下
    	$permName = $this->parentPermissionPrefix . $title->module_name;
    	
    	// 检查该模块是否已经初始化过
    	if ($this->manager->getPermission($permName)) {
    		throw new \Exception('已经添加过该模块');
    	}
    	
    	$db = Yii::$app->db;
    	$dbTrans = $db->beginTransaction();
    	try {
    		// 模块===类别
    		$modulePerm = new Permission();
    		$modulePerm->name = $permName;
    		$modulePerm->data = $this->parentRootPermission;
    		$modulePerm->description = $title->module_description;
    			
    		// 添加该类别，并将该类别统一添加到xxxx_permissions下
    		$this->manager->add($modulePerm);
    		$this->manager->addChild($this->manager->getPermission($this->parentRootPermission), $modulePerm);
    			
    		foreach ($menus as $menu) {
    			$perm = new Permission();
    			$perm->name = $this->parentSpotCode . $menu->menu_url;
    			$perm->data = $permName;
    			$perm->description = $menu->description;
    				
    			// 添加该类别下的菜单权限
    			$this->manager->add($perm);
    			$this->manager->addChild($modulePerm, $perm);
    				
    			// 为该站点管理员角色添加菜单权限
    			if (!$this->manager->hasChild($spotUserRole, $perm)) {
    				$this->manager->addChild($spotUserRole, $perm);
    			}
    		}
    			    			
    		$dbTrans->commit();
    			 
    	} catch (\Exception $e) {
    		$dbTrans->rollback();
    		throw $e;
    	}
    	Yii::$app->getSession()->setFlash('success','添加成功');   	 
    	return $this->redirect(['list']);
    }
    
    
    
}
