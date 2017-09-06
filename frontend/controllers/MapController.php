<?php

namespace frontend\controllers;

use Yii;
use common\models\Map;
use common\models\MapType;
use common\models\MapSearch;
use common\models\MapResult;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Crop;
use yii\data\ActiveDataProvider;
use  yii\db\Query;
use yii\db\ActiveQuery;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use yii\filters\AccessControl;

/**
 * MapController implements the CRUD actions for Map model.
 */
class MapController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::className(),
                /*'actions' => [
                    'delete' => ['post'],
                ],*/
            ],
        ];
    }

    /**
     * Lists all Map models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->getIdentity()->itemName != "admin")
            return $this->redirect( Yii::$app->homeUrl);
        
        $searchModel = new MapSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel->Crop_Id = Yii::$app->session['cropId'];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Map model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionViewAjax($id)
    {
         $this->layout = false;
         
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
        
//        $this->layout = false;
//        
//        $searchModel = new MapResult();
//        
//        $query = $searchModel::find();
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//        $query->andFilterWhere([
//            'Map_Id' => $id,
//           
//        ]);
//                
//        //print_r($dataProvider); exit;
//        return $this->renderAjax('view', [
//            'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'model'=> $this->findModel($id),
//        ]);
    }

    /**
     * Creates a new Map model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Map();
        
        $crop = Crop::find()->where(["IsActive"=>1])->all();
    
        $this->layout = false;
        
        if (Yii::$app->request->isAjax  && $model->load(Yii::$app->request->post())) 
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            $model->IsActive = 1;
            if($isSubmit)
            {
                $model->save();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('view', ['model' => $model,]);
            }else{
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
            
        }else
        {
            return $this->renderAjax('create', [
                'model' => $model, 'crop' => $crop
            ]);
        }      
        
    }
    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
         $crop = Crop::find()->where(["IsActive"=>1])->all();
        $this->layout = false;
         
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->getRemoveConsensusById($id);
            $model->update();
            
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->renderAjax('view', ['model' => $model]);
        } else {
            return $this->renderAjax('update', [
                'model' => $model, 'crop' => $crop
            ]);
        }
    }
    
    /**
     * Deletes an existing Map model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax)
        {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if($isSubmit)
            {                  
                $model->deleteLogic();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', [ 'ok' => true]);
            }
        
        }
        return $this->renderAjax('delete', ['model'=>$model]);
    }

    /**
     * Finds the Map model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Map the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Map::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionMapsTypes()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) 
        {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) 
            {
                $crop_id = $parents[0];
                
                $mType = new MapType();
                $maps=  $mType->getMapsTypeByCrop($crop_id);
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['output' => $maps];
            }
        }
    }
    
    public function actionMapsAvailables()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) 
        {
            //print_r($_POST['depdrop_parents']); exit;
            $parents = $_POST['depdrop_parents'];
            
            if ($parents != null) 
            {
                $mapType = $parents[0];
                $CropID  = $parents[1];
                $map = new Map();
                $map_aviliable=  $map->getMapsAvilablesByType($mapType, $CropID);
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['output' => $map_aviliable];
            }
        }
    }
    
    public function actionExcel()
    {
        $model = $this->findModel(Yii::$app->request->queryParams['id']);
        $sTitle = $model->Name;
        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();
        $oExcel->getProperties()->setTitle($sTitle);

        //HEADERS
        $charCol = 65;
        $row = 1;

        $vHeaders = array("MarkerID","Linkage_Group","Position","Mapped_Population", "Mapping_Team");
        
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));

        $row++;
        
        if ($model)
        {
            foreach ($model->mapResults as $op)
            {
                    $charCol = 65;
                    //print_r($op['snpLabs']); exit;

                    $oSheet->SetCellValue(chr($charCol++).$row, $op->marker->Name);
                    $oSheet->SetCellValue(chr($charCol++).$row, $op->LinkageGroup);
                    $oSheet->SetCellValue(chr($charCol++).$row, $op->Position);
                    $oSheet->SetCellValue(chr($charCol++).$row, $op->MappedPopulation);
                    $oSheet->SetCellValue(chr($charCol++).$row, $op->MappingTeam);

                    $row++;
            }
    
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    public function actionDownloadTemplate()
    {
       
       $sTitle = "Map Template";
        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();
        $oExcel->getProperties()->setTitle($sTitle);

        //HEADERS
        $charCol = 65;
        $row = 1;

        $vHeaders = array("MarkerID","Linkage_Group","Position","Mapped_Population", "Mapping_Team");
        
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));

        $row++;
        
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output'); 
    }
    
    public function actionExportErrors()
    {
        $sTitle = "Failed Markers";
        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();
        $oExcel->getProperties()->setTitle($sTitle);

        //HEADERS
        $charCol = 65;
        $row = 1;

        $vHeaders = array("MarkerID");
        
        foreach ($vHeaders as $key => $lbl){
                $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                $oSheet->SetCellValue(chr($charCol++).$row, $lbl);
        }
        $oSheet->getStyle(chr(65)."$row:".chr($charCol-1)."$row")->applyFromArray(
                array('fill' 	=> array(	'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                                'color'		=> array('rgb' => 'FFFF00')
                                        )
        ));
        //print_r(Yii::$app->request->post()); exit;
        $row++;
        $mfail = Yii::$app->request->post();
        if(is_array($mfail))
        {   
           
            $fails = explode('#-#', $mfail['fails']);
            
            foreach ($fails as $val => $key)
            {
                $charCol = 65;
                $oSheet->SetCellValue(chr($charCol++).$row, $key);
                $row++;
            }
                  
        }else
        {
            echo "Could not perform the requested action.";
            exit;
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output'); 
    }
}
