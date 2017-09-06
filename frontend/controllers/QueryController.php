<?php

namespace frontend\controllers;

use Yii;
use common\models\SnpLab;
use common\models\Marker;
use common\models\Map;
use common\models\SnpLabSearch;
use common\models\Cropbyuser;
use common\models\MapTypeByCrop;
use common\models\MarkerType;
use common\models\FingerprintMaterial;
use common\models\FingerprintSearch;
use common\models\Fingerprint;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use frontend\models\UploadFile;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

class QueryController extends Controller 
{

    public $enableCsrfValidation = false;

    public function behaviors() {
        // Yii::$app->session->removeAll();
        Yii::$app->session->remove("material1");
        Yii::$app->session->remove("material2");
        Yii::$app->session->remove("map");
        $behavior = [
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
        return $behavior;
    }

    /**
     * @return mixed
     */
    public function actionQuery1($snps_checked = NULL) {
        
        /*AUXILIARS PARAMTERES TO RENDER*****/
        $dataProvider = "";
        $limits = null;
        $mapTypes = "";
        $lastChromosme = '';
        $isSearchConsensus = '';
        /***********************************/
        $searchModel = new SnpLabSearch();
        $searchModel->IsActive = 1;
        $searchModel->scenario = 'query1';
        
        $itemsValidated = \common\models\ValidatedStatus::findAll(['IsActive'=>1]);
        
        $sessionCropId = Yii::$app->session->get('cropId');
        if ($sessionCropId != null)
            $searchModel->Crop = $sessionCropId;

        $hasMap = Map::find()->where(["IsActive" => 1, "Crop_Id" => $sessionCropId])->one();
        $methodData = $this->captureMethod(Yii::$app->request);
        
        if ($searchModel->load($methodData) and $searchModel->validate()) 
        {
            if ($searchModel->MarkerType == Marker::SNPLAB)// SnpLab
            {
                if(Map::isConsensus($searchModel->Map))
                {
                    $dataProvider = $searchModel->searchByConsensus($methodData);
                    $isSearchConsensus = true;
                }else
                    $dataProvider = $searchModel->search($methodData);
            }
            else
                $dataProvider = $searchModel->searchMarker($methodData);

            if ($searchModel->Map != "") {
                $map = Map::findOne($searchModel->Map);
                Yii::$app->session->set("map", $map->mapResults[0]->MappedPopulation);
            }

            if ($searchModel->MapTypeId != "") {
                $mapTypes = MapTypeByCrop::find()
                        ->where(["IsActive" => 1, "Crop_Id" => $searchModel->Crop])
                        ->all();
            }
            if ($dataProvider)
                $dataProvider->setPagination(["pageSize" => $methodData == null ? 100 : $methodData['SnpLabSearch']['Pagination']]);

            $limits = $searchModel->getLimitsXcM($methodData);
            
            if(is_array($limits))
            {
                $aux = end($limits);
                $lastChromosme = $aux['LinkageGroup']; 
            }
        }

        //if(isset(Yii::$app->request->queryParams['SnpLabSearch']['hiddenField']))
        if (isset(Yii::$app->request->queryParams['method']) || isset(Yii::$app->request->queryParams['SnpLabSearch']['hiddenField'])) 
        {
            if (isset(Yii::$app->request->queryParams['projectId']))
                Yii::$app->session->set("projectId", Yii::$app->request->queryParams['projectId']);

            return $this->render('/project/_render-querys', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'selectedDataProvider' => $snps_checked,
                        'limitsXcM' => $limits,
                        'lastChromosme' => $lastChromosme,
                        'hasMap' => $hasMap,
                        'mapTypes' => $mapTypes,
                        'itemsValidated' => $itemsValidated,
                        'isSearchConsensus' =>$isSearchConsensus,
                        'BackToLims' => 1,
                        'query_selected' => 1,
                        'update' => Yii::$app->request->queryParams['update'],
                        'projectName' => Yii::$app->session->get('Name'),
            ]);
        }
        
//        return $this->render('query1', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//                    'selectedDataProvider' => $snps_checked,
//                    'limitsXcM' => $limits,
//                    'lastChromosme' => $lastChromosme,
//                    'hasMap' => $hasMap,
//                    'mapTypes' => $mapTypes,
//                    'itemsValidated' => $itemsValidated,
//                    'isSearchConsensus' =>$isSearchConsensus,
//                    'update' => Yii::$app->request->queryParams['update']
//        ]);
    }
    /**
     * @return mixed
     */
    public function actionQuery2($snps_checked = NULL) {
        /* Auxiliars paramteres to render ***/
        $projectId= Yii::$app->request->queryParams['projectId'];
        $fp_material1 =  "";
        $fp_material2 = "";
        $dataProvider = "";
        $limitsXcM = null;
        $mapsType = "";
        $hasFP = "";
        $isSearchConsensus = "";
        $dataMaterials= "";
        /************************/
        
        $Fingerprint = new FingerprintSearch();
        $Fingerprint->scenario = 'query2';
        $Fingerprint->IsActive = 1;

        $sessionCropId = Yii::$app->session->get('cropId');
        if ($sessionCropId != null)
        {
            $Fingerprint->Crop = $sessionCropId;
            //$hasFP = Fingerprint::find()->where(["IsActive" => 1, "Crop_Id" => $sessionCropId])->one();
        }
        
        $hasMap = Map::find()->where(["IsActive" => 1, "Crop_Id" => $sessionCropId])->one();
        
        if(($parents = \common\models\Project::getParentsByProject($projectId)) != null)
        {
            $dataMaterials = $Fingerprint->getMaterialsInfoByParentesSelected($sessionCropId);
            $Fingerprint->Fingerprint_Material_Id = $parents[0]['Material_Test_Id'];
            $Fingerprint->Fingerprint_Material_Id2 = $parents[1]["Material_Test_Id"];
        }   
       
        if ($Fingerprint->load(Yii::$app->request->queryParams) and $Fingerprint->validate()) 
        {

            if(Map::isConsensus($Fingerprint->Map))
                $isSearchConsensus = true;
            
            $dataProvider = $Fingerprint->get_polymorfic(Yii::$app->request->queryParams);
            
            
            $fp_material1 = $this->actionDataMaterialsByFpMat_Id($Fingerprint->radio1);
            $fp_material2 = $this->actionDataMaterialsByFpMat_Id($Fingerprint->radio2);

            Yii::$app->session->set("material1", $fp_material1['MaterialName'] . " ( " . $fp_material1['TissueOrigin'] . " )");
            Yii::$app->session->set("material2", $fp_material2['MaterialName'] . " ( " . $fp_material2['TissueOrigin'] . " )");
            if ($Fingerprint->Map != "") {
                $map = Map::findOne(["Map_Id" => $Fingerprint->Map]);
                Yii::$app->session->set("map", $map->Name);
            }

            $snpLab = new SnpLabSearch;
            $limitsXcM = $snpLab->getLimitsXcM($Fingerprint->Crop);

            $mapsType = MapTypeByCrop::find()
                    ->where(["IsActive" => 1, "Crop_Id" => $Fingerprint->Crop])
                    ->all();
        }

        // $materials = Fingerprint::getMateriasResult();
        if (isset(Yii::$app->request->queryParams['method']) || isset(Yii::$app->request->queryParams['FingerprintSearch']['hiddenField'])) 
        {
            if (isset(Yii::$app->request->queryParams['projectId']))
                Yii::$app->session->set("projectId", Yii::$app->request->queryParams['projectId']);
            
            return $this->render('/project/_render-querys', [
                        'dataProvider' => $dataProvider, //polymorfism result
                        'fp_material1' => $fp_material1, // radiobuton FP_material_id's calculated in the call ajax
                        'fp_material2' => $fp_material2, // radiobuton FP_material_id's calculated in the call ajax
                        'Fingerprint' => $Fingerprint, // model
                        //'materials'            => $materials,   // List of materials to select
                        'totalCompared' => $Fingerprint->getTotalCompared(Yii::$app->request->queryParams),
                        'limitsXcM' => $limitsXcM, // limits to grapfhic
                        'both' => $Fingerprint->Method == 3 ? true : false,
                        'hasFP' => "",
                        'hasMap' => $hasMap,
                        'mapsType' => $mapsType,
                        'isSearchConsensus' =>$isSearchConsensus,
                        'BackToLims' => 1,
                        'query_selected' => 2,
                        'update' => Yii::$app->request->queryParams['update'],
                        'projectName' => Yii::$app->session->get('Name'),
                        'dataMaterials' => $dataMaterials,
            ]);
        }
    }

