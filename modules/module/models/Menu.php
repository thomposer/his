<?php

namespace app\modules\module\models;

use Yii;
use yii\db\Query;
use app\common\Common;
use app\common\base\BaseActiveRecord;
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
 * @property integer $create_time
 * @property integer $update_time
 */
class Menu extends BaseActiveRecord
{
    public $module_description;//模块名称
    public static  $left_menu = array(
        '0' => '不渲染',
        '1' => '渲染'
    );
    public static  $menu_status =  array(
        '1' => '启用',
        '0' => '禁用'
    );
    public static $color = array(
         '0' => 'text-muted',
         '1' => 'text-success',
        
    );
    public static $role_type = array(
        '0' => '否',
        '1' => '是'
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
            [['menu_url','description'],'unique'],
            [['menu_url','description'],'trim'],
            [['type', 'parent_id', 'status','role_type','sort','create_time','update_time'], 'integer'],
            [['menu_url', 'description'], 'string', 'max' => 255],
            [['sort'],'default','value' => 0],
//             [['menu_url'],'match','pattern' => '/^[\/][a-zA-Z\/_-]{3,50}$/','message' => '格式错误，正确格式应为：/m/c/a'],// 斜线/开头，允许4-35字节，允许字母数字下划线
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
            'role_type' => '所属类型(系统管理员)',
            'sort' => '排序',
        ];
    }
    /**
     * @property 检测当前uri是否启用，若启用，则可访问，否则返回404
     * @param 当前访问ur $requestUrl
     */
    public static function checkMenu($requestUrl){
        $result = self::find()->select(['status'])->where(['menu_url' => $requestUrl])->asArray()->one();    
        return $result['status'];
    }
    /**
     * @param $menu_url_array  角色所拥有的url
     * @param $role_type 角色类型－1:系统管理员,0:其他角色
     * @return \yii\db\ActiveQuery
     */
    public static function getParent($menu_url_array)
    {
            $data = '';
            $query = new Query();
            $query->from(['m' => self::tableName()])->select(['t.id as title_id','t.module_description','t.module_name','m.menu_url','m.description']);
            $query->leftJoin(['t' => Title::tableName()],'{{m}}.parent_id = {{t}}.id');
            $query->where(['m.menu_url' => $menu_url_array,'t.status' => 1,'m.type' => 1,'m.status' => 1]);
            $query->orderBy(['t.sort'=>SORT_DESC]);
            $result = $query->all();  
            foreach ($result as $v){
                $data[$v['title_id']]['module_description'] = $v['module_description'];
                $data[$v['title_id']]['module_name'] = $v['module_name'];
                unset($v['module_description']);
                $data[$v['title_id']]['children'][] = $v;
            }    
        return $data;
    }
    public static function searchMenu($description){
        return self::find()->select(['menu_url'])->where(['description' => $description])->asArray()->one();
    }
    public function getTitle()
    {
    	return $this->hasOne(Title::className(), ['id' => 'parent_id']);
    }
}
