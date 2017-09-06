<?php

namespace frontend\controllers;

use Yii;
use common\models\Report;
use common\models\ReportSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Report models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Report model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Report model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Report();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ReportId]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Report model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ReportId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Report model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionUploadReport()
    {
        //$project = \common\models\Project::findOne($projectId);
        $this->layout = false;
        
        $model = new Report();
        
        $model->ProjectId = Yii::$app->request->queryParams['ProjectId'];
        
        if (Yii::$app->request->isPost) 
        {    
            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            
            if ($model->file && $model->validate()) 
            {
                $fileSavePath = 'uploads/Reports/'.$model->ProjectId.'/';
                $fileName = $fileSavePath . $model->file->baseName .'.'. $model->file->extension;
                
                $reportExist = Report::find()->where(["Url" => $fileName, "IsActive" => 1])->one();
                
                if($reportExist)
                {
                    return "This file has been previously loaded <br>";
                }
                /* Upload file in project */
                if (!file_exists ($fileSavePath))
                    if(!mkdir ($fileSavePath, 0777, true))
                       die('error');
                
                if ($model->file->saveAs($fileName)) 
                {
                    $model->Url = $fileName;
                    $model->Date = date('Y-m-d H:i:s');
                    $model->IsActive = 1;
                    $model->save();
                        
                    switch($model->ReportTypeId)
                    {
                       case Report::ROW_DATA :
                           $rawData = new \common\models\RawData();
                           $error = $rawData->saveRawData($fileName, $model);
                           break;

                       case Report::DOUBLE_ENTRY_TABLE:
                           $error = \common\models\DoubleEntryTable::saveDoubleEntryTable($fileName, $model);
                           break;
                       
                       case Report::DOUBLE_ENTRY_TABLE_PROCESED:
                           $error = null;
                           break;

                       case Report::REPORT:
                           //$error = Report::saveReport($fileName);
                           $error = null;
                           break;
                       default:
                           $error = null;
                    }
                    /* If exist any error-, return it and back to project view */
                    if($error)
                    {
                        try {
                                
                                unlink($fileName);
                                
                            } catch(Exception $e) { 
                                $error .= "<br> the file cant be deleted"; 
                                //or even leaving it empty so nothing is displayed
                            } 
                        $result = $model->deleteLogic();
                        /*unused variable $result, only used to host the return value*/
                        return $error;
                    }else
                    
                    /* Save data report for project */
                    //$fileName = $model->file->baseName . '.' . $model->file->extension;
                    
                    {
                        $project = \common\models\Project::findOne($model->ProjectId);
                        $project->StepProjectId = \common\models\StepProject::REPORT;
                        //modified Matias
                        $project->UpdateAt = date("Y-m-d");
                        $project->StepUpdateAt = date("Y-m-d");
                        //END modified Matias
                        $project->save();
                        return 'ok';
                    }
                    
                }else {
                    print_r("File can't upload");
                    exit;
                }
            }else
            {
                echo "Fail: Please check the input data "; exit;
            }
            
        }
        if(isset(Yii::$app->request->queryParams['ReportId']))
            $model->ReportTypeId = Yii::$app->request->queryParams['ReportId'];
        return $this->renderAjax('_form',  ['model' => $model]);
    }
  
    public function actionGetReportTypes()
    {
        $types = \common\models\ReportType::find()->where(["IsActive"=>1])->asArray()->all();
        
        foreach($types as $report)
        {
            $reports[] = ["name" => $report['Name'], 'id'=>$report['ReportTypeId']];
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $reports;
        
    }
    
    public function actionDeleteReport()
    {
        $projectId = Yii::$app->request->post()['projectId'];
        $id = Yii::$app->request->post()['id'];
        $url = Yii::$app->request->post()['url'];
        $url_clean = substr($url, 1 );
        $result = Report::deleteById($id, $projectId, $url_clean);
        
        return $result;
    }
    
    public function actionGetReportData()
    {
        
        $projectId =Yii::$app->request->queryParams['id'];
        $report = \common\models\Report::getReportByProjectId($projectId); 
        return $report;
    }
}
