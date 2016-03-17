<?php

namespace app\modules\module\models;

use Yii;
use yii\db\Query;
use app\modules\module\models\Menu;
use app\common\Common;
/**
 * This is the model class for table "{{%title}}".
 *
 * @property string $id
 * @property string $title
 * @property string $parent_id
 * @property integer $status
 * @property integer $sort
 * @property string icon_url
 * @property Menu[] $menus
 */
class Title extends \app\common\base\BaseActiveRecord
{
    public $baseUploadPath;
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
            [['module_name','module_description','status'],'required'],
            [['parent_id', 'status','sort'], 'integer'],
            [['module_name'], 'string', 'max' => 64],
            [['module_description','icon_url'],'string','max' => 255],
            ['icon_url','file','extensions' => 'jpg,png,jpeg,gif']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_description' => '模块名称',
			'module_name' => '模块简称',
			'menus' => '菜单列表',
		    'status' => '状态(渲染)',
		    'icon_url' => '上传模块图标',
            'sort' => '排序'
        ];
    }
    public static $getStatus = [
          '1' => '是',
          '0' => '否'  
        ];
    
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
        $query->from(['t' => self::tableName()])->select(['t.id as title_id', 't.module_name', 't.module_description','m.menu_url','m.description']);
        $query->leftJoin(['m' => Menu::tableName()],'{{m}}.parent_id = {{t}}.id');
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
    public function upload()
    {
         
        if ($this->validate()) {
            $this->baseUploadPath = 'uploads/'.date('Y-m-d',time());
            Common::mkdir($this->baseUploadPath);
            $imgName = md5_file($this->icon_url->tempName) . '.' . $this->icon_url->extension;
            $fullUrl = $this->baseUploadPath.'/'.$imgName;
            $this->icon_url->saveAs($fullUrl);
            return $fullUrl;
        }else {
            return false;
        }
    }
    
}
