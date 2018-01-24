<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Organization */
$this->title = '机构详情';
$this->params['breadcrumbs'][] = ['label' => '机构管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="organization-view col-xs-12">
    <div class = "box">
         <div class="box-header with-border">
          <span class = 'left-title'><?= Html::encode($this->title) ?></span>
          <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
         </div>
        <div class = "box-body">  

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'spot',
                    'spot_name',
                    [                   
                        'attribute' => 'status',
                        'value' => $model->status == 1?'正常':'已删除',
                    ],
                    'contact_iphone',
                    'contact_name',
                    'contact_email',
                    [
                        'attribute' => 'address',
                        'value' => $model->province.$model->city.$model->area
                    ],
                    'detail_address',
                    'create_time:datetime',
                    'update_time:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
