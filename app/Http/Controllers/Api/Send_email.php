<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;

class Send_email extends Controller
{
    //
    public function sendMail($sendTo,$subject,$body){
        $mail               =    new PHPMailer();
        try {
            $mail->isSMTP(); // tell to use smtp
            //$mail->SMTPDebug = 1;
            $mail->CharSet = "utf-8"; // set charset to utf8
            $mail->SMTPAuth = true;  // use smpt auth
            $mail->SMTPSecure = "ssl"; // or ssl
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->Username = "lmsbcasproject@gmail.com";
            $mail->Password = "multiLMS)(*";
            $mail->setFrom("lmsbcasproject@gmail.com", "MULTI LMS");
            $mail->Subject = $subject;
            $mail->MsgHTML($body."<br/><br/><b>This is a automated message you cannot do not reply to it</b>");
            foreach ($sendTo as $row) {
                $mail->addBCC($row);
            }
            $mail->send();

        } catch (phpmailerException $e) {
            dd($e);
            die();
        } catch (Exception $e) {
            dd($e);
            die();
        }
    }
}
