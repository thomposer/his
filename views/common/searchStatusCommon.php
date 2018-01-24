<?php

use yii\helpers\Url;
use yii\helpers\Html;

$requestUrl = Url::to([$this->params['requestUrl']]);
$queryParams = Yii::$app->request->queryParams;
$params = '';
$allActive = 'active';
$middleActive = '';
$centerActive = '';
$rightActive = '';
$fourthActive = '';
$fifthActive = '';
if (($type = Yii::$app->request->get('type')) != null) {
    $params .= 'type=' . $type . '&';
}
if ($queryParams) {
    if (isset($queryParams[$searchName])) {
        $search = $queryParams[$searchName];
        foreach ($search as $key => $v) {
            if ($key != $statusName) {
                $params .= $searchName . '[' . $key . ']=' . $v . '&';
            } else {
                if ($v == 3) {
                    $allActive = '';
                    $middleActive = 'active';
                    $centerActive = '';
                    $rightActive = '';
                } else if ($v == 1) {
                    $allActive = '';
                    $middleActive = '';
                    $centerActive = '';
                    $rightActive = 'active';
                } else if ($v == 2) {
                    $allActive = '';
                    $middleActive = '';
                    $centerActive = 'active';
                    $rightActive = '';
                } else if ($v == 4) {
                    $fourthActive = 'active';
                    $allActive = '';
                } else if ($v == 5) {
                    $fifthActive = 'active';
                    $allActive = '';
                }
            }
        }
    }
}
?>


<?php $this->beginBlock('renderCss') ?>
<?php
$css = <<<CSS
   .btn-custom:hover, .btn-custom:focus {
        color: #76a6ef !important;
        background-color: #F5F9FE !important;
    }
    .btn-custom.active,.btn-custom.active:focus,.btn-custom.active:hover{
        background-color: #76a6ef !important;
    }
    .btn-custom.active:hover, .btn-custom.active:focus {
      color: #ffffff !important;
    }
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>


<div class="btn-group" id="J-select-box">
    <?php
    if (!empty($buttons)) {
        foreach ($buttons as $key => $v) {
            switch ($v['statusCode']) {
                case 0 :
                    echo Html::a($v['title'] . (isset($v['num']) ? '(' . $v['num'] . ')' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn  btn-group-left btn-custom ' . $allActive]);
                    break;
                case 3 :
                    $dotText = (isset($v['hasDot']) && $v['hasDot']) ? Html::tag('i', '', ['class' => 'fa fa-circle red commonSearchStatus']) : '';
                    echo Html::a($dotText . $v['title'] . (isset($v['num']) ? '(' . $v['num'] . ')' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['statusCode'] . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn  btn-group-middle btn-custom ' . $middleActive]);
                    break;
                case 2 :
                    $dotText = (isset($v['hasDot']) && $v['hasDot']) ? Html::tag('i', '', ['class' => 'fa fa-circle green commonSearchStatus']) : '';
                    echo Html::a($dotText . $v['title'] . (isset($v['num']) ? '(' . $v['num'] . ')' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['statusCode'] . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn  btn-group-center btn-custom ' . $centerActive]);
                    break;
                case 1:
                    $dotText = (isset($v['hasDot']) && $v['hasDot']) ? Html::tag('i', '', ['class' => 'fa fa-circle end commonSearchStatus']) : '';
                    echo Html::a($dotText . $v['title'] . (isset($v['num']) ? '(' . $v['num'] . ')' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['statusCode'] . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn btn-group-right btn-custom ' . $rightActive]);
                    break;
                case 4:
                    $dotText = (isset($v['hasDot']) && $v['hasDot']) ? Html::tag('i', '', ['class' => 'fa fa-circle end commonSearchStatus']) : '';
                    echo Html::a($dotText . $v['title'] . (isset($v['num']) ? '(' . $v['num'] . ')' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['statusCode'] . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn btn-group-right btn-custom ' . $fourthActive]);
                    break;
                case 5:
                    $dotText = (isset($v['hasDot']) && $v['hasDot']) ? Html::tag('i', '', ['class' => 'fa fa-circle end commonSearchStatus']) : '';
                    echo Html::a($dotText . $v['title'] . (isset($v['num']) ? '(' . $v['num'] . '）' : ''), (isset($v['url']) ? $v['url'] : $requestUrl) . '?' . $params . $searchName . '[' . $statusName . ']=' . $v['statusCode'] . $v['suffix'], ['data-pjax' => 1, 'class' => 'btn btn-group-right btn-custom ' . $fifthActive]);
                    break;
            }
        }
    }
    ?>
</div>
