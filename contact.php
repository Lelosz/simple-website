<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// funkcja PokazKontaktHaslo ma za zadanie wyswietlac formularz do przypominania hasla
function PokazKontaktHaslo(){
    echo'
    <div class="longrectangle">
    <h4 style="font-size:25px; margin:5px;">Przypomnij hasło</h4>
    <form method="post">
        <label for="email">Podaj E-mail</label>
        <div><input class="logowanie" type="email" name="email" id="email" required></div>

        <input type="submit" name="sendpass" class="logowanie" value="Wyślij" />
    </form>
    </div>
    ';
}
/*
WyslijMailKontaktowy sluzy do wyslania maila kontaktowego na podstawie wprowadzonych przez uzytkownika 
danych(mail,temat wiadomosci i sama wiadomosc)*/
function WyślijMailKontaktowy(){
    $mail = new PHPMailer();
    $mail->IsSMTP(); 
    $mail->Mailer = "smtp";
    $mail->SMTPDebug  = 0;  
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "TypeMailHere";
    $mail->Password   = "TypePassHere";
    $mail->IsHTML(true);
    $mail->AddAddress(htmlspecialchars($_POST["email"]));
    $mail->SetFrom("TypeMailHere");
    $mail->Subject = htmlspecialchars($_POST["subject"]);
    $content = htmlspecialchars($_POST["message"]);
    $mail->MsgHTML($content); 
    if(!$mail->Send()) {
    echo '
    <div class="longrectangle"
    <h4 style="font-size:25px; margin:5px;">Wyslanie maila nie powiodlo sie.</h4>
    </div>
    ';
    } else {
    echo '
    <div class="longrectangle"
    <h4 style="font-size:25px; margin:5px;">Mail wyslany pomyslnie!</h4>
    </div>
    ';
    }
}
/*
PrzypomnijHaslo Wysyla wiadomosc mailowa z haslem do panelu cms na podanego przez uzytkownika maila
*/
function PrzypomnijHaslo(){

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->SMTPDebug  = 0;  
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "TypeMailHere";
    $mail->Password   = "TypePassHere";
    $mail->IsHTML(true);
    $mail->AddAddress(htmlspecialchars($_POST["email"]));
    $mail->SetFrom("TypeMailHere");
    $mail->Subject = "Przypomnienie hasla";
    $content = "Twoje haslo to:\"123\"";
    $mail->MsgHTML($content); 
    if(!$mail->Send()) {
    echo '
    <div class="longrectangle"
    <h4 style="font-size:25px; margin:5px;">Wyslanie maila z przypomnieniem hasla nie powiodlo sie.</h4>
    </div>
    ';
    } else {
    echo '
    <div class="longrectangle"
    <h4 style="font-size:25px; margin:5px;">Mail z przypomnieniem hasla wyslany pomyslnie!</h4>
    </div>
    ';
    }
}


?>