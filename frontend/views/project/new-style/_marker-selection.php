<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
?>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
        <h1>Marker Selection</h1>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">
    <?php if($model->StepProjectId <=   \common\models\StepProject::REPORT ): ?>
        <?= Html::a("Edit", ["update-select-markers" , "idProject"=>$model->ProjectId],['class' => 'btn btn-primary btn-render-content pull-right'] ); ?>
    <?php endif; ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="grid-marker-render"></div>
    </div>
<style>
		.customClass1 {
			background: #222;
			color: #eee;
		}

		.customClass2 {
			background: #555;
			color: #eee;
		}
                
                .grid-markers{
                    text-align: center;
                    font-size: 14px;
                    
                    border-bottom: 1px solid #aaa !important;
                }
	</style>
    <script>
        var headerAttributes = {'style': 'height: 25px;text-align: center;white-space: pre-line; font-size:20px'};
        $('#grid-marker-render').kendoGrid({
           dataSource: {
                //autoSync: true,
                transport: {
                      read:  {
                        url: "get-markers-and-traits?id=<?= $model->ProjectId ?>",
                        dataType: "json" // "jsonp" is required for cross-domain requests; use "json" for same-domain requests
                      },
                },
               
                schema: {
                   model: {
                       fields: {
                           // datesMaxAmount: { type: "number"},
                           MarkersByProjectId:{type:'number'},
                           SnpLab: {type: 'string'},
                           Trait: { type: "string"},
                       },
                   }
               }
           },
           //height: 250,
           //width: 70%,
           scrollable: false,
           navigatable: true,
           //editable: false,
           columns: [
                {
                   field: "MarkersByProjectId",
                   title: "ID",
                   width: '5px',
                   hidden: true,
                },  
                {
                   field: "LabName",
                   title: "SnpLab",
                   width: '100px',
                        attributes: {
                          "class": "grid-markers",
                        },

                   headerAttributes: headerAttributes
                },
                {
                   field: "Name",
                   title: "Traits",
                   width: '100px',
                   attributes: {
                          "class": "grid-markers",
                        },
                   headerAttributes: headerAttributes
                },
           ],
       });
       
       
       
    </script>
    