<style>
    #plate
    {
        background-color: #eeeeee;
        padding: 10px;
        overflow: auto;
        margin-top: 50px;
        width: 38%;
        border-radius: 10px;
        border: 1px solid #333333;
    }
    #row
    {
        margin: 5px;
        //background-color: #fff;
        overflow: auto;
        
    }
    .position ,.verde ,.rojo
    {
        padding-top: 15px;
        background-color: #ccc;
        margin: 5px;
        width: 30px;
        height: 30px;
        float: left;
        border-radius: 100%;
        box-shadow: -1px 1px .5px #333;
        //float:left;
    }
    .position:hover
    {
        background-color: #eee;
        cursor: pointer;
        
    }
    .verde
    {
        background-color: yellowgreen;
    }
    .rojo
    {
        background-color: red;
    }
    #menu_float2
    {
        right: 340px;
    }
</style>
<h1>Sunflower151214S2_BackCross - BackGround_FX2_20363(20341)_PL6</h1>
<div id="menu_float2"> 
                <a href="javascript: selectAll();" class ="user export" > <span class="glyphicon glyphicon-check size12" aria-hidden="true" > </span> Select All</a>
                   
    </div>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//$this->registerJs('init();', $this::POS_READY);
$row  = 8;
$cols = 12;
echo $this->render('../project/plate-headers');
echo "<div id='plate-example'>";

echo "<form name='plate_samples' id='plate_samples'  action='".Yii::$app->homeUrl."site/plate-load' method='post'>";
for($i= 1; $i <= $row; $i++)
{
    echo "<div id='row' >";
    for($e= 1; $e <= $cols; $e++)
    {
        if($i < 8)
        {
            //print_r("asdqew2"); exit;
            echo "<a href='#' onclick='return actions(event);'>"
                    . "<div class='position' id='".$i."-".$e."'> ASD-".$i.$e
                        ."<input type='hidden' name='".$i."-".$e."' id='sample".$i."-".$e."' size='1'></input>"
                    . "</div>"
               . "</a>"; 
        }else   
        {
            echo "<a href='#' onclick='return actions(event);'>"
                    . "<div class='position' id='".$i."-".$e."'>"
                        ."<input type='hidden' name='".$i."-".$e."' id='sample".$i."-".$e."' size='1'></input>"
                    . "</div>"
               . "</a>";
        }
    }
    echo "</div>";
}
echo "</div>";
//echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-nuevo-reclamo  btn-nuevo-create pull-left']);
?>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"></div>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <?= Html::button('Save & Next',['type'=>'submit','class' => 'btn btn-primary in-nuevos-reclamos','onclick'=>'return saved()']) ?>
</div>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"></div>
<?php
//echo "<a href='#' class='btn btn-primary  btn-nuevo-reclamo  btn-nuevo-create pull-left' onclick='return saved();' > Save</a>";
echo "</form>";


//echo "<button type='submit'>Save</button>"
//
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
   
    actions = function(e)
    {   
        var ide= e.srcElement.id;
        
        if($("#sample"+ide).val() === "")
        {
            $("#sample"+ide).val(1);
            $("#"+ide).removeClass('position');
            $("#"+ide).addClass("verde");
            
        }
        else if($("#sample"+ide).val() === "1")
        {
            $("#sample"+ide).val(2);
            //$("#"+ide).text(2);
            $("#"+ide).removeClass("verde");
            $("#"+ide).addClass("rojo");
        }
        else
        {
            $("#sample"+ide).val("");
            //$("#"+ide).text("");
            $("#"+ide).removeClass('rojo');
            $("#"+ide).addClass('position');
        }
        return false;
    };
    
    saved = function()
    {
        window.location = "<?= Yii::$app->homeUrl; ?><?= Yii::$app->controller->id; ?>/plate-load?"+$("#plate_samples").serialize(); return false; 
    }
    
    selectAll = function()
    {   //console.log($("input[name='vCheck[]']:checked").length);
               
               
            $( ".position" ).each(function( index ) {
             // console.log( index + ": " + $( this ).text() );
             $( this ).addClass("verde");
             
            });
            
           
        //$("#"+ide).removeClass('position');
       // $("#"+ide).addClass("verde");
        //return false;
    }
    
   
</script>

