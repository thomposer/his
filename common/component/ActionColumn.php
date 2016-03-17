<?php
namespace app\common\component;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;


class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * auth和 buttons一样，都包含view update delete 3个元素，且都是回调函数
     * template 是第一层控制为完全是否显示，此为第二层是否有权限显示
     * 这3个属性是可否操作，当不可操作的时候 会显示为灰色（详细见initDefaultButtons）
     */

    public $auth=[];
    public $requestModuleController;
    public $permList;
    /**
     * @inheritdoc
    */
    public function init()
    {
        parent::init();
        $view = Yii::$app->view;
        $this->requestModuleController = $view->params['requestModuleController'];
        $this->permList = $view->params['permList'];
        $this->initDefaultAuth();
        $this->initDefaultButtons();
        $this->headerOptions = empty($this->headerOptions)?['class' => 'op-header']:$this->headerOptions;
        $this->contentOptions = empty($this->contentOptions)?['class' => 'op-group']:$this->contentOptions;
    }

    /**
     * 
     * 判断用户是否有权限，若没权限，返回false,否则返回true
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultAuth()
    {
        
        if (!isset($this->auth['view'])) {
            $this->auth['view'] = function ($url, $model, $key) {
                
            if(!isset($this->permList['role']) && !in_array($this->requestModuleController.'/view', $this->permList)){
                return false;
            }  
                return true;
            };
        }
        if (!isset($this->auth['update'])) {
            $this->auth['update'] = function ($url, $model, $key) {
            if(!isset($this->permList['role']) && !in_array($this->requestModuleController.'/update', $this->permList)){
                return false;
            }
                return true;
            };
        }
        if (!isset($this->auth['delete'])) {
            $this->auth['delete'] = function ($url, $model, $key) {
            if(!isset($this->permList['role']) && !in_array($this->requestModuleController.'/delete', $this->permList)){
                return false;
            }
                return true;
            };
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $auth_class='';
                if(call_user_func($this->auth['view'], $url, $model, $key)!==true)
                {
                    return false;
                }
                $options = array_merge([
                    'title' => Yii::t('yii', 'View'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-eye-open "></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $auth_class='';
                if(call_user_func($this->auth['update'], $url, $model, $key)!==true)
                {
                    return false;
                }
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-pencil gray-dark "></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $auth_class='';
                if(call_user_func($this->auth['delete'], $url, $model, $key)!==true)
                {
                    return false;    
                }
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-trash "></span>', $url, $options);
            };
        }
    }
    protected function renderHeaderCellContent()
    {
        return trim($this->header) !== '' ? $this->header : '操作';
    }
    
    
   
    
}