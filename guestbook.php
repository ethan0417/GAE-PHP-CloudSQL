<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
 <body>
  <?php
  use google\appengine\api\users\User;
  use google\appengine\api\users\UserService;

  $user = UserService::getCurrentUser();
/*
  if ($user) {
    echo $user;
    echo 'Hello, ' . htmlspecialchars($user->getNickname());
  } else {
    echo '$user';
    header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
  };
*/
  if (isset($user)) {
    echo sprintf('Welcome, %s! (<a href="%s">sign out</a>)',
                 $user->getNickname(),
                 UserService::createLogoutUrl('/'));
    //echo "<hr>".htmlspecialchars("Login URL : ".UserService::createLogoutUrl('/')); 
    
  } else {
    echo sprintf('<a href="%s">Sign in or register</a>',
                 UserService::createLoginUrl('/'));
    //echo "<hr>".htmlspecialchars("Logout URL : ".UserService::createLoginUrl('/'));
  }

  ?>

  <h2>Guestbook Entries</h2>
  <?php
  // Create a connection.

  $db = null;
  if (isset($_SERVER['SERVER_SOFTWARE']) &&
  strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
    // Connect from App Engine.
    try{
       $db = new pdo('mysql:unix_socket=/cloudsql/cp-300-ethan-taipei101:micloud;dbname=guestbook', 'root', '');
    }catch(PDOException $ex){
        die(json_encode(
            array('outcome' => false, 'message' => 'Unable to connect.')
            )
        );
    }
  };

  try {
    // Show existing guestbook entries.
    foreach($db->query('SELECT * from entries') as $row) {
            echo "<div><strong>" . $row['guestName'] . "</strong> wrote <br> " . $row['CONTENT'] . "</div>";
     }
  } catch (PDOException $ex) {
    echo "An error occurred in reading or writing to guestbook.";
  }
  $db = null;
  ?>

  <h2>Sign the Guestbook</h2>
  <form action="/sign" method="post">
    <div><textarea name="content" rows="3" cols="60"></textarea></div>
    <div><input type="submit" value="Sign Guestbook"></div>
  </form>
  </body>
</html>