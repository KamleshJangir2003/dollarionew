<?php
session_name('user_session');
session_start();
session_unset();
session_destroy();
session_write_close();
header("Location: auth/login.php");
exit;
?>
