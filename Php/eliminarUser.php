<?php

    include 'coneccion.php';

    $sql ="DELETE from usuario where cont=2";

    $result = $conn->query($sql);

    if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
    } else {
    echo "Error deleting record: " . $conn->error;
    }

    $conn->close();

?>