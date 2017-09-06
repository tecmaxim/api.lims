<?php

namespace frontend\controllers;

use Yii;
use common\models\Crop;
use common\models\CropSearch;
use common\models\Cropbyuser;
use common\models\MapTypeByCrop;
// use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * CropController implements the CRUD actions for Crop model.
 */
class CropController extends ControllerCustom {

    public $enableCsrfValidation = false;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Crop models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CropSearch();
        $searchModel->IsActive = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Crop model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        $this->layout = false;
        return $this->renderAjax('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionViewAjax($id) {
        $this->layout = false;
        return $this->renderAjax('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Crop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $this->layout = false;
        $model = new Crop();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->IsActive = 1;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                if ($model->save())
                    $this->saveMapsType($model->Map, $model->Crop_Id);
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
     * Updates an existing Crop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $this->layout = false;
        $model = $this->findModel($id);

        $maps = MapTypeByCrop::getMapsByCrop($id);

        if ($maps != null) {
            foreach ($maps as $m)
                $arr[] = $m->MapTypeId;

            $model->Map = $arr;
        }
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                if ($model->save())
                    $this->saveMapsType($model->Map, $model->Crop_Id);

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
     * Deletes an existing Crop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $MapsTypeByCrop = new MapTypeByCrop;
                $mapT = $MapsTypeByCrop->find()
                        ->where(["Crop_Id" => $id, "IsActive" => 1])
                        ->all();
                foreach ($mapT as $m) {
                    $m->IsActive = 0;
                    $m->update();
                }
                $UserByCrop = new Cropbyuser;
                $userB = $UserByCrop->find()
                        ->where(["Crop_Id" => $id, "IsActive" => 1])
                        ->all();
                foreach ($userB as $u) {
                    $u->IsActive = 0;
                    $u->update();
                }
                $model->IsActive = 0;
                $model->Map = 0; //Cause is Required in rules
                $model->update();
                if ($id == Yii::$app->session['cropId'])
                    Yii::$app->session->remove('cropId');

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true]);
            }
        }
        return $this->renderAjax('delete', ['model' => $model]);
    }

    /**
     * Finds the Crop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Crop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Crop::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Build an Array with all the parameter to pass to create/update view
     */
    protected function getParameters() {
        $parameters = [];

        return $parameters;
    }

    private function saveMapsType($mapsT, $CropId) {
        foreach ($mapsT as $k => $mapId) {
            $mapsByCrops = MapTypeByCrop::find()
                    ->where(["Crop_Id" => $CropId, "MapTypeId" => $mapId])
                    ->one();
            if ($mapsByCrops) {
                $mapsByCrops->Crop_Id = $CropId;
                $mapsByCrops->MapTypeId = $mapId;
                $mapsByCrops->IsActive = 1;
                $mapsByCrops->update();
            } else {
                $mapsByCrops = new MapTypeByCrop;

                $mapsByCrops->Crop_Id = $CropId;
                $mapsByCrops->MapTypeId = $mapId;
                $mapsByCrops->IsActive = 1;
                $mapsByCrops->save();
            }
        }
        $query = "DELETE FROM map_type_by_crop WHERE Crop_Id=" . $CropId;
        foreach ($mapsT as $k => $mapId) {
            $query .= " and MapTypeId<>" . $mapId;
        }

        $connection = \Yii::$app->dbGdbms;
        try {
            $connection->createCommand($query)->execute();
        } catch (Exception $e) {
            print_r($e);
            exit;
        };
    }

    public function actionGetCropsEnabledByKendo() {
        $connection = \Yii::$app->dbGdbms;
        $Query = "Select c.Crop_Id, c.Name FROM crop c" .
                //inner join marker ON marker.Crop_Id=c.Crop_Id
                " where c.IsActive=1";
        try {
            $crops = $connection->createCommand($Query)->queryAll();

            foreach ($crops as $c)
                $kendoCrops[] = ["id" => $c['Crop_Id'], "name" => $c['Name']];

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $kendoCrops;
        } catch (Exception $e) {
            print_r($e);
            exit;
        };
    }
    
    public function actionGetCropsEnabledByKendoWhitAll() {
        $connection = \Yii::$app->dbGdbms;
        $Query = "Select c.Crop_Id, c.Name FROM crop c" .
                //inner join marker ON marker.Crop_Id=c.Crop_Id
                " where c.IsActive=1";
        try {
            $crops = $connection->createCommand($Query)->queryAll();
                $kendoCrops[] = ["id" => 0, "name" => 'All'];
            foreach ($crops as $c)
                $kendoCrops[] = ["id" => $c['Crop_Id'], "name" => $c['Name']];

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return $kendoCrops;
        } catch (Exception $e) {
            print_r($e);
            exit;
        };
    }

    public function actionGetCropsEnabled() {

        $crops = Crop::find()->where(["IsActive" => 1])->asArray()->all();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $crops;
    }

}
