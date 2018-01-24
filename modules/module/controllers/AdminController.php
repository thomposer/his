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
use app\modules\spot\models\Spot;
use yii\helpers\Url;
use app\modules\rbac\models\Item;
use app\modules\rbac\models\ItemChildForm;
use yii\web\Response;

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
        $titleModel->scenario = 'sort';
        if(Yii::$app->request->isPost && $titleModel->validate()){
            $data = Yii::$app->request->post();  
            foreach ($data['title_id'] as $key => $v){
                $title = Title::findOne($v);
                $title->scenario = 'sort';
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
    	
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        	
        	$db = Yii::$app->db;
        	$dbTrans = $db->beginTransaction();
        	try {
        		
        		$title = Title::find()->where(['module_name' => $model->module_name])->one();        		
        		if ($title === null) {
        			$title = new Title();
        		}      		
        		$title->module_description = $model->module_description;
        		$title->module_name = $model->module_name;
        		$title->status = $model->status;
        		$title->icon_url = $model->icon_url;
        		$title->type = $model->type;       		
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
        	$model->isNewRecord = true;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionCreateChildren(){
        $model = new Title();
        $model->scenario = 'createChildren';
        
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Url::to(['@moduleAdminIndex']));
        }
        $title = Title::find()->select(['id','module_description'])->asArray()->all();
        return $this->render('create-children',[
            'model' => $model,
            'title' => $title
        ]);
    }
    /**
     * @desc 更新模块层级关系
     * @param integer $id 模块id
     */
    public function actionUpdateChildren($id){
        
        $model = $this->findModel($id);
        $model->scenario = 'createChildren';
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Url::to(['@moduleAdminIndex']));
        }
        $title = Title::find()->select(['id','module_description'])->asArray()->all();
        return $this->render('update-children',[
            'model' => $model,
            'title' => $title
        ]);
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
    	 $spotUserRole = $this->manager->getRole($this->parentRolePrefix . 'system');
    	 
    	 // 检查是否存在该站点的管理员
    	if (!$spotUserRole) {
    		throw new \Exception('该站点未初始化，请联系管理员');
    	}
    	
    	$menus = $title->getAllMenus()
    		// 非超级管理员拥有的菜单
    		->where(['role_type' => 0])
    		->all();
        $children = Title::find()->select(['id','module_name','module_description'])->where(['parent_id' => $id])->asArray()->all();
    	if(!empty($children)){
    	    foreach ($children as $key => $v){
    	        $menus[$key] = $v;
   		        $menus[$key]['children'] = Menu::find()->select(['menu_url','description'])->where(['parent_id' => $v['id'],'role_type' => 0])->asArray()->all();
   		    }
   		}else{
   		    $menus = $title->getAllMenus()->where(['role_type' => 0])->asArray()->all();
   		}   
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
    		    if(!isset($menu['children'])){
        			$perm = new Permission();
        			$perm->name = $this->parentSpotCode . $menu['menu_url'];//权限url
        			$perm->data = $permName;//父级分类
        			$perm->description = $menu['description'];//权限描述
        				
        			// 添加该类别下的菜单权限
        			$this->manager->add($perm);
        			$this->manager->addChild($modulePerm, $perm);
    		    }else{
    		        $perm = new Permission();
    		        $perm->name = $this->parentPermissionPrefix. $menu['module_name'];//权限url
    		        $perm->data = $permName;//父级分类
    		        $perm->description = $menu['module_description'];//权限描述
    		        // 添加该类别下的菜单权限
    		        $this->manager->add($perm);
    		        $this->manager->addChild($modulePerm, $perm);
    		        foreach ($menu['children'] as $value){
    		            $childrenPerm = new Permission();
    		            $childrenPerm->name = $this->parentSpotCode.$value['menu_url'];
    		            $childrenPerm->description = $value['description'];
    		            $childrenPerm->data = $this->parentPermissionPrefix. $menu['module_name'];
    		            $this->manager->add($childrenPerm);
    		            $this->manager->addChild($perm, $childrenPerm);
    		        }
    		    }	
    		    // 为该站点管理员角色添加菜单权限
    		    if (!$this->manager->hasChild($spotUserRole, $perm)) {
    		        $this->manager->addChild($spotUserRole, $perm);
    		    }
    		}
    		Yii::$app->getSession()->setFlash('success','添加成功');
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
        $children = Title::getTitleChildren($id);
    	if(!empty($children)){
    	    foreach ($children as $key => $v){
    	        $thirdChild = Title::getTitleChildren($v['id']);
    	        $menus[$key] = $v;
    	        if(!empty($thirdChild)){
    	            foreach ($thirdChild as $k => $value){
    	                $menus[$key]['thirdChildren'] = true;
    	                $menus[$key]['children'][$k] = $value;
    	                $menus[$key]['children'][$k]['children'] = Menu::find()->select(['menu_url','description','sort'])->where(['parent_id' => $value['id'],'role_type' => 0])->asArray()->all();
    	            }
    	        }else{
    	            $menus[$key]['thirdChildren'] = false;
    	            $menus[$key]['children'] = Menu::find()->select(['menu_url','description','sort'])->where(['parent_id' => $v['id'],'role_type' => 0])->asArray()->all();
    	        }
    	    }
    	}else{
    	    $menus = $title->getAllMenus()->where(['role_type' => 0])->asArray()->all();
    	}
    	
