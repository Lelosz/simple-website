<?php
/*
sprawdza czy uzytkownik jest zalogowany jako admin i czy powinny zostac wyswietlone mu odpowiednie dane
udostepnia funkcje wylogowania oraz potwierdza tozsamosc zalogowanej osoby
includuje takze pliki konfiguracyjne jak head i polaczenie z BD
*/
session_start();
include('showpage.php'); // umozliwia wyswietlenie head
PokazPodstrone('6'); // wyswietla head
require("cfg.php"); // wymaga polaczenia z BD
// wyswietla status logowania oraz umozliwia wylogowanie
if(isset($_SESSION["czyzalogowany"])){
    if($_SESSION["czyzalogowany"]===true){ 
    echo 'Jesteś zalogowany jako admin!
    <form method="post">
        <input type="submit" value="Wyloguj" name="logout" />
    </form>';
    if(isset($_POST['logout'])){
        $_SESSION["czyzalogowany"]=false;
        header("Location: index.php");
    }
    }
    else {header("Location: index.php");}
}
if(isset($_POST['edytujkategorie'])){
    $katid = $_POST['idedytujkategorie'];
    $_SESSION['pgidn']= $katid;
    $stmt = $conn->prepare("SELECT * FROM kategorie WHERE id=? LIMIT 1");
    $stmt->bind_param("i",$katid);
    $stmt->execute();
    $result = $stmt->get_result();
    while($page = $result->fetch_assoc()){
            $nazwa= $page["nazwa"];
            $matka = $page["matka"];
        }
    // formularz edytowania kategorii
    echo'
    <div class="center">
    <h1>Edytuj kategorię</h1>
    
    <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
    Nazwa<br><input type="text" name="name" size="50" value='.$nazwa.' required/> <br>
    Matka<br><textarea name="matka" rows="10" cols="80" required>'.$matka.'</textarea><br>
    <input type="submit" value="Zatwierdź" name="sub"/>
    </form>
    ';
    echo'
    <form method="post">
    <input type="submit" value="Powrót" name="sub2"/>
    </form>
    </div>
    ';
    
}
$katid = $_SESSION['pgidn'];
// Zatwierdza zmiany dokonane na stronie i wyswietla ew komunikaty o niepowodzeniu
if(isset($_POST["sub"])){
    $name= htmlspecialchars($_POST["name"]);
    $mtka= htmlspecialchars($_POST["matka"]);
    $stmt = $conn->prepare("UPDATE kategorie SET matka=?,nazwa=? WHERE id=? LIMIT 1");
    $stmt->bind_param("isi",$mtka,$name,$katid);
    $stmt->execute();
    if(empty(!$stmt)){
        header("Location: login-success.php#2");
    }
    else{
        echo "Nie udalo się dodać nowej kategorii";
    }
}
// sprawdza stan aktywacji przycisku powrotu do poprzedniej strony
if(isset($_POST["sub2"])){
    header("Location: login-success.php#2");
}
?>