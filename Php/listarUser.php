<?php

    include 'coneccion.php';

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