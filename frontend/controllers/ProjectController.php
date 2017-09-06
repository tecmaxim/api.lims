<?php

namespace frontend\controllers;

use Yii;
use common\models\Project;
use common\models\ProjectSearch;
use common\models\Traits;
use common\models\StepProject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\SnpLabSearch;
//use common\models\Map;
use common\models\ParentType;
use common\models\ResearchStation;
use common\models\Crop;
use common\models\Plate;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\components\helpers\MailSettings;
use kartik\mpdf\Pdf;
use PHPMailer;
//use yii\helpers\Html;
use ZipArchive;

//parent2 = pollen Receptor
//parent1 = pollen Donnor

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller {

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex() {
        Yii::$app->session->remove('cropId');
        Yii::$app->session->remove('Name');

        //$searchModel = new ProjectSearch();

        if (Yii::$app->user->getIdentity()->itemName == "breeder")
            $searchModel->UserId = Yii::$app->user->id;

        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        foreach($dataProvider->getModels() as $er)
//            print_r($er); 
//        exit;
        return $this->render('index', [
                    //'searchModel' => $searchModel,
                    //'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Project();
        $model->scenario = 'ProjectDefinition';

        if ($model->load(Yii::$app->request->post()) and $model->validate()) {
            
            // fill all fields to model
            $this->fillModel($model);
            
            /*
             * Method old trait
              if ($model->Trait != null) {
              $this->saveTraits($model);
              }
             */
            
            Yii::$app->session->remove('Name');
            Yii::$app->session->set('Name', $model->Name);
            return $this->redirect(['grid-definition', 'ProjectId' => $model->ProjectId]);
            //return $this->redirect(['select-markers', 'ProjectId' => $model->ProjectId]);
        } else {

            if (Yii::$app->user->getIdentity()->itemName == "breeder")
                $model->UserId = Yii::$app->user->id;
            
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) and $model->validate()) {
            
            $this->fillModel($model);
            
            return $this->redirect(['view', 'id' => $model->ProjectId]);
        } else {
            //Is there are more users
            $model->UserByProject = User::getUsersAddedByProject($model->ProjectId);

            $materials = \common\models\MaterialsByProject::find()
                    ->where(["ProjectId" => $model->ProjectId])
                    ->all();
            
            if ($model->ProjectTypeId == Project::FINGERPRINT) {
                $arrayMaterialsId = [];
                foreach ($materials as $m) {
                    $arrayMaterialsId[] = $m->Material_Test_Id;
                    //$arrayMaterialsName[] = $m->materialTest->Name;
                }
                $model->FpMaterials = $arrayMaterialsId;
                //$model->MaterialsContainerHidden = implode(',', $arrayMaterialsId);
            } else {
                $array_materials = null;
                foreach ($materials as $m) {
                    if ($m->ParentTypeId == 1)
                        $model->Parent1_donnor = $m->Material_Test_Id;
                    else
                        $model->Parent2_receptor = $m->Material_Test_Id;
                }
                $model->traits_by_parent1 = \common\models\TraitsByMaterials::getTraitsByParent($model->ProjectId, ParentType::POLLEN_DONNOR);
                $model->traits_by_parent2 = \common\models\TraitsByMaterials::getTraitsByParent($model->ProjectId, ParentType::POLLEN_RECEPTOR);
            }
            
            return $this->render('update', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Displays a single Project model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = Project::find()
                ->where(["ProjectId" => $id, "IsActive" => 1])
                ->one();
        if ($model != null) {

            if ($model->StepProjectId >= StepProject::FINISHED || $model->StepProjectId == StepProject::CANCELED) {
                if ($model->StepProjectId == StepProject::FINISHED)
                    return $this->redirect(['project-preview', "ProjectId" => $model->ProjectId, "finished" => true]);
                else
                    return $this->redirect(['project-preview', "ProjectId" => $model->ProjectId, null, "onHold" => true]);
            }

            $steps = StepProject::find()
                    ->where([">", "StepProjectId", 0])
                    ->andWhere(["<", "StepProjectId", 10])
                    ->andWhere(Yii::$app->user->getIdentity()->ItemName == 'breeder' ? ["not", ["StepProjectId" => 3]] : "")
                    ->andWhere(["not", ["StepProjectId" => 8]])
                    ->asArray()
                    ->orderBy("StepProjectId")
                    ->all();

            // add field "url" to push controller action
            foreach ($steps as &$step) {
                $step['url'] = StepProject::getUrlOrDatasByStep($step['StepProjectId'], $model);
                if ($model->StepProjectId >= $step['StepProjectId'] || $step['StepProjectId'] == StepProject::MARKERS_SELECTION) {
                    
                    $step['info'] = StepProject::getUrlOrDatasByStep($step['StepProjectId'], $model, true);
                } else {
                    $step['info'] = "";
                }
                
                if($model->StepProjectId >= StepProject::SENT && count($model->markersByProjects) == 0 )
                { 
                    $step['not-markers'] = 'not-markers';
                }
            }
            //echo "<pre>";
            //var_dump(array_key_exists('not-markers', $steps)); exit;

            $progress = StepProject::getPercentByStep($model->StepProjectId);
            //print_r($progress);
            //print_r($steps); exit;

            Yii::$app->session->remove('cropId');
            Yii::$app->session->remove('Name');
            Yii::$app->session->set('cropId', $model->Crop_Id);
            Yii::$app->session->set('Name', $model->Name);
            return $this->render('new-style/view-edited', [

                        "model" => $model,
                        "steps" => $steps,
                        "progress" => $progress,
            ]);
        }
    }

    public function actionSelectMarkers($id = null) {
        if (isset(Yii::$app->request->queryParams['ProjectId']) || $id != null) {
            $idProject = $id != null ? $id : Yii::$app->request->queryParams['ProjectId'];
            $project = $this->findModel($idProject);
        }

        Yii::$app->session->set('cropId', $project->Crop_Id);
        $model = new \common\models\MarkersByProject;

        // When comes to query 1 or 2 
        if ((isset(Yii::$app->request->queryParams['vCheck'])) && !Yii::$app->request->post()) {
            //$model->MarkersCopy = $this->getMarkersByCheck(Yii::$app->request->queryParams['vCheck']);
            $model->MarkersCopy = $this->getSnpLabByCheck(Yii::$app->request->queryParams['vCheck']);

            $project = $this->findModel(Yii::$app->session->get('projectId'));

            $checks = true;
        } else
            $checks = null;


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $project = $this->findModel($model->ProjectId);

            if ($model->MarkersCopy !== "") {
                $array_markers = $this->normalizeInArray($model->MarkersCopy);
                if (($result = $model->getSnpLabInArray($array_markers)) !== false) {
                    foreach ($result as $Marker) {
                        $mkByProject = new \common\models\MarkersByProject;
                        $mkByProject->ProjectId = $model->ProjectId;
                        $mkByProject->Snp_lab_Id = $Marker['Snp_lab_Id'];
                        $mkByProject->IsActive = 1;
                        // just to avoid control rule
                        $mkByProject->MarkersCopy = 1;
                        if (!$mkByProject->save()) {
                            return \yii\widgets\ActiveForm::validate($model);
                            exit;
                        }
                        $array_mkByProjectId[] = $mkByProject->MarkersByProjectId;
                        // El ID del project viene por post
                    }
                }
            } else {
                /* If MarkerSelection model is empty, redirect to grid-definiton */
                return $this->redirect(['project/grid-definition', 'ProjectId' => $model->ProjectId]);
            }

            $project->StepProjectId = \common\models\StepProject::MARKERS_SELECTION;
            $project->UpdateAt = date("Y-m-d");
            $project->StepUpdateAt = date("Y-m-d");

            if (!$project->save())
                print_r($project->getErrors());

            if ($project->ProjectTypeId == Project::FINGERPRINT)
            //return $this->redirect(['grid-definition', 'ProjectId' => $project->ProjectId]); //actionGridDefinition($project->ProjectId);
                return $this->redirect(['project-preview', "ProjectId" => $project->ProjectId]); //actionProjectPreview($samplesByProject->ProjectId);
            else
                return $this->redirect(['traits/select-traits-by-markers', 'ProjectId' => $project->ProjectId,
                            'mkByProjectId' => $array_mkByProjectId]); //actionGridDefinition($project->ProjectId);
        }

        $model->ProjectId = $project->ProjectId;
        $model->Crop_Id = $project->Crop_Id;
        return $this->render('_marker-selection', [
                    'model' => $model,
                    'checks' => $checks,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionUpdateSelectMarkers($idProject) {

        $project = $this->findModel($idProject);

        $modelMarkerByProject = new \common\models\MarkersByProject;
        $modelMarkerByProject->MarkersCopy = $modelMarkerByProject->getSnpLabNameByProject($idProject);

        if ((isset(Yii::$app->request->queryParams['vCheck'])) && !Yii::$app->request->post()) {

            $modelMarkerByProject->MarkersCopy = $this->getSnpLabByCheck(Yii::$app->request->queryParams['vCheck']);
            //$project = $this->findModel(Yii::$app->session->get('projectId'));
            //Yii::$app->session->remove('projectId');
            $checks = true;
        } else
            $checks = null;

        if ($modelMarkerByProject->load(Yii::$app->request->post()) && $modelMarkerByProject->validate()) {

            /* To delete all relations with traits_by_markers_bY_project */
            \common\models\TraitsByMarkersByProject::deleteTraitsByMarkersByProject($project->ProjectId);
            /* To delete markers asociated with the project */
            $modelMarkerByProject->delteMarkersPrevios($modelMarkerByProject->ProjectId);
            if ($modelMarkerByProject->MarkersCopy !== "") {
                $array_markers = $this->normalizeInArray($modelMarkerByProject->MarkersCopy);

                if (($result = $modelMarkerByProject->getSnpLabInArray($array_markers)) != null) {
                    foreach ($result as $Marker) {
                        $mkByProject = new \common\models\MarkersByProject;
                        $mkByProject->ProjectId = $modelMarkerByProject->ProjectId;
                        $mkByProject->Snp_lab_Id = $Marker['Snp_lab_Id'];
                        $mkByProject->IsActive = 1;
                        // just to avoid control rule
                        $mkByProject->MarkersCopy = 1;
                        if(!$mkByProject->save()){print_r($mkByProject->getErrors()); exit;};

                        $array_mkByProjectId[] = $mkByProject->MarkersByProjectId;
                        // El ID del project viene por post
                    }
                } else {
                    $modelMarkerByProject->errorMarker = true;
                    $modelMarkerByProject->validate();

                    return $this->render('_marker-selection', [
                                'model' => $modelMarkerByProject,
                                'checks' => $checks,
                                'update' => 1,
                                'projectName' => Yii::$app->session->get('Name'),
                    ]);
                }
            }

            //save update status
            $project->UpdateAt = date("Y-m-d");
            $project->StepUpdateAt = date("Y-m-d");
                
            /* Control project step */
            if ($project->StepProjectId == StepProject::GRID_DEFINITION) {
                $project->StepProjectId = \common\models\StepProject::MARKERS_SELECTION;
                if (!$project->save()) {
                    print_r($project->getErrors()); exit;
                }
            }
            else
                $this->saveProjectUpdateAt($project);

            if ($project->ProjectTypeId == Project::FINGERPRINT)
                return $this->redirect(['view', 'id' => $project->ProjectId]);
            else {
                 //var_dump("expsadsaression"); exit();
                $traits = \common\models\TraitsByMarkersByProject::getTraitsBybProjectId($project->ProjectId);
                if ($traits != null) {
                    return $this->redirect(['traits/select-traits-by-markers', 'ProjectId' => $project->ProjectId,
                                'mkByProjectId' => $array_mkByProjectId,
                                'update' => 1]);
                } else {
                    return $this->redirect(['view', 'id' => $project->ProjectId]);
                }
            }
        }

        $modelMarkerByProject->ProjectId = $project->ProjectId;
        $modelMarkerByProject->Crop_Id = $project->Crop_Id;
        return $this->render('_marker-selection', [
                    'model' => $modelMarkerByProject,
                    'checks' => $checks,
                    'update' => 1,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionRenderQuerys($method) {
        if ($method == 1) {
            $searchModel = new SnpLabSearch();
            $hasMap = null;
            $dataProvider = null;
            $mapTypes = null;
            $limitsXcM = null;

            return $this->render('_marker-selection', [
                        //'model'                 => $model,
                        'searchModel' => $searchModel,
                        'hasMap' => $hasMap,
                        'dataProvider' => $dataProvider,
                        'mapTypes' => $mapTypes,
                        'limitsXcM' => $limitsXcM,
                        'BackToLims' => null,
                        'query_selected' => $method,
            ]);
        } else {
            $Fingerprint = new FingerprintSearch();
            $Fingerprint->scenario = 'query2';

            $Fingerprint->IsActive = 1;
            $fp_material1 = "";
            $fp_material2 = "";
            $dataProvider = "";
            $limitsXcM = null;
            $mapsType = "";

            return $this->render('_marker-selection', [
                        //'model'                 => $model,
                        'searchModel' => $searchModel,
                        'hasMap' => $hasMap,
                        'dataProvider' => $dataProvider,
                        'mapTypes' => $mapTypes,
                        'limitsXcM' => $limitsXcM,
                        'BackToLims' => null,
            ]);
        }
    }

    public function actionGridDefinition($id = null) {
        // var_dump("hola"); exit();
        $samplesByProject = new \common\models\SamplesByProject();
        $samplesByProject->scenario = "unload";
        $parents = [];
        $plates = [];

        if (isset(Yii::$app->request->queryParams['ProjectId']) || $id != null) {
            $samplesByProject->ProjectId = $id != null ? $id : Yii::$app->request->queryParams['ProjectId'];
            $project = $this->findModel($samplesByProject->ProjectId);
        }

        if ($samplesByProject->load(Yii::$app->request->post()) && $samplesByProject->validate()) {
            //allow more memory
            ini_set('memory_limit', '-1');
            //If the user has uploaded any samples template
            if ($samplesByProject->IsTemplate == 1) {
                $array_genotypes = \common\models\SamplesByProject::createSamplesByTemplate($samplesByProject);
            } else
                $array_genotypes = $this->normalizeGenotypesArray($samplesByProject->SampleName);

            //If was found any error on the samples perform, return errors
            if (array_key_exists('error', $array_genotypes)) {
                return $this->render('_grid-definition', [
                            'samplesByProject' => $samplesByProject,
                            'project' => $project,
                            'parents' => $parents,
                            'plates' => $plates,
                            'projectName' => Yii::$app->session->get('Name'),
                ]);
            }
            //Save new relation plates_by_project with the additionals plates selected
            \common\models\PlatesByProject::savePlatesByProjectId($samplesByProject->PlateIdList, $samplesByProject->ProjectId);

            $samplesByProject->saveSamples($array_genotypes, $samplesByProject->ProjectId);

            $project = $this->findModel($samplesByProject->ProjectId);

            $project->StepProjectId = \common\models\StepProject::GRID_DEFINITION;
            
            //modified Matias
            $project->StepUpdateAt = date("Y-m-d");
            $project->UpdateAt = date("Y-m-d");
            //END modified Matias
            if (!$project->save()) {
                //print_r($project->getErrors());
                exit("Error to save");
            }

            if(Yii::$app->user->getIdentity()->itemName == "breeder")
            {
                return $this->redirect(['project-preview', "ProjectId" => $samplesByProject->ProjectId]); //actionProjectPreview($samplesByProject->ProjectId);
            }
            
            return $this->redirect(['select-markers', 'ProjectId' => $samplesByProject->ProjectId]);
            
            
        }

        $mateials = \common\models\MaterialsByProject::find()->where(["ProjectId" => $samplesByProject->ProjectId])->all();
        if (count($mateials) == 2) {
            foreach ($mateials as $m) {
                $parents[] = $m->materialTest->Name;
            }

            //new: Get Plates
            $plates = Plate::getPlatesByParents($mateials);
        }

        return $this->render('_grid-definition', [
                    'samplesByProject' => $samplesByProject,
                    'project' => $project,
                    'parents' => $parents,
                    'plates' => $plates,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionUpdateGridDefinition($idProject) {
        $parents = [];
        $plates = [];
        $samplesByProject = new \common\models\SamplesByProject();
        $samplesByProject->scenario = "unload";

        $project = $this->findModel($idProject);
        $samplesSelected = \common\models\SamplesByProject::getSamples($idProject);

        if ($samplesByProject->load(Yii::$app->request->post()) && $samplesByProject->validate()) {
            $samplesByProject->deleteSamples();
            
            if ($samplesByProject->IsTemplate == 1) {
                $array_genotypes = \common\models\SamplesByProject::createSamplesByTemplate($samplesByProject);
            } else
                $array_genotypes = $this->normalizeGenotypesArray($samplesByProject->SampleName);

            //Delete old relations and save new relation plates_by_project with the additionals plates selected
            \common\models\PlatesByProject::savePlatesByProjectId($samplesByProject->PlateIdList, $samplesByProject->ProjectId, true);

            $samplesByProject->saveSamples($array_genotypes);

            $samplesByProject->scenario = null;

            $this->saveProjectUpdateAt($project);
            
            //This case is for projects that are already on grid_definition on previous verison
            //and that projects must save samples and allow shipment step
            if($project->StepProjectId < StepProject::SENT && count($project->markersByProjects) > 0 )
            {    
                $project->StepProjectId = StepProject::MARKERS_SELECTION;
                $project->update();
            }

            return $this->redirect(['view', 'id' => $samplesByProject->ProjectId]);
        }

        $mateials = \common\models\MaterialsByProject::find()->where(["ProjectId" => $project->ProjectId])->all();

        if (count($mateials) == 2) {
            foreach ($mateials as $m) {
                $parents[] = $m->materialTest->Name;
            }

            //new: Get Plates
            $plates = Plate::getPlatesByParents($mateials);
        }

        $samplesByProject->ProjectId = $project->ProjectId;
        $samplesByProject->SampleName = $samplesSelected;

        //Get the selected plates to load combo multi select
        $platesSelected = \common\models\PlatesByProject::getPlatesByProjectId($project->ProjectId);
        // Add the array of plateId to model variable
        $samplesByProject->PlateIdList = ArrayHelper::getColumn($platesSelected, "PlateId");

        return $this->render('_grid-definition', [
                    'samplesByProject' => $samplesByProject,
                    'project' => $project,
                    'parents' => $parents,
                    'plates' => $plates,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionProjectPreview($ProjectId = null, $finished = null, $onHold = null) {
        $shipment = null;
        $statusByProject = null;

        if ($ProjectId == null) {
            $ProjectId = Yii::$app->request->queryParams['ProjectId'];
        }
        $model = Project::find()
                ->where(["ProjectId" => $ProjectId])
                ->one();

        $materials = \common\models\MaterialsByProject::find()
                ->where(["ProjectId" => $ProjectId])
                ->all();
        $markers = \common\models\MarkersByProject::find()
                ->with('snpLab')
                ->where(["ProjectId" => $ProjectId, "IsActive" => 1])
                ->asArray()
                ->all();
        $samplesByProject = \common\models\SamplesByProject::find()
                ->where(["ProjectId" => $ProjectId])
                ->asArray()
                ->all();
        $parents = \common\models\MaterialsByProject::find()
                ->where("ProjectId =" . $ProjectId . " and ParentTypeId <> 3")
                ->orderBy([ 'ParentTypeId' => SORT_ASC])
                ->all();
        if ($finished != null || $onHold != null) {
            $shipment = $model->IsSent == 1 ? \common\models\ProjectGroups::getDataProjectGroupsByProjectId($ProjectId) : null;
            if ($finished != null) {
                $statusByProject = \common\models\StatusByProject::find()
                        ->where(["ProjectId" => $model->ProjectId, "StepProjectId" => StepProject::FINISHED])
                        ->orderBy(["StatusByProjectId" => SORT_DESC])
                        ->one();
            } else {
                $statusByProject = \common\models\StatusByProject::find()
                        ->where(["ProjectId" => $model->ProjectId])
                        ->orderBy(["StatusByProjectId" => SORT_DESC])
                        ->one();
            }
        }

        $numLastSampleByPlate = \common\models\SamplesByProject::getLastNumberByPlate($model->ProjectId, $parents);
        
        return $this->render('_project-preview', [
                    // "plates" => $plate,
                    "project" => $model,
                    'materials' => $materials,
                    "markers" => $markers,
                    "samplesByProject" => $samplesByProject,
                    "parents" => $parents,
                    "numLastSampleByPlate" => $numLastSampleByPlate,
                    "shipment" => $shipment,
                    //"status" => $finished,
                    "statusByProject" => $statusByProject
        ]);
    }

    /* Return all plates gruoped with the project
     * parameter integer
     * @return mixed
     */

    public function actionViewShipment($idProject) {
        $model = $this->findModel($idProject);
        $sql = $this->getQueryStringPlatesByProjectsGruoup($idProject);
        $samplesByPlate = Yii::$app->db->createCommand($sql)->queryAll();
        $array_parents = \common\models\MaterialsByProject::getMaterialsNameByIdProjectGroups($idProject);
        
        return $this->render('_shipment', [
                    'model' => $model,
                    'samplesByPlate' => $samplesByPlate,
                    'array_parents' => $array_parents,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionSampleDispatch($id = null) {
        $dispatch = new \common\models\DispatchPlate;

        if ($id != null) {
            $project = $this->findModel($id);
            $dispatch->ProjectId = $id;
        }

        if ($dispatch->load(Yii::$app->request->post()) && $dispatch->validate()) {
            $dispatch->IsActive = 1;
            $dispatch->save();

            $project = $this->findModel($dispatch->ProjectId);
            $project->StepProjectId = StepProject::SAMPLE_DISPATCH;
            //modified Matias
            $project->UpdateAt = date("Y-m-d");
            $project->StepUpdateAt = date("Y-m-d");
            //END modified Matias
            if ($project->save()) {
                /* Looking for other projects grouped */
                $groupedIds = \common\models\ProjectGroupsByProject::findProjectsGrouped($project->ProjectId);
                foreach ($groupedIds as $projectId) {
                    $projectGrouped = $this->findModel($projectId['ProjectId']);
                    $projectGrouped->StepProjectId = StepProject::SAMPLE_DISPATCH;
                    $projectGrouped->save();

                    $dispatch = new \common\models\DispatchPlate;
                    $dispatch->load(Yii::$app->request->post());
                    $dispatch->ProjectId = $projectGrouped->ProjectId;
                    $dispatch->IsActive = 1;
                    $dispatch->save();
                }
            }

            return $this->redirect('view?id='.$dispatch->ProjectId);
        }
        //else print_r($dispatch->getErrors() ); exit;
        return $this->render('_sample-dispatch', [
                    'dispatch' => $dispatch,
                    'project' => $project,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionUpdateSampleDispatch($idProject) {
        $model = new \common\models\DispatchPlate();
        $dispatch = $model->find()->where(["ProjectId" => $idProject])->one();
//        //$plate = \common\models\Plate::find()
//                ->where(["ProjectId" => $id])
//                ->all();

        $project = $this->findModel($idProject);


        if ($dispatch->load(Yii::$app->request->post()) && $dispatch->validate()) {
            //print_r($dispatch); exit;
            $dispatch->IsActive = 1;
            if (!$dispatch->save()) {
                print_r($dispatch->getErrors());
                exit;
            };

            $project = $this->findModel($dispatch->ProjectId);
            $project->StepProjectId = StepProject::SAMPLE_DISPATCH;
            //modified Matias
            $project->UpdateAt = date("Y-m-d");
            //END modified Matias
            $project->save();

            return $this->actionView($dispatch->ProjectId);
        }
        //else print_r($dispatch->getErrors() ); exit;
        return $this->render('_sample-dispatch', [
                    'dispatch' => $dispatch,
                    'project' => $project,
                    'projectName' => Yii::$app->session->get('Name'),
        ]);
    }

    public function actionSampleReception($id = null) {

        $reception = new \common\models\ReceptionPlate();

        if ($id != null) {
            $project = $this->findModel($id);
            $reception->ProjectId = $id;
        }
        if ($project->StepProjectId < StepProject::SAMPLE_RECEPTION) {

            if ($reception->load(Yii::$app->request->post()) && $reception->validate()) {
                $reception->IsActive = 1;
                $reception->save();

                $project = $this->findModel($reception->ProjectId);
                $project->StepProjectId = StepProject::SAMPLE_RECEPTION;
                //modified Matias
                $project->UpdateAt = date("Y-m-d");
                $project->StepUpdateAt = date("Y-m-d");
                //END modified Matias
                if ($project->save()) {
                    /* Looking for other projects grouped */
                    $groupedIds = \common\models\ProjectGroupsByProject::findProjectsGrouped($project->ProjectId);
                    foreach ($groupedIds as $projectId) {
                        $projectGrouped = $this->findModel($projectId['ProjectId']);
                        $projectGrouped->StepProjectId = StepProject::SAMPLE_RECEPTION;
                        $projectGrouped->save();

                        $dispatch = new \common\models\ReceptionPlate();
                        $dispatch->load(Yii::$app->request->post());
                        $dispatch->ProjectId = $projectGrouped->ProjectId;
                        $dispatch->IsActive = 1;
                        $dispatch->save();
                    }
                }

                return $this->actionView($reception->ProjectId);
            }
            //else print_r($reception->getErrors() ); exit;
            return $this->render('_sample-reception', [
                        'reception' => $reception,
                        'project' => $project,
                        'projectName' => Yii::$app->session->get('Name'),
            ]);
        }
    }

    public function actionDelete($id) {
        //print_r(Yii::$app->request->isAjax); exit;
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            //print_r('im here'); exit;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            //print_r(Yii::$app->request->post()); exit;
            if ($isSubmit) {
                $model->IsActive = 0;
                if ($model->save()) {
                    $sql = "SET FOREIGN_KEY_CHECKS=0;"
                            // Delete Traits By Markers
                            ."delete traits_by_markers_by_project from traits_by_markers_by_project
                            inner join markers_by_project on markers_by_project.MarkersByProjectId = traits_by_markers_by_project.MarkersByProjectId
                            where markers_by_project.ProjectId = ".$id.";"
                            //Delete projects groups: In real case, the uuser can not delete
                            //the project in this insance
                            /*."delete project_groups from project_groups
                            inner join project_groups_by_project on project_groups.ProjectGroupsId = project_groups_by_project.ProjectGroupsId
                            where project_groups_by_project.ProjectId= .$id.
                            
                            ."delete samples_by_plate from samples_by_plate
                            inner join plates_by_project ON plates_by_project.PlateId = samples_by_plate.PlateId
                            where plates_by_project.ProjectId = .$id.
                            
                            delete genspin from genspin
                            inner join adn_extraction on adn_extraction.AdnExtractionId=genspin.AdnExtractionId
                            inner join plates_by_project on plates_by_project.PlateId = adn_extraction.PlateId
                            where plates_by_project.ProjectId = .$id.
                            
                            delete adn_extraction from adn_extraction
                            inner join plates_by_project ON plates_by_project.PlateId = adn_extraction.PlateId
                            where plates_by_project.ProjectId = .$id.
                            
                            delete discarted_plates from discarted_plates
                            inner join plates_by_project ON plates_by_project.PlateId = discarted_plates.PlateId
                            where plates_by_project.ProjectId = .$id.
                            */
                            ."delete traits_by_materials from traits_by_materials
                            inner join materials_by_project on traits_by_materials.MaterialsByProjectId = materials_by_project.ProjectId
                            where materials_by_project.ProjectId = ".$id.";"

                            /*."delete date_by_plate_status from date_by_plate_status
                            inner join plates_by_project on plates_by_project.PlateId = date_by_plate_status.PlateId
                            where plates_by_project.ProjectId = ".$id.
                            
                            delete plate from plate
                            inner join plates_by_project on plates_by_project.PlateId = plate.PlateId
                            where plates_by_project.ProjectId = .$id.
                            */
                            
                            ."delete from assay_by_project where ProjectId = ".$id.";"
                            ."delete from plates_by_project where ProjectId = ".$id.";"
                            ."delete from samples_by_project where ProjectId = ".$id.";"
                            ."delete from materials_by_project where ProjectId = ".$id.";"
                            ."delete from markers_by_project where ProjectId = ".$id.";"
                            ."delete from dispatch_plate where ProjectId =" .$id.";"
                            ."delete from reception_plate where ProjectId=".$id.";"
                            ."delete from traits_by_project where ProjectId = ".$id.";"
                            ."delete from project_groups_by_project where ProjectId=".$id.";"
                            ."delete from reason_by_project where ProjectId=".$id.";"
                            ."UPDATE project SET IsActive=0 WHERE ProjectId = ".$id.";";
                    $sql .= "SET FOREIGN_KEY_CHECKS=1;";
                    $rows = Yii::$app->db->createCommand($sql)->execute();
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $this->renderAjax('delete', ['model' => $model, 'ok' => 'true']);
                } else {
                    print_r($model->getErrors());
                }

                //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                //return $this->renderAjax('delete', ['model' =>$model, 'ok' => $true]);
                return $this->redirect('index');
            }
        }
        return $this->renderAjax('delete', ['model' => $model]);
    }

    protected function findModel($id) {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTraitsByCrop() {
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $crop_id = $parents[0];

                $traits = Traits::findAll(["Crop_Id" => $crop_id]);
                $trait = array();
                foreach ($traits as $t) {
                    $trait[] = ['name' => $t->Name, 'id' => $t->TraitsId];
                }
                //$trait=  $trait->getTraitsByCrop($crop_id);

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['output' => $trait];
            }
        }
    }

    public function actionGetProjectTypeByJson() {
        $dataType = \common\models\ProjectType::find()->where(["IsActive" => 1])->orderBy("Name", SORT_ASC)->asArray()->all();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $dataType;
    }

    public function actionStep1() {
        return $this->actionSelectMarkers(Yii::$app->request->queryParams['id']);
    }

    public function actionStep2() {
        return $this->redirect(['grid-definition', 'ProjectId' => Yii::$app->request->queryParams['id']]);
    }

    public function actionStep3() {
        $model = $this->findModel(Yii::$app->request->queryParams['id']);
        return $this->render('_sample-dispatch', ['notSent' => true, 'projectName' => $model->Name, 'id' => $model->ProjectId]);
    }

    public function actionStep4() {
        return $this->actionSampleDispatch(Yii::$app->request->queryParams['id']);
    }

    public function actionStep5() {
        return $this->actionSampleReception(Yii::$app->request->queryParams['id']);
    }

    /* private function getLastPlate() {
      $conection = Yii::$app->db;
      $sql = "select max(PlateId) from  plate";
      $lastID = $conection->createCommand($sql)->queryScalar();

      return $lastID + 1;
      } */

    private function NormalizeInArray($array, $flagProjectDefinition = null) {
        if ($flagProjectDefinition != null)
            $array = preg_replace('/\s+/', '', $array);

        if (strpos($array, "\n") !== false)
            $array_markers = explode("\n", $array);
        elseif (strpos($array, ",") !== false)
            $array_markers = explode(",", $array);
        elseif (strpos($array, "; ") !== false)
            $array_markers = explode(";", $array);
        else
            $array_markers[] = $array;

        return $array_markers;
    }

    private function normalizeGenotypesArray($genotypes) {
        if (strpos($genotypes, "\n") !== false)
            $array_genotype = explode("\n", $genotypes);
        elseif (strpos($genotypes, " ") !== false)
            $array_genotype = explode(" ", $genotypes);
        elseif (strpos($genotypes, ", ") !== false)
            $array_genotype = explode(", ", $genotypes);
        elseif (strpos($genotypes, "; ") !== false)
            $array_genotype = explode("; ", $genotypes);
        else
            $array_genotype[] = $genotypes;

        $string_clean = implode(",", $array_genotype);
        $newString = str_replace(chr(13), "", $string_clean);
        $new_array_clean = explode(",", $newString);
        $length = count($new_array_clean);
        if ($new_array_clean[$length - 1] == "")
            unset($new_array_clean[$length - 1]);

        return $new_array_clean;
    }

    private function getMarkersByCheck($Mcheck) {

        $string_marks = implode(',', $Mcheck);

        $conection = Yii::$app->dbGdbms;

        $sql = "SELECT Name FROM marker WHERE Marker_Id in (" . $string_marks . ")";
        $marks = $conection->createCommand($sql)->queryAll();
        if ($marks) {
            $string_to_copy = "";
            foreach ($marks as $m) {
                $string_to_copy .= $m['Name'] . "\n";
            }

            return $string_to_copy;
        } else
            return false;
    }

    public function createName($model) {
        //$name = "";

        $name = Crop::findOne(['Crop_Id' => $model->Crop_Id])->ShortName;
        $name .= date('ymd');
        $name .= ResearchStation::findOne(["ResearchStationId" => $model->ResearchStationId])->Short;
        $name .="_";
        $name .=\common\models\ProjectType::findOne(["ProjectTypeId" => $model->ProjectTypeId])->Short;
        if ($model->GenerationId !== null) {
            $name .="_";
            $name .= \common\models\Generation::findOne(["GenerationId" => $model->GenerationId])->Description;
            
            /* SUSPENDED BECAUSE LENGTH NAME IS EXCEDED
            if ($model->Parent1_donnor !== null && $model->HasParents == 1) {
                $name .="_";
                $name .= $model->parent1_donnor->Name . "(" . $model->parent2_receptor->Name . ")";
            }
            */
            if ($model->traits_by_parent2 != "") {
                foreach ($model->traits_by_parent2 as $key => $idTrait) {
                    if ($key > 0)
                        $name.= '-'; //continue
                    else
                        $name .="_"; //begin
                    $name .= Traits::findOne(["TraitsId" => $idTrait])->Name;
                }
            }
            if ($model->traits_by_parent1 != "") {
                foreach ($model->traits_by_parent1 as $key => $idTrait) {
                    if ($key > 0)
                        $name.= '-'; //continue
                    else{
                        $name .= $model->traits_by_parent2 != "" ? "+":"_";
                    }
                    $name .= Traits::findOne(["TraitsId" => $idTrait])->Name;
                }
            }
        }
        $matchWithname = Project::findBySql("SELECT count(*) FROM project "
                                            . "WHERE Name = '".$name."' OR Name LIKE '".$name."_%' "
                                            . "and IsActive = 1")->scalar();
        
        if ($matchWithname > 0)
        {
            $char = 96 + $matchWithname;
            $name .= "_" . chr($char);
        }
        
        return $name;
        
        //(CropYYMMDDResearchStation_ProjectType_Generation_PollenReceptor(PollenDonnor)_​​Trait​1/​​Trait​2/​​.../TraitN.)
    }

    private function saveMaterialsByMatrix($model) {
        $sql = "INSERT INTO materials_by_project (Material_Test_Id, ProjectId, ParentTypeId, IsActive) VALUES";
        $limit = count($model->vCheck);
        $it = 1;
        if ($limit > 0) {
            foreach ($model->vCheck as $key => $id) {
                if ($it == $limit)
                    $sql .= "(" . $id . "," . $model->ProjectId . ", 3, 1);";
                else
                    $sql .= "(" . $id . "," . $model->ProjectId . ", 3, 1),";
                $it++;
            }
        } else
            return false;


        $rows = Yii::$app->db->createCommand($sql)->execute();

        return $rows;
        /*
         * OLD STEP
          foreach ($model->vCheck as $key => $val)
          {
          $materialsByProject = new \common\models\MaterialsByProject();
          $materialsByProject->Material_Test_Id = $val;
          $materialsByProject->ProjectId = $model->ProjectId;
          $materialsByProject->ParentTypeId = 3;
          $materialsByProject->save();

          }
         * 
         */
    }

    /* Save in materials_by_project as parents, and save the traits each parent
     * @parameter mixed
     * return void
     */

    private function saveMaterialsAndTraitsBy2Parents($model) {
        $materialsByProject = new \common\models\MaterialsByProject();
        $materialsByProject->Material_Test_Id = $model->Parent1_donnor;
        $materialsByProject->ProjectId = $model->ProjectId;
        $materialsByProject->ParentTypeId = \common\models\ParentType::POLLEN_DONNOR;
        $materialsByProject->IsActive = 1;
        if ($materialsByProject->save()) {
            if ($model->traits_by_parent1 != null) {
                foreach ($model->traits_by_parent1 as $key => $val) {
                    $traitByMaterial1 = new \common\models\TraitsByMaterials();
                    $traitByMaterial1->MaterialsByProjectId = $materialsByProject->MaterialsByProject;
                    $traitByMaterial1->TraitsId = $val;
                    $traitByMaterial1->IsActive = 1;
                    $traitByMaterial1->save();
                }
            }
        }

        $materialsByProject2 = new \common\models\MaterialsByProject();
        $materialsByProject2->Material_Test_Id = $model->Parent2_receptor;
        $materialsByProject2->ProjectId = $model->ProjectId;
        $materialsByProject2->ParentTypeId = \common\models\ParentType::POLLEN_RECEPTOR;
        $materialsByProject2->IsActive = 1;
        if ($materialsByProject2->save()) {
            if ($model->traits_by_parent2 != null) {
                foreach ($model->traits_by_parent2 as $key => $val) {
                    $traitByMaterial2 = new \common\models\TraitsByMaterials();
                    $traitByMaterial2->MaterialsByProjectId = $materialsByProject2->MaterialsByProject;
                    $traitByMaterial2->TraitsId = $val;
                    $traitByMaterial2->IsActive = 1;
                    $traitByMaterial2->save();
                }
            }
        }
    }

    /*
     * private function saveTraits($model) 
      {
      $traits = new \common\models\TraitsByProject();

      foreach ($model->Trait as $key => $idTrait) {
      $traits->TraitsId = $idTrait;
      $traits->ProjectId = $model->ProjectId;
      $traits->save();
      }
      }
     */

    private function createCode($model) {

        $yy = date('y');
        $name = Crop::findOne(['Crop_Id' => $model->Crop_Id])->ShortName;
        //$count_p = Project::find()->where(["IsActive" => 1])->count('*');
        $last_projectCode = Project::find()->select('ProjectCode')->where(["IsActive" => 1])->orderBy(['ProjectId' => SORT_DESC])->limit(1)->scalar();
        
        $new_projectCode = (int)substr($last_projectCode, -5);

        $new_projectCode++;
        
        $format_count = sprintf("%05d", $new_projectCode);

        $code = $name . $yy . $format_count;
        
        /*
        $matchCodes = Project::find()->where(['ProjectCode' => $code])->all();
        if (count($matchCodes) > 0)
            $code .= "_" . chr(96 + count($matchCodes));
        */
        return $code;
    }

    public function actionSendGrids($idProjects = null) {
        $paths = "";

        $plate = new \common\models\SamplesByProject();

        $idProjects = $idProjects != null ? [0 => $idProjects] : Yii::$app->request->post()['vCheck'];
        
        // order low to high 
        if (is_array($idProjects))
            sort($idProjects);
        
        $f1 = Project::anyF1($idProjects);
        if ($idProjects != null) {

            $method = (isset(Yii::$app->request->queryParams['type'])) ? Yii::$app->request->queryParams['type'] : null;

            if (($result = $this->validateProjectsSelectes($idProjects)) == true) {
                
                $result = $this->generateGridByProject($idProjects, $method, Project::SEND, $f1);

                if ($result != false) {
                    $data['file'] = $this->actionDownloadPlatePdf($idProjects, Project::SEND);
                    $data['file2'] = $this->createBarcodePdf($idProjects);
                    
                    //save projects_grouped_by_group
                    $this->saveProjectsGroupeds($idProjects);
                } else {
                    return 'NOT';
                    
                }
                
                //if user is breeder, just download zip files and update status project
                if(Yii::$app->user->getIdentity()->itemName == "breeder")
                {
                    $this->setProjectsSents($idProjects);
                    return $this->createZipFile($data);
                    //return $this->actionGetShipmentData($idProjects);
                }
                //Get User
                $data['userId'] = $this->findModel($idProjects[0])->UserId;
                //keep projects to send 
                $data['projectsId'] = $idProjects;
                
                $request = $this->sendEmail($data);

                unlink($data['file']);
                return $request;
            } else
                return 'NOT';
        }else {
            return "Failed to send mail . Please contact the administrator.";
        }

        //return $result; 
    }

    public function actionValidateSelected() {
        if (($result = $this->validateProjectsSelectes(Yii::$app->request->post()['vCheck'])) == true) {
            echo "OK";
        } else {
            echo "NOT";
        }
    }

    public function actionControlF1() {
        if (($result = Project::anyF1(Yii::$app->request->post()['vCheck'])) == true) {
            echo "Y";
        } else {
            echo "N";
        }
    }

    public function actionDownloadGrids($idProjects = null, $f1 = null) {
        $paths = "";
        $plate = new \common\models\SamplesByProject();

        $idProjects = $idProjects != null ? [0 => $idProjects] : Yii::$app->request->queryParams['vCheck'];
        
        if ($idProjects != null) {
            $method = Yii::$app->request->queryParams['type'];
            if (($result = $this->validateProjectsSelectes($idProjects)) == true) {
                //$data = $this->generateGridByProject($idProjects, $method, $f1, Project::DOWNLOAD);
                $files_name[] = $this->actionDownloadPdf($idProjects, $method);
                $files_name[] = $this->createBarcodePdf($idProjects);
                return $this->generateZip($files_name);
            }
        } else {
            return "Failed to download Grid.";
        }

        //return $result; 
    }

    public function generateGridByProject($idProjects, $method, $action, $f1 = null) {
        //$plateByProject = Plate::find()->where(['ProjectId'=>$projectId])->all();
        $p = new \common\models\SamplesByProject();

        //$pathGrid = $p->generateGrid($projectId, $method, $f1, $action);
        $result = $p->prepareSamplesToCreateGrid($idProjects, $method, $f1, $action);

        return $result;
    }

    public function sendEmail($data) {
        $user = User::findOne($data['userId']);
        if ($user != null) {
            $enviroment = $_SERVER['HTTP_HOST'] . Yii::$app->getUrlManager()->getBaseUrl();

            if ((strpos($enviroment, 'localhost') !== false) || (strpos($enviroment, 'testing.lims') !== false) || (strpos($enviroment, 'testing.advanta') !== false)) {
                $mail = MailSettings::localMode($user, $data);
                
                $status = $mail->Send();
                /* para guardar como seteados */
                $this->setProjectsSents($data['projectsId']);
                if ($status == 1)
                {
                    //$this->saveProjectUpdateAt(null);
                    return 'OK';
                }
                else
                    return 'FAIL';
            } else {
                $mail = MailSettings::productionMode($user, $data);

                $mail->Send();

                /* para guardar como seteados */
                $this->setProjectsSents($data['projectsId']);
                return 'OK';
            }

            //$state = $mail->Send();
            //return $state;
        } else
            return "The User has not mail. Please complete his profile";
    }

    /* DISABLED **
      private function setProjectsSents($projectsId)
      {
      foreach($projectsId as $key => $id)
      {
      $model = $this->findModel($id);
      $model->IsSent = 1;
      $model->update();
      }
      }
     */

    private function validateProjectsSelectes($idProjects) {
        $sql = "";
        $array_ids = is_array($idProjects) ? implode(', ', $idProjects) : $idProjects;
        
        //Add control with StepsByProjectId. This step must be equal for all selections
        $sql = "SELECT count(*) FROM project"
                . " LEFT JOIN project p ON p.ProjectId = project.ProjectId"
                . " WHERE project.ProjectId in (" . $array_ids . ") and project.IsActive=1"
                . " GROUP BY"
                . " project.StepProjectId,"
                . " project.UserId;";

        $projects = Yii::$app->db->createCommand($sql)->queryScalar();

        return $projects >= 1;
    }

    public function actionRegisterShipment($projects = null) {
        
    }

    public function actionGetProjectsByBreeder() {
        $model = new ProjectSearch();
        //$params = ['ProjectSearch'=>['UserId'=>Yii::$app->user->id]];
        $model->UserId = Yii::$app->user->id;
        $projects = $model->search();

        return $this->render('indexBreeder', [
                    'searchModel' => $model,
                    'dataProvider' => $projects
                        ]
        );
    }

    public function actionExamples() {
        
        $sr = "";
        $i = 600;

        while ($i <= 7290) {
            $sr .= "TTW-2R-" . $i; //*rand(200, 8000); 
            $sr .= "<br>";
            $i++;
        }
        //$sr = [0 =>'feb', 'lol' => 'mar',5 => 'may','er'=> 'jun'];
        //$sr = Array ( 0 => Array ( 'total' => 1294 ,'Year' => 2016 ,'Month' => 07 ,'Day' => 12 ,'Date' => 2016-07-12 ) );
        //$lal = \common\components\Operations::array_column($sr, 'Month');
        //$this->Hanoi(5, "Pilar1" , "Pilar2", "Pilar3");
        //       exit;
        print_r($sr);
        exit;


        $val = 523;
        $num = 0;
        while ($val > 0) {

            $dig = $val % 10;
            $num = $num + $dig;
            $val = $val / 10;
            echo $val . "<br>";
        }
        print_r($num);
        // print_r(substr('2016-15-12', 0,-3 ));
        exit;

        //return $this->render('/site/plate');
    }

    private function setProjectsSents($projectsId) {
        foreach ($projectsId as $key => $id) {
            $model = $this->findModel($id);
            $model->IsSent = 1;
            $model->StepProjectId = StepProject::SENT;
            $model->UpdateAt = date("Y-m-d");
            $model->StepUpdateAt = date("Y-m-d");
            $model->update();
        }
    }

    /*
     * Set as No Sent the projects
     * parameter array ProjectId
     * return boolean
     */

    private function getQueryStringPlatesByProjectsGruoup($idProject) {
        // In this line i change the sbyp.ProjectId for p1.ProjectId 
        $projects = is_array($idProject) ? implode(',', $idProject) : $idProject;
        $sql = "SELECT sp.SamplesByPlateId, sp.PlateId, sbyp.ProjectId, p1.ProjectId as projectToParent, sp.SamplesByProjectId, sbyp.SampleName, sp.`Type`, sp.StatusSampleId, p1.Name  FROM samples_by_plate sp 
                left join samples_by_project sbyp ON sbyp.SamplesByProjectId=sp.SamplesByProjectId
                left join plates_by_project pbyp ON pbyp.PlateId=sp.PlateId
                left join project p1 ON p1.ProjectId=pbyp.ProjectId
                WHERE sp.PlateId in (
                                        SELECT PlateId FROM plates_by_project pbp
                                        WHERE pbp.ProjectId in (
                                                SELECT ProjectId FROM project_groups_by_project 
                                                WHERE ProjectGroupsId = (
                                                        SELECT ProjectGroupsId FROM project_groups_by_project pgbp
                                                        WHERE pgbp.ProjectId IN (" . $projects . ")
                                                        GROUP BY ProjectGroupsId
                                                        ))                                                     
                                        GROUP BY PlateId)
                GROUP  BY sp.SamplesByPlateId";

        return $sql;
    }

    private function saveMoreUsersByProject($model) {
        foreach ($model->UserByProject as $key => $val) {
            $userByProject = new \common\models\UserByProject();
            $userByProject->ProjectId = $model->ProjectId;
            $userByProject->UserId = $val;
            //$userByProject->IsActive = 1;
            $userByProject->save();
        }
    }

    /* End the project
     * @params integer id
     * return integer
     */

    public function actionFinish($id) {
        $this->layout = false;
        $project = $this->findModel($id);
        $project->scenario = "ChangeStatus";
        if (Yii::$app->request->isAjax) {
            //$isSubmit = !is_null(Yii::$app->request->post('submit'));
            if (Yii::$app->request->post()) {
                $project->StepProjectId = StepProject::FINISHED;
                $project->CommentToChangeStatus = Yii::$app->request->post()['Project']['CommentToChangeStatus'];
                //modified Matias
                $project->UpdateAt = date("Y-m-d");
                $project->StepUpdateAt = date("Y-m-d");
                //END modified Matias
                if ($project->save()) {
                    \common\models\StatusByProject::saveStatusByProject($project, StepProject::FINISHED);
                }
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true, "finished" => true]);
            }
        }
        return $this->renderAjax('delete', ['model' => $project, "finished" => true]);
    }

    public function actionOnHold($id) {
        $this->layout = false;
        $project = $this->findModel($id);
        $project->scenario = "ChangeStatus";
        //$causes = \common\models\CancelCauses::findAll(["IsActive" => 1]);
        if (Yii::$app->request->isAjax) {
            //modify this item to validate, 'submit' not work when there are others inputs
            if (Yii::$app->request->post()) {
                
                $lastStep = $project->StepProjectId;
                $project->StepProjectId = StepProject::ON_HOLD;
                $project->CommentToChangeStatus = Yii::$app->request->post()['Project']['CommentToChangeStatus'];
                
                if ($project->save()) {
                    \common\models\StatusByProject::saveStatusByProject($project, $lastStep);
                    //\common\models\StatusByProject::saveStatusByProject($project, StepProject::ON_HOLD);
                }
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true, "finished" => true]);
            }
        }
                
        return $this->renderAjax('delete', ['model' => $project, "finished" => true]);
    }

    public function actionResume($id) {
        $this->layout = false;
        $project = $this->findModel($id);
        //$project->CommentToChangeStatus = "Resume Project";

        $lasStatus = $statusByProject = \common\models\StatusByProject::find()
                ->where(["ProjectId" => $project->ProjectId])
                ->orderBy(["StatusByProjectId" => SORT_DESC])
                ->one();
        $project->StepProjectId = $lasStatus->StepProjectId;
        $project->save();
        //\common\models\StatusByProject::saveStatusByProject($project, $project->StepProjectId);

        $string = "<div class='alert alert-success'> The project has been resume successfully </div> ";
        return $string;
    }

    public function actionCancel($id) {
        $this->layout = false;
        $project = $this->findModel($id);
        $project->scenario = "CancelProject";
        $plates = \common\models\PlatesByProject::getPlatesByProjectId($project->ProjectId);
        
        $causes = \common\models\CancelCauses::findAll(["IsActive" => 1]);
        
        if (Yii::$app->request->isAjax) {
            
            if (Yii::$app->request->post()) {
                $project->StepProjectId = StepProject::CANCELED;
                $project->load(Yii::$app->request->post());
                //$project->CommentToChangeStatus =  Yii::$app->request->post()['Project']['CommentToChangeStatus'];

                if ($project->save()) {
                    \common\models\StatusByProject::saveStatusByProject($project, StepProject::CANCELED);
                    if ($project->Plates != "") {
                        foreach ($project->Plates as $k => $plateId)
                            \common\models\Plate::saveStatus($plateId, \common\models\StatusPlate::CANCELED);
                    }
                }

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true, "finished" => true]);
            }
        }
        return $this->renderAjax('delete', ['model' => $project, "finished" => true, "causes" => $causes, "plates" => $plates]);
    }

    /*     * *********************************************
     * 
     *                 Snplab refactory
     * 
     * ********************************************** */

    private function getSnpLabByCheck($Mcheck) {

        $string_marks = implode(',', $Mcheck);

        $conection = Yii::$app->dbGdbms;

        $sql = "SELECT LabName FROM snp_lab s WHERE s.Snp_lab_Id in (" . $string_marks . ")";
        $marks = $conection->createCommand($sql)->queryAll();
        if ($marks) {
            $string_to_copy = "";
            foreach ($marks as $m) {
                $string_to_copy .= $m['LabName'] . "\n";
            }
            return $string_to_copy;
        } else
            return false;
    }

    /*
     * Generate PDF file with grids preview
     * @params array
     * @return file
     */

    public function actionDownloadPdf($projectIds = null, $method = null) {
        $var = NULL;
        $destination = null;
        foreach ($projectIds as $key => $projectId) {
            Yii::$app->layout = false;
            $model = Project::find()
                    ->where(["ProjectId" => $projectId, "IsActive" => 1])
                    ->with('platesByProjects')
                    ->one();
            $samplesByProject = \common\models\SamplesByProject::find()
                    ->where(["ProjectId" => $model->ProjectId])
                    ->asArray()
                    ->all();
            $parents = \common\models\MaterialsByProject::find()
                    ->where("ProjectId = " . $model->ProjectId . " and ParentTypeId<>3 ")
                    ->orderBy([ 'ParentTypeId' => SORT_ASC])
                    ->all();

            $numLastSampleByPlate = \common\models\SamplesByProject::getLastNumberByPlate($model->ProjectId, $parents);

            //return $this->renderAjax("exam", ["var" => $var]);
            //echo ($var); exit;
            if (isset($model->platesByProjects) && count($model->platesByProjects) > 0)
                $destination = 'file';
            
            $var = Plate::createStringView($numLastSampleByPlate, $parents, $samplesByProject, $model, $method, $var);
            
        }
        
        if($method == Plate::COMBINED)
        {
            $var = Plate::prepareCompleteGridDiv($var['cont'], $var['row'], $var['var']);
        }
        
        return \common\models\SamplesByProject::generatePDF($var, null, $destination);
    }

    public function actionDownloadPlatePdf($projectIds, $action = null) {
        $var = "";
        Yii::$app->layout = false;
        
        //if($action == Project::SEND)
        //{
            $model = $this->findModel($projectIds);
            $sql = $this->getQueryStringPlatesByProjectsGruoup($projectIds);
            $samplesByPlate = Yii::$app->db->createCommand($sql)->queryAll();
            $array_parents = \common\models\MaterialsByProject::getMaterialsNameByIdProjectGroups($projectIds);
            
            $var .= $this->renderPartial("exam", [
                                                    "model" => $model,
                                                    "samplesByPlate" => $samplesByPlate,
                                                    "array_parents" => $array_parents
                                                ]);
        //}else
        //{
            
            /* Si han sido enviados
             *** recoger las muestras por plates, se puede usar el método de arriba
             * Si no estan enviados
             **** Recogeer las muestras por proyectos, usar Plate::createStringView
             **** al terminar el foreach de samples preguntar si es cominado, retornar contador 
             **** y la variable acumulativa de string
             *  
             */
        //}
        
        return \common\models\SamplesByProject::generatePDF($var, null, $action);
    }

    /*
     * New Dashboard - View Project Summary
     * @params id int
     * @return mixed
     */

    public function actionViewSummary($id) {
        $model = Project::find()
                ->where(["ProjectId" => $id])
                ->one();

        $parents = \common\models\MaterialsByProject::find()
                ->where("ProjectId = " . $model->ProjectId . " and ParentTypeId<>3 ")
                ->orderBy([ 'ParentTypeId' => SORT_ASC])
                ->all();

        return $this->renderPartial("new-style/_project-summary", ["model" => $model, "parents" => $parents]);
    }

    /*
     * New Dashboard - View Markers and Traits - load grid
     * Render the partial view which load the kendogrid
     * @param id int
     * @return mixed
     */

    public function actionViewMarkersAndTraits($id) {
        $model = Project::find()
                ->where(["ProjectId" => $id])
                ->one();

        return $this->renderPartial("new-style/_marker-selection", ["model" => $model]);
    }

    /*
     * New Dashboard - View Markers and Traits
     * @params id int
     * @return Json
     */

    public function actionGetMarkersAndTraits($id) {
        $sql = "SELECT mbp.MarkersByProjectId, s.LabName, t.Name FROM markers_by_project mbp
                INNER JOIN `advanta.gdbms`.snp_lab s ON s.Snp_lab_Id = mbp.Snp_lab_Id
                left JOIN traits_by_markers_by_project tbmbp ON tbmbp.MarkersByProjectId = mbp.MarkersByProjectId
                left JOIN traits_by_materials tbm ON tbm.TraitsByMaterialId = tbmbp.TraitsByMaterialId
                left JOIN traits t ON t.TraitsId = tbm.TraitsId
                where mbp.ProjectId =" . $id;

        $markers = Yii::$app->db->createCommand($sql)->queryAll();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $markers;
    }

    /*
     * New Dashboard - View Markers and Traits - load grid
     * Render the partial view which load the kendogrid
     * @param id int
     * @return mixed
     */

    public function actionGetGridPreview($id) {
        $model = Project::findOne($id);

        Yii::$app->db->createCommand("select * from project ")->sql;
        $samplesByProject = \common\models\SamplesByProject::find()
                ->where(["ProjectId" => $model->ProjectId])
                ->asArray()
                ->all();
        $parents = \common\models\MaterialsByProject::find()
                ->where("ProjectId = " . $model->ProjectId . " and ParentTypeId<>3 ")
                ->orderBy([ 'ParentTypeId' => SORT_ASC])
                ->all();
        
        $numLastSampleByPlate = \common\models\SamplesByProject::getLastNumberByPlate($model->ProjectId, $parents);

        return $this->renderPartial('_gridPreview', ['model' => $model,
                    'samplesByProject' => $samplesByProject,
                    'numLastSampleByPlate' => $numLastSampleByPlate,
                    'parents' => $parents,
        ]);
    }

    public function actionGetShipmentData($id) {
        $model = Project::findOne($id);

     
        if (Yii::$app->request->post() ) {

            $result = $this->actionSendGrids($id);
            //$result = "dasdsad";

            if(Yii::$app->user->getIdentity()->itemName == 'breeder')
                return $result; //in this case result is paht of zip created
            
            if ($result !== 'NOT') {
                $msj['type'] = "success";
                $msj['message'] = "The grid has been sent successfully!";
            } else {
                $msj['type'] = "danger";
                $msj['message'] = "Error:Mail delivery failed!";
            }
            return $this->renderAjax('new-style/_alert-sent', ['msj' => $msj]);
        } elseif ($model->IsSent == 1) {
            Yii::$app->layout = false;
            //$model = $this->findModel($idProject);
            $sql = $this->getQueryStringPlatesByProjectsGruoup($model->ProjectId);
            $samplesByPlate = Yii::$app->db->createCommand($sql)->queryAll();
            $array_parents = \common\models\MaterialsByProject::getMaterialsNameByIdProjectGroups($model->ProjectId);
            return $this->renderPartial('../plate/_plates-by-project', [
                        'model' => $model,
                        'samplesByPlate' => $samplesByPlate,
                        'array_parents' => $array_parents,
                        'projectName' => Yii::$app->session->get('Name'),
            ]);
        }
        
        return $this->renderPartial('new-style/_send-view', ['project' => $model]);
    }

    public function actionGetDispatchData($id) {
        $model = Project::findOne($id);

        $dispatch = \common\models\DispatchPlate::find()
                ->where(["IsActive" => 1, "ProjectId" => $model->ProjectId,])
                ->one();

        return $this->renderPartial('new-style/_sample-dispatch-view', ['dispatch' => $dispatch, "model" => $model]);
    }

    public function actionGetAssayData($id) {
        $model = Project::findOne($id);

        $shipment = $model->IsSent == 1 ? \common\models\ProjectGroups::getDataProjectGroupsByProjectId($id) : null;

        return $this->renderPartial('new-style/_assay-plates-data', ["model" => $model, "shipment" => $shipment]);
    }

    public function actionGetReportData($id) {
        $model = Project::findOne($id);


        return $this->renderAjax('new-style/_reports-view', ["model" => $model]);
    }

    /*
     * Save materials selected to FP project
     * @params $model Project Model
     * @return void
     */
    private function saveMaterialsToFp($model) {
        
        $sql = "INSERT INTO materials_by_project (Material_Test_Id, ProjectId, ParentTypeId, IsActive) VALUES ";
        
        // If textarea is not empty
        if ($model->MaterialsContainer != null) {
            foreach($model->MaterialsContainerHidden as $key => $material_id)
            {
                $sql .= "(" . $material_id . "," . $model->ProjectId . ", 3 , 1 ),";
            }
        }
        
        // If user have selected at least one material: value initial is 1 for white space on selector
        if (count($model->FpMaterials) > 1) {
            $materials = array_filter($model->FpMaterials);            
            //$fieldToSearch = 'Material_Test_Id';
            foreach($materials as $material_id)
            {
                $sql .= "(" . $material_id . "," . $model->ProjectId . ", 3 , 1 ),";
            }
        } 
        
        $sql = substr($sql, 0, -1) . ';';
        
        Yii::$app->db->createCommand($sql)->execute();
        
    }

    /*
     * Crate Barcode Sheet to send project
     * @params array $plates_array
     * $return string
     */
    private function createBarcodePdf($idProjects) {
        $plates_array = [];
        
        $plates_by_projects = \common\models\PlatesByProject::find()
                ->where(['IN' , 'ProjectId', $idProjects])
                ->andWhere(['IsActive' => 1])
                ->groupBy('PlateId')
                ->all();

        $plates_array = ArrayHelper::merge($plates_array, ArrayHelper::getColumn($plates_by_projects, 'PlateId'));
        

        $string .= $this->render("../plate/get-barcode", [
            'plates' => $plates_array,
        ]);

        $filename = 'C:/php/sheet_barcode_' . date('Y-m-d') . '.pdf';

        $pdf = new Pdf([

            //'mode' => Pdf::MODE_UTF8, 
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'filename' => $filename,
            'destination' => Pdf::DEST_FILE,
            'content' => $string,
            //'cssFile' => '@frontend/web/css/site2.css',
            'options' => ['title' => 'Barcode Sheet'],
            'methods' => [

                'SetFooter' => ['Advanta Seeds {PAGENO}'],
            ],
            'marginTop' => 7.50,
        ]);

        $pdf->render();

        return $filename;
    }

    public function actionExample2() {
        $idProjects = Array(90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105);
        $plates_array = [];
        
        $plates_by_projects = \common\models\PlatesByProject::find()
                    ->where(['IN' , 'ProjectId', $idProjects])
                    ->andWhere(['IsActive' => 1])
                    ->groupBy('PlateId')
                    ->all();
        
        $plates_array = ArrayHelper::merge($plates_array, ArrayHelper::getColumn($plates_by_projects, 'PlateId'));
        echo "<pre>";
        print_r($plates_array); exit;
        $plates_array = [];
        $idProjects = [0 => 1];

        foreach ($idProjects as $projectId) {
            $plates_by_projects = \common\models\PlatesByProject::find()
                    ->where(['ProjectId' => $projectId, "IsActive" => 1])
                    ->all();

            $plates_array = ArrayHelper::merge($plates_array, ArrayHelper::map($plates_by_projects, 'PlateId', 'PlateId'));
        }

        $html = '<div style="margin-top:50px;">'
                . '<h1> Barcode Sheet </h1>'
                . '</div>';


        foreach ($plates_array as $key => $plate) {
            $plate_formated = 'TP' . sprintf('%06d', $plate);
            $html .= '<div class="row" style="font-size:20px;">'
                    . '<div style="width:40%; padding:4px; border:1px solid #ccc; float:left; margin:5px">' . $plate_formated . '</div>'
                    . '<div style="width:40%; padding:2px; border:1px solid #ccc; margin:5px">'
                    . '<barcode code="' . $plate_formated . '" type="C39" size="1.5" height="0.5" />'
                    . '</div>'
                    . '</div>';
        }

        $pdf = new Pdf([

            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'filename' => 'C:/php/sdfsdf.pdf',
            'destination' => Pdf::DEST_FILE,
            'content' => $html,
            'cssFile' => '@frontend/web/css/site2.css',
            'options' => ['title' => 'Barcode Sheet'],
            'marginTop' => 7.50,
        ]);

        $pdf->render();
        //exit;*/
        return $this->render("../plate/get-barcode", [
                    'plates' => $plates_array,
        ]);
    }

    private function generateZip($files_name) {
        
       $zipname =  $this->createZipFile($files_name);

        $this->actionDownloadZip($zipname);
        
    }
    
    public function actionDownloadZip($zipname)
    {
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=grids_and_barcodes.zip');
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
        
    }
    
    private function createZipFile($files_name)
    {
        $zipname = 'C:/php/grids_and_barcodes.zip';
        $zip = new ZipArchive();
        $zip->open($zipname, ZipArchive::CREATE);

        foreach ($files_name as $key => $file) {
            $zip->addFromString(basename($file), file_get_contents($file));
        }

        $zip->close();

        foreach ($files_name as $key => $file) {

            unlink($file);
        }
        
        return $zipname;
    }

    //modified matias
    private function saveProjectUpdateAt($model=null)
    {
        if($model != null){
            $model->UpdateAt = date("Y/m/d");
            // var_dump($model->UpdateAt);
            // var_dump("hola"); exit();
            $model->save();
        }
        else
        {
            $project = $this->findModel($id);
            $project->UpdateAt = date("Y-m-d");
            $project->StepUpdateAt = date("Y-m-d");
            $project->save();
        }
    }
    //modified matias

    /*
     * Select instance of Project
     * return integer
     */
    public function actionSelectProjectInstance()
    {
        $this->layout = false;
        return $this->render('select-instance');
    }
    
    /*
     * 
     */
    public function actionSelectProject()
    {
        $searchModel = new ProjectSearch();

        /*if (Yii::$app->user->getIdentity()->itemName == "breeder")
            $searchModel->UserId = Yii::$app->user->id;
        */
        // pass ture by params to filter toArray option on search model
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $select_prject = true);
                
        $pDonnor = \common\models\MaterialsByProject::find()
                                                      //->with('materialTest')
                                                    ->innerJoin('`advanta.gdbms`.material_test', 'material_test.Material_Test_Id=materials_by_project.Material_Test_Id')
                                                    ->where(['material_test.IsActive' => 1, 'ParentTypeId' => 1])
                                                    ->groupBy('`materials_by_project`.Material_Test_Id')
                                                    ->orderBy(['Name' => SORT_ASC])
                                                    ->all();
        $pReceptor = \common\models\MaterialsByProject::find()
                                                    ->innerJoin('`advanta.gdbms`.material_test', 'material_test.Material_Test_Id=materials_by_project.Material_Test_Id')
                                                    ->where(['material_test.IsActive' => 1, 'ParentTypeId' => 2])
                                                    ->groupBy('`materials_by_project`.Material_Test_Id')
                                                    ->orderBy(['Name' => SORT_ASC])
                                                    ->all();
        
        return $this->render('select-project',[
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pDonnor' => $pDonnor,
                    'pReceptor' => $pReceptor,
                ]);
        
    }
    
    public function actionJobContinued($id)
    {
        $model = new Project;
        
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $this->fillModel($model);
            
            $projectsLinkage = new \common\models\ProjectsLinkage;
            $projectsLinkage->ProjectId = $model->ProjectId;
            $projectsLinkage->LinkedTo = $id;
            if(!$projectsLinkage->save())
            {
                print_r($projectsLinkage->getErrors()); exit;
            }
            
            Yii::$app->session->remove('Name');
            Yii::$app->session->set('Name', $model->Name);
            return $this->redirect(['grid-definition', 'ProjectId' => $model->ProjectId]);
                    
        }
        
        $jobContinued = $this->findModel($id);
        
        $model->GenerationId        = $jobContinued->GenerationId;
        $model->ResearchStationId   = $jobContinued->ResearchStationId;
        $model->Crop_Id             = $jobContinued->Crop_Id;
        $model->ProjectTypeId       = $jobContinued->ProjectTypeId;
        
        $materials = \common\models\MaterialsByProject::find()
                    ->where(["ProjectId" => $id])
                    ->all();

        if (count($materials) > 2) {
            foreach ($materials as $m) {
                $arrayMaterialsId[] = $m->Material_Test_Id;
                //$arrayMaterialsName[] = $m->materialTest->Name;
            }
            $model->FpMaterials = $arrayMaterialsId;
            //$model->MaterialsContainerHidden = implode(',', $arrayMaterialsId);
        } else {
            $array_materials = null;
            foreach ($materials as $m) {
                if ($m->ParentTypeId == 1)
                    $model->Parent1_donnor = $m->Material_Test_Id;
                else
                    $model->Parent2_receptor = $m->Material_Test_Id;
            }
            $model->traits_by_parent1 = \common\models\TraitsByMaterials::getTraitsByParent($jobContinued->ProjectId, ParentType::POLLEN_DONNOR);
            $model->traits_by_parent2 = \common\models\TraitsByMaterials::getTraitsByParent($jobContinued->ProjectId, ParentType::POLLEN_RECEPTOR);
        }
        
        //print_r($model->traits_by_parent2); exit;
                
        if (Yii::$app->user->getIdentity()->itemName == "breeder")
                $model->UserId = Yii::$app->user->id;
            
        return $this->render('create', [
                        'model' => $model,
        ]);
        
    }
    
    public function fillModel($model)
    {
            $model->IsActive = 1;
            $model->Name = $this->createName($model);
            
            //Do not generate another porjectcode when editing
            if(Yii::$app->controller->action->id != 'update')
            {
                $model->ProjectCode = $this->createCode($model);
                $model->StepProjectId = StepProject::PROJECT_DEFINITION;
                $model->Date = date("Y-m-d");
                $model->IsSent = 0;
            }

            //modified Matias
            $model->UpdateAt = date("Y-m-d");
            $model->StepUpdateAt = date("Y-m-d");
            //END modified Matias

            if (!$model->save()) {
                print_r($model->getErrors());
                exit;
            }
            
            //remove old materials
            if(Yii::$app->controller->action->id == 'update')
                $model->deleteMaterialsByProject();
            
            // Save materilas if is FingerPrint
            switch ($model->ProjectTypeId) {
                case Project::FINGERPRINT:
                    //$this->saveMaterialsByMatrix($model);
                    $this->saveMaterialsToFp($model);
                    break;
                default:
                    //$this->saveMaterialsBy2Parents($model);
                    $this->saveMaterialsAndTraitsBy2Parents($model);
                    break;
            }

            //Is there are more users
            if ($model->UserByProject != null) {
                $this->saveMoreUsersByProject($model);
            }         

    }
    
    public function actionGetProjects()
    {

        $sql = "SELECT p.ProjectId, p.Name, p.ProjectCode, p.Priority,u.Username as User, DATEDIFF(p.FloweringExpectedDate,now()) as DayRemaingToFlowering, p.Date,p.StepUpdateAt, p.UpdateAt, s.Name as Step, p.IsSent FROM project p
                INNER JOIN `advanta.gdbms`.user u on u.UserId = p.UserId
                INNER JOIN step_project s on s.StepProjectId = p.StepProjectId
                WHERE p.IsActive = 1";
        if (Yii::$app->user->getIdentity()->itemName == "breeder")
            $sql .= " and p.UserId = ".Yii::$app->user->id;
        
        $sql .= " group by p.ProjectId order by p.ProjectId DESC";
        
        $projects = Yii::$app->db->createCommand($sql)->queryAll();
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $projects;
    }
    
    private function saveProjectsGroupeds($idProjects)
    {
        $group = new \common\models\ProjectGroups();
        $group->CreationDate = date("Y-m-d");
        $group->IsActive = 1;
        $group->save();

        foreach ($idProjects as $key => $val)   {
            $group_by_projects = new \common\models\ProjectGroupsByProject();
            $group_by_projects->ProjectGroupsId = $group->ProjectGroupsId;
            $group_by_projects->ProjectId = $val;
            $group_by_projects->save();
        }
    }
}
