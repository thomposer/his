<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\AssignmentForm;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\modules\user\models\User;
use yii\data\Pagination;
use app\modules\user\models\search\UserSearch;
use yii\db\Query;
use app\modules\spot\models\Spot;
use yii\helpers\ArrayHelper;
/**
 * AssginmentController implements the CRUD actions for Assignment model.
 */
class AssignmentController extends BaseController
{
    public function init(){
        parent::init();
        //用户管理－当前站点权限选择
       
        $session = Yii::$app->session;
        
        $this->wxcode = $session->get('currentSpot')?$session->get('currentSpot'):$session->get('spot');
    }
    public $typeList = array(
        '' => '全部',
        '1' => 'OA',
        '2' => 'QQ',
    );
    public function behaviors(){
        
       $parent =  parent::behaviors();        
       $current = [
            'verbs' => [
               'class' => \yii\filters\VerbFilter::className(),
               'actions' => [
                   'delete' => ['post']
               ]
            ],
        ];
       return ArrayHelper::merge($current, $parent);
       
    }
    /**
     * 用户管理列表
     * Lists all Assignment models.
     * @return mixed
     */
    public function actionIndex()
    {
            $searchModel = new UserSearch();
            
            $searchModel->load(Yii::$app->request->queryParams);
            
            if(!$searchModel->validate()){
                throw new \ErrorException($searchModel->errors);
            }

            $userId = Yii::$app->user->identity->userInfo->getUserId();
            $spotPrefix = Yii::getAlias('@spotPrefix');//站点前缀spot_
            // 判断当前用户是否为超级管理员
            $systemRole = Yii::getAlias('@systemRole');
            $isSuperSystem = $this->manager->getAssignment($systemRole, $userId);
            $where = 1;
            
            $query = new Query();
            $query->select(['user.user_id', 'user.username', 'user.type', 'auth.item_name','item.description'])
            	->from('gzh_user as user')
            	->join(' INNER JOIN', 'gzh_auth_assignment as auth','user.user_id = auth.user_id')
                ->join('INNER JOIN','gzh_auth_item as item','item.name = auth.item_name')
            	//->where("user.user_id != :userId")
            	//->addParams([':userId' => $userId])
            	->andFilterWhere(['user.type' => $searchModel->type])
            	->andFilterWhere(['like','user.user_id',$searchModel->user_id])
            	->andFilterWhere(['like','user.username',$searchModel->username])
            	->orderBy(['user.created_at' => SORT_DESC]);
            
            //如果不是系统管理员，则只查出当前站点的用户出来
            if (!$isSuperSystem) {
    			$query->andWhere("auth.item_name like :itemName")
    			->addParams([':itemName' => $this->wxcode.'_roles_%']);
            } else {
                //如果是系统管理员，则查出所有站点的用户，以及系统管理员出来
                // 所有站点
                $list = Spot::find()->select(['spot', 'spot_name'])->asArray()->all();
                
                $allspotLists = array('' => '全部');
                foreach ($list as $v) {
                    $allspotLists[$v['spot']] = $v['spot_name'];
                }
                if($searchModel->spot){
                    $query->andFilterWhere(['like','auth.item_name',$searchModel->spot.'_roles_%',false]);
                    $spotRoleInfo = $this->manager->getRole($spotPrefix.$searchModel->spot);
                    $spotShortName = trim(str_replace($spotPrefix, '', $spotRoleInfo->name));
                    $spotList[$spotShortName] = $spotRoleInfo->description;
                }
            }
           
            $countquery = clone $query;
            $pages = new Pagination([
            	'totalCount' => $countquery->count(),
            	'pageSize' => $this->pageSize,
            ]);
            
            $command = $query->offset($pages->offset)->limit($pages->limit)->createCommand();           
            $datas = $command->queryAll();
            
            foreach ($datas as $k => $v){
	            if (strpos($v['item_name'], $spotPrefix) === 0) {                       
	            	$spotShortName = trim(str_replace($spotPrefix, '', $v['item_name']));
	            	$spotList[$spotShortName] = $v['description'];
	            	unset($datas[$k]);
	            	
	            } else {
	            	$profix = explode('_',$v['item_name']);
	            	$datas[$k]['spot_name'] = $profix[0];//添加站点简称标记
	            	
	            }
            }
            
            /* 重组合并站点相同的用户信息 */
            /* 如果是超级管理员看，则同一个用户，不同站点是一条数据 */
            /* 如果是站点管理员看，则一个用户一条数据，没有站点之分  */
            $result = array();
            $userMap = array();
            foreach ($datas as $key => $v) {
            	if (isset($result[$v['user_id']])) {
                	$user = $result[$v['user_id']];
                	$user['item_name'][] = $v['item_name'];
                	$user['description'][] = $v['description'];
                	
                } else {
                	$user = array();
                	$user['user_id'] = $v['user_id'];
                	$user['username'] = $v['username'];
                	$user['type'] = $v['type'];
                	$user['spot'] = $v['spot_name'];
                	$user['item_name'] = array();
                	$user['item_name'][] = $v['item_name'];
                	$user['description'] = array();
                	$user['description'][] = $v['description'];
                }
                	
                $result[$v['user_id']] = $user;
           }
              	
           return $this->render('index', [
	           'models' => $result,
	           'allspotLists' => isset($allspotLists)?$allspotLists:'',
	           'searchModel' => $searchModel,
	           'typeList' => $this->typeList,
	           'pages' => $pages,
	           'spotList' => isset($spotList)?$spotList:'',
           ]);
        
    }

