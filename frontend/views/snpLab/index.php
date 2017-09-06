<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\Crop;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SnpLabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Snp Labs');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('init();', $this::POS_READY);
?>
<div class="snp-lab-index">
 <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
      
       <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <p>
                <?= Html::button(Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app', 'Snp Lab'),
                ])
                , ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create'
                , 'data-toggle' => 'modal'
                , 'data-target' => '#modal'

                , 'data-url' => Url::to('create')])
            ?>
            </p>
       </div>
 </div>
     <?= $this->render('_search', ['model' => $searchModel, 'fromDashboard' => $fromDashboard,'mapTypes'     => $mapTypes]);  ?>
    <?php if (isset($dataProvider)){ ?>
        <?php if ( $dataProvider->count > 0){ ?>
    
    <!-- ----------------  MENU FLOTANTE   -------------------- -->
    <div id="menu_float">
        <a href="javascript: selectAll();" class ="user export"> <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
        <!-- <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
        <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a>
        -->
        <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
    </div>
<!-- ------------------------------------------------------- -->
    
    
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right">
            <div id="menu_float2">
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
                <a href="#" onclick="return downloadTempalte();" class ="user export template" > <span class="glyphicon glyphicon-save-file size12"  aria-hidden="true"> </span> Template </a>
               <!-- <a href="#" onclick="return exportPartial();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12"  aria-hidden="true"> </span> Export Selection</a>
                <a href="#" onclick="return exportToExcel();" class ="user export" > <span class="glyphicon glyphicon glyphicon-save size12" aria-hidden="true"> </span> Export All</a> -->
                <a href="#" onclick="return deleteSelection();" class ="user export" alt="dasdsad"> <span class="glyphicon glyphicon-trash size12" aria-hidden="true" > </span> Delete</a>
            </div>
        </div>
    </div>
        <?php } ?>
  
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
                                        
					 return '<input type="checkbox" name="vCheck[]" value="'.$data['Snp_lab_Id'].'" >';
					},
            ],
            'LabName',
            ['label' => 'Crop','format' => 'raw','value'=>function ($data) {   
					return Crop::find()->where(["Crop_Id" =>$data['Crop_Id']])->One()->Name;
					}, 
            ],
//                      
           'AlleleFam',
           'AlleleVicHex',
//            [
//            'label'=>'Validate Status',
//            'format' => 'raw',
//            'value'=>function ($data) {
//
//                                    return $data['ValidatedStatus'] == null ? "<kbd>None</kbd>" : $data['ValidatedStatus'];
//                                    },
//            ],
            ['label'=>'Barcode', 'value' => 'Number'],

            'PIC',
            [
            'label'=>'Quality',
            'format' => 'raw',
            'value'=>function ($data) {

                                    return $data['Quality'] == null ? "<kbd>None</kbd>" : $data['Quality'];
                                    },
        ],
            'Box',
            'PositionInBox',
                                                
            // 'IsActive',
            // 'Observation',

             ['class' => 'frontend\widgets\RowLinkColumn'],
        ],
    ]); ?>
    <?php  Pjax::end(); ?>
 <?php } else{  echo "<p>Please complete the necessary filters and click Search.</p>"; }?>
</div>
<script>
    init = function()
	{
         
        var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
        var $pos = 530;
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
  
        <?php if(Yii::$app->session->get('cropId') != null ): ?>
            <?php if(!$dataProvider): ?>
               setTimeout(function(){
                $('#snplabsearch-crop').trigger('change');
                }, 100);
    
            <?php endif;?>
        <?php endif;?>
         
        }
    exportToExcel = function(){
        if(confirm("Are you sure you want to import all items? This action can take several minutes. "))
            window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/excel?"+$("#searchSnp").serialize(); return false;
        return false;
   
    };
     exportPartial = function()
    {
         if ($("input[name='vCheck[]']:checked").length == 0){
            alert("You must select at least one item");
            $('#actionsCombo option:first').attr("selected",true);
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
    
    downloadTempalte = function()
    {
        window.location = "<?= Yii::$app->homeUrl; ?>snplab/download-template"; return false; 
    }
    

</script>