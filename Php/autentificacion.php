<?php
    require_once '../includes/sesiones.php';
    iniciarSesion();
    include 'coneccion.php';
    $emailUser=$_POST['emailUser'];
    $passwordUser=$_POST['passwordUser'];


    $sql="SELECT emailUser, passwordUser, rolUser 
        FROM usuario 
        WHERE emailUser = '$emailUser'";
    
    $result = $conn->query($sql);
    // Process the result set
    if ($result->num_rows > 0) {
        echo "debug";
        // Output data of each row
        while($row = $result->fetch_assoc()) {

            if (!password_verify($passwordUser, $row["passwordUser"])){
                $_SESSION['error_login'] = 'Correo o contraseña incorrectos. Intenta de nuevo.';
                header('Location: ../Html/login.php');
                exit;
            }

            echo $row["emailUser"];
            echo "email: " . $row["emailUser"] . " - contraseña: " . $row["passwordUser"] . $row['rolUser']. "<br>";
             
            error_log("usuario: ". $nombreUser." rol: ". $row['rolUser']. " inicio sesion");
            switch($row['rolUser']){
                case 'doctor':
                    echo "eres un doctor";
                    break;
                case 'admin':
                    $_SESSION['usuario'] = $emailUser;
                    $_SESSION['nombre']  = $nombreUser;
                    $_SESSION['rol']     = 'admin';
                    $_SESSION['id']      = $idUser;
                    header("Location: ../index.php");
                    exit;
                case 'recep':
                    $_SESSION['usuario'] = $emailUser;
                    $_SESSION['nombre']  = $nombreUser;
                    $_SESSION['rol']     = 'recepcionista';
                    $_SESSION['id']      = $idUser;
                    header("Location: ../index.php");
                    exit;
            }
        }
    } else {
        $_SESSION['error_login'] = 'Correo o contraseña incorrectos. Intenta de nuevo.';
        header('Location: ../Html/login.php');
        exit;
    }

    $conn->close();
?>