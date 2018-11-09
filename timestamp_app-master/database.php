<?php

define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "root");
define("DB_NAME", "timestamp_app");


function db_connect() {
    $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if($connection) {
        //
    } else {
        //
    }
    return $connection;
}
function db_disconnect() {
    if(isset($connection)) {
        mysqli_close($connection);
    }
}

?>
