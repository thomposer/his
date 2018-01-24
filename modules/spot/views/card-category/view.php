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
    <div class="box">

        <div class="box-header with-border">
            <span class='left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['@spotCardManageGroupIndex']), ['class' => 'right-cancel']) ?>
        </div>
        <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
        <div class="box-body card-recharge-category-view-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_name required">
                        <label class="control-label" for="cardrechargecategory-f_category_name">卡种名称</label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"
                               value="<?= Html::encode($model->f_category_name) ?>"
                               name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_name required">
                        <label class="control-label" for="cardrechargecategory-f_category_name">所属卡组</label>
                        <input type="text" id="cardrechargecategory-f_category_name" class="form-control"
                               value="<?= Html::encode($cardCategory[$model->f_parent_id]['f_category_name']) ?>"
                               name="CardRechargeCategory[f_category_name]" disabled="disabled">
                    </div>
                </div>
                <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@spotCardManageCategoryUpdate'), $this->params['permList'])): ?>
                    <span class="card-manage-category-view-edit basic-family-right-up">
                        <a class="card-view-btn" role="modal-remote" data-modal-size="large"
                           href="<?= $baseUrl ?>/spot/card-manage/category-update.html?id=<?= $model->f_physical_id ?>">
                            <i class="his-pencil fa"></i>
                            修改</a>      
                    </span>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">描述</label>
                        <textarea id="cardrechargecategory-f_category_desc" class="form-control"
                                  name="CardRechargeCategory[f_category_desc]" rows="4"
                                  disabled="disabled"><?= Html::encode($model->f_category_desc) ?></textarea>
                    </div>
                </div>
            </div>


            <div>自动升级：<?php echo $model->f_auto_upgrade == 1 ? '可从其他卡种自动升级' : '不可从其他卡种自动升级'; ?></div>
            <?php if ($model->f_auto_upgrade == 1): ?>
                <div class="service-content card-manage-group-view-service ">
                    <div class="row auto-upgrade-content" style="display: block;">
                        <div class="col-md-7">
                            <div>
                                <label class="control-label" for="cardrechargecategory-f_upgrade_amount">条件：365天内，充值额累计（元）</label>
                                <input type="text" value="<?= $model->f_upgrade_amount ?>"
                                       id="cardrechargecategory-f_upgrade_amount" class="form-control"
                                       name="CardRechargeCategory[f_upgrade_amount]" style="width: 60%" disabled>
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
                        <input id="cardrechargecategory-f_category_desc" class="form-control"
                               value="<?= date('Y-m-d H:i', $model->f_create_time) ?>"
                               name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">最后修改时间</label>
                        <input id="cardrechargecategory-f_category_desc" class="form-control"
                               value="<?= date('Y-m-d H:i', $model->f_update_time) ?>"
                               name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group field-cardrechargecategory-f_category_desc">
                        <label class="control-label" for="cardrechargecategory-f_category_desc">卡组状态</label>
                        <input id="cardrechargecategory-f_category_desc" class="form-control"
                               value="<?= CardRechargeCategory::$getState[$model->f_state] ?>"
                               name="CardRechargeCategory[f_category_desc]" rows="4" disabled="disabled">
                    </div>
                </div>
                <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@spotCardManageCategoryOperation'), $this->params['permList'])): ?>
                    <div class="col-md-6">
                        <div class="form-group category-operation-btn">
                            <?php 
                                if ($model->f_state == 2) {
                                    $text = '确定停止发行吗?停止发行后诊所下的充值卡也会停止发行。';
                                    $dataDelete = false;
                                } else {
                                    $text = '确定发行吗?';
                                    $dataDelete = true;
                                }
                                $options = [
                                    'class' => 'btn btn-default',
                                    'data-confirm' => false,
                                    'data-method' => false,
                                    'data-request-method' => 'post',
                                    'role' => 'modal-remote',
                                    'data-confirm-title' => '系统提示',
                                    'data-delete' => $dataDelete,
                                    'data-confirm-message' => $text,
                                ];
                            ?>
                            <?= Html::a($stateArr[$model->f_state], Url::to(['@spotCardManageCategoryOperation', 'id' => $model->f_physical_id]), $options) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="control-label">诊所服务折扣</label>

                </div>
                <div class="col-md-12">
                <table class="table table-border table-hover">
                    <thead>
                    <tr>
                        <th class="col-sm-4">诊所</th>
                        <th class="col-sm-8">服务与折扣</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($data) {
                        foreach ($data as $key => $val):?>
                            <tr data-key="0">
                                <td><?=Html::encode($val['spotName'])?></td>
                                <td><?=Html::encode($val['discount'])?></td>
                            </tr>
                        <?php endforeach;
                    }
                    ?>
                    </tbody>
                </table>
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
