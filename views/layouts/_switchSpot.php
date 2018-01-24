<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<form action="" method="post">

    <?php if ($spotList): ?>
        <?php
        foreach ($spotList as &$v) {
            if ($v['id'] == $defaultSpotId) {
                $v['spot_name'] .= '(当前默认)';
            }
            if ($v['id'] == $_COOKIE['spotId']) {
                $currentSpotId = $v['id'];
            }
        }
        ?>
        <div class="row" style="margin-top:30px;margin-bottom: 10px;">
            <div class="text-right" style="width:15%;padding-top: 8px;float: left;"><label class="control-label">切换诊所</label></div>
            <div class="col-xs-9 col-sm-9">
    <?= Html::dropDownList('', $currentSpotId, ArrayHelper::map($spotList, 'id', 'spot_name'), ['class' => "form-control switch-spot", 'data-default-id' => $defaultSpotId]) ?>
            </div>
        </div>
        <div class="row" style="margin-bottom: 30px;margin-left: 25px;">
            <label>
                <?= Html::checkbox('defaultSpot', ($currentSpotId ? ($currentSpotId == $defaultSpotId) : false)) . ' 默认登录诊所' ?>
            </label>
        </div>
        <div class="row" style="margin-bottom: 30px;text-align: center;">
            <input type="button" class = 'btn btn-spot' style="float: none;" value="进入">
        </div>
<?php endif; ?>
</form>