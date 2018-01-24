<?php

use yii\helpers\Html;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\ConsumablesClinic */
?>
<?php  AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<div class="consumables-clinic-create">
    <?= $this->render('_form', [
        'model' => $model,
        'consumablesList' => $consumablesList
    ]) ?>
</div>

<script type="text/javascript">
	var baseUrl = '<?= Yii::$app->request->baseUrl; ?>';
	var error = '<?= $model->errors ? 1 : 0  ?>';
	var consumablesList = <?= json_encode($consumablesList,true) ?>;
	require([baseUrl + '/public/js/spot_set/consumables-clinic.js'],function(main){
		main.init();
	})
</script>