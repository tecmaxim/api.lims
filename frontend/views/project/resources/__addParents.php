<?php
    $class = "parents";
    if($parents != null)
    { 
        foreach($parents as $p)
        {
            if ($row >= 9 ) 
                {
                    echo "</div>";
                    $row = 1; // reset
                    echo "<div id='row' >";
                }
           
            echo "<a href='#' onclick='return actions(event);'>"
            . "<div class='" . $class . "' id=''>" . $p->materialTest->Name . "<input type='hidden' name='' size='1'></input>"
            . "</div>"
            . "</a>";
            $row++;
        }
    }
    
    // me devuelve el q sigue
    if (isset($last))
    {
        //print_r($row); exit;
        if ($row >= 9) 
        {
            echo "</div>";
            $row = 1; // reset
            echo "<div id='row' >";
        }
    }
  
    
    
    echo "<a href='#' onclick='return actions(event);'>"
    . "<div class='" . $class . "' id=''> N <input type='hidden' name='' size='1'></input>"
    . "</div>"
    . "</a>";
    
?>
