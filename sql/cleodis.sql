# Création des vues pour l'espace CLIENT pro CLEODIS
CREATE VIEW coordonnees_client AS SELECT id_societe, ref, societe, famille.famille as type_client,  nom_commercial, adresse, adresse_2, adresse_3, cp, ville, facturation_adresse, facturation_adresse_2, facturation_adresse_3, facturation_cp, facturation_ville, livraison_adresse, livraison_adresse_2, livraison_adresse_3, livraison_cp, livraison_ville, email, tel,particulier_civilite, particulier_nom, particulier_prenom, particulier_portable, num_carte_fidelite, particulier_fixe, particulier_email
FROM societe
INNER JOIN famille ON societe.id_famille = societe.id_famille;



CREATE VIEW factures_client AS SELECT id_facture, ref, ref_externe, id_societe, prix, etat, date, date_paiement, type_facture, date_periode_debut, date_periode_fin, tva, id_affaire, mode_paiement, nature, rejet, date_rejet, date_regularisation FROM facture;


CREATE VIEW parc_client AS SELECT id_societe, ref, libelle, divers, serial, code, date, date_inactif, date_garantie, date_achat, existence FROM parc;

CREATE VIEW affaire_client AS SELECT id_societe, id_affaire, ref, ref_externe, date, affaire, id_parent, id_fille, nature, date_garantie, site_associe, mail_signature, date_signature, signataire, langue, adresse_livraison, adresse_livraison_2, adresse_livraison_3, cp_adresse_livraison as adresse_livraison_cp, ville_adresse_livraison as adresse_livraison_ville, adresse_facturation, adresse_facturation_2, adresse_facturation_3, cp_adresse_facturation as adresse_facturation_cp, ville_adresse_facturation as adresse_facturation_ville, id_magasin FROM affaire;


CREATE VIEW abonnement_client AS SELECT commande.id_societe, commande.id_affaire, commande.ref AS num_dossier, commande.commande AS dossier, commande.etat as statut, commande.date, date_debut, date_evolution AS date_fin, date_arret
FROM commande
INNER JOIN affaire ON commande.id_affaire = affaire.id_affaire
WHERE affaire.etat NOT IN ("devis", "perdue", "demande_refi", "facture_refi");


CREATE VIEW historique_affaire AS SELECT id_affaire, date, etat, commentaire FROM affaire_etat ORDER BY id_affaire_etat ASC;

CREATE VIEW loyer_affaire AS SELECT id_affaire, type,
									COALESCE(loyer,0) +
									COALESCE(assurance,0) +
									COALESCE(frais_de_gestion,0) +
									COALESCE(serenite,0) +
									COALESCE(maintenance,0) +
									COALESCE(hotline,0) +
									COALESCE(supervision,0) +
									COALESCE(support,0) AS loyer, frequence_loyer AS frequence FROM loyer;


CREATE VIEW abonnement_detail AS SELECT commande.id_affaire, commande_ligne.ref AS ref, produit, quantite
FROM commande_ligne
INNER JOIN commande ON commande_ligne.id_commande = commande.id_commande
WHERE visible = "oui"
ORDER BY commande.id_affaire ASC, ordre ASC;