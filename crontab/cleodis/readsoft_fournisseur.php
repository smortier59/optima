<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
/*if(exec('whoami')!="apache"){
	print_r("************************Penser à vérifier les droits de TEMP***********************************");
}
*/
		function clean($s){ return str_replace("&","",$s); }

class readsoft {
	const HEAD = '<?xml version="1.0" encoding="UTF-8"?>';

	/* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * Récupérer la constante qui mémorise le dernier id exporté précédemment
	 */
	private static function setupLast($func) {
		if ($id = ATF::constante()->getValue('__READSOFT_'.$func.'__'))
			ATF::$func()->q->andWhere('id_'.$func,$id,'minId','>');
	}

	/* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * Mémorise en constante le dernier id exporté
	 */
	private static function updateLast($func,$id) {
		ATF::constante()->setValue('__READSOFT_'.$func.'__',$id);
	}

	/* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * Extraction XML fournisseur
	 */
	public function fournisseur(){
		return self::societe();
	}
	private function societe(){
		ATF::societe()->q->reset()->where("fournisseur","oui")
//->setLimit(200)
;

		$xml = '<Suppliers>';
		if ($fournisseurs = ATF::societe()->select_all()) {
			foreach($fournisseurs as $i) {
				array_walk($i,'htmlspecialchars');
				array_walk($i,'clean');
				$xml .= "\n".'<Supplier>';
				$xml .= "\n".'<SupplierNumber>'.ATF::societe()->select($i['id_'.__FUNCTION__],'code_fournisseur').'</SupplierNumber>'; // Identifiant du fournisseur dans l’ERP ';
				$xml .= "\n".'<Name>'.$i['societe'].'</Name>'; // Nom du fournisseur ';
				$xml .= "\n".'<OrganizationNumber>001</OrganizationNumber>'; // Identifiant de la société acheteuse dans l’ERP';
				$xml .= "\n".'<Street>'.htmlspecialchars($i['adresse'].($i['adresse_2'] ? $i['adresse_2'] : '').($i['adresse_3'] ? $i['adresse_3'] : '')).'</Street>'; // Adresse ';
				$xml .= "\n".'<PostalCode>'.$i['cp'].'</PostalCode>'; // Code postal';
				$xml .= "\n".'<City>'.$i['ville'].'</City>'; // Ville';
				$xml .= "\n".'<CountryName>'.$i['id_pays'].'</CountryName>'; // Code du pays sur 2 caractères (ex. : FR)';
				$xml .= "\n".'<Blocked>'.($i['etat']=='actif' ? 0 : 1).'</Blocked>'; // 0 : fournisseur actif, 1 : fournisseur bloqué
				$xml .= "\n".'<TaxCode>'.$i['siret'].'</TaxCode>'; // SIRET du fournisseur ';
				$xml .= "\n".'<TelephoneNumber>'.$i['tel'].'</TelephoneNumber>'; // Téléphone (facultatif) ';
				$xml .= "\n".'<FaxNumber>'.$i['fax'].'</FaxNumber>'; // Fax (facultatif) ';
				$xml .= "\n".'</Supplier>';
				$lastID = $i['id_'.__FUNCTION__];
			}
			self::setupLast(__FUNCTION__,$lastID); // Mémoriser le dernier ID exporté
		}
		$xml .= "\n".'</Suppliers>';
		return self::HEAD . $xml;
	}

