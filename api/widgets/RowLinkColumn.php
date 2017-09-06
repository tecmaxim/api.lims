<?php
namespace frontend\widgets;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * RowLinkColumn displays a column with a link to view screen
 *
 * To add a RowLinkColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\RowLinkColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Ignacio Martinez
 */

use yii\grid\DataColumn;
use yii\helpers\Html;

class RowLinkColumn extends DataColumn
{
    public $format = 'html';
    public $hidden = true;

    public function init()
    {
        if($this->hidden)
        {
            $this->contentOptions = ['style' => 'display: none'];
            $this->headerOptions = ['style' => 'display: none'];
        }
    }
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
       
       if(\Yii::$app->controller->id == "query" )
       {     //print_r($key); exit;
            if(!isset($model['Snp_lab_Id']))
                return '<a data-toggle="modal" href="'. \Yii::$app->getUrlManager()->baseUrl . '/marker/view-ajax?id='.$key.'"></a>';
            else
                return '<a data-toggle="modal" href="'. \Yii::$app->getUrlManager()->baseUrl . '/snplab/view-ajax?id='.$model['Snp_lab_Id'].'"></a>';             
       }
        else
            return Html::a($key, ['view-ajax?id='.$key],[  'data-toggle' => 'modal']);

    }
    
   
    
}
