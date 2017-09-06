<?php

namespace frontend\controllers;

use Yii;
use common\models\ProtocolResult;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * ProtocolResultController implements the CRUD actions for ProtocolResult model.
 */
class ProtocolResultController extends Controller
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
                    //'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProtocolResult models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProtocolResult::find()->where(["IsActive"=>1]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProtocolResult model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id)
    {
        $this->layout = false;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProtocolResult model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProtocolResult();

        $this->layout = false;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->IsActive = 1;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            $parameters = [];

            $parameters['model'] = $model;
            return $this->renderAjax('create', $parameters);
        }
    }

    /**
     * Updates an existing ProtocolResult model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProtocolResult model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
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
     * Finds the ProtocolResult model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProtocolResult the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProtocolResult::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetProtocolResult()
    {
        $pResults = \common\models\ProtocolResult::find()->where(["IsActive"=>1])->asArray()->all();
        
        foreach($pResults as $result)
        {
            $array_results[] = ["name" => $result['Name'], 'id'=>$result['ProtocolResultId']];
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $array_results;
    }
}
