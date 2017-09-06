<?php

namespace frontend\controllers;

use Yii;
use common\models\ResearchStation;
use common\models\ResearchStationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ResearchStationController implements the CRUD actions for ResearchStation model.
 */
class ResearchStationController extends Controller {

    public $enableCsrfValidation = false; 
    
    public function behaviors() {
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
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * Lists all ResearchStation models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ResearchStationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ResearchStation model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id) {
        $this->layout = false;
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ResearchStation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $this->layout = false;
        $model = new ResearchStation();

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
            $parameters = $this->getParameters();

            $parameters['model'] = $model;
            return $this->renderAjax('create', $parameters);
        }
    }

    /**
     * Updates an existing Country model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
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
            return $this->renderAjax('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ResearchStation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        
        $this->layout = false;
        $model = $this->findModel($id);
        $projectsEnvolved = null;
        
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->IsActive = 0;
                $model->update();

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true]);
            }
        }
        
        $projectsEnvolved = \common\models\Project::find()
                                ->where(['ResearchStationId' => $model->ResearchStationId, 'IsActive' => 1])
                                ->count();

        return $this->renderAjax('delete', ['model' => $model, 'projectsEnvolved' => $projectsEnvolved]);
    }

    /**
     * Finds the ResearchStation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ResearchStation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ResearchStation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function getParameters() {
        $parameters = [];

        return $parameters;
    }

    public function actionGetRsByJson()
    {
        $rs = ResearchStation::find()->where(["IsActive"=>1])->orderBy("Short", SORT_ASC)->all();
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $rs;
    }
}
