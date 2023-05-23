<?php
/*
sprawdza czy uzytkownik jest zalogowany jako admin i czy powinny zostac wyswietlone mu odpowiednie dane
udostepnia funkcje wylogowania oraz potwierdza tozsamosc zalogowanej osoby
includuje takze pliki konfiguracyjne jak head i polaczenie z BD
*/
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
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
// szablon sluzacy do wyswietlania listy stron w panelu CMS
?>
<div class="center"> 
<h1>Panel CMS</h1>
<p id=1>Lista podstron</p>
<form action="dodaj_podstrone.php">
    <input type="submit" value="Dodaj nową podstronę" />
</form>
<table class="center2">
    <tr>
        <th>ID</th>
        <th>Tytuł podstrony</th>
        <th>Edytuj</th>
        <th>Usuń</th>
    </tr>
    <?php
    // Wyswietla tabele, a w niej informacje o znajdujacych sie stronach w BD   
    $stmt = $conn->prepare("SELECT * FROM page_list LIMIT 100"); // wysyla zapytanie do BD
    $stmt->execute();
    $result = $stmt->get_result();
    if (empty($result)){
        echo "Nie ma wynikow";
    }
    // tworzy tabele i wypelnia ja danymi z BD z powyzszego zapytania
    while($page = $result->fetch_assoc()){
        $id= $page["id"];
        $page_title = $page["page_title"];
        echo'<tr>
        <th>'.$id.'</th>
        <th>'.$page_title.'</th>
        <th><form action="edytuj_podstrone.php" method="POST"><input type="hidden" name="idedytujpodstrone" value='.$id.'/><input type="submit" value="Edytuj" name="edit" /></form></th>
        <th><form method="post"><input type="hidden" name="usunpodstrone" value='.$id.'/><input type="submit" value="Usuń" name="usun"/></form></th>
        </tr>';
    }   
    // odpowiada za przycisk usuwania podstrony z BD
    if(isset($_POST['usun'])){
        $idus = $_POST['usunpodstrone'];
        $stmt = $conn->prepare("DELETE FROM page_list WHERE id=? LIMIT 1");
        $stmt->bind_param('i',$idus);
        $stmt->execute();
            if (empty(!$stmt)) {
                echo "<script> window.location.href='login-success.php';</script>";
              } else {
                echo "Wystąpił błąd podczas usuwania: ";
              }
    }
    ?>
</table>
</div>
<div class="center"> 
    <p id=2>Lista kategorii</p>
    <form action="dodaj_kategorie.php">
        <input type="submit" value="Dodaj nową kategorię" />
    </form>
    <table class="center2">
            <tr>
                <th>id</th>
                <th>matka</th>
                <th>kategoria</th>
                <th>podkategoria</th>
                <th>Edytuj</th>
                <th>Usuń</th>
            </tr>
        <?php
        // wyswietla kategorie znajdujace sie w BD
         $stmt2 = $conn->prepare("SELECT * FROM kategorie where matka='0' LIMIT 100"); // wysyla zapytanie do BD
         $stmt2->execute();
         $result2 = $stmt2->get_result();
        
         while($page2 = $result2->fetch_assoc()){
            $id = $page2['id'];
            $matka = $page2['matka'];
            $nazwa = $page2['nazwa'];
           // formularz kategorii
            echo '
            <tr>
                <th>id:'.$id.'</th>
                <th></th>
                <th>'.$nazwa.':</th>
                <th> </th>
                <th><form action="edytuj_kategorie.php" method="POST"><input type="hidden" name="idedytujkategorie" value='.$id.'/><input type="submit" value="Edytuj" name="edytujkategorie" /></form></th>
                <th><form method="post"><input type="hidden" name="idusunkategorie" value='.$id.'/><input type="submit" value="Usuń" name="usunkategorie"/></form></th>
            </tr>
            ';
            // wyswietla podkategorie znajdujace sie w BD
            $stmt3 = $conn->prepare("SELECT * FROM kategorie where matka=$id LIMIT 100"); // wysyla zapytanie do BD
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            while($page3 = $result3->fetch_assoc()){
                $id2 = $page3['id'];
                $matka2 = $page3['matka'];
                $nazwa2 = $page3['nazwa'];
                // formularz wyswietlajacy podkategorie
                echo '
                <tr>
                    <th>id:'.$id2.'</th>
                    <th>matka:'.$matka2.'</th>
                    <th>----</th>
                    <th>'.$nazwa2.'</th>
                    <th><form action="edytuj_kategorie.php" method="POST"><input type="hidden" name="idedytujkategorie" value='.$id2.'/><input type="submit" value="Edytuj" name="edytujkategorie" /></form></th>
                    <th><form method="post"><input type="hidden" name="idusunkategorie" value='.$id2.'/><input type="submit" value="Usuń" name="usunkategorie"/></form></th>
                </tr>
                ';
            }
         }
         // odpowiada za przycisk usuwania kategorii/podkategorii z BD
         if(isset($_POST['usunkategorie'])){
            $iduskat = $_POST['idusunkategorie'];
            $stmt = $conn->prepare("DELETE FROM kategorie WHERE id=? LIMIT 1");
            $stmt->bind_param('i',$iduskat);
            $stmt->execute();
                if (empty(!$stmt)) {
                    echo "<script> window.location.href='login-success.php';</script>";
                  } else {
                    echo "Wystąpił błąd podczas usuwania: ";
                  }
        }
        ?>
    </table>
