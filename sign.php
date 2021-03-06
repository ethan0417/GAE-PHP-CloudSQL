<?php
header('Location: '."/");

use google\appengine\api\users\User;
use google\appengine\api\users\UserService;

$user = UserService::getCurrentUser();

$db = null;
if (isset($_SERVER['SERVER_SOFTWARE']) &&
strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
  // Connect from App Engine.
  try{
     $db = new pdo('mysql:unix_socket=/cloudsql/cp-300-ethan-taipei101:test1;dbname=guestbook', 'root', '');
  }catch(PDOException $ex){
      die(json_encode(
          array('outcome' => false, 'message' => 'Unable to connect.')
          )
      );
  }
};

try {
  if (array_key_exists('content', $_POST)) {
    $stmt = $db->prepare('INSERT INTO entries (guestName, content) VALUES (:name, :content)');
    $stmt->execute(array(':name' => htmlspecialchars($user->getNickname()), ':content' => htmlspecialchars($_POST['content'])));
    $affected_rows = $stmt->rowCount();
    // Log $affected_rows.
  }
} catch (PDOException $ex) {
  // Log error.
}
$db = null;
?>