<?php /*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ 
$this->registerJs('init_view_new_style('.$model->ProjectId.',"'.Yii::$app->request->baseUrl.'");',$this::POS_READY);
?>

<h1>Protocols</h1>
<div id="gridProtocols" style="margin-bottom:15px; margin-top:15px"></div>
<h1>Reports</h1>
<div id="gridReport" style="margin-top:15px"></div>
<div id="modal-ajax"></div>

<script>
    /*
     * 
     
    alert('sdfdsf');
    var headerAttributes = {'style': 'height: 25px;text-align: center;white-space: pre-line; font-size:20px'};
    $('#gridProtocols').kendoGrid({
        dataSource: {
            //autoSync: true,
            transport: {
                read: {
                    url: "../protocol/get-protocols-data?id=<?= $model->ProjectId?>",
                    dataType: "json" // "jsonp" is required for cross-domain requests; use "json" for same-domain requests
                },
            },
            schema: {
                model: {
                    fields: {
                        // datesMaxAmount: { type: "number"},
                        ProtocolId: {type: 'number'},
                        Code: {type: "string"},
                        ProtocolResult: {type: "string"},
                        //ProtocolResultId: { type: "number"},
                        Comments: {type: 'string'}
                    },
                }
            }
        },
        //height: 250,
        // width:
        //scrollable: true,
        navigatable: true,
        //editable: false,
        toolbar: [
            {
                template: '<a class="k-button" href="\\#" onclick="return addProtocol();">Add Protocol</a>'
            }
        ],
        columns: [
            {
                field: "ProtocolId",
                title: "ID",
                width: '5px',
                hidden: true,
            },
            {
                field: "Code",
                title: "Code",
                width: '50px',
                attributes: {
                    "class": "cell-title",
                },
//                   editor: function(cont, options) 
//                   {
//                       $("<span>" + options.model.trial  + "</span>").appendTo(cont);
//                   },
                headerAttributes: headerAttributes
            },
            {
                field: "ProtocolResult",
                title: "Result",
                width: '100px',
                headerAttributes: headerAttributes
            },
            {
                field: "Comments",
                title: "Comments",
                width: '150px',
                headerAttributes: headerAttributes
            },
            {
                command: [
                    {
                        //name: 'destroy', 
                        text: 'Update',
                        click: function (e)
                        {
                            var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                            $.ajax({
                                url: "../protocol/update?id=" + dataItem.ProtocolId, //Server script to process data
                                cache: false,
                                type: "GET",
                                // Form data
                                //data:{protocolId: dataItem.ProtocolId},
                                beforeSend: function () {
                                    //$("#FormField").hide('500');
                                    $("#modal-ajax").html("<div class='modal fade' id='myModal' role='dialog'>\n\
                                                                    <div class='modal-dialog'>\n\
                                                                        <div class='modal-content'>\n\
                                                                            <div class='modal-header'>\n\
                                                                                <button type='button' class='close' data-dismiss='modal'>&times;</button>\n\
                                                                                <div id='result-ajax' style='min-height:400px !important;'></div>\n\
                                                                                <div id='loading_modal' style='text-align:center; top:150px;display: inline'>\n\
                                                                                    <img src='" + urlPhp + "/images/loading.gif' width='60'/>\n\
                                                                                    <br>\n\
                                                                                    <span style='color:#333'>Wait please..</span>\n\
                                                                                </div>\n\
                                                                            </div>\n\
                                                                        </div>\n\
                                                                    </div>\n\
                                                                </div>");

                                    $("#myModal").modal();
                                },
                                success: function (response) {
                                    $("#loading_modal").hide();
                                    $("#result-ajax").html(response);
                                    return false;
                                },
                                error: function (error) {
                                    alert('error');
                                    //$(".modal-body").html(error);
                                },
                            });
                        }
                    },
                    {
                        //name: 'destroy', 
                        text: 'Delete',
                        click: function (e)
                        {
                            e.preventDefault();
                            var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                            if (confirm('Are you sure you want to delete: ' + dataItem.Code)) {
                                $.ajax({
                                    url: "../protocol/delete-protocol", //Server script to process data
                                    type: 'POST',
                                    data: {protocolId: dataItem.ProtocolId, projectId: projectId},
                                    beforeSend: function () {
                                        //console.log('esperando');
                                    }, // its a function which you have to define
                                    success: function (response) {

                                        if (response === 'alert')
                                        {
                                            alert("This protocol is asociated with other projects.")
                                        }
                                        var grid = $('#gridProtocols').data("kendoGrid");
                                        var grid2 = $('#gridReport').data("kendoGrid");
                                        //grid.dataSource.remove(dataItem);
                                        grid2.dataSource.read();

                                        grid.dataSource.remove(dataItem);
                                        grid.dataSource.read();
                                        //grid.refresh();
                                        //}
                                        return false;
                                    },
                                    error: function (error) {
                                        alert('error');
                                    },
                                });
                            }
                        }
                    }],
                title: '',
                width: 55,
                headerAttributes: headerAttributes
            },
        ],
    });

    $('#gridReport').kendoGrid({
        dataSource: {
            autoSync: true,
            transport: {
                read: {
                    url: urlPhp + "/report/get-report-data?id=" + projectId,
                    dataType: "json" // "jsonp" is required for cross-domain requests; use "json" for same-domain requests
                },
            },
            schema: {
                model: {
                    fields: {
                        // datesMaxAmount: { type: "number"},
                        ReportTypeId: {type: 'number'},
                        ReportName: {type: "string"},
                        Url: {type: "string"},
                        Date: {type: "datetime"},
                        ReportId: {type: 'number'}
                    },
                }
            }
        },
        //height: 250,
        // width:
        scrollable: true,
        navigatable: true,
        editable: false,
        resizable: true,
        sortable: true,
        //toolbar: ["create"],
        columns: [
            {
                field: "ReportTypeId",
                title: "ID",
                width: '5px',
                hidden: true,
            },
            {
                field: "ReportName",
                title: "Report Type",
                width: '100px',
                attributes: {
                    "class": "cell-title",
                },
//                   editor: function(cont, options) 
//                   {
//                       $("<span>" + options.model.trial  + "</span>").appendTo(cont);
//                   },
                headerAttributes: headerAttributes
            },
            {
                field: "Url",
                title: "URL",
                template: function (dataItem) {
                    if (dataItem.Url !== null)
                        return '<a href="' + urlPhp + dataItem.Url + '" class="new-link">' + dataItem.File + '<\/a>';
                    else
                        return 'Empty';
                },
                width: '190px',
                headerAttributes: headerAttributes
            },
            {
                field: "Date",
                title: "Date",
                width: '70px',
                headerAttributes: headerAttributes
            },
            {
                field: "ReportId",
                title: "ReportId",
                width: '5px',
                hidden: true,
            },
            {
                command: [
                    {
                        text: 'Upload',
                        click: function (e)
                        {
                            e.preventDefault();
                            var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                            //console.log(dataItem);
                            //var formData = new FormData($('form')[0]);

                            $.ajax({
                                url: "../report/upload-report?ProjectId=" + projectId, //Server script to process data
                                cache: false,
                                type: "GET",
                                // Form data
                                data: {ReportId: dataItem.ReportTypeId},
                                beforeSend: function () {
                                    //$("#FormField").hide('500');
                                    $("#modal-ajax").html("<div class='modal fade' id='myModal' role='dialog'>\n\
                                                                    <div class='modal-dialog'>\n\
                                                                        <div class='modal-content'>\n\
                                                                            <div class='modal-header'>\n\
                                                                                <button type='button' class='close' data-dismiss='modal'>&times;</button>\n\
                                                                                <div id='result-ajax' style='height:400px !important;'></div>\n\
                                                                                <div id='loading_modal' style='text-align:center; top:150px;display: inline'>\n\
                                                                                    <img src='" + urlPhp + "/images/loading.gif' width='60'/>\n\
                                                                                    <br>\n\
                                                                                    <span style='color:#333'>Wait please..</span>\n\
                                                                                </div>\n\
                                                                            </div>\n\
                                                                        </div>\n\
                                                                    </div>\n\
                                                                </div>");

                                    $("#myModal").modal();
                                },
                                success: function (response) {
                                    $("#loading_modal").hide();
                                    $("#result-ajax").html(response);
                                    return false;
                                },
                                error: function (error) {
                                    alert('error');
                                    //$(".modal-body").html(error);
                                },
                            });
                        }
                    },
                    {
                        //name: 'destroy', 
                        text: 'Delete',
                        click: function (e)
                        {
                            e.preventDefault();
                            var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
                            if (confirm('Are you sure you want to delete : ' + dataItem.ReportName)) {
                                $.ajax({
                                    url: "../report/delete-report", //?ProjectId="+projectId,  //Server script to process data
                                    type: 'POST',
                                    data: {id: dataItem.ReportId, url: dataItem.Url, projectId: projectId},
                                    beforeSend: function () {
                                        //console.log('esperando');
                                    }, // its a function which you have to define
                                    success: function (response) {
                                        console.log(response);
                                        if (response)
                                        {
                                            var grid = $('#gridReport').data("kendoGrid");
                                            //grid.dataSource.remove(dataItem);
                                            grid.dataSource.read();

                                            var grid = $('#gridProtocols').data("kendoGrid");
                                            grid.dataSource.read();
                                            //grid.refresh();
                                        }
                                        return false;
                                    },
                                    error: function (error) {
                                        alert('error');
                                    },
                                    //Options to tell jQuery not to process data or worry about content-type.
                                    //cache: false,
                                    //contentType: false,
                                    //processData: false
                                });
                            }
                        }
                    }],
                title: '',
                width: 65,
                headerAttributes: headerAttributes
            },
        ],
        sort: {field: "ReportName", dir: "desc"}
    });
    */
</script>