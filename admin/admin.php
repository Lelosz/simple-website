<?php
session_start();
include('cfg.php'); // zawiera polaczenie z serwerem

// odpowiada za logowanie do panelu CMS, sprawdza login i haslo, wyswietla ew. bledy
function Logowanie(){
 
    if(isset($_POST["x1_submit"])){
        $log = htmlspecialchars($_POST["login_mail"]);
        $pas = htmlspecialchars($_POST["login_pass"]);
        if($log ==="admin" && $pas==="123"){
            $_SESSION["czyzalogowany"]=true;
            header("Location: login-success.php");
            die();
        }
            
        if(!($log===$login && $pas ==="123")){
            echo "Podałeś złe hasło lub login.";
        }
    }
    
}

?>