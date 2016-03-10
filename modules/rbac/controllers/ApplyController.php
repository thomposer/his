<?php
namespace app\modules\rbac\controllers;

use Yii;
use app\modules\apply\models\ApplyPermissionList;
use app\modules\apply\models\search\ApplyPermissionListSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\common\base\BaseController;
use app\modules\user\models\User;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\Spot;
use yii\base\Object;
use app\common\Common;
use yii\helpers\Url;

class ApplyController extends BaseController
{

    public $info;

    public $status = array(
        '' => '全部',
        0 => '待审核',
        1 => '已通过',
        2 => '已冻结'
    );

    public function behaviors()
    {
        $parent = parent::behaviors();
        $current = [
            'Verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ]
                ]
            ]
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * 展示所有申请记录列表
     * Lists all ApplyPermissionList models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = '';
        $searchModel = new ApplyPermissionListSearch();
        $where = 1;
        $field = ['id','username','item_name_description','reason','status','spot_name'];
        $systemsRole = $this->manager->checkAccess($this->userInfo->user_id, Yii::getAlias('@systemPermission'));
        if (! $systemsRole) {
            $where = [
                'spot' => $this->wxcode
            ];
            unset($field[5]);
        }
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize, $where,$field);
        
        $spot = Spot::find()->select(['spot_name','spot'])->where($where)->all(); // 获取所有站点信息
                   
        // 开始整合，匹配对应站点的所有角色
        if ($spot) {
            foreach ($spot as $key => $v) {
                $item_name = $this->manager->getChildren($v->spot . '_roles'); // 获取站点对应的所有角色
                if ($item_name) {
                    
                    foreach ($item_name as $k => $res) {
                        
                        $data[] = array(
                            'name' => $res->name,
                            'description' => $res->description,
                            'spot' => $v->spot_name
                        );
                    }
                }
            }
        }
        $locals = [
            'searchModel' => $searchModel,
            'systemsRole' => $systemsRole,
            'dataProvider' => $dataProvider,
            'spotList' => $spot,
            'roleList' => $data,
            'status' => $this->status
        ];
        return $this->render('index', $locals);
    }
    public function actionCreate(){
        
        $model = new ApplyPermissionList();
        $childrens = $this->manager->getChildren($this->wxcode . '_roles');
        $item_data = '';
        foreach ($childrens as $key => $v) {
            //该站点下所有的角色
            $item_data[$key]['name'] = $v->name;
            $item_data[$key]['description'] = $v->description;
        }
        $userData = User::getUserData();
        $model->scenario = 'create';
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $checkRecord = ApplyPermissionList::find()->select(['id'])->where(['user_id' => $model->user_id,'spot' => $this->wxcode])->asArray()->one();
            if($checkRecord){
                Common::showInfo('该记录已经存在',Url::to(['@rbacApplyIndex']));
            }
            $permsCheck = array();
            foreach ($model->item_data as $v) {
                $permsCheck[$v] = true;// 用户要添加的角色
                $item_name_description .= $this->manager->getRole($v)->description.',';
            }
            if($model->status == 1){
                foreach ($childrens as $perm) {
                        $permName = $perm->name;
                        
                        $role = $this->manager->getRole($permName);
                        // 添加选中的角色
                        if (isset($permsCheck[$permName])) {
                            // 没有则添加
                            if (! $this->manager->getAssignment($permName, $model->user_id)) {
                                $this->manager->assign($role, $model->user_id);
                            }
                        } else {
                            // 删除未选中的角色
                            $this->manager->revoke($role, $model->user_id);
                        }
                    }
            }
            
            $username = User::find()->select(['username'])->where(['user_id' => $model->user_id])->asArray()->one();
            $model->username = $username['username'];
            $model->item_name = implode(',', $model->item_data);
            $model->item_name_description = rtrim($item_name_description,',');
            $model->created_time = time();
            $model->spot_name = Yii::$app->session->get('spot_name');
            $model->apply_persons = $this->userInfo->username;
            $model->spot = $this->wxcode;
            $model->save();
            Common::showInfo('添加成功', 'index.html');
            
        }
        return $this->render('create', [
            'model' => $model,
            'item_name' => $item_data,
            'userData' => $userData
        ]);
    }
    /**
     * 批准用户的权限申请
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->scenario = 'update';
       
        $childrens = $this->manager->getChildren($model->spot . '_roles');
        $item_data = '';
        foreach ($childrens as $key => $v) {
            //该站点下所有的角色
            $item_data[$key]['name'] = $v->name;
            $item_data[$key]['description'] = $v->description;
        }
        $roles = explode(',', $model->item_name);
        foreach ($roles as $key => $v){
            
            $model->item_data[] = $v;//之前用户已经选中的角色
        }
        $item_name_description = null;
        $item_name_datas = null;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $permsCheck = array();
            foreach ($model->item_data as $v) {
                $item_name_description .= $this->manager->getRole($v)->description . ',';
                $permsCheck[$v] = true;// 用户要添加的角色
            }
            $model->item_name = implode(',', $model->item_data);
            $model->item_name_description = rtrim($item_name_description, ',');
            $model->apply_persons = $this->userInfo->username;
            $model->updated_time = time();
            $rows = $model->save();
            if ($rows && $model->status == 1) {
                foreach ($childrens as $perm) {
                    $permName = $perm->name;
                    
                    $role = $this->manager->getRole($permName);
                    // 添加选中的角色
                    if (isset($permsCheck[$permName])) {
                        // 没有则添加
                        if (! $this->manager->getAssignment($permName, $model->user_id)) {
                            $this->manager->assign($role, $model->user_id);
                        }
                    } else {
                        // 删除未选中的角色
                        $this->manager->revoke($role, $model->user_id);
                    }
                }
            }
            return Common::showInfo('修改成功', Url::to(['@rbacApplyIndex']));
        }
        
        
        return $this->render('update', [
            'model' => $model,
            'item_name' => $item_data
        ]);
    }

    /**
     * 冻结用户的权限申请
     */
    public function actionForbidden($id)
    {
        $result = $this->findModel($id);
        
        $result->status = 2;
        $rows = $result->save();
        if (! $rows) {
            
            $this->showInfo("审批失败，请确认用户的状态是否为已批准状态");
        }
        $this->showInfo('冻结成功', 'index.html');
    }

    /**
     * Deletes an existing ApplyPermissionList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id            
     * @return mixed 删除权限申请记录
     */
    public function actionDelete($id)
    {
        $result = $this->findModel($id);
        if ($result) {
            if ($result->item_name) {
                $roles = explode(',', $result->item_name);
                foreach ($roles as $v) {
                    $roleModel = $this->manager->getRole($v);
                    if ($roleModel) {
                        $this->manager->revoke($roleModel, $result->user_id); // 逐个删除对应角色
                    }
                }
            }
            $result->delete();
            
            Common::showInfo("删除成功");
        }
        Common::showInfo("删除失败，请确认该权限申请记录是否存在");
    }

    /**
     * Finds the ApplyPermissionList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id            
     * @return ApplyPermissionList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApplyPermissionList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('该权限申请纪录不存在，请重新选择.');
        }
    }
}