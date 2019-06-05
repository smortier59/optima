
<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

echo "========= DEBUT DE SCRIPT =========\n";


// Début de transaction SQL
ATF::db()->begin_transaction();

// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
// ATF::db()->commit_transaction();

echo "========= FIN DE SCRIPT =========\n";