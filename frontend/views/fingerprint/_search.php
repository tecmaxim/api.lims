<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;



/* @var $this yii\web\View */
/* @var $model common\models\FingerprintSearch */
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
                        <?= $form->field($model, 'search')->textInput(['class' => 'form-control', 'placeholder' => 'Enter a full or partial name to search..'])->label(false)?>
                      </div>       
                            
                        <span class="input-group-btn">
                           
                               <!-- <button class="btn btn-default find" type="submit" ><img src="<?= Yii::$app->urlManager->baseUrl ?>/images/ico-search.png" alt="" > Search</button> -->
                                <?= Html::submitButton("<img src='".Yii::$app->urlManager->baseUrl."/images/ico-search.png' alt='' / >".Yii::t('app', 'Search'), ['class' => 'btn btn-default find']) ?>
                        </span>
                            
                        <a class="collapse-reclamo asd" data-toggle="collapse" href="#tipo-de-reclamo" aria-expanded="false" aria-controls="tipo-de-reclamo">+ More Filters</a>
				<div class="collapse" id="tipo-de-reclamo">
					 
					<div class="container-selects margin-top">										
                                             <?= $form->field($model, 'Name') ?>
					</div>
                                        <div class="container-selects margin-top">
                                            <label>Date Created</label> 
                                           <?= DatePicker::widget([
                                            'model' => $model,
                                            'attribute' => 'DateCreated',
                                            //'changeMonth' => true,
                                            //'changeYear' => true,
                                            'language' => 'es',
                                            'options' => ['class' => 'form-control'],
                                            'dateFormat' => 'yyyy-MM-dd',
                                            'clientOptions' => ['showAnim' => 'slideDown']
                                            ]);
                                            ?>                    
					</div>
                                        
					</div>     
                                          
                                        <?php //= $form->field($model, 'DateCreated') ?>
				</div>
                                       
<!--					<div class="container-selects sl-detalle">
					</div>-->
                    </div>
                                				
        </div>
                      <?php $form->end(); ?>
    </div>       
<script>
    
</script>
        




