<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MaterialTestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Materials');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('init();', $this::POS_READY);
?>
<div class="material-test-index">

    <div class="row">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <?php if(Yii::$app->user->getIdentity()->itemName != "lab"): ?>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?=  Html::button(Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Material'),]), [
                'class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-url' => Url::to('create')
            ]);?>
         </div>
        <?php endif; ?>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel, 'crop' => $crop, 'fromDashboard' => $fromDashboard]); ?>
<?php if ($dataProvider->count > 0){ ?>
    
     <!-- ----------------  MENU FLOTANTE   -------------------- -->
    <div id="menu_float"> 
        <a href="javascript: selectAll();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
        <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
        <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a>
        <?php if(Yii::$app->user->getIdentity()->itemName != "lab"): ?>
            <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
        <?php endif;?>
    </div>
<!-- ------------------------------------------------------- -->
    
    
    <div class="row">
    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7"></div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <div id="menu_float2"> 
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
                <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
                <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a>
                <?php if(Yii::$app->user->getIdentity()->itemName != "lab"): ?>
                    <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
                <?php endif; ?>
            </div>
        </div>
	 
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
  
   <?php  Pjax::begin(['id' => 'itemList']) ; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['id'=>'Grids', 'class' => 'table table-condensed table-reclamos table-con-link'],
        'columns' => [
            //'Snp_lab_Id',
            //'Snp_Id',
            [
           	'label'=>'',
           	'format' => 'raw',
                'contentOptions' => ['class' => 'no-link'],
       		'value'=>function ($data) {
                                        
					 return '<input type="checkbox" name="vCheck[]" value="'.$data->Material_Test_Id.'" >';
					},
            ],
            'Name',
            ['label'=>'Crop','value'=>'crop.Name'],
            'CodeType',
            'PreviousCode',
            'Owner',
            //'ValidatedStatus',
            
            'Pedigree',    

             ['class' => 'frontend\widgets\RowLinkColumn'],
        ],
    ]); ?>
    <?php  Pjax::end(); ?>

            </div>
        </div>
        
    </div>


<?php } else{  echo "<p>No Materials found</p>"; }?>
</div>
<script>
    init = function()
    {
         
        var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
        var $pos = 330;
        $win.scroll(function () {
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         });
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
         
    };
    exportToExcel = function(){
        window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("#searchSnp").serialize(); return false;
   
    };
     exportPartial = function()
    {
         if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            return false;             
        }else{
           window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("input[name='vCheck[]']").serialize(); return false; 
        };
    };
    
    selectAll = function()
    {   console.log($("input[name='vCheck[]']:checked").length);
        if ( $("input[name='vCheck[]']:checked").length == 0){
		$("input[name='vCheck[]']").prop("checked",true);	
	}else{
		$("input[name='vCheck[]']").prop("checked", false);	
	}
        //return false;
    };
    
    deleteSelection = function()
    {
          if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            return false;             
        }else{
           if( confirm("Are you sure to delete these items ?"))
                window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/delete-selection?"+$("input[name='vCheck[]']").serialize(); return false; 
        };
        
    }

</script>

