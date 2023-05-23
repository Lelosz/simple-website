<?php
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	include('showpage.php'); // odpowiada za wyswietlanie podstron
	include('./admin/admin.php'); // umozliwia funkcje logowania do panelu CMS
	include('contact.php'); // zawiera funkcje dotyczace przypominania hasel/maili kontaktowych

?>
<head>
<?php PokazPodstrone('6'); // wyswietla head 
?>

</head>
<body onload="showtime();" onload="kolorujtlo();">
<div id="container">
	<div class="rectangle"><a href="index.php" class="link">Home</a></div>
    <div class="rectangle"><a href="index.php?idp=zaloguj" class="link">Zaloguj</a></div>
    <div class="rectangle"><a href="index.php?idp=copyright" class="link">Copyright</a></div>
    <div class="rectangle"><a href="index.php?idp=ciekawostki" class="link">Ciekawostki</a></div>
    <div class="rectangle"><a href="index.php?idp=wiki" class="link">Wiki</a></div>
    <div class="rectangle"><a href="index.php?idp=kontakt" class="link">Kontakt</a></div>
	<div class="rectangle"><a href="index.php?idp=filmy" class="link">Filmy</a></div>
	<div class="rectangle"><a href="koszyk.php" class="link">Produkty</a></div>
    <div style="clear:both;">
    <?php 
	// na podstawie nazwy strony wyznacza jej ID
	$strona = $_GET['idp'];
	if($_GET['idp'] == '') $strona = '5'; 
	if($_GET['idp'] == 'zaloguj') $strona = '9';
	if($_GET['idp'] == 'ciekawostki') $strona = '1'; 
	if($_GET['idp'] == 'copyright') $strona = '2'; 
	if($_GET['idp'] == 'kontakt') $strona = '7'; 
	if($_GET['idp'] == 'wiki') $strona = '8'; 
	if($_GET['idp'] == 'zresetujhaslo') $strona = '10'; 
	if($_GET['idp'] == 'filmy') $strona = '3'; 
	PokazPodstrone($strona); // wyswietla wybrana podstrone
	// odpowiada za nie powtarzanie sie strony glownej i nie wyswietlanie jej w przypadku gdy podstrona zajmuje
	// znacza czesc ekranu
	if($_GET['idp'] != 'filmy' and $_GET['idp']!='zaloguj' and $_GET['idp']!='kontakt' and $_GET['idp']!=''){
		PokazPodstrone('5'); // wyswietla strone glowna
	}
	
	Logowanie(); // umozliwia dzialanie logowania do panelu CMS
	if(isset($_POST["x2_submit"])){ // umozliwia dzialanie funkcji przypominania hasla do panelu CMS
		PokazKontaktHaslo();} // wyswietla formularz do przypominania hasla panelu CMS
	if(isset($_POST["sendpass"])){PrzypomnijHaslo();} // umozliwia wyslanie maila z przypomnieniem hasla do panelu CMS
	if(isset($_POST["sendinfmail"])){WyślijMailKontaktowy();} // umozliwia wyslanie maila kontaktowego
	?>
	</div>
</div>
<?php PokazPodstrone('4');// wyswietla footer ?> 
</body>
