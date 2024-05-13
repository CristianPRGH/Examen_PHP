<?php
  $err = "";
  if (isset($_POST["formSubmit"]))
  {
    $username = $_POST["user_Username"];
    $password = $_POST["user_Password"];
    

    $err = ValidateUserExists($username, $password);

    if ($err === "OK")
    {

      // SESSION
      session_start([
        "use_only_cookies"  =>1,
        "cookie_lifetime"   =>0,
        "cookie_secure"     =>1,
        "cookie_httponly"   =>1
      ]);

      
      $_SESSION["user_id"] = 1;
      $_SESSION["username"] = $username;
      
      // COOKIE
      if (isset($_POST["rememberme"]))
      {
        $cookie_name = "remember_me";
        $cookie_value = 1;
        $cookie_expire_time = time() + (24*3600);

        setcookie($cookie_name, $cookie_value, $cookie_expire_time);
      }

      // REDIRECT
      header("Location: Home.php");
    }
  }

  function ValidateUserExists($username, $password)
  {
    global $err;
    $usersTable = "../Tables/Users.csv";

    if (($res = fopen($usersTable, 'r')) !== false)
    {
        while (($user = fgetcsv($res)) !== false)
        {
            $codedPassword = sha1(md5($password));
  
            if ($user[0] == $username && $user[5] == $codedPassword)
            {
                return "OK";
                // break;
            }
        }
  
        fclose($res);
    }
  
    if ($err == "")
    {
        return "El nombre de usuario o la contraseña son incorrectos";
    }
  }

 
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="../css/MainStyles.css" />
  <link rel="stylesheet" href="../css/FormsStyles.css" />

  <title>Login</title>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      let toRegister = document.getElementById("userRegister");

      toRegister.addEventListener("click", () => window.location.href = "Register.php");
    })
  </script>  

</head>

<body>
  <div class="flexColumn formContainers">
    <div class="flexColumn formWindows">
      <span><p id="formType">sign-in</p></span>
      <h1>LOGIN</h1>
      <hr class="formLines">
      <form id="signinForm" class="forms flexColumn" method="post" action="">
        <fieldset id="signFieldset" class="flexColumn fieldsets">
          
          <div class="flexRow formData">
            <label for="usuario" class="formLabels">Usuario</label>
            <div class="flexColumn">
              <input type="text" id="usuario" name="user_Username" class="formInputs"/>
            </div>
          </div>

          <div class="flexRow formData">
            <label for="password" class="formLabels">Password</label>
            <div class="flexColumn">
              <input type="password" id="password" name="user_Password" class="formInputs"/>
            </div>
          </div>

          <div>
            <input type="checkbox" name="rememberme"> Remember me
          </div>

          <input type="submit" id="formSubmit" name="formSubmit" class="formButtons pointer" value="SIGN-IN">
          <input type="button" id="userRegister" name="userRegister" class="formButtons pointer" value="SIGN-UP">

          <p id="err-login"  class="formErrors"><?php echo $err ?></p>
        </fieldset>
      </form>
    </div>
  </div>
</body>

<!-- <script src="../js/LoginManager.js"></script> -->

</html>