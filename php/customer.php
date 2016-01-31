<?php

include "person-class.php";

$Pers = new $Person;
$Pers->GetPerson();

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die($conn->connect_error);