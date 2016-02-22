<?php

namespace app\modules\module\models;

use Yii;
use app\common\base\BaseActiveRecord;
use app\modules\module\models\Title;

class TitleMenu extends BaseActiveRecord {
	
	public $module_description;
	public $module_name;
	public $menus;
	
	public $menusList;
	
	/* (non-PHPdoc)
	 * @see \yii\base\Model::getAttributeLabel($attribute)
	 */
	public function attributeLabels() {
		return [
			'module_description' => '模块名称',
			'module_name' => '模块简称',
			'menus' => '菜单列表'
		];
	}

	/* (non-PHPdoc)
	 * @see \yii\base\Model::rules()
	 */
	public function rules() {
		return [
			[['module_name', 'module_description', 'menus'], 'required'],
		    ['module_name','checkCode'],
			['menus', 'checkMenus'],
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
	
}

?>