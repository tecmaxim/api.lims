<?php   
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Crop;
//use common\models\Campaign;
//use common\models\User;
//use common\models\Marker;
use common\models\ProjectType;
use yii\jui\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('init();', $this::POS_READY);
?>

<div class="project-form">
    <div class="container">
        <div class="row">
            <?= $this->render('_header-steps'); ?>
        </div>
        <div class="row marker-blue-container" >
            <?php $form = ActiveForm::begin(["id" => "form_step1", 'enableAjaxValidation' => false,
                                                    'enableClientValidation' => false,]); ?>
            <div class="row" id="hide-msj" style="display:none">
                <div class="col-md-10 col-md-offset-1 alert alert-danger">
                    * Some errors were found . Please check the data entered .
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($model, 'Priority')->textInput() ?>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?= $form->field($model, 'GenerationId')->textInput() ?>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?= $form->field($model, 'ResearchStationId')->textInput() ?>
                </div>
                <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top-35-padding5">
                    <?=
                    Html::button(Yii::t('app', 'New {modelClass}', [
                                'modelClass' => Yii::t('app', 'Research Station'),
                            ])
                            , ['data-toggle' => 'modal'
                        , 'data-target' => '#modal'
                        , 'class' => 'padding-6_margin-top--5'
                        , 'data-url' => Url::to('../researchstation/create')
                        , 'data-callback' => 'reloadResearchStations'])
                    ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <?php //if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($model, 'UserId')->textInput() ?>
                </div>
                <?php //endif; ?>
                <!-- <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php //= $form->field($model, 'ProjectCode')->textInput(['maxlength' => 50]) ?>
                </div> -->
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($model, 'Crop_Id')->textInput(); ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php echo $form->field($model, 'ProjectTypeId')->textInput(["id" => "typeP","onChange" => "js:changeParents()"]); ?>
                </div>
                <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                    <div class="col-xs-12">
                     <?= $form->field($model, 'UserByProject')->widget(DepDrop::classname(), [
                                                'options' => ["id" => "UserByProject", 'multiple' => true],
                                                'data' => $model->UserByProject != "" ? ArrayHelper::map(\common\models\User::find()->where(["IsActive" => 1])->orderBy(["Username" => SORT_ASC])->all(), "UserId", "Username") : [],
                                                'type' => 2,
                                                'select2Options' => [
                                                                    'options' => [
                                                                                   'placeholder' => 'Loading..',
                                                                                 ],
                                                                    ],
                                                'pluginOptions' => [
                                                    'depends' => ['project-userid'],
                                                    'placeholder' => false,
                                                    'Loading' => false,
                                                    'url' => Url::to(['/user/get-all-users'])
                                                ],
                                        ]); 
                        ?>
                </div>
                <?php endif; ?>
            
                <!-- <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php //$form->field($model, 'HasParents')->textInput(["onChange" => "js:changeParents()"]); ?>
                </div> -->
            </div>
            <div class="row">
                <div class="col-xs-12 ">
                    <div id="divLoad" style="display:none;">
                        <div id="loading-min" class="margin-top-30 " >
                            <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="30"/>
                        </div>
                    </div>
                </div>
            </div>
            <div id="for_projects" style="display: none;">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <?php echo $form->field($model, 'Parent1_donnor')->textInput(['maxlength' => 50, "id" => "parent1"]); ?>
                    </div>
                    <!-- parent 1 -->
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <?= $form->field($model, 'Parent2_receptor')->textInput(['maxlength' => 50, "id" => "parent2"]) ?>
                    </div>
                    <!-- parent 2 -->
                    <!-- <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <?php // $form->field($model, 'NumberSamples') ?>
                    </div> -->
                    <!-- number of samples -->
                    <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 margin-top-40">
                        <?=
                        Html::button(Yii::t('app', 'New {modelClass}', [
                                    'modelClass' => Yii::t('app', 'Material'),
                                ])
                                , ['data-toggle' => 'modal'
                            , 'data-target' => '#modal'
                            , 'class' => 'padding-6_margin-top--5'
                            , 'data-url' => Url::to('../materialtest/create')])
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($model, 'traits_by_parent1')->widget(DepDrop::classname(), [
                                            'options' => ["id" => "traits_by_parent1", 'multiple' => true],
                                            'data' => $model->traits_by_parent1 != "" ? ArrayHelper::map(\common\models\Traits::find()->where(["IsActive" => 1, "Crop_Id"=>$model->Crop_Id])->all(), "TraitsId", "Name") : [],
                                            'type' => 2,
                                            'select2Options' => ['options' => ['placeholder' => 'Loading..',
                                                ],],
                                            'pluginOptions' => [
                                                'depends' => ['project-crop_id'],
                                                'placeholder' => false,
                                                'Loading' => false,
                                                'url' => Url::to(['/traits/traits-by-parent'])
                                                ],
                                            ]); 
                    ?>
                    </div> <!-- / trait Parent 1 -->

                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <?= $form->field($model, 'traits_by_parent2')->widget(DepDrop::classname(), [
                                                'options' => ["id" => "traits_by_parent2", 'multiple' => true],
                                                'data' => $model->traits_by_parent2 != "" ? ArrayHelper::map(\common\models\Traits::find()->where(["IsActive" => 1, "Crop_Id"=>$model->Crop_Id])->all(), "TraitsId", "Name") : [],
                                                'type' => 2,
                                                'select2Options' => [
                                                                    'options' => [
                                                                                   'placeholder' => 'Loading..',
                                                                                 ],
                                                                    ],
                                                'pluginOptions' => [
                                                    //'depends' => ['parent2','project-crop_id'],
                                                    'depends' => ['project-crop_id'],
                                                    'placeholder' => false,
                                                    'Loading' => false,
                                                    'url' => Url::to(['/traits/traits-by-parent'])
                                                ],
                                        ]); 
                        ?>
                    </div> <!-- / trait Parent 2 -->
                </div>
            </div>
            <div id="for_fp" style="display:none">
                <div class="col-md-8 col-xs-12">
                    <?= $form->field($model, 'FpMaterials')->widget(DepDrop::classname(), [
                                                'options' => ['multiple' => true, 'prompt' => 'Loading'],
                                                'data' => $model->FpMaterials != "" ? ArrayHelper::map(\common\models\MaterialTest::find()->where(["IsActive" => 1,"Crop_Id" => $model->Crop_Id ])->orderBy(["Name" => SORT_ASC])->all(), "Material_Test_Id", "Name") : [],
                                                'type' => 2,
                                                'select2Options' => [
                                                                    'options' => [
                                                                                   'placeholder' => 'Loading..',
                                                                                 ],
                                                                    ],
                                                'pluginOptions' => [
                                                    //'allowClear' => true,
                                                    //'depends' => ['parent1','project-crop_id'],
                                                    'depends' => ['project-crop_id'],
                                                    'placeholder' => false,
                                                    //'Loading' => false,
                                                    'url' => Url::to(['/material-test/materials-by-crops-to-fp'])
                                                ],
                                                /*'pluginEvents' => [
                                                    "depdrop.afterChange"=>"function(event, id, value, count) { console.log('murder'); }"
                                                    ]*/
                                        ]); 
                        ?>
                </div>
                <div class="col-md-2 col-xs-6">
                    <?= $form->field($model, 'TissueOrigin')->textInput(); ?>
                </div>
                <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 margin-top-40">
                        <?=
                        Html::button(Yii::t('app', 'New {modelClass}', [
                                    'modelClass' => Yii::t('app', 'Material'),
                                ])
                                , ['data-toggle' => 'modal'
                            , 'data-target' => '#modal'
                            , 'class' => 'padding-6_margin-top--5'
                            , 'data-url' => Url::to('../materialtest/create')])
                        ?>
                    </div>
                <?php endif; ?>
                <?php // $form->field($model, 'MaterialsContainerHidden')->hiddenInput()->label(false) ?>
                <div class="col-md-12">
                    <?= $form->field($model, 'MaterialsContainer')->textarea(); ?>
                </div>
            </div>
            
            <!-- <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " id="traits">
                    <?php /*
                    echo $form->field($model, 'Trait')->widget(DepDrop::classname(), [
                        'options' => ["id" => "Traits", 'multiple' => true],
                        'data' => $model->Trait != "" ? ArrayHelper::map(\common\models\Traits::find()->where(["IsActive" => 1, "Crop_Id"=>$model->Crop_Id])->all(), "TraitsId", "Name") : [],
                        'type' => 2,
                        'select2Options' => ['options' => ['placeholder' => 'Loading..',
                            ],],
                        'pluginOptions' => [
                            'depends' => ['project-crop_id'],
                            'placeholder' => false,
                            'Loading' => false,
                            'url' => Url::to(['/project/traits-by-crop'])
                        ],
                    ]);*/
                    ?>
                </div>
            </div> --> <!-- /Traits -->
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php //= $form->field($model, 'SowingDate')->textInput()   ?>
                    <label>SowingDate</label> 
                    <?=
                    DatePicker::widget([
                        'model' => $model,
                        'attribute' => 'SowingDate',
                        //'changeMonth' => true,
                        //'changeYear' => true,
                        'language' => 'es',
                        'options' => ['class' => 'form-control'],
                        'dateFormat' => 'yyyy-MM-dd',
                        'clientOptions' => ['showAnim' => 'slideDown']
                    ]);
                    ?>      
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?=
                    $form->field($model, 'DeadLine')->widget(\yii\jui\DatePicker::classname(), [
                        'model' => $model,
                        'attribute' => 'DeadLine',
                        //'changeMonth' => true,
                        //'changeYear' => true,
                        'language' => 'es',
                        'options' => ['class' => 'form-control'],
                        'dateFormat' => 'yyyy-MM-dd',
                        'clientOptions' => ['showAnim' => 'slideDown']
                    ]);
                    ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php //= $form->field($model, 'FloweringExpectedDate')->textInput()   ?>
                    <label>Flowering Expected Date</label> 
                    <?=
                    DatePicker::widget([
                        'model' => $model,
                        'attribute' => 'FloweringExpectedDate',
                        //'changeMonth' => true,
                        //'changeYear' => true,
                        'language' => 'es',
                        'options' => ['class' => 'form-control'],
                        'dateFormat' => 'yyyy-MM-dd',
                        'clientOptions' => ['showAnim' => 'slideDown']
                    ]);
                    ?> 
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <?= $form->field($model, 'Comments')->textarea(['rows' => 6]) ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?= Html::Button('Save & Next', ['type' => 'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']) ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset" id="cancel"><?= Yii::t('app', 'Cancel'); ?></button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <?php //= Html::a('Restart', ['#'], ['class' => 'btn btn-gray-clear  btn-nuevo-reclamo  btn-nuevo-create reset', "id"=>"reset"])  ?>       
    </div>
