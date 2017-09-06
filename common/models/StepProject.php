<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "step_project".
 *
 * @property integer $StepProjectId
 * @property string $Name
 *
 * @property Project[] $projects
 */
class StepProject extends \yii\db\ActiveRecord
{
    const CANCELED = 0;
    const PROJECT_DEFINITION = 1;
    const MARKERS_SELECTION = 3;
    const GRID_DEFINITION = 2;
    const SENT = 4;
    const SAMPLE_DISPATCH = 5;
    const SAMPLE_RECEPTION = 6;
    const DNA_EXTRACTION = 7;
    const GENOTYPED = 8;
    const REPORT = 9;
    const FINISHED = 10;
    const ON_HOLD = 11;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'step_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'StepProjectId' => Yii::t('app', 'Step Project ID'),
            'Name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['StepProjectId' => 'StepProjectId']);
    }
    
    static function getPercentByStep($id)
    {
        $percent = [];
        if(Yii::$app->user->getIdentity()->ItemName != 'breeder')
        {
            switch($id)
            {
                case StepProject::PROJECT_DEFINITION:
                    $percent['percent'] = 5;
                    //$percent['Url'] = "project/view-summary"
                    break;
                case StepProject::GRID_DEFINITION:
                     $percent['percent'] = 18;
                    break;
                case StepProject::MARKERS_SELECTION:
                     $percent['percent'] = 30;
                    break;
                case StepProject::SENT:
                     $percent['percent'] = 43;
                    break;
                case StepProject::SAMPLE_DISPATCH:
                     $percent['percent'] = 56;
                    break;
                case StepProject::SAMPLE_RECEPTION:
                    $percent['percent'] = 69;
                    break;
                case StepProject::DNA_EXTRACTION:
                    $percent['percent'] = 80;
                    break;
                case StepProject::GENOTYPED: //its reports on dashboard
                    $percent['percent'] = 80;
                    break;
                default:
                    $percent['percent'] = 100;
                    break;   
            }
            //ADD ONE STEP TO COMPLETE THE NEXT STEP
        $percent['avilableStep'] = $id+1;
        }  else {
            $percent = self::getPercentByStepForBreeders($id);
             if($id == self::GRID_DEFINITION)
                 //ADD 2 more steps if is breeder, to avoid markers_selection.
                $percent['avilableStep'] = $id+2;
             else
                $percent['avilableStep'] = $id+1;

        }
        
        return $percent;
    }
    
    static function getUrlOrDatasByStep($id, $project, $getInfo = null)
    {
        
        $data = "";
        if(Yii::$app->user->getIdentity()->ItemName != 'breeder')
        {
            switch($id)
            {
                case StepProject::PROJECT_DEFINITION:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->Date));
                    else
                        $data = '<a href="#" onclick="return actionStep(\'view-summary?id='.$project->ProjectId.'\');" />';
                    break;
                
                case StepProject::GRID_DEFINITION:
                    if($getInfo != null)
                        $data = count($project->samplesByProjects). " Samples";
                    else
                        {
                            if($project->ProjectId == 1 && count($project->samplesByProjects) == 0)
                            {
                                $data = "<a href='update-grid-definition?idProject=".$project->ProjectId."' />";
                            }elseif($id > $project->StepProjectId)
                            {
                                $data = "<a href='grid-definition?id=".$project->ProjectId."' />";
                            }else
                            {
                                $data = "<a href='#' onclick='return actionStep(\"get-grid-preview?id=".$project->ProjectId."\");' /> ";
                            }
                            /************************************
                            if($id > $project->StepProjectId && count($project->samplesByProjects) == 0)
                            {
                                if( count($project->samplesByProjects) == 0)
                                    $data = "update-grid-definition?idProject=".$project->ProjectId;
                                elseif($id == ($project->StepProjectId + 1) )
                                    $data = "grid-definition?id=".$project->ProjectId;
                                else
                                    $data = "";

                            }else{
                                $data = "get-grid-preview";
                            }
                            */
                        }
                    break;
                case StepProject::MARKERS_SELECTION:
                if($getInfo != null)
                    $data = count($project->markersByProjects). " Snplabs";
                else
                {
                    if($id > $project->StepProjectId || count($project->markersByProjects) == 0)
                    {
                        $data = count($project->markersByProjects) == 0 ? "<a href='update-select-markers?idProject=".$project->ProjectId." ' />" : "<a href='select-markers?id=".$project->ProjectId." ' />" ;
                    }else
                    {
                        $data = "<a href='#' onclick='return actionStep(\"view-markers-and-traits?id=".$project->ProjectId."\"); ' />";
                    }
                    /*
                    if(($id > $project->StepProjectId && count($project->markersByProjects) == 0) && $project->StepProjectId != self::GRID_DEFINITION)
                    {
                        $data = count($project->markersByProjects) == 0 ? "update-select-markers?idProject=" : "select-markers?id=" ;
                        $data .= $project->ProjectId;
                    }else{
                        $data = "view-markers-and-traits";
                    }*/
                    
                }
                break;
                case StepProject::SENT:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->platesByProjects[0]->plate->Date));
                    else                    
                        if($id > $project->StepProjectId)
                        {
                            if($id == ($project->StepProjectId + 1))
                                $data = "<a href='#' onclick='return actionStep(\"get-shipment-data?id=".$project->ProjectId."\");' /> ";
                            else
                                $data = "";

                        }else{
                            $data = "<a href='#' onclick='return actionStep(\"get-shipment-data?id=".$project->ProjectId."\");' /> "; 
                        }

                    break;
                case StepProject::SAMPLE_DISPATCH:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->dispatchPlates->Date));
                    else
                    if($id > $project->StepProjectId)
                    {
                        //if the id step is bigger only for one step, enable it
                        if($id == ($project->StepProjectId + 1))
                            $data = "<a href='sample-dispatch?id=".$project->ProjectId."' />";
                        else
                            $data = "";
                    }else{
                        //if not, only call view ajax
                        $data = "<a href='#' onclick='return actionStep(\"get-dispatch-data?id=".$project->ProjectId."\");' />";
                    }
                    break;
                case StepProject::SAMPLE_RECEPTION:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                    else
                        if($id > $project->StepProjectId)
                        {
                            //if the id step is bigger only for one step, enable it
                            if($id == ($project->StepProjectId + 1))
                                $data = "<a href='sample-reception?id=".$project->ProjectId."' />";
                            else
                                $data = "";
                        }else{
                            //if not, only call view ajax
                            // Return null because this item hasn't actions
                             $date = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                             $data = "<a href='#' onclick='return actionStep(\"show-pop-up/".$date."\");' />";
                        }
                    break;
                case StepProject::DNA_EXTRACTION:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                    else
                        if($id >= $project->StepProjectId)
                        {
                            //if the id step is bigger only for one step, enable it
                            //if($id == ($project->StepProjectId + 1))
                               $data = "<a href='#' onclick='return actionStep(\"get-assay-data?id=".$project->ProjectId."\");' />";
                            //else
                            //    $data = "";
                        }else{
                            //if not, only call view ajax
                            $data = "<a href='#' onclick='return actionStep(\"get-assay-data?id=".$project->ProjectId."\");' />";
                        }

                    break;
                default:
                    if($getInfo != null)
                        $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                    else
                        //if not, only call view ajax
                        if($id > $project->StepProjectId)
                        {
                           
                            //if the id step is bigger only for one step, enable it
                            if($id == ($project->StepProjectId + 1))
                            {
                              $data = "<a href='#' onclick='return actionStep(\"get-report-data?id=".$project->ProjectId."\");' />";
                            }
                            else
                                $data = "";
                        }else{
                            
                            //if not, only call view ajax
                            $data = "<a href='#' onlcik='return actionStep(\"get-report-data?id=".$project->ProjectId."\");' />";
                        }
                    break; 
            }
        }else
            $data = self::getUrlOrDatasByStepForBreeder($id, $project, $getInfo);
        
        return $data;
    }
    
    static function getPercentByStepForBreeders($id)
    {
        switch($id)
        {
            case StepProject::PROJECT_DEFINITION:
                $percent['percent'] = 5;
                //$percent['Url'] = "project/view-summary"
                break;
            case StepProject::GRID_DEFINITION:
                 $percent['percent'] = 17;
                break;
            case StepProject::MARKERS_SELECTION:
                 $percent['percent'] = 17; //repite for progress bar
                break;
            case StepProject::SENT:
                 $percent['percent'] = 31;
                break;
            case StepProject::SAMPLE_DISPATCH:
                 $percent['percent'] = 43;
                break;
            case StepProject::SAMPLE_RECEPTION:
                $percent['percent'] = 55;
                break;
            case StepProject::DNA_EXTRACTION:
                $percent['percent'] = 70;
                break;
            case StepProject::GENOTYPED: //its reports on dashboard
                $percent['percent'] = 85;
                break;
            default:
                $percent['percent'] = 100;
                break;   
        }
            return $percent;
    }
    
    static function getUrlOrDatasByStepForBreeder($id, $project, $getInfo = null)
    {
        switch($id)
        {
            case StepProject::PROJECT_DEFINITION:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->Date));
                else
                    $data = '<a href="#" onclick="return actionStep(\'view-summary?id='.$project->ProjectId.'\');" />';
                break;
            case StepProject::GRID_DEFINITION:
                if($getInfo != null)
                    $data = count($project->samplesByProjects). " Samples";
                else
                    {
                        if($project->ProjectId == 1 && count($project->samplesByProjects) == 0)
                        {
                            $data = "<a href='update-grid-definition?idProject=".$project->ProjectId."' />";
                        }elseif($id > $project->StepProjectId)
                        {
                            $data = "<a href='grid-definition?id=".$project->ProjectId."' />";
                        }else
                        {
                            $data = "<a href='#' onclick='return actionStep(\"get-grid-preview?id=".$project->ProjectId."\");' /> ";
                        }
                        /************************************
                        if($id > $project->StepProjectId && count($project->samplesByProjects) == 0)
                        {
                            if( count($project->samplesByProjects) == 0)
                                $data = "update-grid-definition?idProject=".$project->ProjectId;
                            elseif($id == ($project->StepProjectId + 1) )
                                $data = "grid-definition?id=".$project->ProjectId;
                            else
                                $data = "";

                        }else{
                            $data = "get-grid-preview";
                        }
                        */
                    }
                break;
            case StepProject::SENT:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->platesByProjects[0]->plate->Date));
                else                    
                {
                    if($id > $project->StepProjectId)
                        {
                            if($id == ($project->StepProjectId + 1))
                                $data = "<a href='#' onclick='return actionStep(\"get-shipment-data?id=".$project->ProjectId."\");' /> ";
                            else
                                $data = "";

                        }else{
                            $data = "<a href='#' onclick='return actionStep(\"get-shipment-data?id=".$project->ProjectId."\");' /> "; 
                        }
                }
                break;
            case StepProject::SAMPLE_DISPATCH:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->dispatchPlates->Date));
                else
                {                
                    if($id > $project->StepProjectId)
                    {
                        //if the id step is bigger only for one step, enable it
                        if($id == ($project->StepProjectId + 1))
                            $data = "<a href='sample-dispatch?id=".$project->ProjectId."' />";
                        else
                            $data = "";
                    }else{
                        //if not, only call view ajax
                        $data = "<a href='#' onclick='return actionStep(\"get-dispatch-data?id=".$project->ProjectId."\");' />";
                    }
                }
                break;
            case StepProject::SAMPLE_RECEPTION:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                else
                {
                    if($id > $project->StepProjectId)
                        {
                           $data = "<a href='#' onlcik='return actionStep(\"not-allow\");' />";
                            
                        }else{
                            //if not, only call view ajax
                            // Return null because this item hasn't actions
                             $date = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                             $data = "<a href='#' onlcik='return actionStep(\"show-pop-up/".$date."\");' />";
                        }
                }
                break;
            case StepProject::DNA_EXTRACTION:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                else
                    if($id > $project->StepProjectId)
                    {
                        //if the id step is bigger only for one step, enable it
                       
                           $data = "not-allow";
                    }else{
                        //if not, only call view ajax
                        $data = "get-assay-data";
                    }

                break;
            default:
                if($getInfo != null)
                    $data = date('d-m-Y',strtotime($project->receptionPlates->LabReception));
                else
                    //if not, only call view ajax
                    if($id > $project->StepProjectId)
                    {
                        //if the id step is bigger only for one step, enable it
                       $data = "not-allow";
                    }else{
                        //if not, only call view ajax
                        $data = "get-report-data";
                    }
                break; 
        }
        
        return $data;
    }
    
    static function getNumberBreeder($step)
    {
        if($step == self::REPORT )
            return $step - 2;
        if($step > self::GRID_DEFINITION )
            return $step - 1;
        else
            return $step;
    }
    
}
