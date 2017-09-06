<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Cropbyuser;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\models\MapTypeByCrop;
?>

<?php $form = ActiveForm::begin([
    'id' => 'searchSnpLab',
    //'action' => ['query1'],
    'method' => 'get',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
   
]); ?> 
<div id="filters_query">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!--  <div class="col-md-2">
                <?php //= $form->field($searchModel, 'ValidatedStatus') ?>
            </div> -->
            <?php
                if (isset($isLims)) 
                {
                    echo '<input type="hidden" name="SnpLabSearch[hiddenField]" id="snplabsearch-hiddenfield" value="' . $isLims . '">';
                }
            ?>
            <div class="col-md-3">
                <?= $form->field($searchModel, 'MarkerType')->dropDownList([4=> "SNP LAB",/* 1=> "SNP" , 2=>"Microsatellite" */ ]) ?>
            </div>
            <div class="col-md-2" style="<?= Yii::$app->session->get('cropId') != null ? 'display:none;' : '' ?>">
                <?= $form->field($searchModel, 'Crop')->dropDownList(ArrayHelper::map(Cropbyuser::getCropsByUser(), 'Crop_Id', 'crop.Name'), ['prompt'=>'-- Select Crop --']) ?>
            </div>
            <div class="col-md-3">
                <?php
                echo $form->field($searchModel, 'MapTypeId')->widget(DepDrop::classname(), 
                                                                        [
                                                                            //'options' => ["onChange" => "js:materials(this)"],
                                                                           'data' => $searchModel->MapTypeId != ""? ArrayHelper::map(MapTypeByCrop::find()->where(["IsActive" => 1, "Crop_Id" =>Yii::$app->session->get('cropId') ])->all(), "MapTypeId", "mapType.Name"  ): [], //hay que meterle un array helper no queda otra
                                                                           'pluginOptions'=>[
                                                                           'depends'=>['snplabsearch-crop'],
                                                                           'placeholder'=>'Select...',
                                                                           'loading' => true,
                                                                           'url'=>Url::to(['/map/maps-types'])
                                                                           ],

                                                                        ]); 
                ?>
            </div>
            <div class="col-md-3">
                <?php 
                    echo $form->field($searchModel, 'Map')->widget(DepDrop::classname(), [
                                                                                    //'options' => ["onChange" => "js:materials(this)"],
                                                                                    'data' => [$searchModel->Map => Yii::$app->session['map']],
                                                                                    'pluginOptions'=>[
                                                                                    'depends'=>['snplabsearch-maptypeid','snplabsearch-crop'],
                                                                                    'placeholder'=>'Select...',
                                                                                    'loading' => false,
                                                                                    'url'=>Url::to(['/map/maps-availables'])
                                                                                    ],

                                                                        ]); 
                ?>
            </div>
            <div class="col-md-3">										
                <?= $form->field($searchModel, 'Pagination')->dropDownList(['50' => '50', '100' => '100', '500' => '500', 'false' => 'All']) ?>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-md-12">
                <?= $form->field($searchModel, 'batch')->textInput(["placeholder" => "Insert SNP LABS separated by non-breaking space (' '), ',' or ';'  "]) ?>
            </div>
        </div>
    </div>
    <a class="collapse-reclamo asd" data-toggle="collapse" href="#more-filters" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
    <div class="collapse" id="more-filters">
        
        <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'LinkageGroupFrom') ?> 
                </div>            
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'LinkageGroupTo') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'PositionFrom') ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'PositionTo') ?>
                </div>
        </div>
        <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'assayBrandName') ?>
                </div>
                
                <div class="col-md-3">
                     <?= $form->field($searchModel, 'Quality') ?>
                </div>
        </div>
        <div class="row">
                <div class="col-md-12">
                     <?= $form->field($searchModel, 'ValidatedStatus')->checkboxList(ArrayHelper::map($itemsValidated, 'Value', 'Name')) ?>
                </div>
            
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="pull-right">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-nuevo-reclamo  btn-nuevo-create']) ?>
            <a href="#" class='btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create' onclick="return reset();" > Reset</a>
            <a href="#" onclick="return back();" class="btn btn-warning btn-nuevo-reclamo btn-nuevo-create"> Back</a>
            <?php //= Html::resetButton (Yii::t('app', 'Reset'), ['class' => 'btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create']) ?>
        </div>
    </div>
</div>
<?php  ActiveForm::end(); ?>

