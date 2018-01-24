<?php

use yii\widgets\ActiveForm;
use rkit\yii2\plugins\ajaxform\Asset;
Asset::register($this);
?>
<div class="patient-basic col-sm-12 col-md-12">

    <?= $this->render('_basic_basic', ['model' => $model]) ?>
    
     <?= $this->render('_basicAllergy', ['model' => $model->getModel('patient')]) ?>

    <?= $this->render('_basic_child', ['model' => $model]) ?>

    <?php  /*$this->render('_basic_case', ['model' => $model, 'allergy_list' => $allergy_list])*/?>

    <?php /*$this->render('_basic_other', ['model' => $model])*/ ?>
   

    <?= $this->render('_basic_family', ['model' => $model, 'familyData' => $familyData]) ?>

</div>
