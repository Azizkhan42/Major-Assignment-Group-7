<?php
require_once __DIR__ . '/helpers/session.php';

startSession();
requireLogin();

logoutUser();
header("Location: /Personal-budget-dashboard/index.php?msg=logout_success");
exit();
?>
