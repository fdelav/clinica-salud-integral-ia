<?php

    include 'coneccion.php';
    $countText=$_POST['countText'];
    $camposFormulario=$_POST['camposFormulario'];
    $newValue=$_POST['newValue'];
    echo $countText.$camposFormulario.$newValue;

    $sql = "UPDATE usuario set $camposFormulario='$newValue' where cont=$countText;";

    if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
    } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }

    echo "los nuevos valores son: "."<br>";

    $sql = "SELECT nameUser, secondNameUser, tipoId,
	        idUser, fechaNacimientoUsr, generoUser,
	        emailUser, passwordUser, telUser
        FROM usuario where cont=$countText;";
    // Execute the SQL query
    $result = $conn->query($sql);

    // Process the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<b>Usuario:</b> " . $row["nameUser"] . " " . $row["secondNameUser"] . "<br>";
            echo "<b>ID:</b> " . $row["tipoId"] . " " . $row["idUser"] . "<br>";
            echo "<b>Fecha Nacimiento:</b> " . $row["fechaNacimientoUsr"] . "<br>";
            echo "<b>Género:</b> " . $row["generoUser"] . "<br>";
            echo "<b>Email:</b> " . $row["emailUser"] . "<br>";
            echo "<b>Teléfono:</b> " . $row["telUser"] . "<br>";
        }
    } else {
    echo "0 results";
    }

?>