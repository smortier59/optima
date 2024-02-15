<?php
  error_reporting(E_ALL);

  include(dirname(__FILE__)."/../../global.inc.php");
  echo "DANS WEBHOOK";
  log::logger("---------------------- ALERTE WEBHOOK CONTACT", "webhookContact");
  log::logger($_POST, "webhookContact");

?>