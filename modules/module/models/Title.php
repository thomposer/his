<?php

namespace app\modules\module\models;

use Yii;
use yii\db\Query;
use app\modules\module\models\Menu;
use app\common\Common;
use yii\db\Connection;
use app\common\base\BaseActiveRecord;
use app\modules\spot\models\Spot;
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
 * @property integer $update_time
 * @property integer $create_time
 * @property string $type
 */
class Title extends BaseActiveRecord
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
            [['module_name','module_description','status','icon_url'],'required'],
            [['module_name','module_description','status','icon_url'],'trim'],
            [['id','parent_id', 'status','sort','create_time','update_time','type'], 'integer'],
            [['module_name'], 'string', 'max' => 64],
            [['module_description'],'string','max' => 255],
            ['sort','default','value' => 0],
            [['icon_url'],'default','value' => '']
        ];
    }
    public function scenarios(){
        $parent = parent::scenarios();
        $parent['sort'] = ['id','sort'];
        $parent['createChildren'] = ['parent_id','module_name','module_description','status','type','sort'];
        return $parent;
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
            'sort' => '排序',
            'type' => '类型',
            'parent_id' => '父级类名',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    public static $getStatus = [
          '1' => '是',
          '0' => '否'  
        ];
    public static $getType = [
        2 => '二级导航',
        1 => '一级导航',
        0 => '无'
     ];
    /**
     * 查找所属模块的列表
     */
    public static function selectAll(){
        $items = self::find()->select(['module_name','module_description','id','parent_id'])->orderBy(['sort' => SORT_DESC])->indexBy('id')->asArray()->all();
        $tree = array();
        foreach($items as $key => $item){
            $items[$key]['data'] = '首级分类';
            if(isset($items[$item['parent_id']])){
//                 $items[$item['parent_id']]['son'][] = &$items[$item['id']];
                $items[$key]['data'] = $items[$item['parent_id']]['module_description'];
                
            }
            $tree[$key] = &$items[$key];
            
        }
        return $tree;
    }
    
    /**
     * 获取站点对应模块的菜单
     * @return \yii\db\ActiveQuery
     */
    public static function getMenus($isSuperSystem = 0)
    {
        $dependency = new \yii\caching\DbDependency(['sql' => 'select count(1) from '.Menu::tableName()]);
        $data = Yii::$app->cache->get(Yii::getAlias('@systemMenu'));       
        if(!$data){
            $data = [];
            $query = new Query();
            $query->from(['t' => self::tableName()])->select(['t.id as title_id','t.parent_id','t.icon_url','t.type','titleSort'=>'t.sort', 't.module_name', 't.module_description','menuId'=>'m.id','m.role_type','m.sort','m.menu_url','m.description']);
            $query->leftJoin(['m' => Menu::tableName()],'{{m}}.parent_id = {{t}}.id');
            $query->where(['t.status' => 1,'m.type' => 1,'m.status' => 1]);
            $query->orderBy(['t.sort'=>SORT_DESC,'m.sort' => SORT_DESC]);
            $result = $query->all();
            
            foreach ($result as $key => $v){
                if($v['parent_id'] == 0){
                    
                    $data[$v['title_id']]['module_description'] = $v['module_description'];
                    $data[$v['title_id']]['module_name'] = $v['module_name'];
                    $data[$v['title_id']]['type'] = $v['type'];
                    $data[$v['title_id']]['parent_id'] = 0;
                    $data[$v['title_id']]['icon_url'] = $v['icon_url'];
                    $data[$v['title_id']]['sort'] = $v['titleSort'];
                    $data[$v['title_id']]['children'] = [];
                    $titleSort[$v['title_id']] = $v['titleSort'];
                    $k = $v['title_id'];
                }else{
                    $title = Title::find()->select(['module_name','module_description','icon_url','type','sort','parent_id'])->where(['id' => $v['parent_id']])->asArray()->one();
                    $data[$v['parent_id']]['module_description'] = $title['module_description'];
                    $data[$v['parent_id']]['module_name'] = $title['module_name'];
                    $data[$v['parent_id']]['type'] = $title['type'];
                    $data[$v['parent_id']]['parent_id'] = $title['parent_id'];
                    $data[$v['parent_id']]['icon_url'] = $title['icon_url'];
                    $data[$v['parent_id']]['sort'] = $title['sort'];
                    $data[$v['parent_id']]['children'] = [];
                    $titleSort[$v['parent_id']] = $title['sort'];
                    
                }
            }

            foreach ($result as $key => $value){
                $k = $value['parent_id'] == 0 ? $value['title_id']:$value['parent_id'];
                $data[$k]['children'][] = $value;

            }
            foreach ($data as $key => $value){//临时
                if($value['parent_id'] != 0){
                    array_unshift($data[$value['parent_id']]['children'], $value);
                    if($isSuperSystem){
                        foreach ($value['children'] as $ch => $v){
                            $data[$value['parent_id']]['children'][0]['description'] = $value['module_description'];
                            if(!isset($data[$value['parent_id']]['children'][0]['menuSort']) || $data[$value['parent_id']]['children'][0]['menuSort'] < $v['titleSort']){
                                $data[$value['parent_id']]['children'][0]['menu_url'] = $v['menu_url'];
                                $data[$value['parent_id']]['children'][0]['menuSort'] = 0;
                            }
                        }
                    }
                    unset($data[$key]);
                    unset($titleSort[$key]);
                }
            }
            array_multisort($titleSort,SORT_DESC,$data);
            
           Yii::$app->cache->set(Yii::getAlias('@systemMenu'),$data,0,$dependency);
        }
        return $data;
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
    
    
    public static function getTitleChildren($id){
        return self::find()->select(['id','module_name','module_description','sort','parent_id'])->where(['parent_id' => $id])->asArray()->all();
    }
    
    
}
