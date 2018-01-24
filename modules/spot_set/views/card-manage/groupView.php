<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\assets\AppAsset;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\spot\models\CardRechargeCategory;
use yii\widgets\Pjax;
use yii\helpers\Url;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */

$this->title = '卡组信息';
$this->params['breadcrumbs'][] = ['label' => '充值卡配置', 'url' => ['group-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/cardCategory.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>

<div class="card-recharge-category-view col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['@spot_setCardManageGroupIndex']), ['class' => 'right-cancel', 'data-pjax' => 0]) ?>
        </div>
        <div class = "box-body card-recharge-category-view-content">  
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_name required">
                        <label class="control-label" for="cardrechargecategory-f_category_name">卡组名称</label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"  value="<?= Html::encode($model->f_category_name) ?>" name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div> 
                </div>
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_level">
                        <label class="control-label" for="cardrechargecategory-f_level">等级<span class="f_level">(卡片自动升级，只能从低等级卡组升至高等级)</span></label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"  value="<?= Html::encode(CardRechargeCategory::getLevel()[$model->f_level]) ?>" name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">描述</label>
                        <textarea id="cardrechargecategory-f_category_desc" class="form-control"  name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled"><?= Html::encode($model->f_category_desc) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">创建时间</label>
                        <input id="cardrechargecategory-f_category_desc" class="form-control" value="<?= date('Y-m-d H:i', $model->f_create_time) ?>" name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">最后修改时间</label>
                        <input id="cardrechargecategory-f_category_desc" class="form-control" value="<?= date('Y-m-d H:i', $model->f_update_time) ?>" name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php Pjax::end() ?>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
