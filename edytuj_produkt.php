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
if(isset($_POST['edytujprodukt'])){
    $pgid = $_POST['idedytujprodukt'];
    $_SESSION['pgidn']= $pgid;
    $stmt = $conn->prepare("SELECT * FROM produkty WHERE id=? LIMIT 1");
    $stmt->bind_param("i",$pgid);
    $stmt->execute();
    $result = $stmt->get_result();
    while($page = $result->fetch_assoc()){
            $tytul = $page["tytul"];
            $opis = $page["opis"];
            $dat_wyg = $page["data_wygasniecia"];
            $cena_netto = $page["cena_netto"];
            $podatek_vat = $page["podatek_vat"];
            $dostepne_sztuki = $page["dostepne_sztuki"];
            $kategoria = $page["kategoria"];
            $gabaryt_produktu = $page["gabaryt_produktu"];
            $zdjecie = $page["zdjecie"];
        }
    // formularz edytowania produktu
    echo'
    <div class="center">
    <h1>Dodaj produkt</h1>

    <form method="post" name="ProductForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
        Tytuł<br><input type="text" name="tytul" size="50" value='.$tytul.' required/> <br><br>
        Opis<br><textarea name="opis" rows="5" cols="50" required>'.$opis.'</textarea><br><br>
        Data wygaśnięcia<br><input type="date" name="data_wygasniecia" size="50" value='.$dat_wyg.' required/> <br><br>
        Cena netto<br><input type="text" name="cena_netto" size="50" value='.$cena_netto.' required/> <br><br>
        Podatek vat<br><input type="text" name="podatek_vat" size="50" value='.$podatek_vat.' required/> <br><br>
        Dostepne sztuki<br><input type="text" name="dostepne_sztuki" value='.$dostepne_sztuki.' size="50" required/> <br><br>
        Kategoria<br>
        <select name="nazwa_kategorii">
        <option value="">--- Wybierz kategorie ---</option>';
        $stmt2 = $conn->prepare("SELECT * FROM kategorie where matka='0' LIMIT 100"); // wysyla zapytanie do BD
        $stmt2->execute();
        $result2 = $stmt2->get_result();
       
        while($page2 = $result2->fetch_assoc()){
            $val1 = $page2["nazwa"];
            $id1 = $page2["id"];

            $stmt3 = $conn->prepare("SELECT * FROM kategorie where matka=$id1 LIMIT 100"); // wysyla zapytanie do BD
            $stmt3->execute();
            $result3 = $stmt3->get_result();
           
            while($page3 = $result3->fetch_assoc()){
                $val2 = $page3["nazwa"];
                if($val2 == $kategoria){
                    echo '<option selected="selected" value='.$val2.'>'.$val1.' --> '.$val2.'</option>';
                }else{
                    echo '<option value='.$val2.'>'.$val1.' --> '.$val2.'</option>';
                }
            }
        }
        // druga czesc formularza edytowania produktu
        echo'
        </select><br><br>
        Gabaryt produktu<br>
        <select name="gabaryt_produktu">
        <option value="">--- Wybierz gabaryt produktu ---</option>';
        switch($gabaryt_produktu){
            case 'maly':
                echo '<option selected="selected" value="maly">Mały</option>
                <option value="sredni">Średni</option>
                <option value="duzy">Duży</option>';
                break;
            case 'sredni':
                echo '<option value="maly">Mały</option>
                <option selected="selected" value="sredni">Średni</option>
                <option value="duzy">Duży</option>';
                break;
            case 'duzy':
                echo '<option value="maly">Mały</option>
                <option value="sredni">Średni</option>
                <option selected="selected" value="duzy">Duży</option>';
            break;
        }
        //trzecia czesc formularza edytowania produktu
        echo'
        </select><br><br>
        Zdjęcie<br>
        <div >
            <input  type="file" name="uploadfile" value="" required/>
        </div>
        <div id="display-image">
        <img src="./image/'; echo $zdjecie; echo'">
        </div>
        <input type="submit" value="Zatwierdź" name="sub"/>

    </form>
    <form method="post">
    <input type="submit" value="Powrót" name="sub2"/>
    </form>
    </div>
    ';
}
$pgid = $_SESSION['pgidn'];
// wysyla dane z formularza do BD
if(isset($_POST["sub"])){
    
    
    $filename = $_FILES["uploadfile"]["name"];
    
    $data_utworzenia = date("Y-m-d");
    $data_modyfikacji = date("Y-m-d");

    if($_POST["dostepne_sztuki"]>0){
        $status_dostepnosci = "Dostepne";
    }else{
        $status_dostepnosci = "Brak";
    }

    $tytul= htmlspecialchars($_POST["tytul"]);
    $opis= htmlspecialchars($_POST["opis"]);
    $d_wygas= htmlspecialchars($_POST["data_wygasniecia"]);
    $cena_netto= htmlspecialchars($_POST["cena_netto"]);
    $podatek_vat= htmlspecialchars($_POST["podatek_vat"]);
    $dostepne_sztuki= htmlspecialchars($_POST["dostepne_sztuki"]);
    $kategoria= htmlspecialchars($_POST["nazwa_kategorii"]);
    $gabaryt_produktu= htmlspecialchars($_POST["gabaryt_produktu"]);
    $stmt = $conn->prepare("UPDATE produkty SET tytul=? ,opis=? ,data_utworzenia=? ,data_modyfikacji=? ,data_wygasniecia=? ,cena_netto=?,podatek_vat=? ,dostepne_sztuki=? ,status_dostepnosci=? ,kategoria=?,gabaryt_produktu=? ,zdjecie=? WHERE id=? LIMIT 1");
    $stmt->bind_param("ssssssssssssi",$tytul,$opis,$data_utworzenia,$data_modyfikacji,$d_wygas,$cena_netto,$podatek_vat,$dostepne_sztuki,$status_dostepnosci,$kategoria,$gabaryt_produktu,$filename,$pgid);
    $stmt->execute();
    if(empty(!$stmt)){
        header("Location: login-success.php#3");
    }
    else{
        echo "Nie udalo się dodać nowego produktu";
    }
}
// sprawdza stan aktywacji przycisku powrotu do poprzedniej strony
if(isset($_POST["sub2"])){
    header("Location: login-success.php#3");
}
?>