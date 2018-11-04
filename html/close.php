<?php

include "/srv/cordmon/src/Socket.php";

error_reporting(E_ALL);

ob_implicit_flush();

echo "THIS IS A TEST\n";


$server = new Socket;

$client = new Socket;

socket_close($server->socket);
socket_close($server->spawn);
