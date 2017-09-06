<?php
        $row = $rowActual;
        $limitTotal = isset($genSpin) ? 384:96;
        $limit = isset($genSpin) ? 17:9;
        
        for($i = $cont; $i <= $limitTotal;$i++)
        {
            if ($row >= $limit) 
            {
                echo "</div>";
                $row = 1; // reset
                echo "<div id='row' >";
            }
            
            /* CONTROL END COL */
           
            echo "<a href='#' onclick='return actions(event);'>"
                    . "<div class='position-null' id='' ></div>"
                . "</a>";
            
            $row++;
             
        }
                    
?>

