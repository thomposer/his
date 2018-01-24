<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\medicine\models\MedicineDescription */

?>
<div class="medicine-description-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<script type="text/javascript">
		var getItemUrl = '<?= Url::to(['@apiMedicineDescriptionView']) ?>';
		var medicineIndexDeleteItem = '<?= Url::to(['@medicineIndexDeleteItem']) ?>';
		require([baseUrl+"/public/js/medicine/update.js?v="+versionNumber],function(main){
			main.init();
		});
  </script>