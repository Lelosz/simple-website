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
/*Szablon do wypelnienia w przypadku checi dodania nowej strony. Zawiera tytul, zawartosc strony, 
status aktywacji oraz przycisk zatwierdz(ID strony tworzone jest automatycznie przez BD)*/
echo'
    <div class="center">
    <h1>Dodaj podstronę</h1>

    <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
        Tytuł<br><input type="text" name="title" size="50" required/> <br>
        Treść<br><textarea name="content" rows="10" cols="80" required></textarea><br>
        Aktywacja<br><input type="checkbox" name="actuation" value="1"/> <br>
        <input type="submit" value="Zatwierdź" name="sub"/>
    </form>
    <form method="post">
    <input type="submit" value="Powrót" name="sub2"/>
    </form>
    </div>
    ';
// Warunek pomocniczy przypisujacy wartosc 0 guzikowi aktywacji strony w wypadku braku aktywacji
if(isset($_POST["sub"])){
    if(!isset($_POST["actuation"])){
        $_POST["actuation"]="0";
    }
}
// Dodaje nowa strone i zwraca ew. bledy
if(isset($_POST["sub"])){
    $ttle= htmlspecialchars($_POST["title"]);
    $cntnt= htmlspecialchars($_POST["content"]);
    $actu= htmlspecialchars($_POST["actuation"]);
    $stmt = $conn->prepare("INSERT INTO page_list (page_title,page_content,status)
    VALUES(?,?,?) LIMIT 1");
    $stmt->bind_param("ssi",$ttle,$cntnt,$actu);
    $stmt->execute();
    if(empty(!$stmt)){
        echo "Dodano nową podstronę";
    }
    else{
        echo "Nie udalo się dodać nowej podstrony";
    }
}      
// sprawdza stan aktywacji przycisku powrotu do poprzedniej strony
if(isset($_POST["sub2"])){
    header("Location: login-success.php#1");
}
?>