<?php

/**
 * @property api接口类继承的公共类
 */

namespace app\modules\api\controllers;

use app\modules\spot\models\Spot;
use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\growth\models\Weight;
use app\modules\patient\models\Patient;
use yii\helpers\Html;
use app\modules\growth\models\Height;
use app\modules\growth\models\HeadCircumference;
use app\modules\growth\models\Bmi;
use app\modules\growth\models\ZscoreWeight;
use app\modules\growth\models\ZscoreHeight;
use app\modules\growth\models\ZscoreHeadCircumference;
use app\modules\growth\models\ZscoreBmi;
use app\modules\report\models\Report;
use app\modules\spot\models\SpotConfig;

class GrowthController extends CommonController
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * view
     * @param int $id 患者id
     * @param integer $diagnosisTime 接诊日期
     * @return string title 标题
     * @return array content 渲染的生长曲线视图
     * @return string footer 弹窗按钮
     * @desc 返回该用户所有的就诊记录生长曲线信息
     */
    public function actionView($id, $diagnosisTime = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //获取用户所有就诊记录的身高，体重，头围信息
        $query = new Query();
        $query->from(['a' => Patient::tableName()]);
        $query->select(['a.username', 'a.sex', 'a.birthday', 'a.patient_number', 'recordId' => 'b.id', 'b.status', 'c.diagnosis_time', 'c.heightcm', 'c.weightkg', 'c.head_circumference', 'reportTime' =>'d.create_time']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.id = {{b}}.patient_id');
        $query->leftJoin(['c' => TriageInfo::tableName()], '{{b}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => Report::tableName()],'{{b}}.id = {{d}}.record_id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->parentSpotId]);
        $query->orderBy(['c.diagnosis_time' => SORT_ASC]);
        $result = $query->all();

        if (empty($result)) {
            $result = Patient::find()->select(['username', 'sex', 'birthday', 'patient_number'])->where(['id' => $id])->asArray()->all();
        }
        $spotInfo = Spot::getSpot();
        $date = Patient::dateDiffageTime($result[0]['birthday'], time());
        $result[0]['age'] = Patient::dateDiffage($result[0]['birthday'], time());
        $result[0]['birth'] = date('Y-m-d',$result[0]['birthday']);
        $result[0]['print_sex'] = Patient::$getSex[$result[0]['sex']];

        if ($date['year'] < 6) {//判断是否小于 6岁
            $yearsSex = 1;
            $where = 'age_type = 1 and age % 30 = 0';  // 按天数获取,并且获取 30天的整数数据

            $minMonth = 0;
            $maxMonth = 12 * 5;
        } else {
            $yearsSex = 2;
            $where = [
                'age_type' => 2 //按月龄获取
            ];
            $minMonth = 12 * 5;
            $maxMonth = 12 * 19;
        }
//        $yearsSex = array(1,2);
        $minMonth = array(0,12*5);
        $maxMonth = array(12*5,12*19);
        //百分率数据获取

        $cache = Yii::$app->cache; 
        $growthCacheKey = Yii::getAlias('@growthData').$result[0]['sex'];
        $data = $cache->get($growthCacheKey); 
        if ($data === false) { 
            $weight[] =  Weight::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $weight[] =  Weight::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();
//            var_dump($weight);
            $height[] =  Height::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $height[] =  Height::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();
            $headCircumference[] =  HeadCircumference::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $headCircumference[] =  HeadCircumference::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();
            $bmi[] =  Bmi::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $bmi[] =  Bmi::find()->select(['age','th3','th15','th50','th85','th97'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();
            //Z值评分
            $zscoreWeight[] = ZscoreWeight::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $zscoreWeight[] = ZscoreWeight::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();

            $zscoreHeight[] = ZscoreHeight::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $zscoreHeight[] = ZscoreHeight::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();

            $zscoreHeadCircumference[] = ZscoreHeadCircumference::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $zscoreHeadCircumference[] = ZscoreHeadCircumference::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();

            $zscoreBmi[] = ZscoreBmi::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere('age_type = 1 and age % 30 = 0')->asArray()->all();
            $zscoreBmi[] = ZscoreBmi::find()->select(['age','sd3neg','sd2neg','sd1neg','sd0','sd1','sd2','sd3'])->where(['sex' => $result[0]['sex']])->andWhere(['age_type' => 2])->asArray()->all();
            
            $data = array(
                'weight' => $weight,
                'height' => $height,
                'headCircumference' => $headCircumference,
                'bmi' => $bmi,
                'zscoreWeight' => $zscoreWeight,
                'zscoreHeight' => $zscoreHeight,
                'zscoreHeadCircumference' => $zscoreHeadCircumference,
                'zscoreBmi' => $zscoreBmi,
            );
           $cache->set($growthCacheKey, $data);
        }
        $spotConfig = SpotConfig::getConfig(['logo_img','pub_tel','spot_name','logo_shape']);
        return [
            'title' => "生长曲线",
            'content' => $this->renderAjax('@growthIndexViewPath', [
                'result' => $result,
                'spotInfo' => $spotInfo,
                'diagnosisTime' => $diagnosisTime,
                //百分率
                'weight' => $data['weight'],
                'height' => $data['height'],
                'headCircumference' => $data['headCircumference'],
                'bmi' => $data['bmi'],
                //z值
                'zscoreWeight' => $data['zscoreWeight'],
                'zscoreHeight' => $data['zscoreHeight'],
                'zscoreHeadCircumference' => $data['zscoreHeadCircumference'],
                'zscoreBmi' => $data['zscoreBmi'],

                'yearsSex' => $yearsSex,
                'minMonth' => $minMonth,
                'maxMonth' => $maxMonth,
                'spotConfig' => $spotConfig
            ]),
            'footer' => Html::button('关闭', ['class' => 'btn btn-default btn-form close-btn-style ', 'data-dismiss' => "modal"]) . Html::button('打印', ['class' => 'print btn btn-default btn-form close-btn-style'])
        ];
//         $tringInfo['bmi'] = $tringInfo['heightcm'] ? (sprintf('%.2f', round($tringInfo['weightkg'] / ($height * $height),2))) : (($tringInfo['heightcm'] == null) ? null : 0);

    }

    public function actionGrowth(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $result['child_examination'] = [
            'guidance' => '指导意见',
            'growth' => [
                'result' => '生长评估结果：需随访',
                'data' => [
                    ''
                ],
            ]

        ];
        return $result;
    }

}