    public function actionUpload_file() {

        $model = new UploadFile();
        //$model->type = 3;
        //    $have_snp = Marker::find()->exists();
        //    $have_snp_lab = SnpLab::find()->exists();

        $crop = Cropbyuser::getCropsByUser();
        $sessionCropId = Yii::$app->session->get('cropId');
        if ($sessionCropId != null)
            $model->crop = $sessionCropId;
        $marker_type = MarkerType::find()
                ->where(["IsActive" => 1])
                ->all();

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            //print_r($model->file); exit;
            if ($model->file && $model->validate()) {

                if ($model->file->saveAs('uploads/' . $model->file->baseName . '.' . $model->file->extension)) {
                    //$gets = Yii::$app->request->queryParams;
                    $fileName = $model->file->baseName . '.' . $model->file->extension;
                    $file = 'uploads/' . $fileName;

                    switch ($model->type) { //fingerprint
                        case 1:
                            $fingerprint = new Fingerprint();
                            if ($model->test == null)
                                $result = $fingerprint->import_file($file, null, $model->crop);
                            else {
                                $result = $fingerprint->import_file($file, $model->test, $model->crop);
                            }
                            unlink($file);
                            return $this->render('select_import', ['model' => $model, 'crop' => $crop, 'marker_type' => $marker_type, 'result_fp' => $result]);
                            break;
                        case 2:
                            $snpLab = new SnpLab();
                            $result = $snpLab->import_snplab($file, $model->crop);
                            //unlink(Yii::$app->homeUrl.$file);
                            return $this->render('select_import', ['model' => $model, 'crop' => $crop, 'marker_type' => $marker_type, "result_snp_lab" => $result]);
                            break;
                        case 3:
                            $marker = new Marker();
                            $result = $marker->import_marker($file, $model->crop, $model->marker_type);
                            //unlink(Yii::$app->homeUrl.$file);
                            return $this->render('select_import', ['model' => $model, 'crop' => $crop, 'marker_type' => $marker_type, "result_marker" => $result]);
                            break;
                        case 4:

                            $map = new Map();
                            $result = $map->importMap($file, $model->crop, $model->map_type, $model->mType, $model->IsCurrent);
                            return $this->render('select_import', ['model' => $model, 'crop' => $crop, 'marker_type' => $marker_type, "result_map" => $result]);
                    }
                    //return $this->render('select_import',['model' => $model, 'crop' =>$crop, 'marker_type' =>$marker_type,"result_snp"=>$result ] );
                } else {
                    print_r("no se puede subir el archivo");
                    exit;
                }
            } else {
                print_r("SERVER Error - COD #409");
                exit;
            }
        }

