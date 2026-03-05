<?php
/**
 * Déconnexion
 */
require_once 'config/functions.php';

destroySession();
header('Location: /index.php');
exit;