    /**
     * Displays a single Assignment model.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionView($user_id)
    {
        $assignmentmodel = new AssignmentForm();
        $assignmentmodel->user_id = $user_id;
        $assigndata = $this->manager->getAssignments($user_id);
        $username = User::find()->select(['username'])->where(['user_id' => $user_id])->all();
        foreach($username as $p){
            $username = $p->username;
        }
        
        return $this->render('view', [
            'model' =>$assignmentmodel,
            'rolename' => $assigndata,
            'username' => $username
        ]);
    }

    /**
     * 分配用户角色
     * Creates a new Assignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AssignmentForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $result = $this->manager->revokeAll($model->user_id);
            foreach ($model->item_name as $v){
                $rolemodel = new \yii\rbac\Role();                
                $rolemodel->name = $v;               
                $this->manager->assign($rolemodel, $model->user_id);//角色role的对象，userid         
            }
            return $this->redirect('index');
            
        }
            $userlist = User::find()->select(['user_id','username'])->orderBy(['updated_at'=>SORT_DESC]);     
            if($userlist){
                foreach ($userlist as $user){
            
                  
                    $userdata[$user->user_id] = $user->username;
            
                }
            }
            
            $rolename = $this->manager->getRoles();
           
            return $this->render('create', [
                'model' => $model,
                'userlist' => $userdata?$userdata:'',
                'roles' => $rolename?$rolename:''
            ]);
        
    }

    /**
     * 更新用户角色信息
     * Updates an existing Assignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionUpdate($user_id, $spot = null)
    {
        $model = new AssignmentForm();
        $model->user_id = $user_id;
        
        $curUserId = Yii::$app->user->identity->userInfo->getUserId();
        // 系统管理员才有权限使用$spot这个参数
        $systemRole = Yii::getAlias('@systemRole');
        if ($spot !== null && !$this->manager->getAssignment($systemRole, $curUserId)) {
        	throw new ForbiddenHttpException('非法操作');
        }
        
        // 当前修改的站点
        $spot = $spot ? $spot : $this->wxcode;
        
        // 当前站点权限分类
        $rootRole = $spot . '_roles';
                        
        // 是否为更新动作
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        	
        	$permsCheck = array();
        	if($model->item_name){//若有，则更新用户角色，若无，则相当于删除清除该站点用户下的所有角色
            	foreach ($model->item_name as $selectPerm) {
            		 $permsCheck[$selectPerm] = true;//用户要添加的角色
            	}
        	}
        	$perms = $this->manager->getChildren($rootRole);
        	
        	foreach ($perms as $perm) {
        		$permName = $perm->name;
        		$role = $this->manager->getRole($permName);
        		//添加选中的角色
        		if (isset($permsCheck[$permName])) {
        		    //没有则添加
        			if(!$this->manager->getAssignment($permName, $model->user_id)){
        			     $this->manager->assign($role, $model->user_id);
        			}
        		} else {
        		    //删除未选中的角色
        			$this->manager->revoke($role, $model->user_id);
        		}
        	}
        	
        	return $this->redirect(['index']);

        }
        
        // 当前用户所拥有的角色
        $userbyroles = $this->manager->getAssignments($user_id);
        if($userbyroles){
            foreach ($userbyroles as $role) {
            	if (strpos($role->roleName, $spot) === 0) {
            		$model->item_name[] = $role->roleName;
            	}
            }
        }
        
        // 用户信息
        $user = User::find()->select(['user_id','username'])->where(['user_id'=>$user_id])->one();
        $user_data[$user->user_id] = $user->username;
        
        // 当前站点所有角色
        $item_name = $this->manager->getChildren($rootRole);
        
        return $this->render('update', [
        	'model' => $model,
        	'item_name' => $item_name,//所有角色
        	'userName' => $user->username,
        	'user_data' =>$user_data,//当前用户名
        ]);
        
        
    }

    /**
     * Deletes an existing Assignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionDelete()
    {
        
        $user_id = Yii::$app->request->post('user_id'); 
        $roles = rtrim(Yii::$app->request->post('rolelist'),'|');
        
        $lists = explode('|',$roles);
        
        if(is_array($lists)){
            
            foreach ($lists as $v){                
                $roleModel = $this->manager->getRole($v);
                if($roleModel){
                    $result =  $this->manager->revoke($roleModel,$user_id);                    
                }
            }
            $spotRoleName = explode('_', $lists[0])[0];
            $spotModel = $this->manager->getRole(Yii::getAlias('@spotPrefix').$spotRoleName);//删除该用户的站点角色
            $this->manager->revoke($spotModel, $user_id);
            return "删除成功";
        }                    
        return  "删除失败，请确认用户是否拥有该角色";
        //return $this->redirect(['index']);
    }

    /**
     * Finds the Assignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $item_name
     * @param string $user_id
     * @return Assignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_id)
    {
       
        if (($model = User::find()->select(['user_id'])->where(['user_id' => $user_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