        return $this->render('select_import', ['model' => $model, 'crop' => $crop, 'marker_type' => $marker_type, /* 'have_snp' => $have_snp, 'have_snp_lab' =>true */]);
    }

    public function actionSearchBySelect($poly = NULL) {

        $Snps_checked = SnpLabSearch::searchByIds(\Yii::$app->request->queryParams['vCheck']);
        //print_r($Snps_checked); exit;
        if (isset(\Yii::$app->request->queryParams['SnpLabSearch']))
            return $this->actionQuery1($Snps_checked);
        else {
            //$FPSearch['FingerprintSearch'] = Yii::$app->request->queryParams['FingerprintSearch'];
            $Snps_checked = Marker::searchByIdsMarkers(\Yii::$app->request->queryParams['vCheck']);

            return $this->actionQuery2($Snps_checked);
        }
    }

    public function actionExcel() {
        $model = new SnpLabSearch();
        $model->IsActive = 1;

        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Markers Selection " . date("d-h-Y");

        $oExcel->getProperties()->setTitle("$sTitle");

        //HEADERS
        $charCol = 65;
        $row = 1;

        $vSnpCheck = "";
        if (isset(Yii::$app->request->queryParams['vCheck']))
            $vSnpCheck = $model->searchByIds(Yii::$app->request->queryParams['vCheck'])->getModels();
        else
            $vSnp = $model->search(Yii::$app->request->queryParams)->getModels();

        //search()->getData();
        if (is_array($vSnpCheck)) {
            if (Yii::$app->request->queryParams["id"] == 1) { //Purchase
                $vHeaders = array("LabName", "Barcode", "PurchaseSequence");
                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));
                $row++;
                foreach ($vSnpCheck as $op) {
                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->LabName);
                    // $oSheet->SetCellValue(chr($charCol++).$row, $op->Number);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->PurchaseSequence);

                    $row++;
                }
            } elseif (Yii::$app->request->queryParams["id"] == 2) { //Box
                $vHeaders = array("LabName", "Box", "Position In Box");
                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));
                $row++;
                foreach ($vSnpCheck as $op) {

                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->LabName);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->Box);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->PositionInBox);

                    $row++;
                }
            } else {
                $vHeaders = array("LabName", "Barcode", "PurchaseSequence", "Quality", "Allele Fam", "Allele Vic Hex", "Box", "Position In Box");

                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));
                $row++;
                foreach ($vSnpCheck as $op) {
                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->LabName);
                    foreach ($op->barcodes as $b)
                        $oSheet->SetCellValue(chr($charCol++) . $row, $b->Number);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->PurchaseSequence);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->Quality);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->AlleleFam);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->AlleleVicHex);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->Box);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op->PositionInBox);

                    $row++;
                }
            }
        } else {
            if (Yii::$app->request->queryParams["id"] == 3) {
                $vHeaders = array("LabName", "Barcode", "PurchaseSequence", "Quality", "Allele Fam", "Allele Vic Hex", "Box", "Position In Box");
                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));
                $row++;
                foreach ($vSnp as $op) {
                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['LabName']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Number']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['PurchaseSequence']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Quality']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['AlleleFam']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['AlleleVicHex']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Box']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['PositionInBox']);

                    $row++;
                }
            } elseif (Yii::$app->request->queryParams["id"] == 2) {
                $vHeaders = array("LabName", "Box", "Position In Box");
                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));
                $row++;
                foreach ($vSnp as $op) {
                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['LabName']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Box']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['PositionInBox']);

                    $row++;
                }
            } else {
                $vHeaders = array("LabName", "Barcode", "PurchaseSequence");
                foreach ($vHeaders as $key => $lbl) {
                    $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
                }
                $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                        array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF00')
                            )
                ));

                $row++;
                foreach ($vSnp as $op) {

                    $charCol = 65;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['LabName']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Number']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['PurchaseSequence']);

                    $row++;
                }
            }
        }

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function actionExcelPolymorfism() {
        $model = new FingerprintSearch();
        $model->IsActive = 1;

        $materials_array = Yii::$app->request->queryParams['FingerprintSearch'];

        $material_1 = $model->getMaterialByFpMaterialId($materials_array['radio1']);
        $material_2 = $model->getMaterialByFpMaterialId($materials_array['radio2']);

        if (isset(Yii::$app->request->queryParams['vCheck'])) {
            $snpLab = new SnpLabSearch();
            $snpLab->IsActive = 1;
            $vSnp = $snpLab->searchByIds(Yii::$app->request->queryParams['vCheck'])->getModels();
        } else {   // print_r(Yii::$app->request->queryParams["id"]); exit;
            if (Yii::$app->request->queryParams["id"] == 2) { // 1 ->Box Search : 2 ->FP
                $vFormFP = Yii::$app->request->queryParams;
                $vFormFP['FingerprintSearch']['Method'] = 3;
            } else
                $vFormFP = Yii::$app->request->queryParams;
            //print_r(Yii::$app->request->queryParams['FingerprintSearch']['Method']); exit;
            $vSnp = $model->get_polymorfic($vFormFP)->getModels();
        }


        $totalCompared = $model->getTotalCompared(Yii::$app->request->queryParams);

        $oExcel = new PHPExcel();
        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();
        $sTitle = $material_1["Name"] . "(" . $material_1["TissueOrigin"] . ")_X_" . $material_2["Name"] . "(" . $material_2["TissueOrigin"] . ")";
        $oExcel->getProperties()->setTitle($sTitle . date("d-h-Y"));

        //HEADERS
        $charCol = 65;
        $row = 1;

        $oSheet->getColumnDimension(chr($charCol))->setAutoSize(true);
        $oSheet->SetCellValue(chr(65) . $row++, $sTitle);
        $oSheet->SetCellValue(chr(65) . $row++, count($vSnp) . " of " . $totalCompared . "  Markers");
        $oSheet->SetCellValue(chr(65) . $row++, "");

        if (Yii::$app->request->queryParams["id"] == 1)
            $vHeaders = array("Name", "LabName", "Box", "Position In Box");
        else
            $vHeaders = array("Name", "LabName", "Position", "LinkageGroup", $material_1["Name"] . "(" . $material_1["TissueOrigin"] . ")", $material_2["Name"] . "(" . $material_2["TissueOrigin"] . ")", "Result");

        foreach ($vHeaders as $key => $lbl) {
            $oSheet->getColumnDimension(chr($charCol))->setAutoSize(false);
            $oSheet->SetCellValue(chr($charCol++) . $row, $lbl);
        }
        $oSheet->getStyle(chr(65) . "$row:" . chr($charCol - 1) . "$row")->applyFromArray(
                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFF00')
                    )
        ));

        $row++;

        if (is_array($vSnp)) {
            if (Yii::$app->request->queryParams["id"] == 1) {

                foreach ($vSnp as $op) {
                    $charCol = 65;
                    //print_r($op['snpLabs']); exit;

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Name']);
                    foreach ($op['snpLabs'] as $snplab) {
                        $oSheet->SetCellValue(chr($charCol++) . $row, $snplab['LabName']);
                        $oSheet->SetCellValue(chr($charCol++) . $row, $snplab['Box']);
                        $oSheet->SetCellValue(chr($charCol++) . $row, $snplab['PositionInBox']);
                        //$oSheet->SetCellValue(chr($charCol++).$row, $snplab['PIC']);
                    }
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['PurchaseSequence']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['AlleleFam']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['AlleleVicHex']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['Quality']);

                    $row++;
                }
            } else {
                foreach ($vSnp as $op) {
                    $charCol = 65;
                    switch ($op['Allele_Id']) {
                        case 1:
                            $Allele_Id = "Allele 1";
                            break;
                        case 2:
                            $Allele_Id = "Allele 2";
                            break;
                        case 3:
                            $Allele_Id = "Allele 1 & 2";
                            break;
                        default:
                            $Allele_Id = "NA";
                            break;
                    }

                    switch ($op['Allele2']) {
                        case 1:
                            $Allele_Id2 = "Allele 1";
                            break;
                        case 2:
                            $Allele_Id2 = "Allele 2";
                            break;
                        case 3:
                            $Allele_Id2 = "Allele 1 & 2";
                            break;
                        default:
                            $Allele_Id2 = "NA";
                            break;
                    }

                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Name']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['LabName']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['PurchaseSequence']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['AlleleFam']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['AlleleVicHex']);
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['Quality']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['LinkageGroup']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Position']);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $Allele_Id);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $Allele_Id2);
                    $oSheet->SetCellValue(chr($charCol++) . $row, $op['Result'] == 1 ? "Polymorphic" : "Monomorphic");
                    //$oSheet->SetCellValue(chr($charCol++).$row, $op['PIC']);


                    $row++;
                }
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function actionFp_materialsByCrop() {
        $out = [];
        
        if (isset($_POST['depdrop_parents'])) {

            $parents = $_POST['depdrop_parents'];
            
            if ($parents != null) {
                $crop_id = $parents[0];
                
                $fp = new Fingerprint();
                $fpmaterials = $fp->getMaterialResult($crop_id);

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['output' => $fpmaterials];
            }
        }
       
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionDataMaterials($id = null) {
        $connection = \Yii::$app->dbGdbms;
        $Query = "SELECT * from fingerprint f
        INNER JOIN fingerprint_material fm ON f.Fingerprint_Id=fm.Fingerprint_Id
        where fm.Material_Test_Id=" . $id." Group By f.Name";

        $b[0] = $connection->createCommand($Query)->queryAll();

        for ($i = 0; $i < count($b[0]); $i++) {
            $sql = 'SELECT COUNT(Fingerprint_SnpLab_Id) From fingerprint_snplab fm
                WHERE fm.Fingerprint_Id =' . $b[0][$i]["Fingerprint_Id"];

            $count = $connection->createCommand($sql)->queryOne();
            $b[1][$i] = ($count["COUNT(Fingerprint_SnpLab_Id)"]);
        }

        //$b[1]=$count["COUNT(Fingerprint_SnpLab_Id)"];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return( $b);
        exit;
    }

    public function actionDataMaterialsByFpMat_Id($id = null) {
        $connectionGdbms = \Yii::$app->dbGdbms;
        $Query = "SELECT fm.Fingerprint_Material_Id, f.Fingerprint_Id, f.Name AS Name, fm.TissueOrigin, m.Name AS MaterialName  from fingerprint f
        INNER JOIN fingerprint_material fm ON f.Fingerprint_Id=fm.Fingerprint_Id
        INNER JOIN material_test m ON m.Material_Test_Id=fm.Material_Test_Id
        where fm.Fingerprint_Material_Id=" . $id;      

        return  $connectionGdbms->createCommand($Query)->queryOne();
    }

    public function actionIdByFpMaterial() {
        $idMaterial = Yii::$app->request->queryParams["id"];
        $idFp = Yii::$app->request->queryParams["idfp"];
        $id = FingerprintMaterial::find()
                ->where(["Fingerprint_Id" => $idFp, "Material_Test_Id" => $idMaterial])
                ->one();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ($id["Fingerprint_Material_Id"]);
        exit;
    }

    public function stringMaterialByIdFp($id_fpMaterial) {
        $connection = \Yii::$app->dbGdbms;
        $Query = "SELECT m.Name, fm.TissueOrigin,  FROM fingerprint_material fm
                   INNER JOIN material_test m ON m.Material_Test_Id=fm.Material_Test_Id
                   WHERE fm.Fingerprint_Material_Id = " . $id_fpMaterial;
        $name = $connection->createCommand($Query)->queryScalar();
        print_r($name);
        exit;
    }
    
    private function captureMethod($request)
    {
        $method = array();
        if($request->post() != null)
            $method=$request->post();
        elseif($request->queryParams != null)
        {
            $method = $method != null ? $request->queryParams : array_merge ($method, $request->queryParams);
        }
        
        //print_r($method); exit;
        return $method;
    }

}

// Fin del archivo QueryController.php
