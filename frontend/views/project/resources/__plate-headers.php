<?php if(isset($genspin)): ?>

   <style> 
    #index_left {
        height: 32px;
        text-align: right;
        font-weight: bold;
    }
    #container-index-left {
        height: 350px;
        margin-top: 35px;
        padding: 20px;
        margin-right: -25px;
    }
    
    #index_up {
        margin-left: 6.5px;
        width: 30px;
        font-weight: bold;
    }
 </style>
        
<?php elseif(Yii::$app->controller->action->id == "adn-extraction" ): ?> 
  <style>
    #index_up {
        margin-left: 6.5px;
        width: 40px;
        font-weight: bold;
    }
 </style>
<?php endif; ?>
    
<?php
        $limitRow = (isset($genspin)) ? 80 : 72;
        $limitCol = (isset($genspin)) ? 24 : 12;       
        echo '<div class="hidden-xs hidden-sm col-md-1 col-lg-1">';
             echo "<div id='container-index-left'>";
                    for ($chr = 65; $chr <= $limitRow; $chr++) {
                        echo "<div id='index_left'>" . chr($chr) . "</div>";
                    }
            echo "</div>";
        echo "</div>";

        echo '<div class="hidden-xs hidden-sm col-md-11 col-lg-11">';
            echo "<div id='index-vertical-container'>";
              for ($a = 1; $a <= $limitCol; $a++) {
                        echo Yii::$app->controller->action->id == 'samples-control' ? "<div id='index_up_modal'>" : "<div id='index_up'>";
                        echo $a . "</div>";
                    }

        echo "</div>"; //end div container;
    echo "</div>"; //lg-11;
?>