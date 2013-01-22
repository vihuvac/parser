<?php

$dbh = 'mysql:dbname=parsing;host=localhost';
$username = 'dev';
$password = 'dev';

try {
    $gbd = new PDO($dbh, $username, $password);
} catch (PDOException $e) {
    echo 'Could not connect: ' . $e->getMessage();
}

$stmt = $gbd->prepare("INSERT INTO articles (title, description, img_path) VALUES (:articleTitle, :articleDesc, :imgSrc)");
$stmt->bindParam(':articleTitle', $articleTitle);
$stmt->bindParam(':articleDesc', $articleDesc);
$stmt->bindParam(':imgSrc', $imgSrc);