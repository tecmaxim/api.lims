<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
/*


<style>
    
    .asd{
        font-size: 14px;
        color:#0000ff;
    }
   .glyphicon-1{ font-size: 12px; width: 12px; height: 12px; }
   #mar{
        margin-bottom: 5px;
   }
   #mar2{
        margin-bottom: 35px;
        position: relative;
        
   }
   //.btn-gray{ width: 100%; border:1px solid #0097cf;
   //}
   .btn.btn-default.find
   {
       padding: 9px;
       margin-top: -7px;
   }
</style>
*/
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
                        <?= $form->field($searchModel, 'search')->textInput(['class' => 'form-control', 'placeholder' => 'Enter a full or partial Name to search..'])->label(false)?>
                      </div>       
                            
                        <span class="input-group-btn">
                           
                               <!-- <button class="btn btn-default find" type="submit" ><img src="<?= Yii::$app->urlManager->baseUrl ?>/images/ico-search.png" alt="" > Search</button> -->
                                <?= Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                        </span>
                            
                        <a class="collapse-reclamo asd" data-toggle="collapse" href="#tipo-de-reclamo" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
				<div class="collapse" id="tipo-de-reclamo">
					 
					<div class="container-selects margin-top">										
                                            <?= $form->field($searchModel, 'Username') ?>    
					</div>
                                        <div class="container-selects margin-top">										
                                            <?= $form->field($searchModel, 'Email') ?>    
					</div>
                                        <div class="container-selects margin-top">										
                                            <?= $form->field($searchModel, 'Role')->dropDownList(ArrayHelper::map($role, 'name', 'name'), ['prompt'=>'-- Select rol --'],  [ "class" => "mySelectBoxClass"]) ?>
					</div>
<!--					<div class="container-selects sl-detalle">
					</div>-->
				</div>
                                				
                        </div>
                      <?php $form->end(); ?>
                </div>
                
            </div>
        </div>


