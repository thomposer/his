<?php

namespace app\modules\spot\models;

use Yii;
use app\modules\spot\models\Spot;

class Organization extends Spot
{
	
    public $spot_count;
    public $address;
    public function init(){
        parent::init();
        $this->type = 1;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户简称',
            'template' =>'机构模板',
            'spot' => '机构代码',
            'status' => '状态',
            'spot_name' => '机构名称',
            'contact_iphone' => '负责人手机',
            'contact_name' => '负责人姓名',
            'contact_email' => '负责人邮箱',
            'spot_count' => '诊所数量',
            'address' => '省/市/区',
            'telephone' => '电话',
            'fax_number' => '传真',
            'detail_address' => '街道/详细地址',
            'update_time' => '更新时间',
            'create_time' => '创建时间'
        ];
    }
    public static $getStatus = [
        1 => '正常',
        2 => '已删除'
    ];
    
}
