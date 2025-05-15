<?php
require_once 'vendor/autoload.php';
session_start();
include('configuracion/conexion.php');  // Conexión a tu base de datos

$clientID = '292298832394-lsujs32n6se50mhnahjd0dj2vqfqjv9c.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/bootcamp/syscamp/login_google.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        $email = $userInfo->email;
        $nombre = $userInfo->name;

        // Verificar si ya existe en la base de datos
        $query = "SELECT id_persona, username, id_rol, nombre_persona FROM persona WHERE username = '$email'";
        $resultado = pg_query($conexion, $query);

        if ($resultado && pg_num_rows($resultado) == 1) {
            // Usuario ya existe, iniciar sesión
            $usuario = pg_fetch_assoc($resultado);
        } else {
            // Usuario no existe, lo insertamos con rol 2 (Usuario)
            $insert = "INSERT INTO persona (username, nombre_persona, clave_persona, id_rol)
                       VALUES ('$email', '$nombre', '', 1)";
            pg_query($conexion, $insert);

            // Obtener datos del nuevo usuario insertado
            $resultado = pg_query($conexion, "SELECT id_persona, username, id_rol, nombre_persona FROM persona WHERE username = '$email'");
            $usuario = pg_fetch_assoc($resultado);
        }

        // Crear sesión como en login normal
        $_SESSION['id_persona'] = $usuario['id_persona'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['id_rol'] = $usuario['id_rol'];
        $_SESSION['nombre_persona'] = $usuario['nombre_persona'];

        header('Location: index.php');
        exit();
    } else {
        echo 'Error al obtener token: ' . htmlspecialchars($token['error']);
        exit();
    }
} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
