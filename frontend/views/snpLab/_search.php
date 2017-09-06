    <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\SnpLabSearch;
use common\models\Crop;
use kartik\depdrop\DepDrop;
use common\models\MapType;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SnpLabSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="mar2">
    <div role="tabpanel" class="tabpanel" id="mar">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="search">
                <?php $form = ActiveForm::begin([
                                                'action' => ['index'],
                                                'method' => 'get',
                                                'options' => ['class' => 'form-search form-search-planificacion']
                                                ] ); 
                ?>
                <div class="col-lg-11">
          <!--          <a href="#" class='btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create' onclick="return reset();" > Reset</a> -->
                    <?= $form->field($model, 'search')->textInput(['class' => 'form-control', 'placeholder' => 'Enter a full or partial SNP Lab name to search..'])->label(false)?>
                </div> 
                <span class="input-group-btn">
                    <?= Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                </span>
                <a class="collapse-reclamo asd" data-toggle="collapse" href="#tipo-de-reclamo" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
                <div class="collapse" id="tipo-de-reclamo">
                    <div class="container-selects margin-top" style="<?= Yii::$app->session->get('cropId') != null ? 'display:none;' : '' ?>">
                       <?= $form->field($model, 'Crop')->dropDownList(ArrayHelper::map(Crop::getCropsEnabled(), 'Crop_Id', 'Name'), ['prompt'=>'-- Select Crop --']) ?>
                    </div>
                   <div class="col-md-3">
                    <?php  echo $form->field($model, 'MapTypeId')->widget(DepDrop::classname(), [
                                                                                           //'options' => ["onChange" => "js:materials(this)"],
                                                                                           'data' => $model->MapTypeId != ""? ArrayHelper::map($mapTypes, "MapTypeId", "mapType.Name"  ): [], //hay que meterle un array helper no queda otra
                                                                                           //'options' => ['class' => 'oranges'],
                                                                                           'pluginOptions'=>[
                                                                                           'depends'=>['snplabsearch-crop'],
                                                                                           'placeholder'=>'Select...',
                                                                                           'loading' => false,
                                                                                           'url'=>Url::to(['/map/maps-types'])
                                                                                           ],

                                                                               ]); 
                   ?>
                   </div>
                    <div class="col-md-3">
                        <?php 
                        echo $form->field($model, 'Map')->widget(DepDrop::classname(), [
                                                                                //'options' => ["onChange" => "js:materials(this)"],
                                                                                'data' => [$model->Map => Yii::$app->session['map']],
                                                                                'pluginOptions'=>[
                                                                                'depends'=>['snplabsearch-maptypeid','snplabsearch-crop'],
                                                                                'placeholder'=>'Select',
                                                                                'loading' => false,
                                                                                'url'=>Url::to(['/map/maps-availables'])
                                                                                ],
                                                                                
                                                                    ]); 
                        ?>
                    </div>
                    <div class="col-md-3">										
                        <?= $form->field($model, 'Box') ?>
                    </div>
               
                
                
                    
                    <div class="container-selects margin-top">										
                        <?= $form->field($model, 'Quality') ?>
                    </div>
                    <div class="container-selects margin-top">										
                        <?= $form->field($model, 'AlleleFam')->dropDownList(['A' => 'A', 'T' => 'T'], ['prompt'=>'-- Select Allele Fam --']) ?>
                    </div>
                    <div class="container-selects margin-top">										
                        <?= $form->field($model, 'AlleleVicHex')->dropDownList(['C' => 'C', 'G' => 'G'], ['prompt'=>'-- Select Allele Vic Hex --']) ?>
                    </div>
                     <div class="container-selects margin-top">										
                        <?= $form->field($model, 'Pagination')->dropDownList(['50' => '50', '100' => '100', '500' => '500', 'false' => 'All']) ?>
                    </div>
                </div>
                 <?php $form->end(); ?>
            </div>
                
        </div>
    </div>
</div>
    
    <?php // echo $form->field($model, 'AlleleVicHex') ?>

    <?php // echo $form->field($model, 'ValidatedStatus') ?>

    <?php // echo $form->field($model, 'Quality') ?>

    <?php // echo $form->field($model, 'Box') ?>

    <?php // echo $form->field($model, 'PositionInBox') ?>

    <?php // echo $form->field($model, 'PIC') ?>

    <?php // echo $form->field($model, 'IsActive') ?>

    <?php // echo $form->field($model, 'Observation') ?>