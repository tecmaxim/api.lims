<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="row">
    <div class="col-sm-12">
        <h1> Select the Job Instance</h1>
    </div>
</div>
<div class="row">
    <div class="col-md-6 text-center instance-mode">
        <h1 class="title-button-instance"> New Job </h1>
        <div class="button-instance" id="new-project">
            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
        </div>
    </div>
    <div class="col-md-6 text-center instance-mode">
        <h1 class="title-button-instance"> Child Job </h1>
        <div class="button-instance" id="continued">
            <span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span>
        </div>
    </div>
</div>

<script>
    var button_new = document.getElementById('new-project');
    var button_continued = document.getElementById('continued');
    
    button_new.addEventListener("click", function(){
                window.location = "<?= Yii::$app->homeUrl ?>project/create";
    });
    
     button_continued.addEventListener("click", function(){
                window.location = "<?= Yii::$app->homeUrl ?>project/select-project";
    });
</script>