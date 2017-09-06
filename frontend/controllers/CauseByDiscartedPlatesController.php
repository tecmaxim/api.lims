<?php

namespace frontend\controllers;

use Yii;
use common\models\CauseByDiscartedPlates;
use common\models\CauseByDiscartedPlatesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CauseByDiscartedPlatesController implements the CRUD actions for CauseByDiscartedPlates model.
 */
class CauseByDiscartedPlatesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CauseByDiscartedPlates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CauseByDiscartedPlatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CauseByDiscartedPlates model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id)
    {
        $this->layout = false;
        return $this->renderaJAX('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CauseByDiscartedPlates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CauseByDiscartedPlates();

        $this->layout = false;
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->IsActive = 1;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->save();                    
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            }
            else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            $parameters = $this->getParameters();

            $parameters['model'] = $model;
            return $this->renderAjax('create', $parameters);
        }
    }

    /**
     * Updates an existing CauseByDiscartedPlates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->layout = false;
       
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->save();  
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            }
            else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            $parameters = $this->getParameters();

            $parameters['model'] = $model;
            return $this->renderAjax('update', $parameters);
        }
    }

    /**
     * Deletes an existing CauseByDiscartedPlates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->IsActive = 0;
                $model->update();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true]);
            }
        }
        return $this->renderAjax('delete', ['model' => $model]);
    }

    /**
     * Finds the CauseByDiscartedPlates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CauseByDiscartedPlates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CauseByDiscartedPlates::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function getParameters() 
    {
        $parameters = [];

        return $parameters;
    }

}
