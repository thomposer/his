<?php

namespace app\modules\overview\controllers;

use Yii;
use app\modules\overview\models\Overview;
use app\modules\overview\models\search\OverviewSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Spot;
/**
 * IndexController implements the CRUD actions for Overview model.
 */
class IndexController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Overview models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OverviewSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $overviewNum=  Overview::getSpotNum();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'overviewNum' => $overviewNum,
        ]);
    }
    public function actionList(){
        $searchModel = new OverviewSearch();
        $dataProvider = $searchModel->spotSearch(Yii::$app->request->queryParams,$this->pageSize);
        $overviewNum=  Overview::getSpotNum();
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'overviewNum' => $overviewNum,
        ]);
    }
    /**
     * Displays a single Overview model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
   
    /**
     * Displays a single Overview model.
     * @param integer $id
     * @return mixed
     */
    public function actionSpotView($id)
    {
    
        return $this->render('spot-view',[
            'model' => $this->findSpotModel($id)
        ]);
    }
     
    public function actionDetail(){
        if (isset($_POST['expandRowKey'])) {
            $dataProvider = $this->findSpotDataProvider($_POST['expandRowKey']);
            return $this->renderPartial('_detail', ['dataProvider'=>$dataProvider]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    protected function findSpotDataProvider($id){
        $query = Overview::find()->select(['id','spot_name','contact_iphone','contact_name','create_time'])->where(['parent_spot' => $id,'status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }
    protected function findSpotModel($id){
        if (($model = Spot::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Finds the Overview model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Overview the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Overview::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
