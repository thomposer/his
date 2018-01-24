<?php

namespace app\common\component;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionTextColumn extends \yii\grid\ActionColumn
{

    /**
     * auth和 buttons一样，都包含view update delete 3个元素，且都是回调函数
     * template 是第一层控制为完全是否显示，此为第二层是否有权限显示
     * 这3个属性是可否操作，当不可操作的时候 会显示为灰色（详细见initDefaultButtons）
     * ajaxList  判断view update delete 3个元素哪个使用ajax验证
     */
    public $auth = [];
    public $ajaxList = ['delete' => true];
    public $viewOptions = [];
    public $updateOptions = [];
    public $deleteOptions = [];
    public $requestModuleController;
    public $permList;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $view = Yii::$app->view;
        $this->requestModuleController = $view->params['requestModuleController'];
        $this->permList = $view->params['permList'];
        $this->ajaxOption();
        $this->initDefaultAuth();
        $this->initDefaultButtons();
        $this->headerOptions = $this->headerOptions == ['class' => 'action-column'] ? ['class' => 'col-sm-2 col-md-2'] : $this->headerOptions;
        $this->contentOptions = empty($this->contentOptions) ? ['class' => 'op-group'] : $this->contentOptions;
    }

    public function ajaxOption() {

        if (isset($this->ajaxList['view']) && $this->ajaxList['view'] === true) {
            $this->viewOptions = [
                'role' => 'modal-remote',
            ];
        }
        if (isset($this->ajaxList['update']) && $this->ajaxList['update'] === true) {
            $this->updateOptions = array_merge([
                'role' => 'modal-remote',
                    ], $this->updateOptions);
        }
        if (isset($this->ajaxList['delete']) && $this->ajaxList['delete'] === true) {
            $this->deleteOptions = array_merge([
                'data-confirm' => false,
                'data-method' => false,
                'data-request-method' => 'post',
                'role' => 'modal-remote',
                'data-confirm-title' => '系统提示',
                'data-delete' => false,
                'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    ], $this->deleteOptions);
        }
    }

    /**
     * 
     * 判断用户是否有权限，若没权限，返回false,否则返回true
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultAuth() {

        if (!isset($this->auth['view'])) {
            $this->auth['view'] = function ($url, $model, $key) {

                if (!isset($this->permList['role']) && !in_array($this->requestModuleController . '/view', $this->permList)) {
                    return false;
                }
                return true;
            };
        }
        if (!isset($this->auth['update'])) {
            $this->auth['update'] = function ($url, $model, $key) {
                if (!isset($this->permList['role']) && !in_array($this->requestModuleController . '/update', $this->permList)) {
                    return false;
                }
                return true;
            };
        }
        if (!isset($this->auth['delete'])) {
            $this->auth['delete'] = function ($url, $model, $key) {
                if (!isset($this->permList['role']) && !in_array($this->requestModuleController . '/delete', $this->permList)) {
                    return false;
                }
                return true;
            };
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons() {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $auth_class = '';
                if (call_user_func($this->auth['view'], $url, $model, $key) !== true) {
                    return false;
                }
                $options = array_merge([
//                    'title' => Yii::t('yii', 'View'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                    'class' => 'op-group-a'
                        ], $this->buttonOptions, $this->viewOptions);
                /* fa-eye是查看 */
                return Html::a('查看', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $auth_class = '';
                if (call_user_func($this->auth['update'], $url, $model, $key) !== true) {
                    return false;
                }
                $options = array_merge([
                   'title' => Yii::t('yii', '修改'),
                    'aria-label' => Yii::t('yii', '修改'),
                    'data-pjax' => '0',
                    'class' => 'op-group-a'
                        ], $this->buttonOptions, $this->updateOptions);
                return Html::a('修改', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $auth_class = '';
                if (call_user_func($this->auth['delete'], $url, $model, $key) !== true) {
                    return false;
                }
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                    'data-delete' => 'false', //true为普通弹框(确定按钮在左侧), false为确定按钮在右侧
//                    'data-title' => '系统提示', //弹框的标题 默认为系统提示
                    'data-method' => 'post',
                    'data-pjax' => '1',
                    'class' => 'op-group-a'
                        ], $this->buttonOptions, $this->deleteOptions);
                return Html::a($options['title'], $url, $options);
            };
        }
    }

    protected function renderHeaderCellContent() {
        return trim($this->header) !== '' ? $this->header : '操作';
    }

}
