<?php
    require_once '../includes/sesiones.php';
    iniciarSesion();
    include 'coneccion.php';
    $emailUser=$_POST['emailUser'];
    $passwordUser=$_POST['passwordUser'];


    $sql="SELECT emailUser, passwordUser, nameUser, idUser, rolUser 
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
             
            error_log("usuario: ". $row["nameUser"]." rol: ". $row['rolUser']. " inicio sesion");

            $_SESSION['usuario'] = $row["emailUser"];
            $_SESSION['nombre']  = $row["nameUser"];
            $_SESSION['id']      = $row["cont"];

            switch($row['rolUser']){
                case 'doctor':
                    $_SESSION['rol']     = 'doctor';
                    break;
                case 'admin':
                    $_SESSION['rol']     = 'admin';
                    break;
                case 'recep':
                    $_SESSION['rol']     = 'recepcionista';
                    break;
                case 'paciente':
                    $_SESSION['rol']     = 'paciente';
                    break;
            }
            error_log(print_r($_SESSION));
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['error_login'] = 'Correo o contraseña incorrectos. Intenta de nuevo.';
        header('Location: ../Html/login.php');
        exit;
    }

    $conn->close();
?>