<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * PerformanceController implements the CRUD actions for Performance model.
 */
class ControllerCustom extends Controller
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get','post'],
                ],
            ],
            'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                            [
                                    'allow' => true,
                                    'roles' => ['@'],
                            ],
                    ],
            ],
        ];
    }
    
    public function init()
    {
        Yii::$app->language= "en-EN";
    }
}
?>