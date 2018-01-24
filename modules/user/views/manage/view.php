<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = '员工详情';
$this->params['breadcrumbs'][] = ['label' => '人员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>

<div class="user-view col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Yii::$app->request->referrer, ['class' => 'right-cancel']) ?>
        </div>
        <div class = "box-body">  
            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'head_img',
                        'format' => 'raw',
                        'value' => $model->head_img ? Html::img(Yii::$app->params['cdnHost'].$model->head_img, ['onerror' => "this.src='{$public_img_path}default.png'"]) : Html::img($baseUrl . '/public/img/user/img_user_small.png')
                    ],
                    'username',
                    'email',
                    'iphone',
                    [
                        'attribute' => 'sex',
                        'value' => User::$getSex[$model->sex],
                    ],
                    'card',
                    [
                        'attribute' => 'birthday',
                        'value' => $model->birthday == '0000-00-00' ? '' : $model->birthday
                    ],
                    [
                        'attribute' => 'occupation',
                        'value' => User::$getOccuption[$model->occupation]
                    ],
                    [
                        'attribute' => 'occupation_type',
                        'value' => User::$getOccupationType[$model->occupation_type]
                    ],
                    [
                        'attribute' => 'position_title',
                        'value' => User::$getPositionTitle[$model->position_title]
                    ],
                    [
                        'attribute' => 'clinic_name',
                        'value' => function ($model) use($spotNameList){
                           return $spotNameList[$model->id]['clinic_name'];
                        }
                    ],
                    'introduce',
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
