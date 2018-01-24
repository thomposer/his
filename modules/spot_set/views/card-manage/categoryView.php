<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\assets\AppAsset;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\spot\models\CardRechargeCategory;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */

$this->title = '卡种信息';
$this->params['breadcrumbs'][] = ['label' => '充值卡配置', 'url' => ['group-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$stateArr = [1 => '发行', 2 => '停止发行', 3 => '发行'];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/cardCategory.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>

<div class="card-recharge-category-view col-xs-12">
    <div class = "box">

        <div class="box-header with-border">
            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['@spot_setCardManageGroupIndex']), ['class' => 'right-cancel']) ?>
        </div>
        <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
        <div class = "box-body card-recharge-category-view-content">  
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_name required">
                        <label class="control-label" for="cardrechargecategory-f_category_name">卡种名称</label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"  value="<?= Html::encode($model->f_category_name) ?>" name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div> 
                </div>
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_name required">
                        <label class="control-label" for="cardrechargecategory-f_category_name">所属卡组</label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"  value="<?= Html::encode($cardCategory[$model->f_parent_id]['f_category_name']) ?>" name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div> 
                </div>
                <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@spot_setCardManageCategoryUpdate'), $this->params['permList'])): ?>
                    <span class="card-manage-category-view-edit basic-family-right-up">
                        <a class="card-view-btn" role="modal-remote" data-modal-size="large" href="<?= Url::to(['@spot_setCardManageCategoryUpdate', 'id' => $model->f_physical_id]) ?>" >
                            <i class="his-pencil fa"></i>
                            修改</a>      
                    </span>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">描述</label>
                        <textarea id="cardrechargecategory-f_category_desc" class="form-control"  name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled"><?= Html::encode($model->f_category_desc) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card-manage-group-view-service-content">
                <div>服务折扣（%）</div>
                <div class="service-content card-manage-group-view-service form-group">
                    <?php foreach ($cardDiscountList as $v): ?>
                        <div class = 'row'>
                            <div class = 'col-md-2 form-group'>
                                <?= $v['name'] ?>
                            </div>
                            <div class = 'col-md-4 form-group'>
                                <?= Html::textInput('CardDiscountClinic[discount][]', $v['discount'], ['class'=>'form-control','disabled'=>true])?>%
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

            <div>自动升级：<?php echo $model->f_auto_upgrade == 1 ? '可从其他卡种自动升级' : '不可从其他卡种自动升级'; ?></div>
            <?php if ($model->f_auto_upgrade == 1): ?>
                <div class="service-content card-manage-group-view-service ">
                    <div class="row auto-upgrade-content" style="display: block;">
                        <div class="col-md-7">
                            <div class=" field-cardrechargecategory-f_upgrade_amount">
                                <label class="control-label" for="cardrechargecategory-f_upgrade_amount">条件：365天内，充值额累计（元）</label>
                                <input type="text" value="<?= $model->f_upgrade_amount ?>" id="cardrechargecategory-f_upgrade_amount" class="form-control" name="CardRechargeCategory[f_upgrade_amount]" style="width: 60%" disabled>
                            </div> 
                        </div>
                        <!--                        <div class="col-md-6 auto_upgrade" style="margin-top: 7px;">
                                                    <div class=" field-cardrechargecategory-f_upgrade_time">
                                                        <label class="control-label" for="cardrechargecategory-f_upgrade_time">时长范围：</label>
                                                        365天
                                                    </div>
                                                </div>-->
                    </div>
                </div>
            <?php endif; ?>
            <div class="row" style="margin-top: 15px;">
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
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">卡组状态</label>
                        <input id="cardrechargecategory-f_category_desc" class="form-control" value="<?= CardRechargeCategory::$getState[$model->f_state] ?>" name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
            </div>
            <?php Pjax::end() ?>
        </div>

    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>