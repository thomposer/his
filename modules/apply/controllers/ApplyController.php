<?php

namespace app\modules\apply\controllers;

use Yii;
use app\modules\apply\models\ApplyPermissionList;
use app\modules\apply\models\search\ApplyPermissionListSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\Spot;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use app\modules\rbac\models\AssignmentForm;
use yii\helpers\Url;
use app\common\Common;

/**
 * ApplyController implements the CRUD actions for ApplyPermissionList model.
 */
class ApplyController extends BaseController
{

    public function beforeAction($action){
        
       
        Yii::$app->session->remove('spot');
        return parent::beforeAction($action);
    }
    
    /**
     * 用户申请站点角色记录列表
     * Lists all ApplyPermissionList models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $searchModel = new ApplyPermissionListSearch();
        $where = array(
            'user_id' =>$this->userId,
        );
        $field = ['id','wxname','item_name_description','status','reason','apply_persons','created_at','updated_at'];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,$where,$field);
        
        $data = '';
        $spot = Spot::find()->select(['spot_name','spot'])->all();//获取所有站点信息
        if($spot){
            //开始整合，匹配对应站点的所有角色
            foreach ($spot as $key => $v){
               $item_name = $this->manager->getChildren($v->spot.'_roles'); //获取站点对应的所有角色 
               if($item_name){
                   foreach ($item_name as $k => $res){
                           $data[] = array(
                               'name' => $res->name,
                               'description' => $res->description,
                               'spot' => $v->spot_name
                           ); 
                   }
               }
            }
        }
      
//         $applyPersons = $this->userId.'('.Yii::$app->user->identity->userInfo->getUserName().')';
        $applyPersons = $this->userId;
        $locals = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,          
            'spotList' => $spot,
            'roleList' => $data,
                        
        ];
        return $this->render('index',$locals);
    }

    /**
     * Displays a single ApplyPermissionList model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 申请站点权限
     * Creates a new ApplyPermissionList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        $model = new ApplyPermissionList();
        
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            
            $spot = explode('|', $model->spot);
            
            $result = $model::find()->select('id')->where(['user_id' => Yii::$app->user->identity->userInfo->getUserId(),'spot' => $spot[0]])->all();//判断用户是否重复申请相同角色
           
            if($result){
                Common::showInfo('你已经申请过该站点权限了哦',Url::to(['@applyApplyIndex']));
                
            }
           
            $type = Yii::$app->user->identity->userInfo->getType();        
            $loginType = '';
            switch ($type){
                case 'OA' :
                    $loginType = 1;
                    break;
                case 'QQ' :
                    $loginType= 2;
                    break;
            }
            $applyPersons = '';
            $datas = AssignmentForm::getUser_id($spot[0].'_roles_system',4);
            if($datas){
                foreach ($datas as $v){
                    $applyPersons .= $v['user_id'].',';
                }
            }
            $model->spot = $spot[0];
            $model->wxname = $spot[1];
            $model->item_name = '';
            $model->item_name_description = '';
            $model->status = 0;       
            $model->user_id = Yii::$app->user->identity->userInfo->getUserId();
            $model->username = Yii::$app->user->identity->userInfo->getUsername();
            $model->login_type = $loginType;
            $model->apply_persons = rtrim($applyPersons,',');//默认当前处理人
            $model->created_at = time();
            $model->updated_at = time();
            
            if($model->validate() && $model->save()){
                
                Common::showInfo('权限申请成功，请等待批准',Url::to(['@applyApplyIndex']));
            }
           
        }
       
       $spotInfo = Spot::find()->select(['spot_name','spot'])->asArray()->all();//获取所有站点信息
       foreach ($spotInfo as $key => $v){
           $spotInfo[$key]['spot_name'] = $v['spot_name'].' ['.$v['spot'].']';
           $spotInfo[$key]['spot'] = $v['spot'].'|'.$v['spot_name'];
       }
      
       $result = [
                'model' => $model,
                'spot' => $spotInfo,
               
            ];
       
        return $this->render('create',$result);
        
    }

    /**
     * Updates an existing ApplyPermissionList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    public function actionWxcreate(){
        
        return $this->redirect(['/spot/sites/create']);
    }
    

    /**
     * Finds the ApplyPermissionList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ApplyPermissionList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApplyPermissionList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
