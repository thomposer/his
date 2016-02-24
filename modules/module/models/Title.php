<?php

namespace app\modules\module\models;

use Yii;
use yii\base\Object;
use yii\db\Query;
use app\modules\module\models\Menu;
/**
 * This is the model class for table "{{%title}}".
 *
 * @property string $id
 * @property string $title
 * @property string $parent_id
 * @property integer $status
 * @property integer $sort
 * @property Menu[] $menus
 */
class Title extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%title}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'status','sort'], 'integer'],
            [['module_name'], 'string', 'max' => 64],
            [['module_description'],'string','max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_name' => 'module_name',
            'module_description' => 'module_description',
            'parent_id' => 'Parent ID',
            'status' => 'Status',
            'sort' => '排序'
        ];
    }
    /**
     * 查找所属模块的列表
     */
    public static function selectAll(){
        return self::find()->select(['module_description','id'])->asArray()->all();
    }
    
    /**
     * 获取站点对应模块的菜单
     * @return \yii\db\ActiveQuery
     */
    public static function getMenus($spot = 0)
    {
        $data = '';
        $query = new Query();
        $query->from('gzh_title as t')->select(['t.id as title_id', 't.module_name', 't.module_description','m.menu_url','m.description']);
        $query->join('LEFT JOIN','gzh_menu as m','m.parent_id = t.id');
        $query->where(['t.status' => 1,'m.type' => 1,'m.status' => 1]);
        $query->orderBy(['t.sort'=>SORT_DESC]);
        $result = $query->all();
        foreach ($result as $key => $v){
            $data[$v['title_id']]['module_description'] = $v['module_description'];
            $data[$v['title_id']]['module_name'] = $v['module_name'];
            unset($v['module_description']);
            unset($v['module_name']);
            $data[$v['title_id']]['children'][] = $v;
           
        }
        return $data;
        
        //return $this->hasMany(Menu::className(), ['parent_id' => 'id']);
    }
    
    public function getAllMenus() {
    	return $this->hasMany(Menu::className(), ['parent_id' => 'id']);
    }
}
