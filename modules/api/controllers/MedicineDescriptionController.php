<?php
namespace app\modules\api\controllers;
use Yii;
use app\modules\api\controllers\CommonController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use \yii\web\Response;
use app\modules\medicine\models\MedicineItem;
use yii\db\ActiveQuery;
use app\modules\medicine\models\MedicineDescription;
use yii\web\NotFoundHttpException;
use yii\base\Object;
class MedicineDescriptionController extends CommonController
{

    public function behaviors()
    {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => [
                        'get'
                    ],
                    'view' => ['post']
                ]
            ]
        ];
        return ArrayHelper::merge($current, $parent);
    }
    /**
     * item
     * @param int $id 用药指南id 
     * @return string title 标题描述
     * @return array content 渲染的视图内容
     * @throws NotFoundHttpException
     * @desc 返回用药指南关联指征的详情页
     */
    public function actionItem($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $medicineItemList = MedicineItem::find()->select(['id','indication'])->where(['medicine_description_id' => $id])->asArray()->all();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "预览-用药指南",
                'content'=>$this->renderAjax('@medicineItemViewPath', [
                    'model' => $model,
                    'medicineModel' => new MedicineItem(),
                    'medicineItemList' => $medicineItemList
                ]),
            ];
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    /**
     * view
     * @param int $id 用药指南-使用指征id 
     * 
     * @return int errorCode 错误代码(0-成功,1001-参数错误,1004-记录不存在,404-页面不存在)
     * @return string msg 错误提示信息
     * @return array list 对应的指证记录信息
     * @throws NotFoundHttpException
     * @desc 根据使用指征id 获取对应的记录信息
     * 
     */
    public function actionView()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($request->isAjax){
            $id = Yii::$app->request->post('id');
            if(!$id){
                $this->result['errorCode'] = 1001;//参数错误
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $medicineItemList = MedicineItem::find()->select(['id','used','renal_description','liver_description','contraindication','side_effect','pregnant_woman','breast','careful'])->where(['id' => $id])->asArray()->one();
            if(!$medicineItemList){
                $this->result['errorCode'] = 1004;//记录不存在
                $this->result['msg'] = '记录不存在';
                return $this->result;
            }
            $this->result['list'] = $medicineItemList;
            return $this->result;
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    protected  function findModel($id) {
        $query = new ActiveQuery(MedicineItem::className());
        $query->from(['a' => MedicineDescription::tableName()]);
        $query->select(['a.id','a.chinese_name','a.english_name','b.indication','b.used','b.renal_description','b.liver_description','b.contraindication','b.side_effect','b.pregnant_woman','b.breast','b.careful']);
        $query->leftJoin(['b' => MedicineItem::tableName()],'{{a}}.id = {{b}}.medicine_description_id');
        $query->where(['a.id' => $id]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}