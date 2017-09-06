<?php
use yii\helpers\Html;
use common\models\Marker;


$this->registerJs('init_dashboard('.json_encode($categories).','.json_encode($series).',"'.Yii::$app->request->baseUrl.'");', $this::POS_READY);
?>

<a href="#" onclick="return downloadDashboardXls();" class ="user export dashboard pull-right" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Export XLS </a>
<div id="highchart_airplane">    
    
</div>
