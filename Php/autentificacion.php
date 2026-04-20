<?php
    include 'coneccion.php';
    $emailUser=$_POST['emailUser'];
    $passwordUser=$_POST['passwordUser'];


    $sql="SELECT emailUser, passwordUser, rolUser 
        FROM usuario 
        WHERE emailUser = '$emailUser' AND passwordUser = '$passwordUser'";
    
    $result = $conn->query($sql);
    // Process the result set
    if ($result->num_rows > 0) {
        echo "debug";
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo $row["emailUser"];
            echo "email: " . $row["emailUser"] . " - contraseña: " . $row["passwordUser"] . $row['rolUser']. "<br>";
             
            require_once '../includes/sesiones.php';
            iniciarSesion();
            switch($row['rolUser']){
                case 'doctor':
                    echo "eres un doctor";
                    break;
                case 'admin':
                    $_SESSION['usuario'] = $emailUser;
                    $_SESSION['nombre']  = $nombreUser;
                    $_SESSION['rol']     = 'admin';
                    $_SESSION['id']      = $idUser;
                    header("Location: ../dashboard/dashboard_admin.php");
                    exit;
            }
        }
    } else {
    echo "0 results";
    }

    $conn->close();
?>