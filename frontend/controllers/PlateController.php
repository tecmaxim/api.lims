<?php

namespace frontend\controllers;

use Yii;
use common\models\Plate;
use common\models\PlateSearch;
use common\models\StatusPlate;
use common\models\SamplesByPlate;
use common\models\Genspin;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use PHPExcel_Style_Alignment;
use ZipArchive;
use yii\data\Pagination;
use common\models\PlatesByProject;

/**
 * PlateController implements the CRUD actions for Plate model.
 */
class PlateController extends Controller {

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
        ];
    }

    /**
     * Lists all Plate models.
     * @return mixed
     */
    public function actionIndex($success = NULL) {
        $searchModel = new PlateSearch();
        
        $queryParams = Yii::$app->request->queryParams;        
        
        //$queryParams['PlateSearch']['PlateId'] = (int)substr($queryParams['PlateSearch']['PlateId'], 2);
        
        $dataProvider = $searchModel->search($queryParams);
        $parents = Plate::getParentsInPlates();
       
        //Format again PlateId when search results
        if($searchModel->PlateId != null && $searchModel->hasErrors() == null)
            $searchModel->PlateId = 'TP'. sprintf('%06d', $searchModel->PlateId );
        
        if ($success == NULL) {
            return $this->render('index', [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'parents' => $parents
            ]);
        } else {
            return $this->render('index', [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'parents' => $parents,
                        'success' => true,
            ]);
        }
    }

    /**
     * Displays a single Plate model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id) {
        $this->layout = false;
        $genSpin = $this->getGenSpinByPlateId($id);
        if ($genSpin != null) {
            $method = 'GenSpin';
        } else {
            $method = \common\models\AdnExtraction::getMethodByPlateId($id);
        }
        return $this->render('view', [
                    'model' => $this->findModel($id), 'method' => $method
        ]);
    }

    /**
     * Creates a new Plate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*
     * unused
     */
    public function actionCreate() {
        $model = new Plate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PlateId]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Plate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->PlateId]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Plate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Plate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Plate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSamplesControl($id) {
        $this->layout = false;
        //$model = $this->findModel($id);
        $samples = \common\models\SamplesByPlate::find()
                ->with('samplesByProject')
                ->where(["PlateId" => $id])
                ->asArray()
                ->all();
        //print_r(Yii::$app->request->post()); exit;

        $parents = \common\models\PlatesByProject::getParentsByPlateId($id);
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $control_samples = Yii::$app->request->post()['samples'];
                $plateId = Yii::$app->request->post()['PlateId'];
                SamplesByPlate::saveControlSamples($control_samples);
                Plate::saveStatus($plateId, StatusPlate::CONTROLLED);
                \common\models\DateByPlateStatus::saveDateByStatus($plateId, StatusPlate::CONTROLLED);

                // Update StatusProject to ADN Extracted if the project is not updated
                \common\models\Project::updatesStatusByPlateId($plateId, \common\models\StepProject::DNA_EXTRACTION);

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('plate-view', [ 'ok' => true]);
                //return $this->redirect(['project']);
            }
        }


        return $this->renderAjax('plate-view', ["samplesByPlate" => $samples,
                    "parents" => $parents]);
    }

    public function actionPlateLoad() {

        //print_r($plateId);
        //print_r(Yii::$app->request->queryParams);

        return "ok";
        // exit;
    }

    public function actionOrderAgain($id = null) {
        $this->layout = false;

        $model = new \common\models\DiscartedPlates();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->IsActive = 1;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->Date = date('Y-m-d h:m:s');
                if ($model->save()) {
                    $plateOld = $this->findModel($id);
                    $plateOld->StatusPlateId = StatusPlate::SENT;
                    $plateOld->resetSamples();
                    $plateOld->deleteDateStatus();
                    $plateOld->save();
                }

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('success', ['model' => $model]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        } else {

            $model->PlateId = $id;

            $causes = \common\models\CauseByDiscartedPlates::find()->where(["IsActive" => 1])->All();
            return $this->renderAjax('select-reason', ['model' => $model, 'causes' => $causes]);
        }
    }

    /*
     * This method saves all data of adn extraction
     * @parameter integer PlateID
     * @return mixed
     */

    public function actionAdnExtraction($id) {
        $this->layout = false;
        //$model = $this->findModel($id);
        $samples = \common\models\SamplesByPlate::find()
                ->with('samplesByProject')
                ->where(["PlateId" => $id])
                ->asArray()
                ->all();
        
        $adnModel = \common\models\AdnExtraction::find()
                ->where(['PlateId' => $id])
                ->one();
        
        $parents = \common\models\PlatesByProject::getParentsByPlateId($id);
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $control_samples = Yii::$app->request->post()['samples'];
                $plateId = Yii::$app->request->post()['PlateId'];
                /* To set samples by ADN_EXTRACTED OK */
                if ($adnModel->load(Yii::$app->request->post())) {
                    //$adnModel->PlateId = $plateId;
                    //$adnModel->IsActivce = 1;
                    if (!$adnModel->save()) {
                        print_r($adnModel->getErrors());
                        exit;
                    }
                }

                SamplesByPlate::saveControlSamples($control_samples, $flagAdn = true);
                Plate::saveStatus($plateId, StatusPlate::ADN_EXTRACTED);
                \common\models\DateByPlateStatus::saveDateByStatus($plateId, StatusPlate::ADN_EXTRACTED);

                // Update StatusProject to ADN Extracted if the project is not updated
                \common\models\Project::updatesStatusByPlateId($plateId, \common\models\StepProject::DNA_EXTRACTION);

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('plate-view', [ 'ok' => true]);
            }
        }

        return $this->renderAjax('plate-view', ["samplesByPlate" => $samples,
                    "parents" => $parents,
                    "adnExtraction" => true,
                    "adnModel" => $adnModel
        ]);
    }

    public function actionSelectMethod($id, $adnMethod = null) {
        if ($adnMethod != null) {
            if ($adnMethod == 'GenSpin') {
//                $platesControlled = Plate::find()
//                                        ->where(['StatusPlateId' => StatusPlate::CONTROLLED, 'IsActive' => 1 ])
//                                        ->andWhere("PlateId <>".$id)
//                                        ->asArray()
//                                        ->select('PlateId')
//                                        ->all();
//                if(count($platesControlled))
//                {   
                return $this->actionSelectPlates($id);
//                }
//                else
//                {   
//                    return $this->renderAjax('select-method', ['model' => $this->findModel($id), 'error'=>true]);
//                }  
            } else {
                $adnModel = new \common\models\AdnExtraction();
                $adnModel->Method = $adnMethod;
                $adnModel->PlateId = $id;
                $adnModel->IsActivce = 1;

                if ($adnModel->save()) {
                    $model = $this->findModel($id);
                    $model->StatusPlateId = StatusPlate::SAVED_METHOD;
                    $model->save();
                }

                return $this->renderAjax('view', [ 'model' => $this->findModel($id), 'method' => $adnMethod]);
            }
        } else {
            $this->layout = false;
            $model = $this->findModel($id);
            return $this->renderAjax('select-method', ['model' => $model]);
        }
    }

    /*
      public function actionUpdateMethod($id, $adnMethod = null)
      {
      if($adnMethod != null)
      {
      $adnModel = \common\models\AdnExtraction::findOne(["PlateId" => $id]);
      // if the record is not found, it search if the plate is associated in a genSpin plate
      if(!$adnModel || $adnModel->Method == 'GenSpin')
      {
      $genspin = $this->getGenSpinByPlateId($id);

      $adnModel = \common\models\AdnExtraction::findOne($genspin['AdnExtractionId']);

      $this->resetStatusPlateFromGenspin($genspin, $id);
      Genspin::deleteById($genspin['GenspinId']);
      }

      if($adnMethod == 'GenSpin')
      {
      $adnModel->delete();
      $platesControlled = Plate::find()
      ->where(['StatusPlateId' => StatusPlate::CONTROLLED, 'IsActive' => 1 ])
      ->andWhere("PlateId <>".$id)
      ->asArray()
      ->select('PlateId')
      ->all();
      if(count($platesControlled))
      {
      return $this->actionSelectPlates($id);
      }
      else
      {
      return $this->renderAjax('select-method', ['model' => $this->findModel($id), 'error'=>true]);
      }
      }else
      {
      $adnModel->Method = $adnMethod;
      //$adnModel->PlateId = $id;
      //$adnModel->IsActivce = 1;
      $adnModel->save();

      return $this->renderAjax('view', [ 'model' => $this->findModel($id),'method'=> $adnMethod]);
      }
      }else{
      $this->layout = false;
      $model = $this->findModel($id);
      return $this->renderAjax('select-method', ['model' => $model, 'update' => true]);
      }
      }
     */

    public function actionCancelMethod($id) {

        $adnModel = \common\models\AdnExtraction::findOne(["PlateId" => $id]);
        /* if the record is not found, it search if the plate is associated in a genSpin plate */
        if (!$adnModel || $adnModel->Method == 'GenSpin') {
            $genspin = $this->getGenSpinByPlateId($id);

            $adnModel = \common\models\AdnExtraction::findOne($genspin['AdnExtractionId']);

            $this->resetStatusPlateFromGenspin($genspin, $id);
            Genspin::deleteById($genspin['GenspinId']);
        }

        $adnModel->delete();
        Plate::saveStatus($id, StatusPlate::CONTROLLED);

        return $this->renderAjax('view', [ 'model' => $this->findModel($id)]);
    }

    /*
     * Get all plates distinct by Id and controlled to genspin and render view to select
     * @parameter PalteId integer
     * @ return mixed
     */

    public function actionCreateGenspinMethod($id) {
        $this->layout = false;
        //$model = $this->findModel($id);
        $genspinModel = $this->getGenSpinByPlateId($id);
        /* get samples from plate selected in list */
        $genspinPlates = json_decode($genspinModel['Plates']);
        foreach ($genspinPlates as $plateId) {
            $parents[$plateId->PlateId] = \common\models\PlatesByProject::getParentsByPlateId($plateId->PlateId);
            //$array_plates[] = ["PlateId" => $plateId];
            $samples[] = SamplesByPlate::find()
                    ->with('samplesByProject')
                    ->where(["PlateId" => $plateId->PlateId])
                    ->asArray()
                    ->all();
        }

        $adnModel = \common\models\AdnExtraction::findByArrayPlateId($genspinPlates);

        if (Yii::$app->request->isAjax && $adnModel->load(Yii::$app->request->post())) {
            $isSubmit2 = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit2) {
                $genSpin = Genspin::find()
                        ->where(['AdnExtractionId' => Yii::$app->request->post()['AdnExtraction']['AdnExtractionId']])
                        ->one();

                $platesDecodes = json_decode($genSpin->Plates);

                /* save plate status as ADN_EXTRACTED */
                foreach ($platesDecodes as $plates) {
                    Plate::saveStatus($plates->PlateId, StatusPlate::ADN_EXTRACTED);
                    /* Save date_by_plate_status for all plates */
                    \common\models\DateByPlateStatus::saveDateByStatus($plates->PlateId, StatusPlate::ADN_EXTRACTED);

                    // Update StatusProject to ADN Extracted if the project is not updated
                    \common\models\Project::updatesStatusByPlateId($plates->PlateId, \common\models\StepProject::DNA_EXTRACTION);
                }

                $adnModel->save();

                /* Save the new status of samples */
                $control_samples = Yii::$app->request->post()['samples'];
                SamplesByPlate::saveControlSamples($control_samples, $flagAdn = true);
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('plate-view', [ 'ok' => true]);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($genspinModel);
            }
        }
        //print_r($samples); exit;
        $samplesByGenSpin = SamplesByPlate::orderSamplesByGenSpin($samples);
        //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $this->renderAjax('genspin-extraction', ['adnModel' => $adnModel,
                    'adnExtraction' => true,
                    'genspinSamples' => $samplesByGenSpin,
                    'parents' => $parents,
        ]);
    }

    /*
      public function actionGetPlatesControlled()
      {
      $platesControlled = Plate::find()
      ->where(['StatusPlateId' => StatusPlate::CONTROLLED, 'IsActive' => 1 ])
      ->andWhere("PlateId <>".$id)
      ->asArray()
      ->select('PlateId')
      ->all();
      }
     */

    public function actionCancelPlateGenspin($adnExtractionId) {
        //$sql = "DELETE FROM genspin WHERE AdnExtractionId = ".$adnExtractionId;
        //Yii::$app->db->createCommand($sql)->execute();

        echo 'ok';
    }

    public function actionSaveGenSpin() {
        //print_r(Yii::$app->request->post()); exit;
        //$adnModel = \common\models\AdnExtraction::findOne(Yii::$app->request->post()['AdnExtraction']['AdnExtractionId']);
        //$adnModel->load(Yii::$app->request->post());
        //$adnModel->save();

        $genSpin = Genspin::find()
                ->where(['AdnExtractionId' => Yii::$app->request->post()['AdnExtraction']['AdnExtractionId']])
                ->one();

        $platesDecodes = json_decode($genSpin->Plates);

        /* save plate status as ADN_EXTRACTED */
        foreach ($platesDecodes as $plates) {
            Plate::saveStatus($plates->PlateId, StatusPlate::ADN_EXTRACTED);
            \common\models\DateByPlateStatus::saveDateByStatus($plates->PlateId, StatusPlate::ADN_EXTRACTED);
        }

        /* Save the new status of samples */
        $control_samples = Yii::$app->request->post()['samples'];
        SamplesByPlate::saveControlSamples($control_samples, $flagAdn = true);

        /* Save date_by_plate_status for all plates */

        $searchModel = new Plate();
        $dataProvider = new ActiveDataProvider([
            'query' => Plate::find(),
        ]);

        return $this->redirect(['index',
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'success' => true
        ]);
    }

    public function actionSelectPlates($id) {
        $this->layout = false;
        $model = $this->findModel($id);
        $genspinModel = new Genspin();
        $genspinModel->plateSelected = 'TP'.sprintf("%06d",$id);

        $platesControlled = Plate::find()
                ->select(["CONCAT('TP',LPAD(PlateId, 6, 0)) as Name", "PlateId"])
                ->where(['StatusPlateId' => StatusPlate::CONTROLLED, 'IsActive' => 1])
                ->andWhere("PlateId <>" . $id)
                ->asArray()
                //->select('PlateId')
                ->all();

        if (Yii::$app->request->isAjax && $genspinModel->load(Yii::$app->request->post())) {
            $genspinModel->IsActive = 1;
            $isSubmit2 = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit2) {
                /* get samples from plate selected in list */
//                $samples[]  = SamplesByPlate::find()
//                                    ->with('samplesByProject')
//                                    ->where(["PlateId" => $id])
//                                    ->asArray()
//                                    ->all();
                //$parents[$id] = \common\models\PlatesByProject::getParentsByPlateId($id);
                /* create a instance of adn_extraction model */
                $adnModel = new \common\models\AdnExtraction();
                $adnModel->Method = "GenSpin";
                $adnModel->PlateId = $id;
                $adnModel->IsActivce = 1;

                if ($adnModel->save()) {
                    $model = $this->findModel($id);
                    $model->StatusPlateId = StatusPlate::SAVED_METHOD;
                    $model->save();
                }
                /* init the array plates with PlatesId */
                $array_plates[] = ["PlateId" => $id];
                if ($genspinModel->Plates != "") {
                    foreach ($genspinModel->Plates as $key => $plateId) {
                        $array_plates[] = ["PlateId" => $plateId];
                        Plate::saveStatus($plateId, StatusPlate::SAVED_METHOD);
                    }
                }
                $genspinModel->AdnExtractionId = $adnModel->AdnExtractionId;
                $genspinModel->Plates = json_encode($array_plates);
                $genspinModel->save();

                return $this->renderAjax('view', [ 'model' => $this->findModel($id), 'method' => 'GenSpin']);
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($genspinModel);
            }
        }
