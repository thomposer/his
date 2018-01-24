<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="nurse-print">
    <div class = 'row'>
        <div class = 'col-md-12 nurse-print-list' data-id="<?= $recordId ?>">
            <?= Html::checkboxList('printList','',ArrayHelper::map($printList, 'id', 'name')); ?>
        </div>
    </div>
</div>
<?php
    $this->registerCss('
        .nurse-print .title{
            font-size: 16px;
            font-weight: normal;
        }
        .nurse-print-list label{
            display: block;
            padding-left: 20px;
        }
        .nurse-print .row{
            margin:15px;
        }
        #ajaxCrudModal .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
    ');
?>