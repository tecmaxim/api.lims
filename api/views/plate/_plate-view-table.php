<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 $var =  '
    
    <style>
    #plate
    {
        background-color: #eeeeee;
        
        overflow: hidden;
        margin: 10px auto 10px auto;
        display: inline-block;
        border-radius: 10px;
        border: 1px solid #333333;
        text-align:center;
    }

    #plate-example{
        margin-top: 50px;
        display: block;
        width: 630px !important;
        position: relative;
        clear: both;

    }
    #row, #index_up
    {
        margin: 5px;
        overflow: auto;
        float:left;
        display:block;

    }

    .position ,.parents, .position-null, .emptyness
    {
        width: 20px !important;
        height: 40px !important;
        background-color: #ccc;
        margin: 2px 2px 0 0 ;
        font: 7.5px Arial;
        padding: 12px 12px 12px 12px;
        //float: left;
        text-align:center;
        //text-overflow: ellipsis;
        //overflow: visible;
        //white-space: nowrap;
        word-wrap:break-word;
        //display: table-cell;
        vertical-align: middle;
        //box-shadow: -0.5px .5px 1px #333;
        border: 1px solid #888;
        border-radius:2px;
        //float:left;
    }
    #index_up
    {
        margin-left: 6px;
        width:40px;
        //text-align: center;
        font-weight: bold;

    }
    #index_left
    {
        height:42px;
        text-align: right;
        font-weight: bold;

    }
    #index-container
    {
        //float:left;
        //background-color: #c5c5c5;
        height: 40px;
        padding-left: 5px;
        margin-bottom: -10px;
        margin-top: 10px;
    }
    #container-index-left
    {
        height: 350px;
        margin-top: 40px;
        padding: 20px;
        margin-right: -25px;
    }
    .position-null
    {
        background-color: #eee;
    }
    .position:hover
    {
        background-color: #eee;
        cursor: pointer;

    }

    .position-example:hover
    {
        background-color: #eee;
        cursor: pointer;

    }

    .position-example
    {
        padding-top: 10px;        
    }

    .parents
    {
        background-color: #faf2cc;
        color:#8a6d3b;
        text-decoration: none;
    }

    .emptyness
    {
        background-color: #eee;
        cursor:default;
    }
   
    .buton-action
    {
        margin-top:-4px;
    }
    .padding-6_margin-top--5
    {
        padding: 6px;
        margin-top:-4px;
    }

    
    #row
    {
        margin: 5px;
        overflow: auto;
        float:left;
        display:block;

    }
    
    exam{
    }
    
    </style>';
    
    
      $row = 1;
    $col = 1;
    $cont = 1;
    $class = "position";
    $var .=  '<table border="1" id="plate"><tr><td width="28" >';
 foreach ($samplesByProject as $sample) {
        /*
         * Begin of grid
         */
        if ($cont == 1) {
            //$var .=  '<div style="clear:both"></div>';
            
            $var .=  '<table border="1" id="row">';
            //$var .=  '<tr><td>';
        }
        /*
         * End actual Row and open new row
         */
        if ($row >= 9) {
            $var .=  "</table></td>"; // close Row
            $row = 1; // reset
            $var .=  '<td width="27"><table border="1" id="row">'; // open Row
        }
        /*
         * New cell
         */
        $var .=  '<tr class="' . $class . '" id="' . $sample['SamplesByProjectId'] . '"><td width="25" height="26">' . $sample['SampleName'] . '</td></tr>';
        $row++;
        /*
         * Control of parents
         */
        
        if ($cont == $numLastSampleByPlate) 
        {
            $cont = 1;
           
        } else {
            $cont++;
        }
    }
    /*
     * If number pf samples by plate < $NumLastSampleByPlate, autocomplete the grid
     */
    
    //El anterior Me da el próximo valor a insertar
    if ($cont <= $numLastSampleByPlate && $cont > 1) {
        
            //print_r($row); exit;
       
       
        $num_to_add = $parents != null ? 3 : 1;
        for($i = 0; $i < $num_to_add; $i++)
        {
            if($row < 9)
                $row++;
            else
                $row = 2;
        }
        // Me da el proximo
        $cont += $num_to_add;
        //print_r($row); exit;
        
        $var .=  "</table>"; //plate ID
        $var .=  "</td></tr></table>"; //col-8;
    }
      //echo "<pre>";
      //print  htmlspecialchars($var); exit;
      //print($var); exit;
    
    
    ?>
    

