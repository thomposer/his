<?php



?>

    <div class="clinic-cure-create col-xs-12">
        <div class="box">
            <div class="box-body">
                <?= $this -> render('_form', [
                    'model' => $model,
                    "parentCureList" => $parentCureList
                ]) ?>
            </div>
        </div>
    </div>