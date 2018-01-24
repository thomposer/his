<?php

namespace app\modules\spot\controllers;

use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\spot\models\Spot;
use Yii;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\search\InspectSearch;
use app\common\base\BaseController;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\InspectItemUnion;
use app\modules\spot\models\InspectItem;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot_set\models\InspectItemUnionClinic;

/**
 * InspectController implements the CRUD actions for Inspect model.
 */
trait InspectTrait
{

    /**
     * Lists all Inspect models.
     * @return mixed
     */
    public function actionInspectIndex() {
        $searchModel = new InspectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $itemList = InspectItem::getItemList();

        $spotNameList = ConfigureClinicUnion::getClinicNameListString($dataProvider->keys, ChargeInfo::$inspectType);
        $spotList = Spot::getSpotList(['status' => 1]);

        return $this->render('inspect/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'item_list' => $itemList,
                    'spotNameList' => $spotNameList,
                    'spotList' => $spotList,
        ]);
    }

    /*
     * 获取检验项目的信息
     */

    public function actionInspectItem() {
        $id = Yii::$app->request->post('id');
        $info = InspectItem::findOne($id);
        $ret = [
            'success' => true,
            'errorCode' => 0,
            'msg' => '',
            'data' => $info
        ];
        exit(\yii\helpers\Json::encode($ret));
    }

    /*
     * 关联检验项目
     */

    public function actionInspectUnion($id) {
        $request = Yii::$app->request;
//        $model = $this->findInspectModel($id);
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $query = new \yii\db\ActiveQuery(InspectItemUnion::className());
        $query->from(['i' => Inspect::tableName()]);
        $query->select([
            'it.id', 'it.item_name', 'it.english_name', 'it.unit', 'it.reference', 'unionId' => 'itu.id'
        ]);
        $query->leftJoin(['itu' => InspectItemUnion::tableName()], '{{i}}.id={{itu}}.inspect_id');
        $query->leftJoin(['it' => InspectItem::tableName()], '{{it}}.id={{itu}}.item_id');
        $query->where(['inspect_id' => $id, 'i.spot_id' => $this->parentSpotId, 'it.status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['itu.id' => SORT_ASC],
                'attributes' => ['itu.id']
            ],
            'pagination' => false
        ]);
        $dataCount = count($dataProvider->models);
        $newRecord = 2;
        if ($dataCount == 0) {
            $itemList = InspectItem::getItemList();
            foreach ($itemList as $key => $value) {
                $itemList[$key]['item_name'] = $value['id'] . '-' . $value['item_name'];
            }
            $select = Html::dropDownList('item_ist', 0, ArrayHelper::map($itemList, 'id', 'item_name'), ['class' => 'form-control item_list select2', 'prompt' => '请输入项目名称', 'style' => 'width:198px;']);
            $models = new \stdClass();
            $models->item_name = $select;
            $models->id = '';
            $models->english_name = '';
            $models->unit = '';
            $models->reference = '';
            $dataProvider->models = [$models];
            $dataProvider->setKeys([4]);
            $newRecord = 1;
        }
        $param = Yii::$app->request->post();
        /*
         *   Process for ajax request
         */
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isGet) {
            return [
                'title' => "关联项目",
                'content' => $this->renderAjax('inspect/_union', [
                    'dataProvider' => $dataProvider,
                    'inspect_id' => $id,
                    'newRecord' => $newRecord
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('保存', ['class' => 'btn btn-default union_submit btn-form', 'type' => "button", 'id' => 'union_submit'])
            ];
        } else if (isset($param['item_id']) && !empty($param['item_id'])) {
            //先全部删掉
//            InspectItemUnion::deleteAll(['inspect_id' => $param['inspect_id']]);
            $data = [];
            if (!empty($param['item_id'])) {
                $itemId = $param['item_id'];
                $delete = $param['deleted'];
                $newRecord = $param['newRecord'];
                $unionId = $param['unionId'];
                foreach ($itemId as $key => $val) {
                    if ($newRecord[$key] == 1) {//新增
                        $newRow[] = [$param['inspect_id'], $val, time(), time()];
                    }
                    if ($newRecord[$key] == 2 && $delete[$key] == 1) {//删除的数据
                        if ($unionId[$key]) {
                            InspectItemUnion::deleteAll(['id' => $unionId[$key]]);
                            //删除掉诊所下  关联的检验项目
                            InspectItemUnionClinic::deleteAll(['inspect_id' => $param['inspect_id'], 'item_id' => $val]);
                        }
                    }
                }
            }
            !empty($newRow) && Yii::$app->db->createCommand()->batchInsert(InspectItemUnion::tableName(), ['inspect_id', 'item_id', 'create_time', 'update_time'], $newRow)->execute();
            Yii::$app->getSession()->setFlash('success', '保存成功');
            $this->redirect(['inspect-index']);
//            return [
//                'forceReload' => '#crud-datatable-pjax',
//                'forceClose' => true
//            ];
        } else {
            return [
                'title' => "Update InspectItem #" . $id,
                'content' => $this->renderAjax('inspect/update', [
                    'model' => $model,
                ]),
                'footer' => Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"]) .
                Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
            ];
        }
    }

    /**
     * Displays a single Inspect model.
     * @param string $id
     * @return mixed
     */
    public function actionInspectView($id) {
        return $this->render('inspect/view', [
                    'model' => $this->findInspectModel($id),
        ]);
    }

    /**
     * Creates a new Inspect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionInspectCreate() {
        $model = new Inspect();
        $model->scenario='unionSpotId';
        $spotList=Spot::getSpotList(['status'=>1]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans=Yii::$app->db->beginTransaction();
            try{
                    $model->save();
                    if(!ConfigureClinicUnion::saveInfo($model->id,$model->unionSpotId,ChargeInfo::$inspectType)){
                        $dbTrans->rollBack();
                        Yii::error('保存实验室配置适用诊所失败', 'spot-inspectlist-create');
                        Yii::$app->getSession()->setFlash('error','保存失败');
                        return $this->redirect(['inspect-index']);
                    }
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['inspect-index']);
            }catch (\yii\db\Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-inspectlist-create');
            }


        } else {
            return $this->render('inspect/create', [
                        'model' => $model,
                        'spotList'=>$spotList,
            ]);
        }
    }

    /**
     * Updates an existing Inspect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionInspectUpdate($id) {
        $model = $this->findInspectModel($id);

        $spotIdList = ConfigureClinicUnion::getClinicIdList(['configure_id'=>$id,'type'=>ChargeInfo::$inspectType]);
        if(!empty($spotIdList)){
            $model->unionSpotId = array_column($spotIdList,'spot_id');
        }
        $model->scenario='unionSpotId';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try{
                $model->save();
                if(!ConfigureClinicUnion::saveInfo($id,$model->unionSpotId,ChargeInfo::$inspectType)){
                    $dbTrans->rollBack();
                    Yii::error('保存实验室配置适用诊所失败', 'spot-inspectlist-create');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['inspect-index']);
                }
                $dbTrans->commit();

                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['inspect-index']);
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-inspectlist-update');
            }

        } else {
            $spotList=Spot::getSpotList(['status'=>1]);
            return $this->render('inspect/update', [
                        'model' => $model,
                        'spotList'=>$spotList,
            ]);
        }
    }

    /**
     * Deletes an existing Inspect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionInspectDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = $this->findInspectModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            //停用/启用 诊所下所有的相关的检验医嘱
            InspectClinic::updateAll(['status' => $model->status], ['inspect_id' => $id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['inspect-index']);
        }
    }

    /**
     * Finds the Inspect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Inspect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInspectModel($id) {
        if (($model = Inspect::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }

    protected function getTemplate() {
        $itemList = InspectItem::getItemList();
        $itemList = InspectItem::getItemList();
        $select = Html::dropDownList('item_ist', 0, $itemList, ['class' => 'form-control item_list', 'prompt' => '请输入项目名称']);
        $template = <<<TEMPLATE
                <tr data-key="1">
                    <td>
                         $select 
                        <input type="hidden" class="checkitemid" value="" name="item_id[]">
                    </td>
                    <td class="item-english_name"></td>
                    <td class="item-unit"></td>
                    <td class="item-ref"></td>
                    <td>                                        
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn-from-delete-add btn clinic-delete">
                                <i class="fa fa-minus"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn-from-delete-add btn clinic-add">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </td>
                </tr>
TEMPLATE;
        return $template;
    }

}