</div>
<div class="center"> 
    <p id=3>Lista produktów</p>
    <form action="dodaj_produkt.php">
        <input type="submit" value="Dodaj nowy produkt" />
    </form>
    <table class="center2">
        <tr>
            <th>Tytuł</th>
            <th>Kategoria</th>
            <th>Cena netto</th>
            <th>Status dostępności</th>
            <th>Gabaryt produktu</th>
            <th>Edytuj</th>
            <th>Usuń</th>
        </tr>
        <?php
        // wyswietla liste produktow
        $stmt4 = $conn->prepare("SELECT * FROM produkty LIMIT 100"); // wysyla zapytanie do BD
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        
        while($page4 = $result4->fetch_assoc()){
            $idprod = $page4['id'];
            $tytul = $page4['tytul'];
            $kategoria = $page4['kategoria'];
            $cena_netto = $page4['cena_netto'];
            $data_wygasniecia= $page4['data_wygasniecia'];
            if($data_wygasniecia> date("Y-m-d")){
                $status_dostepnosci = $page4['status_dostepnosci'];
            }else{
                $status_dostepnosci = 'Wygaslo';
            }
            $gabaryt_produktu = $page4['gabaryt_produktu'];
            // formularz odpowiedzialny za wyswietlanie listy produktow
            echo '
            <tr>
                <th>'.$tytul.'</th>
                <th>'.$kategoria.'</th>
                <th>'.$cena_netto.'</th>
                <th>'.$status_dostepnosci.'</th>
                <th>'.$gabaryt_produktu.'</th>
                <th><form action="edytuj_produkt.php" method="POST"><input type="hidden" name="idedytujprodukt" value='.$idprod.'/><input type="submit" value="Edytuj" name="edytujprodukt" /></form></th>
                <th><form method="post"><input type="hidden" name="idusunprodukt" value='.$idprod.'/><input type="submit" value="Usuń" name="usunprodukt"/></form></th>
            </tr>
            ';
        }
        // odpowiada za przycisk usuwania produktu z BD
        if(isset($_POST['usunprodukt'])){
            $idusprod = $_POST['idusunprodukt'];
            $stmt = $conn->prepare("DELETE FROM produkty WHERE id=? LIMIT 1");
            $stmt->bind_param('i',$idusprod);
            $stmt->execute();
                if (empty(!$stmt)) {
                    echo "<script> window.location.href='login-success.php';</script>";
                  } else {
                    echo "Wystąpił błąd podczas usuwania: ";
                  }
        }
        
        ?>
    </table>
</div>
<div>
    <br><br><br><br><br><br>
</div>