//         if(count($platesControlled))
//        {   
        return $this->renderAjax('select-plates-view', ['model' => $genspinModel, 'platesControlled' => $platesControlled]);
//        }
//        else
//        {   
//            return $this->renderAjax('select-method', ['model' => $model, 'error'=>true]);
//        }
    }

    public function actionRedirectEx() {
        return $this->redirect(['index',
                    'success' => true
        ]);
    }

    private function getGenSpinByPlateId($id) {
        $plate_json = json_encode(["PlateId" => $id]);
        $sql = "SELECT * FROM genspin WHERE Plates LIKE '%" . $plate_json . "%'";

        $genspin = Yii::$app->db->createCommand($sql)->queryOne();

        return $genspin;
    }

    /**
     * Creates a new Generation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateCuantification($id) {
        $this->layout = false;
        $adnModel = \common\models\AdnExtraction::findByPlateId($id);
        //print_r(Yii::$app->request->isAjax); exit;
        if (Yii::$app->request->isAjax && $adnModel->load(Yii::$app->request->post())) {
            //$model->IsActive = 1;

            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                if ($adnModel->save()) {
                    $genSpin = $this->getGenSpinByPlateId($id);
                    if ($genSpin != null) {
                        $plates = json_decode($genSpin['Plates']);
                        foreach ($plates as $plate) {
                            Plate::saveStatus($plate->PlateId, StatusPlate::CUANTIFICATION);
                        }
                        $method = 'GenSpin';
                    } else {
                        Plate::saveStatus($id, StatusPlate::CUANTIFICATION);

                        $method = \common\models\AdnExtraction::getMethodByPlateId($id);
                    }
                    return $this->render('view', [
                                'model' => $this->findModel($id), 'method' => $method
                    ]);
                }
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($adnModel);
            }
        } else {
            $parameters = [];

            $parameters['adnModel'] = $adnModel;
            return $this->renderAjax('_cuantification', $parameters);
        }
    }

    private function resetStatusPlateFromGenspin($genspin, $selectedPlateId) {
        $platesDecodes = json_decode($genspin['Plates']);

        foreach ($platesDecodes as $plates) {
            if ($selectedPlateId !== $plates->PlateId) {
                Plate::saveStatus($plates->PlateId, StatusPlate::CONTROLLED);
            }
        }
    }

    public function actionDownloadAssaySamplesByProject() {
        $palteIds = Yii::$app->request->post()['vCheck'];
        $name = "";

        foreach ($palteIds as $key => $plateId) {
            if ($name == "") {
                $name = \common\models\PlatesByProject::getProjectByPlateId($plateId);
            }
            $files[] = $this->actionExportAssaySamples($plateId);
        }

        $zipname = $name . '.zip';
        $zip = new ZipArchive();

        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($files as $key => $file) {
            $zip->addFromString(basename($file), file_get_contents($file));
        }

        $zip->close();

        foreach ($files as $key => $file) {
            unlink($file);
        }
        return $zipname;
    }

    public function actionExportAssaySamples($plateId = null) {
        $id = $plateId != null ? $plateId : Yii::$app->request->post()['id'];
        $model = $this->findModel($id);
        $samples = $model->getSamplesByPlates();
       
        //echo "<pre>";
        //print_r($samples->with('samplesByProject')->asArray()->all()); exit;
        
        $oExcel = new PHPExcel();
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Genotype-Samples-TP" . sprintf('%06d',$id) . "(" . date("d-m-Y") . ")";
        $oExcel->getProperties()->setTitle("$sTitle");

        $styleCell = $this->defineStyle(10, true);
        //Begin to sheet
        $charCol = 65; //A
        $row = 1;
        //Set style sheet;
        $oSheet->getStyle()->applyFromArray($styleCell);

        //Define Barcode row
        $oSheet->SetCellValue(chr($charCol) . $row, 'Barcode:');
        $oSheet->SetCellValue(chr($charCol + 1) . $row++, "TP".sprintf('%06d',$id));

        //Define PlateType row
        $oSheet->SetCellValue(chr($charCol) . $row, 'Plate Type:');
        $oSheet->SetCellValue(chr($charCol + 1) . $row++, 'Sample');

        //Define WellLayout row
        $oSheet->SetCellValue(chr($charCol) . $row, 'Well Layout:');
        $oSheet->SetCellValue(chr($charCol + 1) . $row++, 'SBS-96');

        $vHeaders = array("Well Location", "Liquid Name", "Liquid Type");

        foreach ($vHeaders as $key => $lbl) {
            $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
            $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
        }

        $row++;
        $charCol = 65; // reset A
        $charCol2 = 65;
        $i = 1;
        $projectId = null;
        $parent = null;
        $valueParents = "";
        $arraySamples = $samples->with('samplesByProject')->asArray()->all();
        
        $ex = "";
        for($row_plate=0; $row_plate < 8; $row_plate++)
        {
            for($col_plate=0; $col_plate < 12; $col_plate++)
            {
                //$ex .= $arraySamples[$row_plate+(8 * $col_plate)]['samplesByProject']['SampleName'] ."<br>";
                
                switch ($arraySamples[$row_plate+(8 * $col_plate)]['Type']) {
                    case 'PARENT':
                        if ($parent == null) {
                            if ($valueParents == "") {
                                
                                $projectId = count($model->platesByProjects) == 1 ? $model->platesByProjects[0]->ProjectId : $model->getProjectsByPlates();
                                $valueParents = \common\models\Project::getParentsInArray($projectId);
                                $valueParents = \common\components\Operations::flatArray($valueParents);
                                
                                // if existe 0 key in array. Then there are only 2 parents
                                if (!isset($valueParents[0])) {
                                    $valueParents = $this->orderParents($valueParents);
                                    //print_r($value); exit;
                                }
                            } else
                                $projectId = 1;
                        }
                        break;
                    case 'CN':
                        $value = "Drop Out";
                        break;
                    case 'SAMPLE':
                        $value = $arraySamples[$row_plate+(8 * $col_plate)]['samplesByProject']['SampleName'];
                        break;
                    default:
                        $value = "Drop Out";
                }

                //print_r($sample); exit;
                
                if ($parent == null ) {
                    /*if($row_plate == 6 && $col_plate == 0 )
                    {
                        print_r( $parent); 
                        print_r( $arraySamples[$row_plate+(8 * $col_plate)]); exit;
                    }*/
                    if ($projectId != null) {
                        $contParents = 0;                            
                        //foreach ($valueParents as $parent) {
                            $oSheet->SetCellValue(chr($charCol) . $row, chr($charCol2) . $i++);
                            $oSheet->SetCellValue(chr($charCol + 1) . $row, $valueParents[$contParents]);
                            $oSheet->SetCellValue(chr($charCol + 2) . $row, 'Sample');
                            $row++;

                            array_shift($valueParents);
                            
                            if ($i == 13) {
                                $i = 1;
                                $charCol2++;
                            }
                            //break;
                            /*if ($contParents < 2) {
                                $contParents++;
                            } */
                        //}
                        $projectId = null;
                        //$parent = true;
                    } else {
                        $oSheet->SetCellValue(chr($charCol) . $row, chr($charCol2) . $i++);
                        $oSheet->SetCellValue(chr($charCol + 1) . $row, $value);
                        $oSheet->SetCellValue(chr($charCol + 2) . $row, 'Sample');

                        $row++;

                        if ($i == 13) {
                            $i = 1;
                            $charCol2++;
                        }
                    }
                } else
                    $parent = null;
            }
            
        }
        
        /*foreach ($samples->all() as $sample) {
            //$plateIndex = 1;
            switch ($sample->Type) {
                case 'PARENT':
                    if ($parent == null) {
                        if ($valueParents == "") {
                            $projectId = count($model->platesByProjects) == 1 ? $model->platesByProjects[0]->ProjectId : $model->getProjectsByPlates();
                            $valueParents = \common\models\Project::getParentsInArray($projectId);
                            // if existe 0 key in array. Then there are only 2 parents
                            if (!isset($valueParents[0])) {
                                $valueParents = $this->orderParents($valueParents);
                                //print_r($value); exit;
                            }
                        } else
                            $projectId = 1;
                    }
                    break;
                case 'CN':
                    $value = "Drop Out";
                    break;
                case 'SAMPLE':
                    $value = $sample->samplesByProject->SampleName;
                    break;
            }

            //print_r($sample); exit;
            if ($parent == null) {
                if ($projectId != null) {
                    $contParents = 1;
                    foreach ($valueParents as $parent) {
                        $oSheet->SetCellValue(chr($charCol) . $row, chr($charCol2) . $i++);
                        $oSheet->SetCellValue(chr($charCol + 1) . $row, $parent);
                        $oSheet->SetCellValue(chr($charCol + 2) . $row, 'Sample');
                        $row++;

                        array_shift($valueParents);
                        //print_r($value); exit;
                        if ($i == 13) {
                            $i = 1;
                            $charCol2++;
                        }
                        if ($contParents < 2) {
                            $contParents++;
                        } else {
                            break;
                        }
                    }
                    $projectId = null;
                    $parent = true;
                } else {
                    $oSheet->SetCellValue(chr($charCol) . $row, chr($charCol2) . $i++);
                    $oSheet->SetCellValue(chr($charCol + 1) . $row, $value);
                    $oSheet->SetCellValue(chr($charCol + 2) . $row, 'Sample');

                    $row++;

                    if ($i == 13) {
                        $i = 1;
                        $charCol2++;
                    }
                }
            } else
                $parent = null;
        }
        /*
          header('Content-Type: application/vnd.ms-excel');
          header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
          $objWriter->save('php://output');
         */
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'CSV');
        $objWriter->setDelimiter(',');
        $objWriter->setEnclosure(false);
        $objWriter->setUseBOM(true);
        $objWriter->save('C:/php/' . $sTitle . '.csv');
        $salida = 'C:/php/' . $sTitle . '.csv';
        return $salida;
    }

    private function defineStyle($fontSize, $bold) {
        $styleCell = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
            ),
            'font' => array(
                'bold' => $bold,
                'color' => array('rgb' => '000000'),
                'size' => $fontSize,
                'name' => 'Arial'
            ),
        );

        return $styleCell;
    }

    public function actionExcelSaved() {
        $file = \Yii::$app->request->queryParams['pat'];
        if (file_exists($file) && is_readable($file)) {
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=" . substr($file, 7));
            header('Cache-Control: max-age=0');
            readfile($file);
        }
        unlink($file);
    }

    public function actionZipSave() {
        $zipname = \Yii::$app->request->queryParams['pat'];
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
    }

    private function orderParents($array_parents) {
        foreach ($array_parents as $parent) {
            $newParents[] = $parent[0];
            $newParents[] = $parent[1];
        }
        return $newParents;
    }

    /*
     * this method updates the plate by other project and it saves the before relation
     * @params palteId int, projectId int
     * @return mixed
     */

    public function actionChangeProject($id) 
    {   

    }
       
    /*
     * List all projects availables to do changes between plates and projects
     * @return mixed
     */
    public function actionGetProjectsAvailablesToChange($id)
    {
        $list = [];
        //$this->layout = false;
        $platesByProject = new \common\models\PlatesByProject();
        $platesByProject->PlateId = $id;  
        
        if (Yii::$app->request->isAjax && $platesByProject->load(Yii::$app->request->post())) 
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $previous_projectByPlate = \common\models\PlatesByProject::getProjectByPlateId($id, true);

                if ($previous_projectByPlate != null) {
                    $history = new \common\models\PlateHistoryByProject();
                    $history->PlateId = $platesByProject->PlateId;
                    $history->ProjectId = $previous_projectByPlate->ProjectId;
                    $history->IsActive = 1;
                    $history->Date = date("Y-m-d H:i:s");
                    $history->save();

                    //Delete the previous register 
                    $previous_projectByPlate->delete();

                    $platesByProject->IsActive = 1;
                    $platesByProject->save();

                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $this->renderAjax('success',  ["ok" => true]);
                } else
                {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $this->renderAjax('success',  ["fail" => true]);
                }
            }else
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($platesByProject);
            }
                
        }
        
        $project = \common\models\PlatesByProject::getProjectByPlateId($id, true);
        if($project != null)
        {
            $parents = \common\models\Project::getParentsByProject($project->ProjectId);

            $projectsAvilables = \common\models\Project::getProjectsByParents($parents, $project->ProjectId);    

            if($projectsAvilables)
            {
                foreach($projectsAvilables as $p)
                {
                    $list[] = ["name" => $p->Name, "id"=> $p->ProjectId ];
                }
            }
        }else
            return $this->renderAjax('_projects-availables-list', ['projectsList' => "", "model" => $platesByProject, "hasMoreThanOneParent" => true]);
        //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $this->renderAjax('_projects-availables-list', ['projectsList' => $list, "model" => $platesByProject]);

    }   
    
    public function actionGenerateBarcodes()
    {
        $plates = Plate::findAll(["IsActive" => 1]);
        return $this->render('get-barcode',["plates"=>$plates]);
    }
    
    /*
     * Receive plates by scanner
     * @params $barcode int
     * @return mixed
     */
    public function actionReceivePlates()
    {
        $searchModel = new PlateSearch();
        
        if(isset(Yii::$app->request->queryParams['scan']))
        {
            $scan = Yii::$app->request->queryParams['scan'];
                   
            $plateTofind = (int)substr($scan, 2);
            
            return Plate::findOne($plateTofind) != null ? $plateTofind : null;            
        }
        $init = ['PlateSearch' => array("StatusPlateId"=> 1)];
        $plates = $searchModel->search($init );
        return $this->render('plate-receptions',["dataProvider" => $plates, "searchModel" => $searchModel]);
                        
    }
    
    /*
     * Save Plate Receptions and change status of Project
     */
    public function actionSaveReceptions()
    {
        $projects_to_update = "";
        
        if(Yii::$app->request->queryParams)
        {
            $checks = Yii::$app->request->queryParams['vCheck'];
            
            //Get ProjectIds from plates
            $projects = PlatesByProject::find()->select('ProjectId')->where(["in", "PlateId",$checks])->groupBy('ProjectId')->all();
            
            //Format result on simple array
            $project_array = \yii\helpers\ArrayHelper::getColumn($projects, function ($element) {
                return $element['ProjectId'];
            });
            
            //Save automatically sample_reception, sample_dispatch
            foreach ($project_array as $k => $v)
            {
                if( count(\common\models\DispatchPlate::find()->where(["ProjectId" => $v])->all()) == 0)
                {
                    $sample_dispatch = new \common\models\DispatchPlate();
                    $sample_dispatch->ProjectId = $v;
                    $sample_dispatch->Date = date('Y-m-d');
                    $sample_dispatch->IsActive = 1;
                    $sample_dispatch->Carrier = 'No Data';
                    $sample_dispatch->TrackingNumber = 'No Data';
                    $sample_dispatch->save();
                    
                    if( count(\common\models\ReceptionPlate::find()->where(["ProjectId" => $v])->all()) == 0)
                    {
                        $sample_reception = new \common\models\ReceptionPlate();
                        $sample_reception->ProjectId = $v;
                        $sample_reception->LabReception = date('Y-m-d');
                        $sample_reception->IsActive = 1;
                        $sample_reception->save();
                    }
                    
                    $projects_to_update[] = $v;
                }
                
            }
            
            if($projects_to_update != "")
            {
                //UPDATE Project Status which have status_project less than SampleDispatch;
                $sql_update_projects = "UPDATE project SET StepProjectId =".\common\models\StepProject::SAMPLE_RECEPTION." WHERE  ProjectId IN (".implode('$glue', $projects_to_update).")";

                $sql_project = 'UPDATE plate SET StatusPlateId = '.StatusPlate::RECIEVED.' WHERE PlateId IN ('.$sql_update_projects.')';
            }
            //UPDATE Plates status
            $params =  implode(',', $checks);
            
            $sql = 'UPDATE plate SET StatusPlateId = '.StatusPlate::RECIEVED.' WHERE PlateId IN ('.$params.')';
            
            $result = Yii::$app->db->createCommand($sql)->execute();
            
            echo 1;
            exit;
        }
            
    }
    
    /*
     * Render ajax Plate on index
     */
     public function actionRenderPlate($plateId)
    {
        $project = PlatesByProject::find()
                            ->where(["PlateId" => $plateId ])
                            ->one();
        $model =  \common\models\Project::findOne($project->ProjectId);
        $samples = \common\models\SamplesByPlate::find()
                ->select('samples_by_plate.SamplesByPlateId, '
                        . 'samples_by_plate.PlateId, '
                        . 'plates_by_project.ProjectId, '
                        . 'samples_by_plate.SamplesByProjectId, '
                        . 'samples_by_project.SampleName, '
                        . 'samples_by_plate.Type, '
                        . 'samples_by_plate.StatusSampleId, '
                        . 'project.Name')
                ->leftJoin('samples_by_project','samples_by_project.SamplesByProjectId = samples_by_plate.SamplesByProjectId')
                ->leftJoin('project', 'project.ProjectId = samples_by_project.ProjectId')
                ->leftJoin('plates_by_project','plates_by_project.PlateId = samples_by_plate.PlateId')
                //->with('samplesByProject')
                ->where(["samples_by_plate.PlateId" => $plateId])
                ->asArray()
                ->all();
        //echo "<pre>";
        //print_r($samples); exit;
        $array_parents = \common\models\MaterialsByProject::getMaterialsNameByIdProjectGroups($project->ProjectId);
        //echo "<pre>";
        //print_r($array_parents); exit;
        return $this->renderAjax('../plate/_plates-by-project', ['model' => $model,
                        'samplesByPlate' => $samples,
                        'array_parents' => $array_parents,
                        'hiddenActions' => true]); 
    }
    
    public function actionTestRest()
    {
        $authorization = "Authorization: Bearer wGUPtU_l59O16-z6yiXR3TyH-I1bdlVQ";
        $ch = curl_init("http://localhost/advanta.lims/api/web/plates");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($ch);
        curl_close($ch);
        
        echo "<pre>";
        print_r($response); exit;
        
        
    }
}
