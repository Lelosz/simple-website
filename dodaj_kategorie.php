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
//wyswietla formularz dodawania kategorii
echo'
    <div class="center">
    <h1>Dodaj kategorie</h1>

    <form method="post" name="CategoryForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
        Nazwa kategorii<br><input type="text" name="name" size="50" required/> <br>
        Matka(0 domyślne)<br><input type="text" name="matka" size="50" value="0"required/> <br>
        <input type="submit" value="Zatwierdź" name="sub"/>
    </form>
    <form method="post">
    <input type="submit" value="Powrót" name="sub2"/>
    </form>
    </div>
    ';

// Dodaje nowa strone i zwraca ew. bledy
if(isset($_POST["sub"])){
    $name= htmlspecialchars($_POST["name"]);
    $mtka= htmlspecialchars($_POST["matka"]);
    $stmt = $conn->prepare("INSERT INTO kategorie (matka,nazwa)
    VALUES(?,?) LIMIT 1");
    $stmt->bind_param("is",$mtka,$name);
    $stmt->execute();
    if(empty(!$stmt)){
        echo "Dodano nową kategorię";
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