<?php

namespace frontend\controllers;

use Yii;
use common\models\Traits;
use common\models\TraitsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TraitsController implements the CRUD actions for Traits model.
 */
class TraitsController extends Controller
{
    public $enableCsrfValidation = false;
    
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
        ];
    }

    /**
     * Lists all Traits models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TraitsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Traits model.
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
     * Creates a new Traits model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() 
    {
        $this->layout = false;
        $model = new Traits();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->IsActive = 1;
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                if(!$model->save()){print_r($model->getErrors());};
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
     * Updates an existing Traits model.
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
     * Deletes an existing Traits model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) 
    {
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
        
        $projectsEnvolved = \common\models\TraitsByMaterials::find()
                                ->where(['TraitsId' => $model->TraitsId, 'IsActive' => 1])
                                ->count();

        return $this->renderAjax('delete', ['model' => $model, 'projectsEnvolved' => $projectsEnvolved]);
    }

    /**
     * Finds the Traits model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Traits the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Traits::findOne($id)) !== null) {
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
    
    public function actionTraitsByParent()
    {
        $array_trait = [];
        if(isset(Yii::$app->request->post()['depdrop_parents']))
        {
            $cropId = Yii::$app->request->post()['depdrop_parents'][0];
            //$materialTestId = Yii::$app->request->post()['depdrop_parents'][1];
            
            $sql = "SELECT TraitsId, Name FROM traits WHERE IsActive=1 and Crop_Id=".$cropId;
            $sql .= " ORDER BY Name ASC";
            $traits = Yii::$app->db->createCommand($sql)->queryAll();
            if($traits)
            {
                foreach($traits as $trait)
                {
                    $array_trait[] = ["name" => $trait['Name'], "id" => $trait['TraitsId']];
                }
            }
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ["output" => $array_trait];
            
        }
    }
    
    /*
     * View to asociate traits_materials with markers_by_projects
     * return mixed
     * 
     */
       
    public function actionSelectTraitsByMarkers()
    {
        if (isset(Yii::$app->request->queryParams['ProjectId']))
        {
           $project = \common\models\Project::findOne(Yii::$app->request->queryParams['ProjectId']);
           $array_mkByProjectId = $this->getMarkersselectedDetails(Yii::$app->request->queryParams['mkByProjectId']);
           $traits = \common\models\TraitsByMaterials::find()
                   ->join('inner join','materials_by_project', 'MaterialsByProject = MaterialsByProjectId')
                   ->join('inner join','traits', 'traits.TraitsId = traits_by_materials.TraitsId')
                   ->where(["materials_by_project.ProjectId" => $project->ProjectId])
                   ->groupBy("TraitsId")
                   ->orderBy(["traits.Name" => SORT_ASC])
                   //->asArray()
                   ->all();
       
           return $this->render('../project/resources/__select-traits-by-markers', [
                    'model' => $project,
                    'traits' => $traits,
                    'markers' => $array_mkByProjectId,
                    'projectName' => Yii::$app->session->get('Name'),
                    'update' => isset(Yii::$app->request->queryParams['update']) ? 1 : 0,
        ]);
        }
        
        if(Yii::$app->request->post())
        {
            $traitsBymarkers = Yii::$app->request->post()['Project']['Trait'];
            
            foreach($traitsBymarkers as $markersByProjectId => $traitId)
            {
                //foreach($array_traits as $traitId)
                //{
                    $traitsBymarkersByProject = new \common\models\TraitsByMarkersByProject();
                    $traitsBymarkersByProject->MarkersByProjectId = $markersByProjectId;
                    $traitsBymarkersByProject->TraitsByMaterialId = $traitId;
                    
                    $traitsBymarkersByProject->save();
                    unset($traitsBymarkersByProject);
                    //echo $markersByProjectId."-".$traitId."<br>";
                //}
            }
            //exit;
            $projectId = Yii::$app->request->post()['Project']['ProjectId'];
            
            if(isset(Yii::$app->request->post()['update']))
            {
                return $this->redirect(['project/view', 'id' => $projectId]);
            }
            else
                return $this->redirect(['project-preview', "ProjectId" => $projectId]);
                //return $this->redirect(['project/grid-definition', 'ProjectId' => $projectId]); //actionGridDefinition($project->ProjectId);
        }
    }
    
    //Edited with snp_labs 08-09-16
    private function getMarkersSelectedDetails($markersByProjectIds)
    {
        $sql1 = "SELECT MarkersByProjectId, s.Snp_lab_Id, s.LabName FROM markers_by_project mp "
                . " INNER JOIN `advanta.gdbms`.snp_lab s ON s.Snp_lab_Id = mp.Snp_lab_Id"
                ." where mp.MarkersByProjectId in (".implode(',', $markersByProjectIds).") "
                . "ORDER BY MarkersByProjectId"; 
        //print_r($markersByProjectIds);
        $markers = Yii::$app->db->createCommand($sql1)->queryAll();
        
        return $markers; exit;
    }
    
    public function actionGetTraitsByKendo($idProject)
    {
        $traitsByMarkers = new \common\models\TraitsByMarkersByProject();
        
        $traits = $traitsByMarkers->getTraitsBybProjectByKendo($idProject);
        
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $traits;
    }
    
    public function actionControl()
    {
        print_r(Yii::$app->request->queryParams); exit;
    }
    
    public function actionExampleTrait()
    {
        return $this->redirect(['project/project-preview', "ProjectId" => 1]);
    }
}
