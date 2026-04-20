<?php

    include 'coneccion.php';
    $nombreUser=$_POST['nameUser'];
    $secondNameUser=$_POST['secondNameUser'];
    $tipoId=$_POST['tipoId'];
    $idUser = $_POST['idUser'];
    $fechaNacimientoUsr = $_POST['fechaNacimientoUsr'];
    $generoUser = $_POST['generoUser'];
    $emailUser = $_POST['emailUser'];
    $passwordUser = password_hash($_POST['passwordUser'], PASSWORD_DEFAULT);
    $repeatPasswordUser = $_POST['repeatPasswordUser'];
    $telUser = $_POST['telUser'];
    $rolUser = $_POST['rolUser'];
    
    

    $sql = "INSERT INTO usuario (nameUser, secondNameUser, tipoId,
        idUser, fechaNacimientoUsr, generoUser,
        emailUser, passwordUser, telUser, rolUser)
    VALUES ('$nombreUser', '$secondNameUser', '$tipoId',
     '$idUser', '$fechaNacimientoUsr', '$generoUser',
     '$emailUser','$passwordUser', '$telUser', '$rolUser')";

    if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
    } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $sql = "SELECT nameUser, idUser FROM usuario";
    // Execute the SQL query
    $result = $conn->query($sql);

    // Process the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "name: " . $row["nameUser"]. " - id: " . $row["idUser"]. "<br>";
        }
    } else {
    echo "0 results";
    }

    //crear update

    $conn->close();

?>