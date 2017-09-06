<?php

class LimsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ], 
                ],
           ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }
    
    public function actionIndex()
    {
        return $this->render('index');
    }
    
}

?>