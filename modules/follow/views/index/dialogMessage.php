<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\follow\models\Follow;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use app\common\Common;
use kartik\file\FileInputAsset;

CrudAsset::register($this);
FileInputAsset::register($this)->addLanguage(Yii::$app->language, '', 'js/locales');
/* @var $this yii\web\View */
/* @var $searchModel app\modules\follow\models\search\FollowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '对话消息';
$this->params['breadcrumbs'][] = ['label' => '随访管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/follow/selectFollow.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="follow-index col-xs-12">
    
    <div class = "box">
        <div class="box-header with-border recharge-bg">
            <span class = 'left-title'><?php
                $text = Html::encode($this->title);
                if ($bindInfo) {
                    $text.='<span class="user-title">(患者妈咪知道账号：' . $bindInfo['telphone'] . '，昵称：' . Html::encode($bindInfo['nickName']) . ')</span>';
                }
                echo $text;
                ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
        </div>
        <div class="send-btn">
            <?php if ($bindInfo): ?>
                <?= Html::a('发起对话', Url::to(['@followIndexSendMessage', 'telphone' => $bindInfo['telphone']]), ['class' => 'btn btn-default', 'role' => 'modal-remote', 'data-modal-size' => 'large']) ?>
            <?php else: ?>
                <?= Html::button('发起对话', ['class' => 'btn btn-default btn-disabled disabled ', 'data-toggle' => 'tooltip', 'title' => '未绑定妈咪知道账号，无法发起对话', 'data-placement' => 'right']) ?>
            <?php endif; ?>
        </div>
        <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'rowOptions' => function($model, $key, $index, $grid) {
                    if($model['from']=='healthadmin'){
                        $class='label-green';
                    }else{
                        $class='';
                    }
                    return ['class' =>$class];
            },
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
                [
                    'attribute' => 'sender',
                    'label' => '发起人',
                    'value' => function ($data) {
                        return $data['sender'];
                    },
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'sender',
                    'label' => '消息内容',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $res = '';
                        if ($data['message']) {
                            $res = $data['message'];
                        } else {
                            $res = Html::img($data['attachment'], ['style' => 'height:130px']);
                        }
                        return $res;
                    },
                            'headerOptions' => ['class' => 'col-sm-6 col-md-6'],
                        ],
                        [
                            'attribute' => 'sender',
                            'label' => '发送时间',
                            'value' => function ($data) {
                                return $data['sendTime'];
                            },
                            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                        ],
                    ],
                ]);
                ?>
        <?php Pjax::end() ?>
            </div>
            
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>
        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
