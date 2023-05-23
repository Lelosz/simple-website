<?php
include('showpage.php'); // odpowiada za wyswietlanie podstron
include('./admin/admin.php'); // umozliwia funkcje logowania do panelu CMS
include('contact.php'); // zawiera funkcje dotyczace przypominania hasel/maili kontaktowych
require("cfg.php"); // wymaga polaczenia z BD
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>
<head>
<?php PokazPodstrone('6'); // wyswietla head 
?>
</head>
<body onload="showtime();" onload="kolorujtlo();">
    <div id="container">
        <div class="rectangle"><a href="index.php" class="link">Powrót</a></div>
    </div><br><br>
    <div id="container" class="center">

        <h1>Dodaj produkt do koszyka</h1>
        
        <table style="margin-left:auto;margin-right:auto;">
            <tr>
                <th>Produkt</th>
                <th>Cena</th>
                <th>Zdjecie</th>
                <th>Ilość</th>
            </tr>

                <?php
                // odpowiada za wyswietlanie dostepnych produktow w BD
                $stmt4 = $conn->prepare("SELECT * FROM produkty LIMIT 100"); // wysyla zapytanie do BD
                $stmt4->execute();
                $result4 = $stmt4->get_result();
                
                while($page4 = $result4->fetch_assoc()){
                    $idprod = $page4['id'];
                    $stmt = $conn->prepare("SELECT * FROM produkty WHERE id=? LIMIT 1");
                    $stmt->bind_param("i",$idprod);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($page = $result->fetch_assoc()){
                        $zdjecie = $page["zdjecie"];
                    }
                    $tytulprod = $page4['tytul'];
                    $cena_netto = $page4['cena_netto'];
                    $vat = $page4['podatek_vat'];
                    $cena_brutto = round($cena_netto * $vat * 0.01+ $cena_netto,2);
                    $dostepne_szt = $page4['dostepne_sztuki'];
                    $status_dostepnosci = $page4['status_dostepnosci'];
                    $data_wygas = $page4['data_wygasniecia'];
                    $kategoria = $page4['kategoria'];
                    if($data_wygas>date("Y-m-d")&& $dostepne_szt>0){
                    echo'
                        <tr>
                            <th>'.$tytulprod.'</th>
                            <th>'.$cena_brutto.'zł</th>
                            <th><img src="./image/'; echo $zdjecie; echo'"></th>
                            <form action="" method="POST">
                            <th><input type="number" name="ilosc" value="1" min="1" max="'.$dostepne_szt.'"/></th>
                            <th><input type="hidden" name="idwybprod" value='; echo $idprod; echo'><input type="submit" value="Dodaj do koszyka" name="dodajprodukt" /></form></th>
                        </tr>';
                    }
                }
                ?>     
        </table>
        <?php
        // definiuje co sie dzieje w przypadku dodania produktu
        if(isset($_POST['dodajprodukt'])){
            $id_wyb_prod = htmlspecialchars($_POST['idwybprod']);
            $ilosc_prod = htmlspecialchars($_POST['ilosc']);
            if(!isset($_SESSION['count'])){
                $_SESSION['count'] = 1;
            } else{
                $_SESSION['count']++;
            }
            $nr = $_SESSION['count'];

            $prod[$nr]['id_prod'] = $id_wyb_prod;
            $prod[$nr]['ile_sztuk'] = $ilosc_prod;
            

            $nr_0 = $nr.'_0';
            $nr_1 = $nr.'_1';
            $nr_2 = $nr.'_2';

            $_SESSION[$nr_0] = $nr;
            $_SESSION[$nr_1] = $prod[$nr]['id_prod'];
            $_SESSION[$nr_2] = $prod[$nr]['ile_sztuk'];

            

        }
        ?>
        
        <?php
        // odpowiada za ukrywanie koszyka gdy jest on pusty, pokazuje koszyk gdy dodamy do niego jakis produkt
        if(isset($_POST['dodajprodukt'])|| isset($_SESSION['count'])){
            echo'<h1>Twój koszyk</h1>';
            $cena_calkowitakoszyka = 0;
            echo'
            <table style="margin-left:auto;margin-right:auto;">
                <tr>
                    <th>Produkt</th>
                    <th>Ilość</th>
                    <th>Zdjecie</th>
                    <th>Cena</th>
                    <th>Usuń</th>
                </tr>';
                
            $nr = 1;
            while($nr<=($_SESSION['count'])){
                $nr_0 = $nr.'_0';
                $nr_1 = $nr.'_1';
                $nr_2 = $nr.'_2';
                
                $idprodwkosz = $_SESSION[$nr_1];
                $stmt = $conn->prepare("SELECT * FROM produkty WHERE id=? LIMIT 1");
                $stmt->bind_param("i",$idprodwkosz);
                $stmt->execute();
                $result = $stmt->get_result();
                while($page = $result->fetch_assoc()){
                    $zdjecie = $page["zdjecie"];
                    $tytulprod = $page['tytul'];
                    $dostepne_szt = $page['dostepne_sztuki'];
                    $cena_netto = $page['cena_netto'];
                    $vat = $page['podatek_vat'];
                    $cena_brutto = round($cena_netto * $vat * 0.01+ $cena_netto,2);
                    $cena_danego_prod = $cena_brutto * $_SESSION[$nr_2];
                    if($_SESSION[$nr_2]>0){
                        echo '
                        <tr>
                            <th>'.$tytulprod.'</th>
                            <form action="" method="POST">
                            <th><input type="hidden" name="idprodwkoszdoed" value='; echo $_SESSION[$nr_0]; echo'><input type="number" name="iloscwkoszyku" value="'.$_SESSION[$nr_2].'" min="1" max="'.$dostepne_szt.'"/><input type="submit" value="Zatwierdz" name="edytujliczbeprodwkosz" /><form></th>
                            <th><img src="./image/'; echo $zdjecie; echo'"></th>
                            <th>'.$cena_danego_prod.'</th>
                            <form action="" method="POST">
                            <th><input type="hidden" name="idprodwkoszdous" value='; echo $_SESSION[$nr_0]; echo'><input type="submit" value="Usuń z koszyka" name="usunproduktzkoszyka" /></form></th>
                        </tr>
                        ';
                        $cena_calkowitakoszyka = $cena_calkowitakoszyka + ($cena_brutto *  $_SESSION[$nr_2]);
                    }

                }
                $nr++;
            }
            echo'
                <tr>
                    <th>
                    <form method="POST" action="">
                    <input type="submit" value="Wyczyść koszyk" name="clearcart" />
                    </form>
                    </th>
                    <th></th>
                    <th></th>
                    <th>Całkowita cena: '.$cena_calkowitakoszyka.'zł</th>
                    <th><input type="submit" value="Przejdź dalej" name="przejdzdalej" /></th>
                </tr>
                ';
        }
        // odpowiada za funkcjonalnosc edytowania liczby danego produktu w koszyku
        if(isset($_POST['edytujliczbeprodwkosz'])){
            $iloscprzedwkoszykupoedycji = $_POST['iloscwkoszyku'];
            $count_do_edycji = $_POST['idprodwkoszdoed'];
            $nr_0 = $count_do_edycji.'_0';
            $nr_1 = $count_do_edycji.'_1';
            $nr_2 = $count_do_edycji.'_2';
            $_SESSION[$nr_2] = $iloscprzedwkoszykupoedycji;
            echo "<script> window.location.href='koszyk.php';</script>";
        }
        // odpowiada za funkcjonalnosc usuwania produktu z koszyka
        if(isset($_POST['usunproduktzkoszyka'])){
            $id_prod_do_usuniecia = htmlspecialchars($_POST['idprodwkoszdous']);
            $nr_0 = $id_prod_do_usuniecia.'_0';
            $nr_1 = $id_prod_do_usuniecia.'_1';
            $nr_2 = $id_prod_do_usuniecia.'_2';
            $_SESSION[$nr_2] = '0';
            echo "<script> window.location.href='koszyk.php';</script>";
        }
        // odpowiada za czyszczenie calego koszyka
        if(isset($_POST['clearcart'])){
            unset($_SESSION['count']);
            echo "<script> window.location.href='koszyk.php';</script>";
        }
        ?>
        </table>
        
    </div>
<?php PokazPodstrone('4');// wyswietla footer ?> 
</body>