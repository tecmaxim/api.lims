
<div class="container">
    <div class="row">
        <div id="divBlack" style="display:none;">
            <div id="loading" >
                <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
                <br>
                Wait please..
            </div>
        </div>
        <?=  $this->render('_header-steps');  ?>
        <?=  $this->render('resources/__float-title',[ "projectName"=>$projectName]);  ?>
        
    </div>
    
    <div class="row marker-blue-container" >
       <?= $this->render('/plate/_references'); ?> 
       <?= $this->render('../plate/_plates-by-project', ['model' => $model,
                        'samplesByPlate' => $samplesByPlate,
                        'array_parents' => $array_parents,]); ?>
       
        
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Back To Project View'); ?></button>
            </div>
        
    </div>
</div>
<script>
    $("#step1").removeClass("selected");
    $("#sent").addClass("selected");
    $("#cancel").click(function ()
                    {
                    window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?= $model->ProjectId ?>";
                        });
    $(function(){
        $("#adn_extraction").click(function()
        {
            alert ("DISABLED");
            return false;
        });
    
        $("#reorder").click(function()
        {
           alert ("DISABLED");
           return false;
        });
        
        $('body').on('hidden.bs.modal','#modal', function (e) {
            var pageHeight = $( document ).height();
            $('#divBlack').css({"height": pageHeight});
            
            $("#divBlack").show();
            
            window.location.reload();
        });
    });
</script>