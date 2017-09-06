<?php

use yii\helpers\Html;
?>
<!-- This section is for alert mesajes -->
<div id="divBlack3" style="display:none;">
    <div id="vertical-align" class="alert alert-success" >
        <div id="close2"> X </div>
        <b>The grid have been sent successfully!</b>
    </div>
</div>
<div id="divBlack" style="display:none;margin-left: -30px;">
    <div id="loading" >
        <img src="<?= Yii::$app->request->baseUrl ?>/images/loading.gif" width="60"/>
        <br>
        Wait please..
    </div>
</div>
<!-- -- -- -- -- -- -- -- -- -- -- --- -- -->


<?php if ($dataProvider): ?>
    <div class="map-index">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <h1>Project List</h1>
            </div>

            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <?= Html::a('Sample Shipment Record', ['registerShipment'], ['class' => 'btn btn-warning btn-nuevo-reclamo pull-right btn-nuevo-create']) ?>
            </div>
        </div>

    </div>
    <?php
    $data = [];
    $i = 0;

    foreach ($dataProvider->getModels() as $pr) {
        /*
          [ProjectId] => 1
          [Name] => Sunflower150922WJ_BackCross_Nc(FX)
          [Crop_Id] => 1
          [UserId] => 3
          [ProjectTypeId] => 2
          [StepProjectId] => 1
          [ResearchStationId] => 1
          [GenerationId] => 1
          [ProjectCode] => 65465-8
          [Priority] => 2
          [NumberSamples] =>
          [DeadLine] => 2015-09-24
          [FloweringExpectedDate] =>
          [SowingDate] =>
          [Date] => 2015-09-22
          [Comments] =>

         */

        $data[$i]['ProjectId'] = $pr['ProjectId'];
        $data[$i]['Name'] = $pr['Name'];
        $data[$i]['Priority'] = common\models\Project::getPriorityStatic($pr);
        $data[$i]['UserId'] = $pr['user']['Username'];
        $data[$i]['ProjectTypeId'] = $pr['ProjectTypeId'];
        $data[$i]['DeadLine'] = $pr['DeadLine'];
        $data[$i]['IsSent'] = $pr['IsSent'];
        $i++;
    }
    $data = array_values($data);
//$data = array_values($dataProvider->getModels());
    ?>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="gridStep3">
            </div>
        </div>

    </div>
    </div>

    <?php $this->registerJs('initStep3();', $this::POS_READY); ?>
    <script>
        $("#close2").click(function () {
            $("#divBlack3").fadeOut();
            window.location = "<?= Yii::$app->homeUrl ?>/project"
            return false;
        });

        initStep3 = function ()
        {
            $('#gridStep3').kendoGrid({
                dataSource: {
                    data: <?= json_encode($data); ?>,
                    schema: {
                        model: {
                            fields: {
                                Id: {type: "number"},
                                Name: {type: "string"},
                                Priority: {field: 'Priority'},
                                Username: {type: "string"},
                                //stablishmentAmount: { type: "number"},
                                //Stepping: { type: "number"},
                                DeadLine: {type: "date"},
                            },
                        }
                    }
                },
                height: 300,
                scrollable: true,
                sortable: true,
                navigatable: true,
                filterable: true,
                editable: false,
                columns: [
                    {
                        field: "Name",
                        title: "<?= Yii::t('app', 'Name'); ?>",
                        template: '<a href="<?= Yii::$app->homeUrl; ?>project/view?id=#=ProjectId#"> #=Name#<\/a>',
                        width: 550,
                    },
                    {
                        field: "Priority",
                        title: "<?= Yii::t('app', 'Priority'); ?>",
                        template: '<span class=\"#=Priority.color#\"> #=Priority.type#<\/span>',
                    },
                    {
                        field: "UserId",
                        title: "<?= Yii::t('app', 'Requested By'); ?>",
                    },
    //                {
    //                    field: "stablishmentAmount",
    //                    title: "<?= Yii::t('app', 'stablishmentAmount'); ?>",
    //                },
    //                {
    //                    field: "Stepping",
    //                    title: "<?= Yii::t('app', 'Stepping'); ?>",
    //                },
                    {
                        field: "DeadLine",
                        title: "<?= Yii::t('app', 'DeadLine'); ?>",
                        type: "Date",
                        format: "{0:dd-MM-yyyy}"
                    },
                    {
                        template: "<input type='checkbox' class='checkbox' name='vCheck[]' id='#=ProjectId#' value='#=ProjectId#' #=IsSent == 1 ? 'disabled':''#/>",
                        width: 50,
                    },
    //                {
    //                    width: 100,
    //                    command:[{
    //                            name: "DETAILS",                 
    //                            click: function(e) 
    //                                            {
    //                                               e.preventDefault;
    //                                               var obj = this.dataItem($(e.currentTarget).closest("tr"));
    //                                               window.location =  "<?= Yii::$app->homeUrl; ?>project/view?id="+obj.ProjectId;
    //                                               return false;
    //                                            }
    //                                    
    //                            }],
    //                },
                ]
            });
        }

        validateStep3 = function ()
        {
            var ok = true;

            var gridStep3 = $('#gridStep3').data('kendoGrid').dataSource;
            var step3Json = gridStep3.data().toJSON();

            $('#alertStep3').hide();

            $.each(step3Json, function (i, e)
            {
                if (e.Date == null)
                {
                    $('#alertStep3').show();
                    ok = false;
                }
            });

            return ok;
        }

        sendGrids = function (type)
        {   //console.log($("input[name='vCheck[]']:checked").length);
            if ($("input[name='vCheck[]']:checked").length == 0)
            {
                alert('Please select at least one project to send');
                return false;
            }
            else
            {
                $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})
                $.post(
                        "<?= Yii::$app->homeUrl; ?>project/send-grids?type=" + type, $("input[name='vCheck[]']").serialize(), function (e) {
                    $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});
                    switch (e)
                    {
                        case 'NOT':
                            alert("The selected projects do not belong to the same breeder. Select projects belonging to the same breeder.");
                            break;
                        case 'OK':
                            viewFlashMsj();
                            break;
                        case 'FAIL':
                            alert("The mail couldn't be sent.");
                            break;

                    }
                }
                );
            }
        }

        viewFlashMsj = function ()
        {
            $("#divBlack3").fadeIn();

            setTimeout(function () {
                $("#divBlack3").fadeOut('slow');
            }, 13000);
        };


    </script>
    <style>
        .dropdown-menu
        {
            margin-top: -20px !important;
        }

    </style>
<?php else: ?>

    <h1 class="alert alert-info size12">
        
            <li>You do not have active projects.</li>
        
    </h1>
<?php endif; ?>
