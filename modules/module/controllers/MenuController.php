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
use yii\base\Object;
use app\common\Common;
use yii\helpers\Url;

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
        $titleList = (new Title())->selectAll();
        
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
            return $this->redirect(['view', 'id' => $model->id]);
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
            return $this->redirect(['view','id' => $model->id]);
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
        $this->findModel($id)->delete();
        Common::showInfo("删除成功");
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
