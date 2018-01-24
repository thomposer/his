<?php

namespace app\modules\module\controllers;

use Yii;
use app\modules\module\models\Menu;
use app\modules\module\models\Title;
use app\modules\module\models\search\MenuSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\spot\models\Spot;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends BaseController
{

    public function behaviors()
    {
        $parent =  parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'flush' => ['post']
                ],
            ],
        ];
        
       return ArrayHelper::merge($current, $parent);
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $titleList = Title::selectAll();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'titleList' => $titleList
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $data = Title::find()->select(['module_description'])->where(['id' => $model->parent_id])->asArray()->one();
        return $this->render('view', [
            'model' => $model,
            'parent_description' => $data['module_description']
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->sort = time();
            $model->save();
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        }
        $titleList = Title::selectAll();
        return $this->render('create', [
                'model' => $model,
                'titleList' => $titleList
            ]);
        
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        }

        $titleList = Title::selectAll();
        return $this->render('update', [
                'model' => $model,
                'titleList' => $titleList
            ]);
        
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $this->findModel($id)->delete();
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }
    
    public function actionSearch(){
        
        $description = Yii::$app->request->get('description');
        $menu_url = Menu::searchMenu($description);
        if(!$menu_url){
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $absolute_url = Url::to([$menu_url['menu_url']]);
        return $this->redirect($absolute_url);
    }
    public function actionFlush(){
//         $result = Yii::$app->cache->flush();
        /* 获取系统所有的用户id以及对应机构id */
        $query = new Query();
        $query->from(['a' => User::tableName()]);
        $query->select(['a.id','b.spot']);
        $query->leftJoin(['b' => Spot::tableName()],'{{a}}.spot_id = {{b}}.id');
        $userList = $query->all();
        
        if(!empty($userList)){
            foreach ($userList as $v){
                //获取该机构下用户所属所有诊所列表的key值
                $spotListCache = Yii::getAlias('@spotList') . $v['spot'] . '_' . $v['id'];
                $commonRoleMenuCache = Yii::getAlias('@commonRoleMenu') . $v['id'] . '_' . $v['spot']; //普通用户菜单url缓存key
                $commonAllPermCache = Yii::getAlias('@commonAllPerm') . $v['id'] . '_' . $v['spot']; //普通用户全部权限列表缓存key
                Yii::$app->cache->delete($spotListCache);//消除用户所属诊所列表的缓存
                Yii::$app->cache->delete($commonRoleMenuCache);//消除普通用户菜单url缓存
                Yii::$app->cache->delete($commonAllPermCache);//消除普通用户全部权限列表缓存
                
            }
        }
        Yii::$app->db->schema->refresh();
        Yii::$app->cache->delete(Yii::getAlias('@systemMenu'));
        Yii::$app->getSession()->setFlash('success','清除缓存成功');
        $this->redirect(Url::to(['@moduleMenuIndex']));
    }
    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你请求的页面不存在.');
        }
    }
    
    private function removePerm() {
    	
    }
    
  }
