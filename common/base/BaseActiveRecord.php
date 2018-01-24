<?php

namespace app\common\base;

use yii\db\ActiveRecord;
use Yii;

class BaseActiveRecord extends ActiveRecord {

    
    public $spotId;  //当前诊所ID
    public $parentSpotId; //当前机构ID
    public $userInfo; //用户的user信息;
    public static $staticSpotId;//静态变量，当前诊所id
    public static $staticParentSpotId;//静态变量，当前机构id
    public function init() {
        parent::init();
        $this->userInfo = Yii::$app->user->identity;
        $this->spotId = isset($_COOKIE['spotId'])?$_COOKIE['spotId']:'';
        self::$staticSpotId = $this->spotId;
        $this->parentSpotId = isset($_COOKIE['parentSpotId'])?$_COOKIE['parentSpotId']:'';
        self::$staticParentSpotId = $this->parentSpotId;
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
    public function beforeSave($insert) {
        //判断当前表中是否相应更新时间字段
        if ($insert) {
            $this->create_time = time();
        }
        $this->update_time = time();
        return parent::beforeSave($insert);
    }
    public static $getStatus = [
        '1' => '正常',
        '2' => '停用'
    ];

    /**
     * 
     * @param 收费状态 $status
     * @return Ambigous <multitype:, multitype:string > 返回class收费数组样式
     */
    public static function getChargeStatusOptions($status){
        $options = [];
        if($status == 1){
            $options = [
                'class' => 'fa fa-dollar charge_success',
                'title' => '已收费',
                'aria-label' => '已收费',
                'data-toggle' => 'tooltip'
            ];
        }else if($status == 2){
            $options = [
                'class' => 'fa fa-dollar charge_refund',
                'title' => '已退费',
                'aria-label' => '已退费',
                'data-toggle' => 'tooltip'
            ];
        }
        return $options;
    }
    /**
     * 
     * @param 执行状态 $status
     * @return string 根据状态返回颜色
     */
    public static function getStatusColor($status){
        if($status == 1){
            $color = '#76A6EF';//蓝色
        }else if($status == 2){
            $color = '#95CA20';//绿色
        }else if($status == 3){
            $color = '#FF5000';//红色
        }
        return $color;
    }
}
