<?php

use yii\helpers\Html;
use yii\helpers\Url;

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

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6">
        <h1>Jobs List</h1>
    </div>
</div>

<div class="row">
    <?php if(Yii::$app->user->getIdentity()->ItemName != "breeder"): ?>
    <!-- ----------------  MENU FLOTANTE   -------------------- -->
    <div id="menu_float">
        <div class="counter-checks"></div>
        <a href="javascript: selectAllJobs();" class ="user export"> <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span></a>
        <div class="single-selection" >
            <a href="#" onclick="return validateSelect();" class ="user export template" > <span class="glyphicon glyphicon-save size12"></span> Download  </a>
            <a href="#" onclick="return sendGrids();" class ="user export template" alt=""><span class="glyphicon glyphicon-envelope size12"></span> Send Grids </a>
        </div>
        <div class="multiple-selection" style="display:none">
            <div class="btn-group">
                <button type="button" class="user export template dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-save size12"></span> Download 
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#" onclick="return validateSelect(1);">Combined</a></li>
                    <li class="divider"></li>
                    <li><a href="#" onclick="return validateSelect();">Separated</a></li>

                </ul>
            </div>
            <div class="btn-group">
                <button type="button" id="alert" class="user export template dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-envelope size12"></span> Send Grids 
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#" onclick="return sendGrids(1);">Combined</a></li>
                    <li class="divider"></li>
                    <li><a href="#" onclick="return sendGrids();">Separated</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ------------------------------------------------------- -->
    <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-4">
        <div id="menu_float2">
            <div class="counter-checks"></div>
            <a href="javascript: selectAllJobs();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"> </span> </a>
            <div class="single-selection">
                <a href="#" onclick="return validateSelect();" class ="user export template" > <span class="glyphicon glyphicon-save size12"></span> Download  </a>
                <a href="#" onclick="return sendGrids();" class ="user export template" alt=""><span class="glyphicon glyphicon-envelope size12"></span> Send Grids </a>
            </div>
            <div class="multiple-selection" style="display:none">
                <div class="btn-group">
                    <button type="button" class="user export template dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-save size12"></span> Download
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="return validateSelect(1);">Combined</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="return validateSelect();">Separated</a></li>

                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" id="alert" class="user export template dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-envelope size12"></span> Send Grids 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="return sendGrids(1);">Combined</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="return sendGrids();">Separated</a></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ----------------- END MENU FIXED ------------------------ -->
    <?php endif; ?>

    <div class="col-xs-12 col-sm-6 col-md-2 <?php echo Yii::$app->user->getIdentity()->ItemName == "breeder" ? "col-md-offset-10" : ""?>">
        <?php // Html::a('Create Project', ['create'], ['class' => 'btn btn-primary btn-nuevo-reclamo pull-right btn-nuevo-create']) ?>
        <?= Html::button(Yii::t('app', 'New {modelClass}', [
               'modelClass' => Yii::t('app', 'Job'),
           ])
           , ['class' => 'btn btn-primary btn-additions btn-block pull-right'
           , 'data-toggle' => 'modal'
           , 'data-target' => '#modal'
           , 'data-url' => Url::toRoute('project/select-project-instance')])
       ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="z-index: 1 !important;">
       <div class="box grid-project">
            <a href="#" class="k-button" id="save">Save State</a>
            <a href="#" class="k-button" id="load">Load State</a>
        </div> 
        <div id="gridStep3"> </div>
    </div>
</div>

