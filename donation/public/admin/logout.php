<?php
require_once __DIR__ . '/../../includes/security.php';
secureSession();
session_destroy();
header('Location: login.php');
exit;
