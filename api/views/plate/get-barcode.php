<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

echo '<div class="row" style="margin-top:50px; margin-bottom:70px;">';
        echo '<h1> Barcode Sheet </h1>';
echo '</div>';
    
foreach($plates as $key => $plate)
{
    $plate_formated = 'TP'.sprintf('%06d',$plate);
        echo '<table style="font-size:20px; border:0px solid #999" cellpading="2" cellspacing="2">'
            . '<tr>';  
            echo '<td style="width:150px; border:2px solid #888">'.$plate_formated.'</td>';
            echo '<td style="width:300px; border:2px solid #888">';
                echo '<barcode code="'.$plate_formated.'" type="C39" size="1.0" height="0.5" />';
            echo '</td>';
        echo '</tr>'
        . '</table>';
}
