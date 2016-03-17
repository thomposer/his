<?php

namespace app\modules\module\models;

use Yii;
use app\common\base\BaseActiveRecord;
use app\modules\module\models\Title;
use yii\web\UploadedFile;
use app\common\Common;

class TitleMenu extends BaseActiveRecord {
	
	public $module_description;
	public $module_name;
	public $menus;
	public $status;
	public $icon_url;
	public $menusList;
	public $isNewRecord = 0;
	public $baseUploadPath;
	
	public static function tableName()
	{
	    return '{{%title}}';
	}
	/* (non-PHPdoc)
	 * @see \yii\base\Model::getAttributeLabel($attribute)
	 */
	public function attributeLabels() {
		return [
			'module_description' => '模块名称',
			'module_name' => '模块简称',
			'menus' => '菜单列表',
		    'status' => '状态(渲染)',
		    'icon_url' => '上传模块图标',
		];
	}

	/* (non-PHPdoc)
	 * @see \yii\base\Model::rules()
	 */
	public function rules() {
		return [
		    [['parent_id', 'status','sort'], 'integer'],
		    [['module_name'], 'string', 'max' => 64],
			[['module_name', 'module_description', 'menus','icon_url'], 'required'],
		    [['module_description'],'string','max' => 255],
		    ['module_name','checkCode'],
			['menus', 'checkMenus'],
		    ['icon_url','file','extensions' => 'jpg,png,jpeg,gif']
		];		
	}
	
	/**
	 * 判断菜单是否合法
	 * @param unknown $attribute
	 * @param unknown $params
	 */
	public function checkMenus($attribute, $params) {
		
		$menus = preg_split("/;/", trim($this->$attribute, ';'));
		
		$this->menusList = array();
		
		foreach ($menus as $menu) {
			$menu = trim($menu);
			
			$menuArr = preg_split("/,/", $menu);
			
			$url = $menuArr[0];
			$show = $menuArr[2];
			$isSuper = $menuArr[3];
			if (!preg_match("/^(\/[a-zA-Z0-9\\-_]+){1,}$/", $url) ||
				// 是否显示在侧边栏
				!in_array($show, array('0', '1')) ||
				// 是否为超级管理员的模块
				!in_array($isSuper, array('0', '1'))) {
				$this->addError($attribute, '菜单不符合规则');
			}
			
			$this->menusList[] = $menuArr;
		}
			
	}
	
	/**
	 * 判断模块简称是否已经存在
	 * @param unknown $attribute
	 * @param unknown $params
	 */
	public function checkCode($attribute, $params) {
		$exist = Title::find()->select(['id'])
			->where(['module_name' => $this->$attribute])
			->one();
		
		if ($exist) {
			$this->addError($attribute, '模块英文简称已存在');
		}
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

?>