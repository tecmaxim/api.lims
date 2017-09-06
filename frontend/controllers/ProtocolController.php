<?php

namespace frontend\controllers;

use Yii;
use common\models\Protocol;
use common\models\ProtocolSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProtocolController implements the CRUD actions for Protocol model.
 */
class ProtocolController extends Controller
{
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
     * Lists all Protocol models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProtocolSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Protocol model.
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
     * Creates a new Protocol model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddProtocol()
    {
        $model = new Protocol();
        //$project = \common\models\Project::findOne($projectId);
        $this->layout = false;
        
        $model->ProjectId = Yii::$app->request->queryParams['ProjectId'];
        
        if (Yii::$app->request->isPost) 
        {    
            $model->load(Yii::$app->request->post());
            //$model->ProtocolFile = UploadedFile::getInstance($model, 'file');
            
            if ($model->validate()) 
            {
                /*if($model->ProtocolFile)
                {
                    $fileSavePath = 'uploads/Reports/'.$model->ProjectId.'/';
                    $fileName = $fileSavePath . $model->ProtocolFile->baseName .'.'. $model->ProtocolFile->extension;
                                    
                    /* Upload file in project 
                    if (!file_exists ($fileSavePath))
                        if(!mkdir ($fileSavePath, 0777, true))
                           die('error on dir');
                
                    // Save the pdf
                    $model->ProtocolFile->saveAs($fileName); 
                }*/
                
                $model->IsActive = 1;
                if($model->save())
                {
                    
                    $protocolByproject = new \common\models\ProtocolByProject();
                    $protocolByproject->ProjectId = $model->ProjectId;
                    $protocolByproject->ProtocolId = $model->ProtocolId;
                    if(!$protocolByproject->save())
                    {
                        print_r($protocolByproject->getErrors()); exit;
                    }

                    echo 'ok';
                    die();
                }
            }else
            {
                $stringError = "";
                foreach($model->getErrors() as $error)
                {
                    $stringError .= $error[0];
                }
                return $stringError;
            }
        }
        if(isset(Yii::$app->request->queryParams['ReportId']))
            $model->ReportTypeId = Yii::$app->request->queryParams['ReportId'];
        return $this->renderAjax('_form',  ['model' => $model]);
    }

    /**
     * Updates an existing Protocol model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->layout = false;

        if (Yii::$app->request->isPost) 
        {    
            $model->load(Yii::$app->request->post());
            
            if ($model->validate()) 
            {
                //$model->IsActive = 1;
                $model->save();
                
                echo 'ok';
                die();
            }else
            {
                print_r($model->getErrors()); return false;
            }
        }
        
        return $this->renderAjax('_form',  ['model' => $model]);
    }

    /**
     * Deletes an existing Protocol model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Protocol model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Protocol the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Protocol::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetProtocolsData()
    {
        $projectId =Yii::$app->request->queryParams['id'];
        $protocol = \common\models\Protocol::getProtocolsByProjectId($projectId); 
        return $protocol;
    }
    
    public function actionDeleteProtocol()
    {
        $id = Yii::$app->request->post()['protocolId'];
        $projectId = Yii::$app->request->post()['projectId'];
                
        $protocolByProject = \common\models\ProtocolByProject::find()
                                            ->where(["ProtocolId" => $id])
                                            ->count();
                                            //->all();
            
        if($protocolByProject > 1)
        {
            $protocolByProject = \common\models\ProtocolByProject::find()
                                            ->where(["ProtocolId" => $id, "ProjectId" => $projectId])
                                            ->one();
            $protocolByProject->delete();
            
            echo "alert";
        }else
        {
            $protocolByProject = \common\models\ProtocolByProject::find()
                                            ->where(["ProtocolId" => $id])
                                            ->one();
            $protocolByProject->delete();
            $this->findModel($id)->deleteLogic();
        }
    }
    
}
