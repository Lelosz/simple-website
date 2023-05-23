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
/* Pobiera dane o stronie z BD i wyswietla je w tabeli, ktora uzytkownik moze edytowac */
if(isset($_POST['edit'])){
    $pgid = $_POST['idedytujpodstrone'];
    $_SESSION['pgidn']= $pgid;
    $stmt = $conn->prepare("SELECT * FROM page_list WHERE id=? LIMIT 1");
    $stmt->bind_param("i",$pgid);
    $stmt->execute();
    $result = $stmt->get_result();
    while($page = $result->fetch_assoc()){
            $page_title = $page["page_title"];
            $page_content = $page["page_content"];
            $stat = $page["status"];
        }
    if($stat=="1"){
        $is_checked = "checked";
    }else{$is_checked = "";}
    // formularz edytowania podstrony
    echo'
    <div class="center">
    <h1>Edytuj podstronę</h1>
    
    <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
    Tytuł<br><input type="text" name="title" size="50" value='.$page_title.' required/> <br>
    Treść<br><textarea name="content" rows="10" cols="80" required>'.$page_content.'</textarea><br>
    Aktywacja<br><input type="checkbox" name="actuation" value="1" '.$is_checked.'/> <br>
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
$pgid = $_SESSION['pgidn'];
// Warunek pomocniczy przypisujacy wartosc 0 guzikowi aktywacji strony w wypadku braku aktywacji
if(isset($_POST["sub"])){
    if(!isset($_POST["actuation"])){
        $_POST["actuation"]="0";
    }
}
// Zatwierdza zmiany dokonane na stronie i wyswietla ew komunikaty o niepowodzeniu
if(isset($_POST["sub"])){
    $ttle= htmlspecialchars($_POST["title"]);
    $cntnt= htmlspecialchars($_POST["content"]);
    $actu= htmlspecialchars($_POST["actuation"]);
    $stmt = $conn->prepare("UPDATE page_list SET page_title=?, page_content=?, status=? WHERE id=? LIMIT 1");
    $stmt->bind_param("ssii",$ttle,$cntnt,$actu,$pgid);
    $stmt->execute();
    if(empty(!$stmt)){
        header("Location: login-success.php#1");
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