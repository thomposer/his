<?php

namespace app\modules\spot_set\controllers;

use Yii;
use app\modules\spot_set\models\Room;
use app\modules\spot_set\models\search\RoomSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\spot_set\models\DoctorRoomUnion;
use yii\db\Exception;

/**
 * RoomController implements the CRUD actions for Room model.
 */
class RoomController extends BaseController {

    public function behaviors() {
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
     * Lists all Room models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new RoomSearch();
        $param = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($param, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Room model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Room model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Room();
        $model->create_time = time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Room model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->update_time = time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Room model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $model = $this->findModel($id);
            $model->status = 3;
            $model->save();
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
     * Finds the Room model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Room the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Room::findOne(['spot_id' => $this->spotId, 'id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }
    
    public function actionDoctorRoomConfig() {
        $searchModel = new RoomSearch();

        $isError = true;
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post('doctorRoomUnionId');
            $saveData = [];
            if ($data) {
                if(is_array($data)){//防止前端修改数据
                    foreach ($data as $doctorId => $roomArr) {
                        if(!is_array($roomArr) || !is_numeric($doctorId) || !$isError){//数据格式有问题
                            $isError = false;
                            break;
                        }
                        foreach ($roomArr as $roomId) {
                            if(!is_numeric($roomId)){
                                $isError = false;
                                break;
                            }
                            $saveData[] = [
                                'spot_id' => $this->spotId,
                                'doctor_id' => $doctorId,
                                'room_id' => $roomId,
                                'create_time' => time(),
                            ];
                        }
                    }
                }else{
                    $isError = false;
                }
            }
            if($isError){//数据格式没问题，保存
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $db = Yii::$app->db;
                    $db->createCommand()->delete(DoctorRoomUnion::tableName(), ['spot_id' => $this->spotId])->execute();
                    $db->createCommand()->batchInsert(DoctorRoomUnion::tableName(), ['spot_id', 'doctor_id', 'room_id', 'create_time'], $saveData)->execute();
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::$app->getSession()->setFlash('error', '保存失败');
                }
            }else{
                Yii::$app->getSession()->setFlash('error', '保存失败');
            }
        }
        $status = 1;
    	$dataProvider = $searchModel->doctorRoomSearch(Yii::$app->request->queryParams, $this->pageSize);    	
        $roomList = Room::getRoomList($status);
     	return $this->render('doctorRoomConfig', [
    		'dataProvider' => $dataProvider,
    		'searchModel' => $searchModel,
                'roomList' => $roomList,
    	]);
    }

}
