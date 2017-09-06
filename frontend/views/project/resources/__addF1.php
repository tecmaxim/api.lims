<?php
 $class = "parents";
    if ($row >= 9) 
    {
        echo "</div>";
        $row = 1; // reset
        echo "<div id='row' >";
    }
    echo "<a href='#' onclick='return actions(event);'>"
    . "<div class='" . $class . "' id=''> F1 <input type='hidden' name='' size='1'></input>"
    . "</div>"
    . "</a>";
    
?>
