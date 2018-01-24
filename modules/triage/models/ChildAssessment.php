<?php

namespace app\modules\triage\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%child_assessment}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $score
 * @property string $assesment_time
 * @property string $remark
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 */
class ChildAssessment extends \app\common\base\BaseActiveRecord
{

    public $fallScore; //跌倒评分
    public $fallTime; //跌倒评估时间
    public $fallRemark; //跌倒备注

    /**
     * @inheritdoc
     */

    public static function tableName() {
        return '{{%child_assessment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id', 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊流水ID',
            'score' => '疼痛评分(0-10)',
            'fallScore' => '跌倒评分(HDFS 6-20)',
            'assesment_time' => '评估时间',
            'fallTime' => '评估时间',
            'remark' => '备注',
            'fallRemark' => '备注',
            'type' => '评估类型[1-疼痛 2-跌倒]',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public static function findModel($id) {
        $model = self::findOne(['id' => $id, 'spot_id' => self::$staticSpotId]);
        if (!$model || is_null($model)) {
            return new self();
        } else {
            return $model;
        }
    }

    /**
     * 
     * @param type $recordId 就诊流水ID
     * @param type $type 类型 1/疼痛 2/跌倒
     */
    public static function getScore($recordId) {
        $data = self::find()->select(['id', 'record_id', 'score', 'assesment_time', 'remark', 'type'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $recordId])->asArray()->all();
        $res = [1 => [], 2 => []];
        if ($data) {
            foreach ($data as $val) {
                $res[$val['type']][] = $val;
            }
        }
        return $res;
    }

    /**
     * 
     * @param type $recordId 就诊流水ID
     * @return 获取最新一条的评估 分数
     */
    public static function getLastScore($recordId, $type = 1) {
        $data = self::find()->select(['id', 'score'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $recordId, 'type' => $type])->orderBy(['id' => SORT_DESC])->asArray()->one();
        return $data ? $data['score'] : null;
    }

    /**
     * 
     * @param type $recordId 就诊流水ID
     * @return 获取疼痛/跌倒评估
     */
    public static function getAssessmentByRecord($recordId, $type = 1) {
        $data = self::find()->select(['record_id', 'score', 'assesment_time', 'remark', 'type'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->all();
        $res = [];
        $record = [];
        if (!empty($data)) {
            foreach ($data as $val) {
                $text = [];
                if ($val['type'] == 1) {
                    $text[] = $val['score']!=null ? ($val['score'] >= 4 ? '<span style="color: #ff4b00;">' . $val['score'] . '分</span>' : $val['score'] . '分') : '';
                } else {
                    $text[] = $val['score']!=null ? ($val['score'] >= 12 ? '<span style="color: #ff4b00;">' . $val['score'] . '分</span>' : $val['score'] . '分') : '';
                }
                $text[] = $val['assesment_time'] ? date('Y-m-d H:i', $val['assesment_time']) : '';
                $text[] = $val['remark'] ? Html::encode($val['remark']) : '';
                $textStr = implode(',', array_filter($text));
                if ($textStr) {
                    if (isset($res[$val['record_id']]) && isset($res[$val['record_id']][$val['type']])) {
                        $res[$val['record_id']][$val['type']] = $res[$val['record_id']][$val['type']] . $textStr . '<br>';
                    } else {
                        $res[$val['record_id']][$val['type']] = $textStr . '<br>';
                    }
                }
            }
        }
        return $res;
    }

}
