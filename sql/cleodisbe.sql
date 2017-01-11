INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98503 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1508001-4')

INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98468 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1604001-2')

INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98548 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1606001-1')

INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98389 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1404040-21')

INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98442 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1407020-21')

INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98463 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = '1502041-11')



INSERT INTO `facture_ligne`(id_facture,id_produit,ref,produit,quantite,id_fournisseur,prix_achat,serial,code,id_affaire_provenance,visible,afficher)
  SELECT facture.id_facture ,id_produit,facture_ligne.ref,produit,quantite,id_fournisseur,prix_achat,serial,code,
  id_affaire_provenance,visible,afficher
  FROM `facture_ligne`,`facture`
  WHERE facture_ligne.id_facture = 98463 AND facture.id_facture =
    (SELECT id_facture FROM facture WHERE facture.ref = ' 1505002-10')

