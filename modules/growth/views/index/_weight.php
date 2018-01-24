<?php 
    foreach ($weight as $v){
        $month[] = $v['month']; 
        $th3[] = $v['th3'];
        $th15[] = $v['th15'];
        $th50[] = $v['th50'];
        $th85[] = $v['th85'];
        $th97[] = $v['th97'];
        
    }
    foreach ($result as $value){
        $patientWeight[] = $value['weightkg'] == null?0:$value['weightkg'];
        $heightcm[] = $value['heightcm'];
    }
    
?>    	
    	
<div id="main" style="width: 850px;height:400px;"></div>
<script type="text/javascript">
	var baseUrl = '<?= Yii::$app->request->baseUrl; ?>';
	var th3 = <?= json_encode($th3,true); ?>;
	var th15 = <?= json_encode($th15,true); ?>;
	var th50 = <?= json_encode($th50,true); ?>;
	var th85 = <?= json_encode($th85,true); ?>;
	var th97 = <?= json_encode($th97,true); ?>;
	var weight = <?= json_encode($patientWeight,true); ?>;
	var month = <?= json_encode($month,true) ?>;
	var heightcm = <?= json_encode($heightcm,true) ?>;
	var yearType = '<?= $yearType ?>';
    require([ baseUrl + "/public/js/growth/weight.js"], function (main) {
        main.init();
    });
</script>
