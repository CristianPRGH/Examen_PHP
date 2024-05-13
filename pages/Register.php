<?php

  $usersTable = "../Tables/users.csv";
  $err = "";
  $errors = 0;

  
  $username   = "";
  $name       = "";
  $surnames   = "";
  $birthdate  = "";
  $email      = "";
  $password   = "";
  $password2  = "";

  $dataErrors = ResetData();
  function ResetData(){
    return array(
      "errUsername"   =>"",
      "errName"       =>"",
      "errSurnames"   =>"",
      "errBirthdate"  =>"",
      "errEmail"      =>"",
      "errPassword"   =>"",
      "errPassword2"  =>""
    );
  }

  // header("Location: ../pages/Login.html");

  if (isset($_POST["signupSubmit"]))
  {
      $username   = $_POST["user-Username"];
      $name       = $_POST["user-Name"];
      $surnames   = $_POST["user-Surnames"];
      $birthdate  = $_POST["user-Birthdate"];
      $email      = $_POST["user-Email"];
      $password   = $_POST["user-Password"];
      $password2  = $_POST["user-Password2"];

      
      $userData = array(
          "username"  => $username,
          "name"      => $name,
          "surnames"  => $surnames,
          "birthdate" => date("d-m-Y",strtotime($birthdate)),
          "email"     => $email,
          "password"  => $password, //sha1(md5($password))
          "password2" => $password2
      );

      
      $result = ValidateData($userData);
      foreach ($result as $key => $value)
      {
          if ($value != "") $errors++;
      }

      if ($errors == 0) $result = ValidateUserExists($userData);
      if ($errors == 0 && $result) RegisterUser($userData);

  }


  function ValidateUserExists($data)
  {
      global $usersTable;
      $err = "";

      if ( ($res = fopen($usersTable, 'r')) !== false)
      {
          while ( ($user = fgetcsv($res)) !== false )
          {
              if (strtolower($user[0]) === strtolower($data["username"]))   // Valida si nombre de usuario existe en la BBDD
              {
                  $err = "El usuario ya existe";
                  break;
              }
          }

          fclose($res);
          if ($err !== "") return false; else return true;
      }
  }

  function RegisterUser($data)
  {

      header("Location: Login.php");
      global $usersTable, $err;

      if ($err == "")
      {
          if ( ($res = fopen($usersTable, 'a')) !== false)
          {
              $data["password"] = sha1(md5($data["password"]));
              $data["password2"] = null;
              fputcsv($res, $data);
              fclose($res);
              $err = "OK";
          }
      }
  }


  function ValidateData($data)
  {
      global $dataErrors;

      foreach ($data as $key => $value) {
          if ($key === "username")    $dataErrors["errUsername"]  = ValidateLettersAndNumbers($value);
          if ($key === "name")        $dataErrors["errName"]      = ValidateOnlyLetters($value);
          if ($key === "surnames")    $dataErrors["errSurnames"]  = ValidateOnlyLetters($value);
          if ($key === "email")       $dataErrors["errEmail"]     = ValidateEmail($value);
          if ($key === "birthdate")   $dataErrors["errBirthdate"] = ValidateDate($value);
          if ($key === "password")    $dataErrors["errPassword"]  = ValidatePassword($value);
          if ($key === "password2")   $dataErrors["errPassword2"] = ValidateRepeatedPasswords($data["password"], $value);
      }

      return $dataErrors;
  }

  function ValidateOnlyLetters($value)
  {
      $regexOnlyLetters = "/^[a-zA-Z]+$/";
      if (!preg_match($regexOnlyLetters, $value)) return "Solo puede contener letras";
  }

  function ValidateLettersAndNumbers($value)
  {
      $regexLettersNumbers = "/^[a-zA-Z0-9]+$/";
      if (!preg_match($regexLettersNumbers, $value)) return "El nombre de usuario solo puede contener letras y números";
  }

  function ValidateEmail($value)
  {
      $regexEmail = '/^\\S+@\\S+\\.\\S+$/';
      if (!preg_match($regexEmail, $value)) return "El email no tiene el formato correcto";
  }

  function ValidateDate($value)
  {
      // $dateFormat = explode('-', $value);

      // if (!checkdate(intval($dateFormat[0]), intval($dateFormat[1]), intval($dateFormat[2]))) return "La fecha no es válida";

      
      $inputdate = new DateTime($value);
      $currentDate = new DateTime();
      $diff = $inputdate->diff($currentDate);
      $edad = $diff->y;


      if ($edad < 18) return "Debes ser mayor de edad";
  }

  function ValidatePassword($value)
  {
      $level = 0;
      $level += strlen($value) < 6 || strlen($value) > 8 ? 1 : 0;   // Longitud mayor a 6 caracteres
      $level += !preg_match("/[!@#$%^&*?_~]{1,}/", $value) ? 1 : 0;    // Contiene al menos 1 caracteres especiales
      $level += !preg_match("/[a-z]{1,}/", $value) ? 1 : 0;            // Contiene al menos 1 letras minúsculas
      $level += !preg_match("/[A-Z]{1,}/", $value) ? 1 : 0;            // Contiene al menos 1 letras mayúsculas
      $level += !preg_match("/[0-9]{1,}/", $value) ? 1 : 0;            // Contiene al menos 1 números

      if ($level > 0) 
      {
        $passerror = "La contraseña debe:  <br>
                    - tener entre 6 y 8 caracteres <br>
                    - contener al menos 1 caracter especial <br>
                    - contener al menos 1 letra minúscula <br>
                    - contener al menos 1 letra mayúscula
                    - contener al menos 1 número";
        return $passerror;
      }

      // return $level;
  }

  function ValidateRepeatedPasswords($pass1, $pass2)
  {
      if (strcmp($pass1,$pass2) !== 0) return "Las contraseñas no coinciden";
  }

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="../css/MainStyles.css" />
  <link rel="stylesheet" href="../css/FormsStyles.css" />

  <title>Registro</title>
