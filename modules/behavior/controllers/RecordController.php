<?php

namespace app\modules\behavior\controllers;

use Yii;
use app\modules\behavior\models\BehaviorRecord;
use app\modules\behavior\models\search\BehaviorRecordSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\Spot;
use app\modules\module\models\Title;
use yii\helpers\ArrayHelper;
use app\modules\module\models\Menu;
use app\common\Common;
use yii\web\Response;
use app\modules\user\models\User;

/**
 * RecordController implements the CRUD actions for BehaviorRecord model.
 */
class RecordController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                   'delete' => ['post'],
                   'deletemonth' => ['get']
                ],
            ],
        ];
    }

    /**
     * Lists all BehaviorRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BehaviorRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        
		$spots = Spot::find()->select(['id', 'spot_name'])->asArray()->all();
		$spotList = ArrayHelper::map($spots, 'id', 'spot_name');
		
		$modules = Title::find()->select(['module_name', 'module_description'])->all();
		$moduleList = ArrayHelper::map($modules, 'module_name', 'module_description');
		
		$actionList = array();
		// 如果条件中包括module值，则只搜索该module下的菜单列表，否则就全部查询出来
		if ($searchModel->module) {
			$actions = Title::find()->select(['id'])
				->where(['module_name' => $searchModel->module])->one()
				->getAllMenus()
				->select(['menu_url', 'description'])->asArray()->all();
			$actionList = ArrayHelper::map($actions, 'menu_url',  'description');
		} else {
			$actions = Menu::find()->select(['menu_url', 'description'])->asArray()->all();
			$actionList = ArrayHelper::map($actions, 'menu_url',  'description');
		}
				
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        	'spotList' => $spotList,
        	'moduleList' => $moduleList,
        	'actionList' => $actionList,
        ]);
    }

    /**
     * Displays a single BehaviorRecord model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
    	$spots = Spot::find()->select(['id', 'spot_name'])->asArray()->all();
    	$spotList = ArrayHelper::map($spots, 'id', 'spot_name');
    	
    	$modules = Title::find()->select(['module_name', 'module_description'])->all();
    	$moduleList = ArrayHelper::map($modules, 'module_name', 'module_description');
    	
    	$actions = Menu::find()->select(['menu_url', 'description'])->asArray()->all();
    	$actionList = ArrayHelper::map($actions, 'menu_url',  'description');
    	$model = $this->findModel($id);
    	$username = User::find()->select(['username'])->where(['id' => $model->user_id])->asArray()->one();
        return $this->render('view', [
            'model' => $this->findModel($id),
        	'spotList' => $spotList,
        	'moduleList' => $moduleList,
        	'actionList' => $actionList,
            'username' => $username['username']
        ]);
    }

    /**
     * Finds the BehaviorRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BehaviorRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BehaviorRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }
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
    /**
     * 批量删除一个月前的记录
     */
    public function actionDeleteMonth()
    {
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $lastMonth = mktime(0, 0, 0, date('m')-1, date('d'), date('Y'));
            $db = Yii::$app->recordDb;
            $db->createCommand()->delete(BehaviorRecord::tableName(),'operation_time < :lastMonth', [':lastMonth' => $lastMonth])->execute();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    	
//     	BehaviorRecord::deleteAll('operation_time < :lastMonth', [':lastMonth' => $lastMonth]);
//         Yii::$app->getSession()->setFlash('success','删除成功');
//     	return $this->redirect(['index']);
    }
    
}
