<?php

namespace frontend\controllers;

use Yii;
use common\models\Allele;
use common\models\AlleleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AlleleController implements the CRUD actions for Allele model.
 */
class AlleleController extends Controller
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
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Allele models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlleleSearch();
        $searchModel->IsActive = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Allele model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Allele model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Allele();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->IsActive = 1;
            if($model->save()) 
            {
                return $this->redirect(['view', 'id' => $model->Allele_Id]);
            }
        } else {
            $parameters = $this->getParameters();

            $parameters['model'] = $model;
            return $this->render('create', $parameters);
        }
    }

    /**
     * Updates an existing Allele model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Allele_Id]);
        } else {
            $parameters = $this->getParameters();
            
            $parameters['model'] = $model;
            return $this->render('update', $parameters);
        }
    }

    /**
     * Deletes an existing Allele model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->IsActive = 0;
        $model->update();
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Allele model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Allele the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Allele::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
    * Build an Array with all the parameter to pass to create/update view
    */
    protected function getParameters()
    {
        $parameters = [];

        return $parameters;
    }
}