	/* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * Extraction XML des lignes de commandes
	 */
	function bon_de_commande(){
		ATF::bon_de_commande()->q->reset()->setStrict()->addOrder('bon_de_commande.id_'.__FUNCTION__,'ASC')
//->setLimit(200)
;
		self::setupLast(__FUNCTION__); // Ne pas extraire les données déjà extraites précédemment

		$xml = '<PurchaseOrders>';
		if ($bdc = ATF::bon_de_commande()->sa()) {
			foreach($bdc as $c) {
				if (!$c['id_fournisseur']) continue; // Si aucun fournisseur, READSOFT ne veut pas qu'on exporte la commande
				array_walk($c,'htmlspecialchars');
				array_walk($c,'clean');
				$xml .= "\n".'<PurchaseOrder>';
				$xml .= "\n".'<OrderNumber>'.$c['ref'].'</OrderNumber>';
				$xml .= "\n".'<SupplierNumber>'.ATF::societe()->select($c['id_fournisseur'],'code_fournisseur').'</SupplierNumber>';
				$xml .= "\n".'<CurrencyCode>EUR</CurrencyCode>';
				if ($c['date_reception_fournisseur'])
					$xml .= "\n".'<DateCreated>'.$c['date_reception_fournisseur'].'T00:00:00</DateCreated>';
				$xml .= "\n".'<ContactName>'.htmlspecialchars(ATF::contact()->nom($c['id_contact'])).'</ContactName>';
				$xml .= "\n".'<Description>'.$c['bom_de_commande'].'</Description>';
				$xml .= "\n".'<StatusText>'.$c['etat'].'</StatusText>';
				ATF::bon_de_commande_ligne()->q->reset()->where('bon_de_commande_ligne.id_'.__FUNCTION__,$c['id_'.__FUNCTION__]);
				if ($bdcl = ATF::bon_de_commande_ligne()->sa()) {
					$xml .= "\n".'<Lines>';
					foreach($bdcl as $l) {
						array_walk($l,'htmlspecialchars');
						array_walk($l,'clean');
						$xml .= "\n".'<PurchaseOrderLine>';
						$xml .= "\n".'<OrderLineNumber>'.$l['id_'.__FUNCTION__.'_ligne'].'</OrderLineNumber>';
						$xml .= "\n".'<ArticleNumber>'.htmlspecialchars($l['ref']).'</ArticleNumber>';
						$xml .= "\n".'<SupplierArticleNumber>'.htmlspecialchars($l['ref']).'</SupplierArticleNumber>';
						$xml .= "\n".'<ArticleDescription>'.htmlspecialchars($l['produit']).'</ArticleDescription>';
						$cl = ATF::commande_ligne()->select($l['id_commande_ligne']);
						$p = ATF::produit()->select($cl['id_produit']);
						$scat = ATF::sous_categorie()->select($p['id_sous_categorie']);
						$cat = ATF::categorie()->select($scat['id_categorie']);
						$xml .= "\n".'<CategoryNumber>'.$p['id_sous_categorie'].'</CategoryNumber>';
						$xml .= "\n".'<CategoryDescription>'.htmlspecialchars($scat['sous_categorie'].' ('.$cat['categorie'].')').'</CategoryDescription>';
						$xml .= "\n".'<Quantity>'.$l['quantite'].'</Quantity>';
						$xml .= "\n".'<Unit>'.$l['quantite'].'</Unit>';
						$xml .= "\n".'<UnitPrice>'.$l['prix'].'</UnitPrice>';
						$xml .= "\n".'<RowTotalAmountVatExcluded>'.($l['quantite']*$l['prix']).'</RowTotalAmountVatExcluded>';
						$xml .= "\n".'<StatusText>'.$l[''].'</StatusText>';
						$xml .= "\n".'<InvoicedQuantity>'.$l['quantite'].'</InvoicedQuantity>';
						$xml .= "\n".'<DeliveredQuantity>'.$l['quantite'].'</DeliveredQuantity>';
						$xml .= "\n".'<IsDeliveryRequired>false</IsDeliveryRequired>';
						$xml .= "\n".'<PriceUnit>'.$l['prix'].'</PriceUnit>';
						$xml .= "\n".'</PurchaseOrderLine>';
					}
					$xml .= "\n".'</Lines>';
				}
				$xml .= "\n".'</PurchaseOrder>';
			}
			self::setupLast(__FUNCTION__,$lastID); // Mémoriser le dernier ID exporté
		}
		$xml .= "\n".'</PurchaseOrders>';
		return self::HEAD . $xml;
	}

	/* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	 * Extraction XML des comptes comptables
	 */
	function compte(){
		$xml = '<GeneralLedgerAccounts>';
		$xml .= "\n".'<GeneralLedgerAccount>';
		$xml .= "\n".'<Code>'.$i[''].'</Code>'; // Code comptable 
		$xml .= "\n".'<Group>'.$i[''].'</Group>';
		$xml .= "\n".'<Description>'.$i[''].'</Description>'; // Libellé du compte 
		$xml .= "\n".'<Active>'.$i[''].'</Active>'; // « true » ou « false » 
		$xml .= "\n".'</GeneralLedgerAccount>';
		$xml .= "\n".'</GeneralLedgerAccounts>';
	}

}
$rs = new readsoft();
file_put_contents(__DIR__.'/../../www/readsoft/Fournisseurs.xml',$rs->fournisseur());
file_put_contents(__DIR__.'/../../www/readsoft/Purchaseorders.xml',$rs->bon_de_commande());




/*
<xml>
<Suppliers>
<Supplier>A répéter pour chaque fournisseur
<SupplierNumber>Identifiant du fournisseur dans l’ERP 
<Name>Nom du fournisseur 
<OrganizationNumber>Identifiant de la société acheteuse dans l’ERP
<Street>Adresse 
<PostalCode>Code postal
<City>Ville
<CountryName>Code du pays sur 2 caractères (ex. : FR)
<Blocked>
0 : fournisseur actif
1 : fournisseur bloqué
<TaxCode>SIRET du fournisseur 
<TelephoneNumber>Téléphone (facultatif) 
<FaxNumber>Fax (facultatif) 
</Supplier>
</Suppliers> 
*/