</head>

<body>
  <div class="flexColumn formContainers">
    <div class="flexColumn formWindows">
      <span><p id="formType">sign-up</p></span>
      <h1>REGISTRO</h1>

      <hr class="formLines">

      <form id="signupForm" class="forms flexColumn" method="post" action="">
        <fieldset id="loginFieldset" class="flexColumn fieldsets">
          
          <div class="flexColumn formData">
            <!-- <label for="usuario" class="formLabels">Usuario <span class="required">*</span></label> -->
              <input type="text" id="usuario" name="user-Username" class="formInputs" placeholder="NOMBRE DE USUARIO *" value="<?php echo $username; ?>" required/>
              <p id="err-username"  class="formErrors"><?php echo $dataErrors["errUsername"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="nombre" class="formLabels">Nombre <span class="required">*</span></label> -->
              <input type="text" id="nombre" name="user-Name" class="formInputs" placeholder="NOMBRE *" value="<?php echo $name; ?>" required/>
              <p id="err-name"  class="formErrors"><?php echo $dataErrors["errName"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="apellidos" class="formLabels">Apellidos <span class="required">*</span></label> -->
              <input type="text" id="apellidos" name="user-Surnames" class="formInputs" placeholder="APELLIDOS *" value="<?php echo $surnames; ?>" required/>
              <p id="err-surnames"  class="formErrors"><?php echo $dataErrors["errSurnames"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="fechanacimiento" class="formLabels">Fecha de Nacimiento <span class="required">*</span></label> -->
              <input type="date" id="fechanacimiento" name="user-Birthdate" class="formInputs" value="<?php echo $birthdate; ?>" required/>
              <p id="err-birthdate"  class="formErrors"><?php echo $dataErrors["errBirthdate"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="email" class="formLabels">Email <span class="required">*</span></label> -->
              <input type="email" id="email" name="user-Email" class="formInputs" placeholder="EMAIL *" value="<?php echo $email; ?>" required/>
              <p id="err-email"  class="formErrors"><?php echo $dataErrors["errEmail"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="password" class="formLabels">Password <span class="required">*</span></label> -->
              <input type="password" id="password" name="user-Password"  class="formInputs" placeholder="CONTRASEÑA *" value="<?php echo $password; ?>" required/>
              <p id="err-password" class="formErrors"><?php echo $dataErrors["errPassword"]; ?></p>
          </div>

          <div class="flexColumn formData">
            <!-- <label for="rpassword" class="formLabels">Repite Password <span class="required">*</span></label> -->
            <input type="password" id="rpassword" name="user-Password2"  class="formInputs" placeholder="REPITE CONTRASEÑA *" required/>
            <p id="err-password" class="formErrors"><?php echo $dataErrors["errPassword2"]; ?></p>
          </div>

          <input type="submit" id="signupSubmit" name="signupSubmit" class="formButtons pointer" value="ENVIAR">

          <p id="err-login" class="formErrors"></p>
        </fieldset>
      </form>
    </div>
  </div>
</body>


</html>