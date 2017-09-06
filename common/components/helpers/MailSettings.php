<?php
namespace common\components\helpers;

use Yii;
use yii\base\Component;
use PHPMailer;

class MailSettings extends Component
{

    static function localMode($user, $data)
    {
        $mail = new PHPMailer();
        $mail->Mailer="smtp";
        //$mail->SMTPDebug = 3;
        // To avoid google certifacte control
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        //$mail->SMTPDebug = 3;
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = "admin@theseedguru.com";
        $mail->Password = "!QAZ2wsx";
        //$mail->From = "admin@theseedguru.com";
        $mail->FromName = "Advanta Semillas";
        $mail->Subject = utf8_decode("Samples Grids");

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        //$mail->AddAddress($user->Email,"");
        $mail->AddAddress("maximiliano.mendoza@theseedguru.com","");
                
        $usersCc = [];
        foreach($data['projectsId'] as $key => $projectId)
        {
            $usersCc = $usersCc != "" ? \common\models\User::getUsersMailsAddedByProject($projectId) : array_merge($usersCc, \common\models\User::getUsersMailsAddedByProject($projectId));   
        }
        //print_r($usersCc); exit;                         
        /*if($usersCc != null)
        {
            $body = "[Others Mails : ";
            foreach ($usersCc as $key => $value)
            {
                //$mail->addCC($value);
                $body .= $value.' | ';
            }
            $body .= "]";
        }*/
        
        /*
        if($usersCc != null)
        {
            foreach ($usersCc as $key => $value)
            {
                $mail->addCC($value);
            }
        }
        */
        //mail->addCC("maximiliano.mendoza@theseedguru.com","");
        $mail->AddAttachment($data['file']);
        if(key_exists('file2', $data))
            $mail->AddAttachment($data['file2']);
        $body  = "Dear ".$user->Username."!<br>";
        $body .= "The reason for this email is to provide the grid for collection of samples required .<br>
                  Please download the attachment.";

        //$body .= "<br><br><font color='grey'>Saludos</font>";
        $mail->Body = $body;

        return $mail;
    }
    
    static function productionMode($user, $data)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();// Mailer="smtp";
        $mail->SMTPAuth=true;
        //$mail->SMTPDebug = 4;
        $mail->Host = "smtp.office365.com";
        $mail->Port = 587;
        $mail->IsHTML(true);
        $mail->Username = "gdbms-admin@advantasemillas.com.ar";
        $mail->Password = "Baqo0431";
        $mail->From = "Advanta Seeds";
        $mail->FromName = "Advanta Semillas";
        $mail->Subject = utf8_decode("Samples Grids");

        $mail->SMTPSecure = "tls";
        $mail->AddAddress($user->Email,"");
        //$mail->AddAddress("carlos.cimmino@advantaseeds.com","");
        $usersCc = [];
        foreach($data['projectsId'] as $key => $projectId)
        {
            $usersCc[] = \common\models\User::getUsersMailsAddedByProject($projectId);   
        }
        $flat_unique_mails = array_unique(\common\components\Operations::flatArray($usersCc));

        if($flat_unique_mails != null)
        {
            foreach ($flat_unique_mails as $key => $value)
            {
                $mail->addCC($value);
            }
        }

        //$mail->AddAddress("maximiliano.mendoza@theseedguru.com","");
        $mail->AddAttachment($data['file']);
        if(key_exists('file2', $data))
        $mail->AddAttachment($data['file2']);
        $body  = "Dear ".$user->Username."!<br>";
        $body .= "The reason for this email is to provide the grid for collection of samples required .<br>
                  Please download the attachment.";

        //$body .= "<br><br><font color='grey'>Saludos</font>";
        $mail->Body = $body;

        return $mail;
            
    }
}
