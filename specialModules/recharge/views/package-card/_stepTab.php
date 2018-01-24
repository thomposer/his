<?php
$css = <<<CSS
.step-top{
    height: 30px;
    border-bottom: 2px solid #EDF1F7;
}
.step-ul{
    list-style: none;
    overflow: hidden;
    display: table;
    margin: 0 auto;
    padding: 0;
}
.step-ul li{
    float:left;
    padding: 0 16px;
}
.step-ul li span{
    display: inline-block;
    height: 24px;
    line-height: 24px;
}
.step-ul li span.activity{
    border-bottom: 2px solid #76a6ef;
    color: #76a6ef;
    padding-bottom: 28px;
}
.step-ul li span.gray{
    color: #D6DAE0;
}
CSS;


if (!isset($step)) {
    $step = 1;
}
$this->registerCss($css);
?>
<div class="row">
    <div class="col-md-12">
            <div class="step-top form-group">
                <ul class="step-ul ">
                    <li>
                        <span class="<?php echo $step == 1 ? 'activity' : 'gray' ?>">第一步：新增套餐</span>
                    </li>
                    <li>
                        <span class="<?php echo $step == 2 ? 'activity' : 'gray' ?>">第二步：确认支付</span>
                    </li>
                </ul>
            </div>
        </div>
</div>

