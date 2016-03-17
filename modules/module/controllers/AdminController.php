<?php

namespace app\modules\module\controllers;

use Yii;
use app\modules\module\models\Title;
use app\modules\module\models\Menu;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use app\modules\module\models\TitleMenu;
use app\modules\module\models\search\TitleSearch;
use yii\rbac\Permission;
use yii\base\InvalidParamException;
use app\modules\spot\models\Spot;
use app\common\Common;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * AdminController implements the CRUD actions for Menu model and Title model.
 */
class AdminController extends BaseController
{
	
	private function isMenuExist($menuUrl, $parentId) {
		return Menu::find()->select(['id'])->where([ 'parent_id' => $parentId, 'menu_url' => $menuUrl])->one() !== null;
	}
    public function actionIndex(){
        
    $searchModel = new TitleSearch();
        
        $titleModel = new Title();
        
        if(Yii::$app->request->isPost && $titleModel->validate()){
            $data = Yii::$app->request->post();           
            foreach ($data['title_id'] as $key => $v){
                $title = Title::findOne($v);
                $title->sort = $data['sort'][$key]?$data['sort'][$key]:0;
                $title->save();
            }
            
        }   
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);    	
     	return $this->render('index', [
    		'dataProvider' => $dataProvider,
    		'searchModel' => $searchModel,
    	]);
    	
    }
    /**
     * Creates a new Module
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	$model = new TitleMenu();
    	$model->isNewRecord = true;
        if ($model->load(Yii::$app->request->post())) {
        	
        	$db = Yii::$app->db;
        	$dbTrans = $db->beginTransaction();
        	try {
        		
        		$title = Title::find()->where(['module_name' => $model->module_name])->one();        		
        		if ($title === null) {
        			$title = new Title();
        		}      		
        		$title->module_description = $model->module_description;
        		$title->module_name = $model->module_name;
        		$title->sort = time();
        		$title->status = $model->status;
        		$model->icon_url = UploadedFile::getInstance($model, 'icon_url');
        		$title->icon_url = $model->upload();
        		if(!$title->icon_url){
        		    return $this->render('create', [
        		        'model' => $model,
        		    ]);
        		}       		
	        	if ($title->save()) {
	        		
	        		// neededMenu
	        		$needMenus = array();
	        		
	        		foreach ($model->menusList as $menu) {
	        			if (!$this->isMenuExist($menu[0], $title->id)) {
	        				$menu[] = $title->id;
	        				$needMenus[] = $menu;
	        			}
	        		}
	        		$model->menusList = null;
	        		
	        		// 批量添加目录
	        		if (count($needMenus) > 0) {
	        			$db->createCommand()
	        				->batchInsert(Menu::tableName(), ['menu_url', 'description', 'type', 'role_type', 'parent_id'], $needMenus)
	        				->execute();
	        		}					
	        		$dbTrans->commit();
	        		
	            } else {
	            	$dbTrans->rollBack();
	            }
	            return $this->redirect(['view', 'id' => $title->id]);
        	} catch (\Exception $e) {
        		$dbTrans->rollback();
        		throw $e;
        	}       	
            
        } else {
        	
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * 显示添加结果
     * @param unknown $id
     * @return Ambigous <string, string>
     */
    public function actionView($id)
    {
    	return $this->render('view', [
			'model' => $this->findModel($id)
    	]);
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
    	 $spotUserRole = $this->manager->getRole($this->rolePrefix . 'system');
    	 
    	 // 检查是否存在该站点的管理员
    	if (!$spotUserRole) {
    		throw new \Exception('该站点未初始化，请联系管理员');
    	}
    	
    	$menus = $title->getAllMenus()
    		// 非超级管理员拥有的菜单
    		->where(['role_type' => 0])
    		->all();
    	// 添加模块权限到该站点下
    	$permName = $this->permissionPrefix . $title->module_name;
    	
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
    		$modulePerm->data = $this->rootPermission;
    		$modulePerm->description = $title->module_description;
    			
    		// 添加该类别，并将该类别统一添加到xxxx_permissions下
    		$this->manager->add($modulePerm);
    		$this->manager->addChild($this->manager->getPermission($this->rootPermission), $modulePerm);
    			
    		foreach ($menus as $menu) {
    			$perm = new Permission();
    			$perm->name = $this->wxcode . $menu->menu_url;
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
    	
    	return $this->redirect(['list']);
    }
    
    /**
     * 更新某模块下所有角色的权限，对用户是透明的
     * @param unknown $id
     * @throws \Exception
     */
    public function actionUpdate($id) {

        $title = $this->findModel($id);
    	// 更新的模块名称
    	$moduleName = $title->module_name;
    	$menus = $title->getAllMenus()->where(['role_type' => 0])->all();
    	
    	$manager = $this->manager;
    	
    	// 获取所有已经初始化的站点
    	$spots = Spot::find()->where(['render' => 1])->all();
    	
    	// 每个站点下
    	$permMid = '_permissions_';
    	$roleCategorySuffix = '_roles';
//     	$permCategorySuffix = '_permissions';
    	$systemRole = '_roles_system';
    	
    	$dbTrans = Yii::$app->db->beginTransaction();
    	try {
	    	foreach ($spots as $spot) {
	    		$modulePerm = $manager->getPermission($spot->spot . $permMid . $moduleName);
	    		// 判断是否添加了该模块
	    		if ($modulePerm === null) {
	    			continue;
	    		}
	    		
	    		// 获取该站点下旧的菜单权限
	    		$oldPerms = $manager->getChildren($modulePerm->name);
	    		
	    		$roleCategory = $manager->getRole($spot->spot . $roleCategorySuffix);
	    		// 获取该站点下的所有角色
	    		$roles = $manager->getChildren($roleCategory->name);
	    		
	    		// 生成该站点下的一个权限map,记录当前菜单权限新增删除的情况，true为新增，false为删除
	    		$menusMap = array();
	    		$checkPermsMap = array();
	    		foreach ($menus as $menu) {
	    			$checkPermsMap[$spot->spot . $menu->menu_url] = true;
	    			$menusMap[$spot->spot . $menu->menu_url] = $menu;
	    		}
	    		foreach ($oldPerms as $perms) {
	    			if (!isset($checkPermsMap[$perms->name])) {
	    				$checkPermsMap[$perms->name] = false;
	    			}
	    		}
	    		
	    		$newPerms = array();
	    		// 更新该站点下新的菜单权限
	    		foreach ($checkPermsMap as $permName => $isSave) {
    			    // 新的菜单权限，如果本身没有则添加
    				if ($isSave) {
    					$tempPerm = $manager->getPermission($permName);
    					
    					if ($tempPerm === null) {
    						$newPerm = new Permission();
    						$newPerm->name = $permName;
    						$newPerm->description = $menusMap[$permName]->description;
    						$newPerm->data = $modulePerm->name;
    						$newPerm->createdAt = time();
    						$newPerm->updatedAt = time();
    						
    						$manager->add($newPerm);
    						$manager->addChild($modulePerm, $newPerm);
    						$newPerms[] = $newPerm;
    					} else {
    						$tempPerm->description = $menusMap[$permName]->description;
    						$tempPerm->updatedAt = time();
    						$manager->update($tempPerm->name, $tempPerm);
    					}
    					
    				// 不要的进行级联删除
    				} else {
    					$tempPerm = $manager->getPermission($permName);
    					$manager->remove($tempPerm);
    				}
	    		}
	    		    		
	    		// 为站点管理员，则添加新的权限
	    		foreach ($roles as $role) {
	    			if ($role->name === $spot->spot.$systemRole) {
	    				foreach ($newPerms as $newPerm) {
	    					$manager->addChild($role, $newPerm);
	    				}	    				
	    			}
	    		}
	    		
	    		// 如果为空，则删除该模块
	    		if (count($manager->getChildren($modulePerm->name)) === 0) {
	    			$manager->remove($modulePerm);
	    		}
	    	}
	    	
	    	$dbTrans->commit();
    	} catch (\Exception $e) {
    		$dbTrans->rollBack();
    		throw $e;
    	}
    	
    	Common::showInfo('更新成功');
    }
    /**
     * 更新模块的详细信息
     * @param 模块id $id
     */
    public function actionEdit($id){
        
        $model = $this->findModel($id);
        $oldImg = $model->icon_url;
        if($model->load(Yii::$app->request->post())){
            $model->icon_url = UploadedFile::getInstance($model, 'icon_url');
            if($model->icon_url){
                $model->icon_url = $model->upload();              
            }else{
                $model->icon_url = $oldImg;
            }
            if($model->icon_url && $model->save()){
                Common::showInfo('保存成功',Url::to(['@moduleAdminIndex']));
            }else {
                Common::showInfo('保存失败');
            }
        }
        return $this->render('edit',['model' => $model]);
    }
    
}
