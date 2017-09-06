<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="row" id="h3s5">
                <h3 class="grey"> Grid Preview </h3>
            </div>
            <div class="row" id="step5">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert-info padding-10">
                    <div class="row">
                        <div class="col-md-4 font16">Project Name:<b> Project 1</b></div>
                        <div class="col-md-4 font16">Project Code: <b> 1025-4585-XXX</b></div>
                        <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 font16">Pollen Receptor: <b>45850-XX</b></div>
                        <div class="col-md-4 font16">Pollen Donnor: <b>45850-XX</b></div>
                        <div class="col-md-3 font16">Number of Samples:<b> 170</b></div>
                    </div>
                    
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php
                $row  = 8;
                $cols = 12;
                echo "<div id='plate'>";

                    echo "<form name='plate_samples' id='plate_samples'  action='".Yii::$app->homeUrl."site/plate-load' method='post'>";
                    for($i= 1; $i <= $row; $i++)
                    {
                        echo "<div id='row' >";
                        for($e= 1; $e <= $cols; $e++)
                        {
                            if(($i == 6 || $i == 7 || $i == 8 ) && $e == 12)
                                $class="parents";
                            else
                                $class="position";
                            
                            echo "<a href='#' onclick='return actions(event);'>"
                                        . "<div class='".$class."' id='".$i."-".$e."'>1058-B".$i.$e."<input type='hidden' name='".$i."-".$e."' id='sample".$i."-".$e."' size='1'></input>"
                                        . "</div>"
                                   . "</a>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                    ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc" id="BackToStep4"><?= Yii::t('app', '<span class="glyphicon glyphicon-arrow-up"></span> Back To Previous Step'); ?></button>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?=  Html::Button('Send To Breeder', ['type'=>'submit', 'class' => 'btn btn-primary in-nuevos-reclamos']);  ?>
                    
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <button type="button" class="btn btn-primary in-nuevos-reclamos grey-ccc reset"><?= Yii::t('app', 'Cancel'); ?></button>
                    <?php echo "</form>"?>
                </div>
            </div>

<script>
 init = function()
    {
         $("#step1").removeClass("selected");
         $("#step3").addClass("selected");
         
         $('#container').highcharts({
        credits:{'enabled':false},  
        chart: {
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: 'SNP'
        },
        xAxis: {
            title: {
                enabled: true,
                text: 'Chromosome'
            },
            startOnTick: true,
            endOnTick: true,
            showLastLabel: true,
            tickInterval: 1,
            min: 1,
            max: <?= count($limitsXcM) ?>,
            allowDecimals: false,
        },
        yAxis: {
            
            min: 0,
            //tickInterval: 10,
            title: {
                text: 'cM'
            }
        },
        legend: {
            enabled: false,
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 100,
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            borderWidth: 1
        },
        plotOptions: {
            scatter: {
                marker: {
                    radius: 5,
                    // symbol: 'square',
                    states: {
                        hover: {
                            enabled: true,
                            lineColor: 'rgb(100,100,100)'
                        }
                    }
                },
                states: {
                    hover: {
                        marker: {
                            enabled: false
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: '<b>Chromosome:</b> {point.x} <br/><b>Position:</b> {point.y} cM'
                }
            },
            series:
            {
                turboThreshold: 0
            }
        },
        series: 
        [
         /****************  MAXIMOS  ****************/
     <?php if(isset($limitsXcM)): ?>
        {
            //name: 'Max cM',
            color: 'rgba(255, 0, 0, .7)',
           
            data: [
            <?php
                $Linkage =1;
                $cont = 0;
                foreach($limitsXcM as $limit)
                {
                    echo '{name: "Max", x: ' .$limit['LinkageGroup'].', y: ' .$limit ['MAX(Position)'].'},';
                   $Linkage++; 
                }
            ?>//
            ],
            marker:{
                symbol:"triangle-down",
                radius:7,
            }
           
        },
        
        <?php endif;?>
    
        {
            color: 'rgba(119, 152, 191, .6)',
            data: 
            [
            <?php
                if($dataProvider != NULL)
                {
                    $dataProvider->setPagination(false);
                    $dataProvider->refresh();
                    $data = $dataProvider->getModels();
                    foreach($data as $marker)
                    {
                        $ll = $marker['Position'] == '' ? 0 :$marker['Position'];
                        $ll2 = $marker['LinkageGroup'] == '' ? 0 :$marker['LinkageGroup'];
                        echo '{name: "'.$marker['LabName'].'", x: ' .$ll2 .', y: ' .$ll .'},';
                    }
                }
            ?>//
            ],
            marker:{
                radius:4,
            }
        }, 
        
   
        ]
    });
       
        $("#form_step4").submit(function(e){
           e.preventDefault();
           $("#step4").slideUp(600, function()
                    {
                        $("#step5").slideDown("600");
                        $('#h3s4 h3').addClass("grey");
                        $('#h3s5 h3').removeClass("grey");
                    });
         });
         
        $("#plate_samples").submit(function(e){
           e.preventDefault();
           $("#step6").slideDown(600, function()
                    {
                        $("#step5").slideUp("600");
                        $('#h3s5 h3').addClass("grey");
                        $('#h3s6 h3').removeClass("grey");
                    });
         });
       
       /*********************   ONLY STEP 2   ************************/
        $("#button2").click(function(e)
         {
             e.preventDefault();
             $("#step2").slideUp(600, function()
                      {
                          $('#h3s3 h3').removeClass("grey");
                          $('#h3s2 h3').addClass("grey");
                          $("#step3").slideDown("slow");
                      });
         });
         
        $("#button3").click(function(e)
         {
             e.preventDefault();
             $("#step2").slideUp(600, function()
                      {
                          $('#h3s3 h3').removeClass("grey");
                          $('#h3s2 h3').addClass("grey");
                          $("#step3").slideDown("slow");
                      });
         });
         
        $("#button4").click(function(e)
        {
             e.preventDefault();
             $("#step2").slideUp(600, function()
                      {
                          $('#h3s3 h3').removeClass("grey");
                          $('#h3s2 h3').addClass("grey");
                          $("#step3").slideDown("slow");
                      });
         });
        /*********************   //ONLY STEP 2   ************************/

        $(".reset").click(function(e)
        {
           e.preventDefault();
           window.location=  "<?= Yii::$app->homeUrl; ?>project/create";
        });
        $("#select_markers").click(function(e)
        {
           e.preventDefault();
           $("#step3").slideUp(600, function()
            {
                $('#h3s3 h3').addClass("grey");
                $('#h3s2 h3').removeClass("grey");
                $("#step2").slideDown("slow");
            });     
        });
       
       $("#saveStep2").click(function(e)
       {
           e.preventDefault();
           $("#step2").slideUp(600, function()
            {
                $('#h3s2 h3').addClass("grey");
                $("#step4").slideDown("slow");
                $('#h3s4 h3').removeClass("grey");
            });
       })
           
    /*******************    BACK TO  *************************/
    
        $("#BackToStep1").click(function(e)
           {
               e.preventDefault();
               $("#step2").slideUp(600, function()
                {
                    $('#h3s2 h3').addClass("grey");
                    $("#step1").slideDown("slow");
                    $('#h3s1 h3').removeClass("grey");
                });
           });
        $("#BackToStep2").click(function(e)
        {
            e.preventDefault();
            $("#step4").slideUp(600, function()
             {
                 $('#h3s4 h3').addClass("grey");
                 $("#step2").slideDown("slow");
                 $('#h3s2 h3').removeClass("grey");
             });
        });
        $("#BackToStep4").click(function(e)
        {
            e.preventDefault();
            $("#step5").slideUp(600, function()
             {
                 $('#h3s5 h3').addClass("grey");
                 $("#step4").slideDown("slow");
                 $('#h3s4 h3').removeClass("grey");
             });
        });
        
        $.each($('#reset'), function(index, value) { 
            $(this).click(function(e)
            {
                
                e.preventDefault();
                window.location=  "<?= Yii::$app->homeUrl; ?>project/create";
            });
        });
        
        
        /**********************   Dinamic radios by FP Name*****************************/
        <?php if($query_selected == 2): ?>
             <?php if($fp_material1 != "" or $fp_material2 != ""): ?>
                    
            if($("#fingerprintsearch-fingerprint_material_id").val() != ""  )
            {
                
                var $fp_id1= $("#fingerprintsearch-fingerprint_material_id").val();
                                 
                        $("#result").show();
                        var divRes  = $('<div/>').attr('id', 'result1');
                        var idFp    = "<?= $fp_material1["Fingerprint_Id"]  ?>";
                        var tagId   = 1;
                         divRes.css('background-color','#B2F269');

                        divRes.html("<input type='radio' value='<?= $fp_material1["Fingerprint_Material_Id"] ?>' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$fp_id1+","+idFp+",\""+tagId+"\")' checked />\n\
                                   Fingerprint:<strong><?= $fp_material1["Name"] ?></strong>\n\
                                    <br>"); 
                        $("#result").append(divRes);               
            }
            
            if($("#fingerprintsearch-fingerprint_material_id2").val() != "" )
            {
                var $fp_id1= $("#fingerprintsearch-fingerprint_material_id2").val();
                        
                        $("#result2").show();
                        var divRes  = $('<div/>').attr('id', 'result1');
                        var idFp    = "<?= $fp_material2["Fingerprint_Id"] ?>";
                        var tagId   = 2;
                        divRes.css('background-color','#B2F269');
                        
                        divRes.html("<input type='radio' value='<?= $fp_material2["Fingerprint_Material_Id"] ?>' name='FingerprintSearch[radio"+tagId+"]' id='radio"+tagId+"' onchange='alerts("+$fp_id1+","+idFp+",\""+tagId+"\")' checked />\n\
                                   Fingerprint:<strong><?= $fp_material2["Name"] ?></strong>\n\
                                    <br>"); 
                        $("#result2").append(divRes);               
            }
       <?php endif;?>
 
       $("#Polymorfism").submit(function(e)
                        {
                          $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');}) 
                           
                          if(typeof $("#radio1").val() != "undefined" )
                          { 
                                 if($("#radio1").is(":checked"))
                                 {
                                     $("#result div").css('background-color','#B2F269');
                                 }else
                                 {
                                    $("#result div").css('background-color','#EF4242');
                                     e.preventDefault();
                                     $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});                 
                                 }

                                 if($("#radio2").is(":checked"))
                                 {
                                     $("#result2 div").css('background-color','#B2F269');
                                 }else
                                 {
                                    $("#result2 div").css('background-color','#EF4242');
                                     e.preventDefault();
                                     $("#divBlack").fadeOut(function(){$('body').css('overflow','auto');});                    
                                 }

                                  if($("#radio2").is(":checked") && $("#radio1").is(":checked"))
                                     $("#divBlack").fadeIn(function(){$('body').css('overflow','hidden');})  
                            }
                                  
                        }); 

    /****************************  FLOAT MENU  ***********************************/    
    var $win = $(window);
        // definir mediente $pos la altura en píxeles desde el borde superior de la ventana del navegador y el elemento
       
        $win.scroll(function () {
            var i = $("#container").text(); 
            if(i == "")            //if($("#more-filters").css('height')== "270px")
                var $pos = 600;
            else
                var $pos = 900;
           if ($win.scrollTop() >= $pos)
             $("#menu_float").fadeIn();
         else
              $("#menu_float").fadeOut();

         });
         
         $("#menu_float").hover(
                 function(){$(this).css('opacity',1)},
                 function(){$(this).css('opacity',.7)}
                 
          );
	

        /***************  highcharts ************************/
        $('#container').highcharts({
	
        credits:{'enabled':false},  
        chart: {
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
            text: ' Polymorphism ( <?= $dataProvider == NULL ? "" : count($dataProvider->getModels())?> of <?= $totalCompared ?> compared)'
        },
        // subtitle: {
            // text: 'SNP'
        // },
        xAxis: {
            title: {
                enabled: true,
                text: 'Chromosome'
            },
            startOnTick: true,
            endOnTick: true,
            allowDecimals:false,
            tickInterval: 1
            
        },
        yAxis: {
            //allowDecimals:true,
            min: 0,
            tickInterval: 10,
           
            title: {
                text: 'cM'
            },
            //showLastLabel: true
        },
        legend: {
            enabled:false,
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 100,
            y: 70,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            borderWidth: 1
        },
        plotOptions: {
            scatter: {
                marker: {
                    radius: 5,
                    states: {
                        hover: {
                            enabled: true,
                            lineColor: 'rgb(100,100,100)'
                        }
                    }
                },
                states: {
                    hover: {
                        marker: {
                            enabled: false
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: '<b>Chromosome:</b> {point.x} <br/><b>Position:</b> {point.y} cm'
                }
            }
        },
	series: [
             /****************  MAXIMOS****************/
      <?php if(isset($limitsXcM)): ?>
        {
            //name: 'Max cM',
            color: 'rgba(255, 0, 0, .7)',
           
            data: [
            <?php
                $Linkage =1;
                $cont = 0;
                foreach($limitsXcM as $limit)
                {
                    echo '{name: "Max", x: ' .$limit['LinkageGroup'].', y: ' .$limit ['MAX(Position)'].'},';
                   $Linkage++; 
                }
            ?>//
            ],
            marker:{
                symbol:"triangle-down",
                radius:7,
            }
           
        },
        
        <?php endif;?>
            
            {
		
	    color: 'rgba(119, 152, 191, .5)',
            data: [
		
			
           <?php
           if($dataProvider):
                $dataProvider->setPagination(false);
                $dataProvider->refresh();
                $data = $dataProvider->getModels();
                foreach($data as $marker)
                {
                    $ll = $marker['Position'] == '' ? 0 :$marker['Position'];
                    $ll2 = $marker['LinkageGroup'] == '' ? 0 :$marker['LinkageGroup'];
                    echo '{name: "'.$marker['Name'].'", x: ' .$ll2 .', y: ' .$ll .'},';
                }
               endif; 
            ?>//
                ],
            },
            
                
            ],
    });
    <?php endif; ?>

    // Trigger change event on crop if cropId is in session
     <?php if(Yii::$app->session->get('cropId') != null ): ?>
      <?php if($fp_material1 == ""): ?>
        setTimeout(function(){
        $('#fingerprintsearch-crop').trigger('change');
      }, 100);
    
      <?php endif;?>
    <?php endif;?>
    
    };
</script>