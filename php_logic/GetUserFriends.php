<?php
session_start();

$user = $_SESSION["username"];
$tableFriends = "../tables/amigos.csv";
$tableUsers = "../tables/users.csv";
$friendsList = array("misamigos"=>array(), "sugeridos"=>array());
$suggestedFriends = array();

$fname = "";
if (isset($_GET["name"])) $fname = $_GET["name"];
if (isset($_GET["surname"])) $fsurname = $_GET["surname"];

if (($res = fopen($tableFriends, 'r')) !== false)  // ABRE LA TABLA DE AMIGOS
{
    while (($friend = fgetcsv($res)) !== false) // RECORRE LA TABLA DE AMIGOS
    {
        if ($friend[0] == $user)    // VERIFICA QUE EL USUARIO CONECTADO ES IGUAL AL DE LA TABLA
        {
            if ( (strlen($fname) > 0 && str_contains(strtolower($friend[1]), strtolower($fname))) // FILTRO POR NOMBRE
              || (strlen($fsurname) > 0 && str_contains(strtolower($friend[2]), strtolower($fsurname))) // FILTRO POR APELLIDOS
              || strlen($fname) == 0 && strlen($fsurname) == 0) // NO HAY FILTROS
            {
                array_push($friendsList["misamigos"], $friend);
            }
        }
    }

    fclose($res);
}

$myFriendsNames = array();
for ($i=0; $i < count($friendsList["misamigos"]); $i++) { 
    array_push($myFriendsNames,$friendsList["misamigos"][$i][1]);
}


if ( ($res = fopen($tableFriends,'r')) !== false ) // ABRE LA TABLA DE AMIGOS
{        
    while( ($friend = fgetcsv($res)) !== false )
    {
        foreach ($myFriendsNames as $key => $value)
        {
        // echo strtoupper($value);
        

            // print_r($friend[0]);

            // echo $value." = ".$friend[0]."  //  ";

            if ($value == $friend[0])
            {
                array_push($friendsList["sugeridos"], $friend);
            }
        }
    }

    fclose($res);
}

// array_push($friendsList["sugeridos"], $suggestedFriends);
// print_r($friendsList);


header("Content-Type: application/json");
echo json_encode($friendsList);