//     	echo '<pre>';
//     	var_export($menus);
//     	echo '</pre>';
//     	exit();
    	$manager = $this->manager;
    	
    	// 获取所有已经初始化的站点
    	$spots = Spot::find()->where(['status' => 1,'parent_spot' => 0])->all();
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
	    		foreach ($menus as $key => $menu) {
	    			if(!isset($menus[$key]['children'])){//判断是否有子类，若有，则为三级/四级分类
	    			    $checkPermsMap[$spot->spot . $menu['menu_url']] = true;
	    			    $menusMap[$spot->spot . $menu['menu_url']] = $menu;
	    			}else{
	    			    foreach ($menu['children'] as $value){
	    			        $menusMap[$spot->spot.$permMid.$menu['module_name']]['description'] = $menu['module_description'];
	    			        $menusMap[$spot->spot.$permMid.$menu['module_name']]['sort'] = $menu['sort'];
	    			        if(!$menu['thirdChildren']){
	    			            
	    			            $checkPermsMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot . $value['menu_url']] = true;
	    			            $menusMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot . $value['menu_url']] = $value;
	    			            
	    			        }else{
	    			            foreach ($value['children'] as $k  => $thridValue){
	    			                    
	    			                    $menusMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot.$permMid.$value['module_name']]['description'] = $value['module_description'];
	    			                    $menusMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot.$permMid.$value['module_name']]['sort'] = $value['sort'];
	    			                    $checkPermsMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot.$permMid.$value['module_name']][$spot->spot . $thridValue['menu_url']] = true;
	    			                    $menusMap[$spot->spot.$permMid.$menu['module_name']][$spot->spot.$permMid.$value['module_name']][$spot->spot . $thridValue['menu_url']] = $thridValue;
	    			            }
	    			        }
	    			    }
	    			}
	    		}
	    		foreach ($oldPerms as $perms) {
	    		    $childrenPerms = $manager->getChildren($perms->name);
	    		    if(!empty($childrenPerms)){//若有三级菜单，则判断三级菜单下是否有删除的情况
	    		        foreach ($childrenPerms as $child){
	    		            $thirdChildPerms = $manager->getChildren($child->name);
	    		            if(!empty($thirdChildPerms)){
    	    		            foreach ($thirdChildPerms as $value){
    	    		                
    	    		                if(!isset($checkPermsMap[$perms->name][$child->name][$value->name])){
    	    		                    Yii::info($perms->name,'checkPermsMap');
    	    		                    Yii::info($child->name,'checkPermsMap');
    	    		                    Yii::info($value->name,'checkPermsMap');
    	    		                    $checkPermsMap[$perms->name][$child->name][$value->name] = false;
    	    		                }
    	    		            }
	    		            }else{
	    		                if(!isset($checkPermsMap[$perms->name][$child->name])){
	    		                    $checkPermsMap[$perms->name][$child->name] = false;
	    		                }    
    	    		       }
	    		        }
	    		    }
	    			else if (!isset($checkPermsMap[$perms->name])) {//若不存在在菜单列表里，则删除
	    				$checkPermsMap[$perms->name] = false;
	    			}
	    		}
	    		$newPerms = array();
	    		// 更新该站点下新的菜单权限
	    		foreach ($checkPermsMap as $permName => $isSave) {
    			    // 新的菜单权限，如果本身没有则添加
    				if ($isSave == true || !empty($isSave)) {
    					$tempPerm = $manager->getPermission($permName);
    					
    					if ($tempPerm === null) {
    						$newPerm = new Permission();
    						$newPerm->name = $permName;
    						$newPerm->description = $menusMap[$permName]['description'];
    						$newPerm->data = $modulePerm->name;
    						$newPerm->createdAt = time();
    						$newPerm->updatedAt = $menusMap[$permName]['sort'];
    						$manager->add($newPerm);
    						$manager->addChild($modulePerm, $newPerm);
    						$newPerms[] = $newPerm;
    					} else {
    						$tempPerm->description = $menusMap[$permName]['description'];
    						$tempPerm->updatedAt = $menusMap[$permName]['sort'];
    						$manager->update($tempPerm->name, $tempPerm);
    					}
    					if(!is_bool($isSave)){
        					foreach ($isSave as $key => $value){
        					    $tempChildrenPerm = $manager->getPermission($key);
        					    if($value && $tempChildrenPerm == null){
            					    $newChildrenPerm = new Permission();
            					    $newChildrenPerm->name = $key;
            					    $newChildrenPerm->description = $menusMap[$permName][$key]['description'];
            					    $newChildrenPerm->data = $permName;
            					    $newChildrenPerm->createdAt = time();
            					    $newChildrenPerm->updatedAt = time();
            					    $manager->add($newChildrenPerm);
            					    $manager->addChild($tempPerm?$tempPerm:$newPerm, $newChildrenPerm);
        					    }else if($value && $tempChildrenPerm){
        					        $tempChildrenPerm->description = $menusMap[$permName][$key]['description'];
        					        $tempChildrenPerm->data = $permName;
        					        $tempChildrenPerm->updatedAt = time();
        					        $manager->update($tempChildrenPerm->name, $tempChildrenPerm);
        					        $record = ItemChildForm::find()->select(['parent'])->where(['parent' => $permName,'child' => $tempChildrenPerm->name])->asArray()->one();
        					        if(!$record){
        					           $manager->addChild($tempPerm?$tempPerm:$newPerm, $tempChildrenPerm);
        					        }
        					        $manager->removeChild($modulePerm, $tempChildrenPerm);
        					    }else{
        					        $tempPerm = $manager->getPermission($key);
        					        $manager->remove($tempPerm);
        					    }
        					    if(!is_bool($value)){
        					        foreach ($value as $k => $thirdChildValue){
        					            $tempThirdChildrenPerm = $manager->getPermission($k);
        					            
        					            if($thirdChildValue && $tempThirdChildrenPerm == null){
        					                $newThirdChildrenPerm = new Permission();
        					                $newThirdChildrenPerm->name = $k;
        					                $newThirdChildrenPerm->description = $menusMap[$permName][$key][$k]['description'];
        					                $newThirdChildrenPerm->data = $key;
        					                $newThirdChildrenPerm->createdAt = time();
        					                $newThirdChildrenPerm->updatedAt = time();
        					                $manager->add($newThirdChildrenPerm);
        					                $manager->addChild($newChildrenPerm?$newChildrenPerm:$tempChildrenPerm, $newThirdChildrenPerm);
        					            }else if($thirdChildValue && $tempThirdChildrenPerm){
        					                $tempThirdChildrenPerm->description = $menusMap[$permName][$key]['description'];
        					                $tempThirdChildrenPerm->data = $permName;
        					                $tempThirdChildrenPerm->updatedAt = time();
        					                $manager->update($tempThirdChildrenPerm->name, $tempThirdChildrenPerm);
        					                $record = ItemChildForm::find()->select(['parent'])->where(['parent' => $key,'child' => $tempThirdChildrenPerm->name])->asArray()->one();
        					                if(!$record){
        					                    $manager->addChild($newChildrenPerm?$newChildrenPerm:$tempChildrenPerm, $tempThirdChildrenPerm);
        					                }
        					                $manager->removeChild($modulePerm, $tempThirdChildrenPerm);
        					            }else{
        					                $tempPerm = $manager->getPermission($k);
        					                $manager->remove($tempPerm);
        					            }
        					            	
        					        }
        					    }
        					}
    					}   
    					
    				}
    				// 不要的进行级联删除
    				if($isSave == false) {
    				    $tempPerm = $manager->getPermission($permName);
    				    if($tempPerm->data == $modulePerm->name){
    				        $manager->remove($tempPerm);
    				    }
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
    	Yii::$app->getSession()->setFlash('success','更新成功');
    	$this->redirect(Url::to(['@moduleAdminIndex']));
    }
    /**
     * 更新模块的详细信息
     * @param 模块id $id
     */
    public function actionEdit($id){
        
        $model = $this->findModel($id);
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->getSession()->setFlash('success','更新成功');
            return $this->redirect(Url::to(['@moduleAdminIndex']));
        }
        return $this->render('edit',['model' => $model]);
    }
    
    
    public function actionDelete($id){
        
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['forceClose'=>true,'forceRedirect'=> 'true'];
    }
    
    
    
    
}
