<?php

namespace app\modules\module\models;

use Yii;
use yii\db\Query;
/**
 * This is the model class for table "{{%menu}}".
 *
 * @property string $id
 * @property string $menu_url
 * @property integer $type
 * @property string $description
 * @property string $parent_id
 * @property integer $status
 * @property integer $title
 * @property Title $parent
 * @property integer sort
 */
class Menu extends \app\common\base\BaseActiveRecord
{
    public $module_description;//模块名称
    public static  $left_menu = array(
        '0' => '不渲染',
        '1' => '渲染'
    );
    public static  $menu_status =  array(
        '0' => '禁用',
        '1' => '启用'
    );
    public static $color = array(
         '0' => 'text-muted',
         '1' => 'text-success',
        
    );
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_url', 'description', 'parent_id'], 'required'],
            [['type', 'parent_id', 'status','role_type','sort'], 'integer'],
            [['menu_url', 'description'], 'string', 'max' => 255],
            [['menu_url'],'match','pattern' => '/^[\/][a-zA-Z0-9_\/]{3,34}$/','message' => '格式错误，正确格式应为：/m/c/a'],// 斜线/开头，允许4-35字节，允许字母数字下划线
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_url' => '菜单Url',
            'type' => '左侧菜单',
            'description' => '菜单名称',
            'parent_id' => '所属模块',
            'status' => '状态',
            'role_type' => '所属类型',
            'sort' => '排序',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getParent($menu_url_array)
    {
        $query = (new Query())->from('gzh_menu as m')->select(['t.id as title_id','t.module_description','t.module_name','m.menu_url','m.description'])
        ->join('LEFT JOIN','gzh_title as t','m.parent_id = t.id')
        ->andwhere(['m.menu_url' => $menu_url_array,'t.status' => 1,'m.type' => 1,'m.status' => 1,'m.role_type' => 0])
        ->orderBy(['t.sort'=>SORT_DESC])
        ->all();     
        return $query;
        //return $this->hasOne(Title::className(), ['id' => 'parent_id']);
    }
    public static function searchMenu($description){
        return self::find()->select(['menu_url'])->where(['description' => $description])->asArray()->one();
    }
    public function getTitle()
    {
    	return $this->hasOne(Title::className(), ['id' => 'parent_id']);
    }
}
