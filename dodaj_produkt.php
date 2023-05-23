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
// formularz dodawania produktu
echo'
    <div class="center">
    <h1>Dodaj produkt</h1>

    <form method="post" name="ProductForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
        Tytuł<br><input type="text" name="tytul" size="50" required/> <br><br>
        Opis<br><textarea name="opis" rows="5" cols="50" required></textarea><br><br>
        Data wygaśnięcia<br><input type="date" name="data_wygasniecia" size="50" required/> <br><br>
        Cena netto<br><input type="text" name="cena_netto" size="50" required/> <br><br>
        Podatek vat<br><input type="text" name="podatek_vat" size="50" required/> <br><br>
        Dostepne sztuki<br><input type="text" name="dostepne_sztuki" size="50" required/> <br><br>
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
                echo '<option value='.$val2.'>'.$val1.' --> '.$val2.'</option>';
            }
        }
        echo'
        </select><br><br>
        Gabaryt produktu<br>
        <select name="gabaryt_produktu">
            <option value="">--- Wybierz gabaryt produktu ---</option>
            <option value="maly">Mały</option>
            <option value="sredni">Średni</option>
            <option value="duzy">Duży</option>
        </select><br><br>
        Zdjęcie<br>
        <div class="form-group">
            <input class="form-control" type="file" name="uploadfile" value="" required/>
        </div><br><br>
        <input type="submit" value="Zatwierdź" name="sub"/>

    </form>
    <form method="post">
    <input type="submit" value="Powrót" name="sub2"/>
    </form>
    </div>
    ';

// Dodaje nowa strone i zwraca ew. bledy
if(isset($_POST["sub"])){
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "./image/" . $filename;

    $data_utworzenia = date("Y-m-d");
    $data_modyfikacji = date("Y-m-d");

    if($_POST["dostepne_sztuki"]>0 ){
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
    $stmt = $conn->prepare("INSERT INTO produkty (tytul,opis,data_utworzenia,data_modyfikacji,data_wygasniecia,cena_netto,podatek_vat,dostepne_sztuki,status_dostepnosci,kategoria,gabaryt_produktu,zdjecie)
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?) LIMIT 1");
    $stmt->bind_param("ssssssssssss",$tytul,$opis,$data_utworzenia,$data_modyfikacji,$d_wygas,$cena_netto,$podatek_vat,$dostepne_sztuki,$status_dostepnosci,$kategoria,$gabaryt_produktu,$filename);
    $stmt->execute();
    if(empty(!$stmt)){
        echo "Dodano nowy produkt";
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