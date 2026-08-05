<?php
/**
 * Convenience redirect: keeps the app working when the project folder
 * itself is used as the XAMPP doc root (the real webroot is /public).
 * Best practice is to point the virtual host at the /public folder.
 */
header('Location: public/');
exit;
