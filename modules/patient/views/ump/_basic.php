<?php

use yii\widgets\ActiveForm;

?>
<div class="patient-basic col-sm-12 col-md-12">


    <?= $this->render('_basic_basic', ['model' => $model]) ?>

    <?= $this->render('_basic_family', ['model' => $model, 'familyData' => $familyData]) ?>

</div>
