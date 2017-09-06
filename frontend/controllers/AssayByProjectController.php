<?php

namespace frontend\controllers;

use Yii;
use common\models\AssayByProject;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\UploadFile;
use yii\web\UploadedFile;
use common\models\Project;

/**
 * AssayByProjectController implements the CRUD actions for AssayByProject model.
 */
class AssayByProjectController extends Controller
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
     * Lists all AssayByProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AssayByProject::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AssayByProject model.
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
     * Creates a new AssayByProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AssayByProject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->AssayByProjectId]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AssayByProject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->AssayByProjectId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AssayByProject model.
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
     * Finds the AssayByProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AssayByProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AssayByProject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionUploadAssay() 
    {
        $this->layout = false;
        $model = new UploadFile();
        
        $model->ProjectId = Yii::$app->request->queryParams['ProjectId'];
        //print_r(Yii::$app->request->params); exit;
        if (Yii::$app->request->isPost) 
        {    
            $model->load(Yii::$app->request->post());
            
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) 
            {
                $fileSavePath = 'uploads/'.$model->ProjectId."_". $model->file->baseName .'_'.date('d-m-Y h-m-s'). '.' . $model->file->extension;
                
                if($model->file->saveAs($fileSavePath)) {
                    
                    //$fileName = $model->file->baseName . '.' . $model->file->extension;
                    $file = $fileSavePath;
                        
                    $asayByProject = new AssayByProject();
                       
                    $errors =  $asayByProject->checkFile($file);
                    
                    if($errors != null)
                    {
                        unlink($file);
                        $listErrors = $this->makeListErrors($errors);
                        return $listErrors;
                    }else
                    {
                        $barcode = $asayByProject->getBarcodeFromAssay($file);
                        $asayByProject->saveAssay($model ,$file, $barcode);
                        \common\models\Project::setStepProject($model->ProjectId);
                        $this->saveUpdateDate($model->ProjectId);
                        
                        return 'ok';
                    }
                    
                    //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    //return "salió";
                }else {
                    print_r("no se puede subir el archivo");
                    exit;
                }
            }  
        }
        return $this->renderAjax('_form',  ['model' => $model]);
    }
    
    private function makeListErrors($error)
    {
        foreach($error as $key=> $val)
            {
                echo "<li>".$val."</li>";
            }
    }
    
    //modified matias
    private function saveUpdateDate($projectId)
    {
        $model = Project::findOne($projectId);
        $model->UpdateAt = date("Y-d-m");
        $model->StepUpdateAt = date("Y-d-m");
        $model->save();
    }
    //modified matias
    
}
