<?php

namespace frontend\controllers;

use Yii;
use common\models\MaterialTest;
use common\models\MaterialTestSearch;
// use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PHPExcel;
use common\models\Crop;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use yii\filters\AccessControl;

/**
 * MaterialTestController implements the CRUD actions for MaterialTest model.
 */
class MaterialTestController extends ControllerCustom {

    public $enableCsrfValidation = false;

    public function behaviors() {
        // Yii::$app->session->removeAll();
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
                // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MaterialTest models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->getIdentity()->itemName != "admin")
            return $this->redirect(Yii::$app->homeUrl);

        $searchModel = new MaterialTestSearch();
        $searchModel->IsActive = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $crop = Crop::find()
                ->where(["crop.IsActive" => 1])
                ->all();

        $fromDashboard = false;
        if (isset($_GET['fromDashboard']))
            $fromDashboard = true;

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'crop' => $crop,
                    'fromDashboard' => $fromDashboard,
        ]);
    }

    /**
     * Displays a single MaterialTest model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax($id) {
        $this->layout = false;
        return $this->renderAjax('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MaterialTest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $this->layout = false;
        $model = new MaterialTest();

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
            $parameters['crop'] = Crop::find()
                    ->where(['crop.IsActive' => 1])
                    ->all();
            return $this->renderAjax('create', $parameters);
        }
    }

    /**
     * Updates an existing MaterialTest model.
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
            $parameters = $this->getParameters();


            $parameters['model'] = $model;
            $parameters['crop'] = Crop::find()
                    ->where(['crop.IsActive' => 1])
                    ->all();

            return $this->renderAjax('update', $parameters);
        }
    }

    /**
     * Deletes an existing MaterialTest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->layout = false;
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax) {
            $isSubmit = !is_null(Yii::$app->request->post('submit'));
            if ($isSubmit) {
                $model->IsActive = 0;
                $model->update();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->renderAjax('delete', ['ok' => true]);
            }
        }
        return $this->renderAjax('delete', ['model' => $model]);
    }

    /**
     * Finds the MaterialTest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterialTest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = MaterialTest::findOne($id)) !== null) {
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

    public function actionExcel() {
        $model = new MaterialTestSearch();
        $model->IsActive = 1;

        $oExcel = new PHPExcel();

        // Add some data
        $oExcel->setActiveSheetIndex(0);
        $oSheet = $oExcel->getActiveSheet();

        $sTitle = "Materials (" . date("Y-m-d") . ")";

        $oExcel->getProperties()->setTitle("$sTitle");

        $vHeaders = array("Name", "CodeType", "OldCode_1", "Owner", "Material", "cms", "Pedigree", "Origin");

        //HEADERS
        $charCol = 65;
        $row = 1;
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
        if (isset(Yii::$app->request->queryParams['vCheck']))
            $vSnp = $model->searchByIds(Yii::$app->request->queryParams['vCheck'])->getModels();
        else
            $vSnp = $model->search(Yii::$app->request->queryParams)->getModels();
        //search()->getData();
        if (is_array($vSnp)) {
            foreach ($vSnp as $op) {
                $charCol = 65;

                $oSheet->SetCellValue(chr($charCol++) . $row, $op->Name);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->CodeType);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->OldCode_1);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->Owner);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->Material);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->cms);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->Pedigree);
                $oSheet->SetCellValue(chr($charCol++) . $row, $op->Origin);
                $row++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$sTitle.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function actionDeleteSelection() {
        if (Yii::$app->request->queryParams['vCheck'])
            MaterialTest::deleteByIds(Yii::$app->request->queryParams['vCheck']);
        return $this->actionIndex();
    }

    public function actionMaterialsByCropsByKendo() {
        if (Yii::$app->request->queryParams) {

            $parents = Yii::$app->request->queryParams['filter']['filters'];

            if ($parents != null) {

                $crop = $parents[0]['value'];

                $connection = \Yii::$app->db;
                $Query = "SELECT m.Name, m.Material_Test_Id, mf.Fingerprint_Material_Id FROM material_test m
                 left join fingerprint_material mf ON mf.Material_Test_Id=m.Material_Test_Id
                 WHERE m.IsActive=1 and m.Crop_Id=" . $crop;
                if (isset($parents[1])) {
                    $Query .= " and m.Name like '%" . $parents[1]['value'] . "%'";
                };

                $materials = $connection->createCommand($Query)->queryAll();

                if ($materials) {
                    $i = 0;
                    foreach ($materials as $m) {
                        $mAvilables[$i]["name"] = $m['Name'];
                        $mAvilables[$i]["id"] = $m['Material_Test_Id'];
                        $m['Fingerprint_Material_Id'] == NULL ? $mAvilables[$i]["hexa"] = '#F69C35' : $mAvilables[$i]["hexa"] = '#389AE5';
                        $i++;
                    }
                    //print_r($mAvilables); exit;
                } else
                    return false;

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $mAvilables;
            }
        }
    }

    public function actionGetMaterialsByCropsByAjax() {
        if (Yii::$app->request->queryParams) {
            $crop = Yii::$app->request->queryParams['cropId'];
            $materials = MaterialTest::find()->where(["IsActive" => 1, "Crop_Id" => $crop])->asArray()->all();
            //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $inputs = '<button type="button" data-toggle="modal" data-target="#modal" data-url="../material-test/create" class="user export size12 margin-top--30_padding-6 pull-right">'
                    . '<span class="glyphicon glyphicon-plus "></span> New Material'
                    . '</button>'
                    . '<a href="javascript: selectAll();" class ="user export margin20 pull-right" > <span class="glyphicon glyphicon-check size12" aria-hidden="true"></span></a>'
                    . '<div class="container-scrolleable">'
                    . '<label class="control-label" for="materials"> Material </label>';
            $i = 0;
            $divs = 0;
            foreach ($materials as $m) {
                if ((fmod($i, 50) == 0) or $i == 0) {
                    if ((fmod($divs, 6) == 0) or $divs == 6)
                        $inputs .= "<div class='scrolleable'>";
                    $inputs .= "<div class='cols_materials'>";
                }

                $inputs .= "<div><input type='checkbox' value='" . $m['Material_Test_Id'] . "' id='" . $m['Material_Test_Id'] . "' name='Project[vCheck][]' /> " . $m['Name'] . "</div>";
                $i++;

                if ((fmod($i, 50) == 0) and $i >= 50) {
                    $divs++;
                    $inputs .= "</div>";
                    if ((fmod($divs, 6) == 0) and $divs >= 6)
                        $inputs .= "</div> <br>";
                }
            }

            $inputs .= '</div></div>
                               
                                
                          </div>';
            return $inputs;
        }
    }

    public function actionExampleNew() {
        if (Yii::$app->request->queryParams)
            ; {

            $idMaterials = Yii::$app->request->queryParams['a'];
            $mats = "'";
            $mats .= implode("','", $idMaterials);
            $mats .= "'";

            $connection = \Yii::$app->db;

            $Query = "select Material_Test_Id, Name from material_test m where m.Material_Test_Id in (" . $mats . ")";

            $materials = $connection->createCommand($Query)->queryAll();
            $select = "<select name='Project[MaterialsSelected]' id='MaterialSelected' multiple='multiple'>";
            foreach ($materials as $mat) {
                $select .="<option value='" . $mat['Material_Test_Id'] . "' selected='selected'>" . $mat['Name'] . "</option>";
            }
            $select .="</select>";
            return $select;
        }
    }

}