</div>

<script>
        $("#cancel").click(function () {
            $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');});
            <?php if($model->ProjectId != ""): ?>
                window.location = "<?= Yii::$app->homeUrl ?>project/view?id=<?=$model->ProjectId;?>";
            <?php else: ?>
                window.location = "<?= Yii::$app->homeUrl ?>project";
            <?php endif; ?>           
        });
                        
        init = function ()
        {         
            $("#project-hasparents").prop('disabled', true);
            $('#project-userid').kendoDropDownList({
                filter: "startswith",
                optionLabel: "Select User",
                dataTextField: "Username",
                dataValueField: "UserId",
                //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: {
                    type: "json",
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>user/get-users-by-json",
                            //type: "jsonp",
                        }
                    }

                    /*filter: [{
                        field: "UserId",
                        operator: "contains",
                        value: <?php // $model->UserId != "" ? $model->UserId : 'null'; ?>
                    }],*/
                },
                //serverFiltering: true,
            });
            $('#project-researchstationid').kendoDropDownList({
                filter: "startswith",
                optionLabel: "Select Research Station..",
                dataTextField: "Short",
                dataValueField: "ResearchStationId",
                //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: {
                    type: "json",
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>researchstation/get-rs-by-json",
                            //type: "jsonp",
                        }
                    }
                },
                //serverFiltering: true,
            });
            $('#project-generationid').kendoDropDownList({
                //filter: "startswith",
                optionLabel: "Select Generation..",
                dataTextField: "Description",
                dataValueField: "GenerationId",
                dataSource: {
                    type: "json",
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>generation/get-generation-by-json",
                            //type: "jsonp",
                        }
                    }
                },
                //serverFiltering: true,
            });           
            $("#project-crop_id").kendoDropDownList({
                filter: "startswith",
                optionLabel: "Select Crop..",
                dataTextField: "name",
                <?php if($model->ProjectId != ""): ?>
                    enable: false,
                <?php endif;?>
                change: function () {
                    $('#for_fp').slideUp();
                    $('#typeP').trigger('change');
                    
                    //var dataMaterial =  getMaterialsByCrop(this.selectedIndex);
                    //$('#typeP').val("");
                },
                dataValueField: "id",
                //template:'<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: {
                    type: "json",
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>crop/get-crops-enabled-by-kendo",
                            //type: "jsonp",
                        }
                    }
                },
                //serverFiltering: true,
            });
            $("#typeP").kendoDropDownList({
                filter: "startswith",
                optionLabel: "Select Project Type",
                cascadeFrom: "project-crop_id",
                dataTextField: "Name",
                change: function () {
                        var id = $("#typeP").val();
                        //$("#project-crop_id").trigger('change');
                        changeParents(id);
                        
                    //$('#typeP').trigger('change');
                    //dropdownlist.enable();
                },
                dataValueField: "ProjectTypeId",
                dataSource: {
                    serverFiltering: true,
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>project/get-project-type-by-json",
                            //type: "jsonp",
                        }
                    }
                },
                //serverFiltering: true,
            });
            $("#parent1").kendoDropDownList({
                filter: "startswith",
                //autoBind: true,
                dataBound: function(e) {
                            <?php if ($model->Parent1_donnor != "" && $model->traits_by_parent1 == ""): ?>
                                  $('#parent1').trigger('change');
                            <?php endif; ?>
                    },
                cascadeFrom: "project-crop_id",
                optionLabel: "Select parent 1...",
                dataTextField: "name",
                dataValueField: "id",
                template: '<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: {
                    //type: "json",
                    serverFiltering: true,
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>material-test/materials-by-crops-by-kendo",
                            //type: "jsonp",
                        }
                    },
                    filter: [{
                        field: "MaterialTestId",
                        operator: "contains",
                        value: <?= $model->Parent1_donnor != "" ? $model->Parent1_donnor : 'null'; ?>
                    }],
                },
               
            });
            $("#parent2").kendoDropDownList({
                filter: "startswith",
                dataBound: function(e) {
                            <?php if ($model->Parent2_receptor != "" && $model->traits_by_parent2 == ""): ?>
                                  $('#parent2').trigger('change');
                            <?php endif; ?>
                    },
                cascadeFrom: "project-crop_id",
                optionLabel: "Select parent 2...",
                dataTextField: "name",
                dataValueField: "id",
                template: '<span style=\"color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: {
                    
                    //type: "json",
                    serverFiltering: true,
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>material-test/materials-by-crops-by-kendo",
                            //type: "jsonp",
                        }
                    },
                    filter: [{
                        field: "MaterialTestId",
                        operator: "contains",
                        value: <?= $model->Parent2_receptor != "" ? $model->Parent2_receptor : 'null'; ?>
                    }],
                },
            });
            /*$("#traits_parent1").kendoMultiSelect({
                //filter: "startswith",
                //autoBind: true,
                cascadeFrom: "parent1",
                optionLabel: "Select Traits...",
                dataTextField: "name",
                dataValueField: "id",
                dataSource: {
                    //type: "json",
                    serverFiltering: true,
                    transport: {
                        read: {
                            url: "<?= Yii::$app->homeUrl ?>traits/traits-by-parent",
                            //type: "jsonp",
                        }
                    }
                },
            });*/
            $("#project-priority").kendoDropDownList({
                //filter: "startswith",
                //cascadeFrom: "Crop_Id",
                optionLabel: "Select category...",
                dataTextField: "name",
                dataValueField: "value",
                template: '<span style=\"padding: 5px 15px;background-color:${ data.hexa };\">${ data.name }<\/span>',
                dataSource: [{name: "High", value: 1, hexa: "#DF6868"},
                    {name: "Medium", value: 2, hexa: "#FAF388"},
                    {name: "Low", value: 3, hexa: "#BEDB83"}
                ],
            });
            $("#project-hasparents").kendoDropDownList({
                //filter: "startswith",
                enable: false,
                optionLabel: "Select Materials Type...",
                dataTextField: "text",
                dataValueField: "id",
                dataSource: [
                    {text: "Pollen Donor & Receptor", id: 1},
                    {text: "N Materials", id: 2},
                ]
            });
            
            //for new feature to FpMaterials selection

            var dropdownlistUser = $('#project-userid').data("kendoDropDownList");
            <?php if(Yii::$app->user->getIdentity()->itemName == 'breeder'): ?>
                dropdownlistUser.readonly();
            <?php endif;?>

            var dropdownResearch = $("#project-researchstationid").data("kendoDropDownList");
        }
        
        getMaterialsByCrop = function(id)
        {
            $.ajax({
                    url: "<?= Yii::$app->homeUrl ?>material-test/materials-by-crops-by-kendo2",
                    data: {cropId: id},
                    beforeSend: function () {
                        $('#divLoad').show();
                    },
                    success: function (response)
                    {
                        $('#divLoad').fadeOut(500);
                        return response;
                    }
                });
        }
        /*#####################################################################*/
        
        changeParents = function (id)
        {
            if (id == 1)
            {
                /*
                $("#FpMaterials").kendoMultiSelect({
                placeholder: "Find Material...",
                dataTextField: "name",
                dataValueField: "id",
                //autoBind: false,
                height: 150,
                
                /*virtual: {
                    itemHeight: 30,
                    valueMapper: function(options) {
                        console.log('sdfdsf');
                        /*$.ajax({
                            url: "<?php // Yii::$app->homeUrl ?>material-test/example",
                            type: "GET",
                            //dataType: "jsonp",
                            data: convertValues(options.value),
                            success: function (data) {
                                options.success(data);
                            }
                        })
                    }
                },
                dataSource: {
                    //serverFiltering: true,
                    transport: {
                            read: {
                                url: "<?= Yii::$app->homeUrl ?>material-test/materials-by-crops-to-fp?url_crop="+$('#project-crop_id').val(),
                            },
                        },
                    //pageSize: 50,
                    serverPaging: true,
                    serverFiltering: true
                    },
                });*/
                /*
                $('#project-fpmaterials').kendoDropDownList({
                        filter: "startswith",
                        optionLabel: "-- Find Materials --",
                        dataTextField: "name",
                        select:function(e){ 
                                           var dataItem = this.dataSource.view()[e.item.index()];
                                            var target = $("#project-materialscontainer").val();
                                            var idContainers = $("#project-materialscontainerhidden").val();
                                            if (target !== "")
                                            {
                                                $("#project-materialscontainerhidden").val(idContainers+", "+dataItem.id);
                                                $("#project-materialscontainer").val(target+", "+dataItem.name);
                                            }
                                            else
                                            {
                                                $("#project-materialscontainerhidden").val(dataItem.id);
                                                $("#project-materialscontainer").val(dataItem.name);
                                            }
                                         },
                        dataValueField: "id",
                        dataSource: {
                           serverFiltering: true,
                            transport: {
                                read: {
                                    url: "<?php // Yii::$app->homeUrl ?>material-test/materials-by-crops-by-kendo?url_crop="+$('#project-crop_id').val(),
                                }
                            },
                            filter: [{
                                field: "MaterialTestId",
                                operator: "contains",
                                value: <?php // $model->FpMaterials != "" ? $model->FpMaterials : 'null'; ?>
                            }],
                        },
                });
                */
                
                if ($('#for_projects').is(':visible') == true)
                {
                    $('#for_projects').slideUp();
                }
                $('#divLoad').show();
                $('#divLoad').slideUp();
                $('#for_fp').slideDown();
                //$('#for_fp').html(response);
                $('#divLoad').fadeOut(500);
                    
            } else if(id > 1) {
                
                if ($('#for_projects').is(':visible') == false)
                {
                    $('#for_fp').slideUp();
                    $('#for_projects').slideDown();
                    $('#divLoad').fadeOut(500);
                }
            } else if(id == ""){
                
                    $('#for_fp').slideUp();
                    $('#for_projects').slideUp();
                    $('#divLoad').fadeOut(500);
            }
            return false;
            
        };
        
        function convertValues(value) {
            var data = {};

            value = $.isArray(value) ? value : [value];

            for (var idx = 0; idx < value.length; idx++) {
                data["values[" + idx + "]"] = value[idx];
            }

            return data;
        }
        selectAll = function ()
        {
            if ($("input[name='Project[vCheck][]']:checked").length == 0) {
                $("input[name='Project[vCheck][]']").prop("checked", true);
            } else {
                $("input[name='Project[vCheck][]']").prop("checked", false);
            }
        };
        
        $(document).on('change', '#project-floweringexpecteddate', function() {
            var dead = $('#project-deadline').datepicker( "getDate" );
            var flow = $('#project-floweringexpecteddate').datepicker( "getDate" );
            if(flow < dead)
            {
                alert('Warning! You are entering a Flowering Date smaller than the DeadLine');
            }
        });
        
   
        $('body').on('hidden.bs.modal','#modal', function (e) {
            var dropdownResearch = $("#project-researchstationid").data("kendoDropDownList");
            var dropdownMaterial1 = $("#parent1").data("kendoDropDownList");
            var dropdownMaterial2 = $("#parent2").data("kendoDropDownList");
            dropdownResearch.dataSource.read();
            dropdownMaterial1.dataSource.read();
            dropdownMaterial1.dataSource.read();
        });
        $('#form_step1').submit(function(e){
            if($("#for_projects").is(":visible"))
            {
                if($("#parent1").val() === "")
                {
                    alert("Please select Pollen Receptor");
                    return false;
                }
                if($("#parent2").val() === "")
                {
                    alert("Please select Pollen Donor");
                    return false;
                }
            }else if($("#for_fp").is(":visible"))
            {
                /*if ($("input[name='Project[vCheck][]']:checked").length == 0) */
                if($("#project-materialscontainer").val().length == 0)
                {
                    //alert("You must select at least one Material to Fingerprint projects.");
                    //return false;
                }
            }
             $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
        });
        /* EDIT MODE */
    <?php if ($model->ProjectId != "" || Yii::$app->controller->action->id == 'job-continued'): ?>
        $('#divLoad').slideDown();    
        setTimeout(
                    function(){                    
                                changeParents(<?= $model->ProjectTypeId ?>);
                                $('#divBlack').hide();
                                //Disabels to load traits on Update or JobContinued
                                if($('#typeP').val() === "1")
                                {
                                    //$("#project-crop_id").trigger('change');
                                }
                                
                            },
                    4800);
    <?php endif; ?>
        
        /*
         * Find error class form validation
         * @returns {null}
         */
        if($(".has-error")[0])
        {
            $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});
            $("#hide-msj").slideDown();
            changeParents($("#typeP").val());
            //setTimeout(function(){$("#hide-msj").hide(); return false; }, 5000);
            
        };
    </script>
<style>
    #ui-datepicker-div{
        z-index: 100 !important;
    }
</style>