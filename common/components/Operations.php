<?php

namespace common\components;

use Yii;

/**
 * 
 */
class Operations extends yii\base\Component
{
    static function getNumberOfMonth($string)
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        //$number = $lang;
        if($lang == 'es')
        {
            switch ($string)
            {
                case "enero":
                    $number = 1;
                    break;
                case "febrero":
                    $number = 2;
                    break;
                case "marzo":
                    $number = 3;
                    break;
                case "abril":
                    $number = 4;
                    break;
                case "mayo":
                    $number = 5;
                    break;
                case "junio":
                    $number = 6;
                    break;
                case "julio":
                    $number = 7;
                    break;
                case "agosto":
                    $number = 8;
                    break;
                case "septiembre":
                    $number = 9;
                    break;
                case "octubre":
                    $number = 10;
                    break;
                case "noviembre":
                    $number = 11;
                    break;
                case "diciembre":
                    $number = 12;
                    break;

            }
        }else
        {
            switch ($string)
            {
                case "january":
                    $number = 1;
                    break;
                case "february":
                    $number = 2;
                    break;
                case "march":
                    $number = 3;
                    break;
                case "april":
                    $number = 4;
                    break;
                case "may":
                    $number = 5;
                    break;
                case "june":
                    $number = 6;
                    break;
                case "july":
                    $number = 7;
                    break;
                case "august":
                    $number = 8;
                    break;
                case "september":
                    $number = 9;
                    break;
                case "october":
                    $number = 10;
                    break;
                case "november":
                    $number = 11;
                    break;
                case "december":
                    $number = 12;
                    break;

            }
        }
        
        return $number;
    }
    
    static function getCategoriesByDateType($dateType, $dateFrom, $dateTo)
    {
        $init = strtotime($dateFrom);
        $until = strtotime($dateTo);
        $categories = [];
        switch ($dateType)
        {
            case 1: // año
                while( $init < $until )
                {
                    $categories[] = date('Y',$init);
                    
                    $init =  strtotime('+1 year', $init);
                }
                /*
                $rest = $dateTo - $dateFrom;
                for($i = 1; $i <= $rest; $i++)
                {
                    $categories[] = $i;
                }
                 * 
                 */
                break;
                
            case 2://mes
                
                while( $init < $until )
                {
                    $categories[] = date('m/Y',$init);
                    
                    $init =  strtotime('+1 month', $init);
                }
                
                /*
                 * Old method
                $year = date('Y');
                $num_month =  Operations::getNumberOfMonth($date);
                $num = cal_days_in_month(CAL_GREGORIAN, $num_month, $year);
                for($i = 1; $i <= $num; $i++)
                {
                    $categories[] = $i;
                }
                 * 
                 */
                break;
            
            default://day
                while( $init < $until )
                {
                    $categories[] = date('d/m/Y',$init);
                    
                    $init =  strtotime('+1 day', $init);
                }
               
                break;
                
        }
        return $categories;
        
    }
    
    static function array_column($input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $key =>$value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[$key] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$key] = $value[$columnKey];
            }
        }
        return $array;
    }
    
    static function formatDate($date)
    {
        $new_date = implode('-', array_reverse(explode('/', $date)));
        return $new_date;
    }
    
    static function normalizeString($string)
    {
        $mat = $string;
        $material_trim2 = iconv( "ASCII", "UTF-8//IGNORE",  $mat);
        $material_trim2 = trim($material_trim2);
        $material_trim2 = str_replace(" ", "", $material_trim2);
        $material_trim2 = strtoupper($material_trim2);
        
        return $material_trim2;
    }
    
    static function dateFromCategories($columnToSearch, $date)
    {
        $dateCat = "";
        switch($columnToSearch)
        {
            case 3:
                $dateFormated = implode('-', explode('/', $date));
                $dateCat = date('Y-m-d', strtotime($dateFormated));
                break;
            case 2:
                $dateFormated = implode('-', explode('/', $date));
                $dateCat = date('Y-m', strtotime('01-'.$dateFormated));
                break;
            case 1:
                $dateCat = $date;
                break;
        }
        return $dateCat;
    }
    
    static function flatArray($array)
    {
        $objTmp = (object) array('aFlat' => array());

        array_walk_recursive($array, create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $objTmp);

        return $objTmp->aFlat;
                                
    }
}
