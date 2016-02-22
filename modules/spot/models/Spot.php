<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%spot}}".
 *
 * @property integer $id
 * @property string $spot
 * @property integer $render
 * @property string $user_id
 * @property string $spot_name
 * @property string $template
 */
class Spot extends \yii\db\ActiveRecord
{
	
	const HAS_RENDER = 1;
	const NO_REDNER = 0;
	public static $RENDER_STATUS = array(0, 1);
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spot}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template', 'spot','spot_name'], 'required'],
            [['template','user_id','spot_name'], 'string'],
            [['spot'], 'string', 'max' => 20,'min' => 3,'tooLong' => '请输入长度为3-20个字符','tooShort' => '请输入长度为3-20个字符'],
            [['spot_name','user_id'], 'string', 'max' => 50],                                  
            [['spot'], 'unique', 'message' => '已存在公众号的代号'],
            [['spot'],'match','pattern' => '/^[a-zA-Z][a-zA-Z0-9_]{2,19}$/','message' => '必须以字母开头，不能输入中文'],//字母开头，允许3-20字节，允许字母数字下划线
            [['render'],'integer'],
            [['user_id'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户简称',
            'template' =>'初始模板',
            'spot' => '站点代码',
            'render' => '状态',
            'spot_name' => '站点名称'
        ];
    }
    /**
     * 获取当前站点对象
     * @return Ambigous <\yii\db\ActiveRecord, multitype:, NULL>
     */
    public static function getSpot() {
        $spotCode = Yii::$app->session->get('spot');
        return Spot::find()->where(['spot' => $spotCode])->one();
    }
    
    
}
