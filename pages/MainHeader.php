<?php
    if (isset($_POST["logoutBttn"]))
    {
        session_destroy();
        header("Location: Login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/HeaderStyles.css">
    <link rel="stylesheet" href="../css/MainStyles.css">
</head>
<body>
    <div class="header-main">
        <div class="header-content flexRow">
            <img class="logoImg" src="../images/default_icon.png">
            <h1>RED SOCIAL</h1>
            
            <form method="post" action="" class="flexRow">
                <div class="dropdown">
                    <p class="header-username"><?= $_SESSION["username"] ?></p>
                    <div class="dropdown-content">
                        <p class="navItem pointer" onclick="OpenModal('newpost')">> Nuevo post</p>
                        <p class="navItem pointer" onclick="GetFriends()">> Amigos</p>
                    </div>
                </div>
                <button name="logoutBttn" class="logoutBttn pointer">LOGOUT</button>
            </form>

        </div>
    </div>
</body>
</html>