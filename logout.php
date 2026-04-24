<?php
session_start();//start session
session_unset();//remove all session variable
session_destroy();//destroy
header("Location: index.php");//redirect homepage
exit();//stop further execution
?>