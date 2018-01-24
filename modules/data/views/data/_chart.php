<?php
use yii\helpers\Url;

$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");

    $show = '<div style=\'text-align: left\'>
                指标定义</br>
                1. 预约人数：预约成功的人次（不包含取消预约的人次以及改约他日/他月的人次）</br>
        
                2. 接诊人数：接诊人次</br>
        
                3. 付款人数：付款人次</br>
        
                4. 诊金（四大医嘱类比）：实收诊金金额</br>
                
                5. 就诊实收金额=诊金+实验室检查费用+影像学检查费用+治疗费用+处方费用</br>
                
                6. 客单价=就诊实收金额/付款人数</br>
                
                7. 消费占比=就诊实收金额/会员卡充值金额</br>
                
                8. 市场效率=付款人数/预约人数</br>
                
                9. 销售效率=会员卡销量/付款人数
              </div>    
    ';
?>
<div class="chart-content">
    <div class = 'row'>
        <div class="col-sm-12 col-md-12 tc chart-title">
                核心指标<span class="chart-title-span tr">（0000年0月0日 周一）</span><i class="fa fa-question-circle" data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="<?= $show ?>"></i>
        </div>
        <div class="col-sm-12 col-md-12 tc chart-title">
            <?php echo $this->render('_chartSearch'); ?>
        </div>
    </div>

    <div id="chartMain" class="chart-main">
        <div class="row">

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info">
                    <ul class="three-party">
                        <li>
                            <div class="desc">预约人数</div>
                            <div class="num">0<span class="unit">人</span></div>
                        </li>
                        <li>
                            <div class="desc">接诊人数</div>
                            <div class="num">0<span class="unit">人</span></div>
                        </li>
                        <li>
                            <div class="desc">付款人数</div>
                            <div class="num">0<span class="unit">人</span></div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info">
                    <ul class="two-party">
                        <li>
                            <div class="desc">就诊实收金额</div>
                            <div class="num">0<span class="unit">.00元</span></div>
                        </li>
                        <li>
                            <div class="desc">客单价</div>
                            <div class="num">0<span class="unit">.00元</span></div>
                        </li>
                        <span class="clearfix"></span>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info">
                    <ul class="two-party">
                        <li>
                            <div class="desc">会员卡销量</div>
                            <div class="num">0<span class="unit">张</span></div>
                        </li>
                        <li>
                            <div class="desc">会员卡充值金额</div>
                            <div class="num" >0<span class="unit">.00元</span></div>
                        </li>
                        <span class="clearfix"></span>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info">
                    <ul class="three-party">
                        <li>
                            <div class="desc">消费占比</div>
                            <div class="num">0<span class="unit">.00%</span></div>
                        </li>
                        <li>
                            <div class="desc">市场效率</div>
                            <div class="num">0<span class="unit">.00%</span></div>
                        </li>
                        <li>
                            <div class="desc">销售效率</div>
                            <div class="num">0<span class="unit">.00%</span></div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">
    var chartDate = "<?= date("Y-m-d",strtotime("-1 day")) ?>";
    var apiGetChartData = '<?= Url::to(['@apiDataGetChartData']) ?>';
    require(["<?= $baseUrl ?>" + "/public/js/data/chart.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock();?>