<?php $this->registerJs('initStep3();', $this::POS_READY); ?>
<script>
    var $win = $(window);
    // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
    var $pos = 200;
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
    
    
    $("#close2").click(function () {
        $("#divBlack3").fadeOut();
        //window.location = "<?php // Yii::$app->homeUrl ?>/project";
        return false;
    });
  
    initStep3 = function ()
    {
        $('#gridStep3').kendoGrid({
            theme: 'bootstrap',
            dataSource: {
                //data: <?php // json_encode($data); ?>,
                transport: {
                      read:  {
                        url: "<?= Yii::$app->homeUrl; ?>project/get-projects",
                        dataType: "json" // "jsonp" is required for cross-domain requests; use "json" for same-domain requests
                      },
                },
                schema: {
                    model: {
                        fields: {
                            ProjectId: {type: "number"},
                            Name: {type: "string"},
                            ProjectCode: {type: "string"},
                            Priority: {type: 'template'},
                            Username: {type: "string"},
                            //stablishmentAmount: { type: "number"},
                            //Stepping: { type: "number"},
                            DeadLine: {type: "date"},
                            DayRemaingToFlowering: {type: "number"},
                            Date: { type: "date" },
                            StepUpdateAt: { type: "date" },
                            //UpdateAt: { type: "date" }
                            //LastStep:{type: "template"}
                        }
                    }
                },
            },
            columns: [
                {
                    field: "Name",
                    title: "<?= Yii::t('app', 'Name'); ?>",
                    template: '<a href="<?= Yii::$app->homeUrl; ?>project/view?id=#=ProjectId#"> #=Name#<\/a>',
                    width: 300,
                },
                {
                    field: "ProjectCode",
                    title: "<?= Yii::t('app', 'Job Code'); ?>",
                    //template: '<a href="<?= Yii::$app->homeUrl; ?>project/view?id=#=ProjectId#"> #=Name#<\/a>',
                    width: 100,
                },
                {
                    field: "Priority",
                    title: "<?= Yii::t('app', 'Priority'); ?>",
                    //template: '<span class=\"label label-#=Priority.color#\ padding20-20"> #=Priority.type#<\/span>',
                    width: 100,
                    template:function(data)
                            {
                                //console.log(data.Priority);
                                var label = "";
                                switch(data.Priority)
                                {
                                    case "1": //
                                        label = '<div class="label label-danger padding20-20"> High</div>';
                                        break;
                                    case "2": //
                                        label = '<div class="label label-warning padding20-20"> Medium</div>';
                                        break;
                                    case "3": //
                                        label = '<div class="label label-success padding20-20"> Low</div>';
                                        break;
                                }
                                return label;
                                    //"<div class='label #=Step == 'Sent' ? 'label-info text-center': Step == 'DNA Extraction' ? 'label-success text-center' : 'label-default text-center' #'> #=Step# </div>"
                            }
                },
                        {
                    field: "User",
                    title: "<?= Yii::t('app', 'Requested By'); ?>",
                    width: 150
                },
                
                {
                    field: "DayRemaingToFlowering",
                    title: "<?= Yii::t('app', 'Days to Flowering'); ?>",
                    width: 80
                },
                {
                    field: "Date",
                    title: "<?= Yii::t('app', 'Date'); ?>",
                    format: "{0:dd/MM/yyyy}",
                    width: 150,
                    filterable: {
                        extra: false,
                        ui : filterDate,
                    }
                },
                {
                    field: "StepUpdateAt",
                    title: "<?= Yii::t('app', 'Last Step Update'); ?>",
                    format: "{0:dd/MM/yyyy}",
                    width: 150,
                    filterable: {
                        extra: false,
                        ui : filterStepUpdateAt,
                    }
                },
                {
                    field: "Step",
                    title: "<?= Yii::t('app', 'Last Step'); ?>",
                    width: 110,
                    template:function(data)
                                {
                                    //console.log(data.Step);
                                    var label = "";
                                    switch(data.Step)
                                    {
                                        case "Project Definition": //
                                            label = "<div class='label label-primary text-center' > Project Definition </div>";
                                            break;
                                        case 'Markers Selection': //
                                            label = "<div class='label label-primary text-center' > Markers Selection </div>";
                                            break;
                                        case "Grid Definition": //
                                            label = "<div class='label label-primary text-center' > Grid Definition </div>";
                                            break;
                                        case "Sent": //
                                            label = "<div class='label label-info text-center' > Sent </div>";
                                            break;
                                        case "Sample Dispatch": //
                                            label = "<div class='label label-warning text-center' > Sample Dispatch </div>";
                                            break;
                                        case "Sample Reception": //
                                            label = "<div class='label label-warning text-center' > Sample Reception </div>";
                                            break;
                                        case "DNA Extraction": //
                                            label = "<div class='label label-success text-center' > DNA Extraction </div>";
                                            break;
                                        case "Genotyped": //
                                            label = "<div class='label label-default text-center' > Genotyped </div>";
                                            break;
                                        case "Reports": //
                                            label = "<div class='label label-default text-center' > Reports </div>";
                                            break;
                                        case 'Finished': //
                                            label = "<div class='label label-default text-center brown' > Finished </div>";
                                            break;
                                        case 'Canceled': //
                                            label = "<div class='label label-danger text-center' > Canceled </div>";
                                            break;
                                        case 'On Hold': //
                                            label = "<div class='label label-warning text-center onHold' > On Hold </div>";
                                            break;
                                    }
                                    return label;
                                    //"<div class='label #=Step == 'Sent' ? 'label-info text-center': Step == 'DNA Extraction' ? 'label-success text-center' : 'label-default text-center' #'> #=Step# </div>"
                                }
                },
                {
                    template: "<input type='checkbox' onClick='return addCounterJobs()' class='#=(Step == 'Sent' || Step >= 'Grid Definition') ? 'checkbox':'checkbox hidden'#' name='vCheck[]' id='#=ProjectId#'  value='#=ProjectId#' #=IsSent == 1 ? 'disabled':''#/>",
                    width: 50,
                    
                },
                
            ],
            scrollable: true,
            reorderable: true,
            columnMenu: true,
            groupable: true,
            resizable: true,
            height: 780,
            navigatable: true,
            sortable: true,
            filterable: {
                operators: {
                  date: {
                    lte: "Day",
                    eq: "Week",
                    neq: "Month",
                    gte:"Year",
                  }
                }
            },
            pageable: {
                    refresh: true,
                    //pageSizes: true,
                    buttonCount: 5,
                    pageSize: 50,
                    pageSizes: [50, 100, 200, 'All']
            },
            
        });
        
        $('.grid-project').show();
        
        var grid = $("#gridStep3").data("kendoGrid");

        $("#save").click(function (e) {
            e.preventDefault();
            localStorage["kendo-grid-options"] = kendo.stringify(grid.getOptions());
        });

        $("#load").click(function (e) {
            e.preventDefault();
            var options = localStorage["kendo-grid-options"];
            if (options) {
                grid.setOptions(JSON.parse(options));
            }
        });
        
    };
    
    //filtro date 
    filterDate = function(element) {
        element.kendoNumericTextBox({
            change: function(e){
                
                var date = new Date();
                var values = this.value();
                var firstDropDown = $('[data-bind="value: filters[0].operator"]').data('kendoDropDownList');

                if(firstDropDown.value() == "lte")
                {
                    date.setDate(date.getDate()-(values+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "eq")
                {
                    date.setDate(date.getDate()-((7*values)+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "neq")
                {
                    date.setDate(date.getDate()-1);
                    date.setMonth(date.getMonth()-values);
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "gte")
                {
                    date.setDate(date.getDate()-1);
                    date.setFullYear(date.getFullYear()-values);
                    var startOfFilterDate = date;
                }
                startOfFilterDate.setHours(0);
                startOfFilterDate.setMinutes(0);
                startOfFilterDate.setSeconds(0);
                
                var endOfFilterDate = new Date();
                endOfFilterDate.setHours(0);
                endOfFilterDate.setMinutes(0);
                endOfFilterDate.setSeconds(0);

                var filter = {
                    logic: "and",
                    filters: [
                        { field: "Date", operator: "gte", value: startOfFilterDate },
                        { field: "Date", operator: "lte", value: endOfFilterDate },
                    ]
                };

                $("#gridStep3").data("kendoGrid").dataSource.filter(filter);
            }
        });
    }

    //filtro UpdateAt 
    filterUpdateAt = function(element) {
        element.kendoNumericTextBox({
            change: function(e){
                
                var date = new Date();
                var values = this.value();
                var firstDropDown = $('[data-bind="value: filters[0].operator"]').data('kendoDropDownList');
                if(firstDropDown.value() == "lte")
                {
                    date.setDate(date.getDate()-(values+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "eq")
                {
                    date.setDate(date.getDate()-((7*values)+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "neq")
                {
                    date.setDate(date.getDate()-1);
                    date.setMonth(date.getMonth()-values);
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "gte")
                {
                    date.setDate(date.getDate()-1);
                    date.setFullYear(date.getFullYear()-values);
                    var startOfFilterDate = date;
                }
                startOfFilterDate.setHours(0);
                startOfFilterDate.setMinutes(0);
                startOfFilterDate.setSeconds(0);
                var endOfFilterDate = new Date();

                endOfFilterDate.setHours(0);
                endOfFilterDate.setMinutes(0);
                endOfFilterDate.setSeconds(0);
                // alert(startOfFilterDate);
                // alert(endOfFilterDate);
                
                var filter = {
                    logic: "and",
                    filters: [
                        { field: "UpdateAt", operator: "gte", value: startOfFilterDate },
                        { field: "UpdateAt", operator: "lte", value: endOfFilterDate },
                    ]
                };

                $("#gridStep3").data("kendoGrid").dataSource.filter(filter);
                 //dataSource.filter(filter);
            }
        });
    }

    //filtro StepUpdateAt 
    filterStepUpdateAt = function(element) {
        element.kendoNumericTextBox({
            change: function(e){
                
                var date = new Date();
                var values = this.value();
                var firstDropDown = $('[data-bind="value: filters[0].operator"]').data('kendoDropDownList');
                if(firstDropDown.value() == "lte")
                {
                    date.setDate(date.getDate()-(values+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "eq")
                {
                    date.setDate(date.getDate()-((7*values)+1));
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "neq")
                {
                    date.setDate(date.getDate()-1);
                    date.setMonth(date.getMonth()-values);
                    var startOfFilterDate = date;
                }

                if(firstDropDown.value() == "gte")
                {
                    date.setDate(date.getDate()-1);
                    date.setFullYear(date.getFullYear()-values);
                    var startOfFilterDate = date;
                }
                startOfFilterDate.setHours(0);
                startOfFilterDate.setMinutes(0);
                startOfFilterDate.setSeconds(0);
                var endOfFilterDate = new Date();

                endOfFilterDate.setHours(0);
                endOfFilterDate.setMinutes(0);
                endOfFilterDate.setSeconds(0);
                // alert(startOfFilterDate);
                // alert(endOfFilterDate);
                
                var filter = {
                    logic: "and",
                    filters: [
                        { field: "StepUpdateAt", operator: "gte", value: startOfFilterDate },
                        { field: "StepUpdateAt", operator: "lte", value: endOfFilterDate },
                    ]
                };

                $("#gridStep3").data("kendoGrid").dataSource.filter(filter);
                 //dataSource.filter(filter);
            }
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
            if (confirm("Do you want to send the e-mail?"))
            {
                $("#divBlack").fadeIn(function () {
                    $('body').css('overflow', 'hidden');
                });
                $.post(
                        "<?= Yii::$app->homeUrl; ?>project/send-grids?type=" + type, $("input[name='vCheck[]']").serialize(), function (e) {
                    $("#divBlack").fadeOut(function () {
                        $('body').css('overflow', 'auto');
                    });
                    switch (e)
                    {
                        case 'NOT':
                            alert("The selected projects do not belong to the same breeder. Select projects belonging to the same breeder.");
                            break;
                        case 'OK':
                            var grid = $('#gridStep3').data("kendoGrid");
                            var sort = grid.dataSource.sort();
                            var group = grid.dataSource.group();
                            
                            viewFlashMsj();
                            
                            $('.counter-checks').hide();
                            grid.dataSource.read();
                            grid.dataSource.group(group);
                            grid.dataSource.sort(sort);
                                                                                    
                            break;
                        case 'FAIL':
                            alert("The mail couldn't be sent.");
                            break;
                        default:
                            console.log(e);
                            break;

                    }
                }
                );
            }
        }
    }

    validateSelect = function (type)
    {   //console.log($("input[name='vCheck[]']:checked").length);
        if ($("input[name='vCheck[]']:checked").length == 0)
        {
            alert('Please select at least one project to send');
            return false;
        }
        else
        {
            $("#divBlack").fadeIn(function () {
                $('body').css('overflow', 'hidden');
            });
            $.post(
                    "<?= Yii::$app->homeUrl; ?>project/validate-selected", $("input[name='vCheck[]']").serialize(), function (e) {
                $("#divBlack").fadeOut(function () {
                    $('body').css('overflow', 'auto');
                });
                switch (e)
                {
                    case 'NOT':
                        alert("The selected projects do not belong to the same breeder. Select projects belonging to the same breeder.");
                        break;
                    case 'OK':

                        downloadGrids(type);
                        break;
                }
            }
            );

        }
    };

    downloadGrids = function (type)
    {
        $.post(
                "<?= Yii::$app->homeUrl; ?>project/control-f1", $("input[name='vCheck[]']").serialize(), function (e) {
            $("#divBlack").fadeOut(function () {
                $('body').css('overflow', 'auto');
            });
            switch (e)
            {
                case "Y":
                    if (confirm("One project has an F1. Do you want to incorporate it into the grid ?"))
                    {

                        window.location = "<?= Yii::$app->homeUrl; ?>project/download-grids?" + $("input[name='vCheck[]']").serialize() + "&f1=1&type=" + type;

                    } else
                    {
                        window.location = "<?= Yii::$app->homeUrl; ?>project/download-grids?" + $("input[name='vCheck[]']").serialize() + "&type=" + type;
                    }
                    break;
                case "N":
                    window.location = "<?= Yii::$app->homeUrl; ?>project/download-grids?" + $("input[name='vCheck[]']").serialize() + "&type=" + type;

                    break;
            }
        }
        );
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
    .hidden{
        display: none !important;
    }
    
    .k-grid th.k-header .k-link, .k-grid th.k-header
    {
        
        font-weight:700
    }
    .k-grid-content>table>tbody>tr
    {
        background:#fff;
    }


    .k-grid-content>table>tbody>.k-alt
    {
        background:#E7F1F7;
    }

    /*selection*/

    .k-grid table tr.k-state-selected
    {
        background: #f91;
        color: #fff;
    }

    .dropdown-menu
    {
        margin-top: -3px !important;
        z-index:500;
    }
    
    

</style>