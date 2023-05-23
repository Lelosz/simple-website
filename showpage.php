<?php

require('cfg.php'); // wymaga polaczenia z BD
/*na podstawie otrzymanego ID podstrony najpierw pobiera o niej dane z BD,
a potem je wyswietla
*/
function PokazPodstrone($id)
{
    $id = htmlspecialchars($id);
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM page_list WHERE id=? LIMIT 1"); //zapytanie do BD o wybrana podstrone
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (empty($result)){
        echo "Nie ma wynikow";
    }
    while($data = $result->fetch_assoc()){ // wyswietla wszystkie otrzymane wyniki(w tym przypadku jeden konkretny)
        echo $data['page_content'];
    }
}
?>