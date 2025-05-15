<?php
include_once('header.php');
require_once 'vendor/autoload.php';
require_once 'config.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
?>

<!-- CONTENIDO PROTEGIDO DEL SISTEMA -->

<a href="<?php echo htmlspecialchars($client->createAuthUrl()); ?>">Google Login</a>

<?php include_once('footer.php'); ?>
