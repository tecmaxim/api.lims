<?php
$this->registerJs('init();', $this::POS_READY);
?>
<h1>Select Interface</h1>
<div id="container">
    <div class="interface oranges" id="lims">
        LIMS <span class="glyphicon glyphicon-list-alt pull-right"></span>
        
    </div>
    <div class="interface cyan" id="gdbms">
        GDBMS <span class="glyphicon glyphicon-import pull-right"></span>
    </div>
</div>
<?php // print_r($_SERVER['HTTP_HOST']); ?>
<script>
    init = function()
    {
       
        $("#lims").click(function(e)
                                {
                                    window.location = "<?= Yii::$app->homeUrl; ?>project/";
                                    return false;
                                });
        $("#gdbms").click(function(e)
                                {
                                    window.location.href = "/gdbms/"; //"<? Yii::$app->homeUrl; ?>site/index-gdbms";
                                    return false;
                                });
    }
</script>