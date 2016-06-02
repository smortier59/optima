## Description

Optima et AbsysTech Framework (ATF) sont maintenant téléchargeables sur Github, vous pouvez fork ou contribuer de la manière qui vous convienne.

Les fichiers statiques restent disponible sur [le bucket AWS historique ici](https://s3-eu-west-1.amazonaws.com/static-absystech/).

Optima est un outil de gestion de relations clients, il permet de créer, et modifier des données dans une base de données mysql.

### Pré-requis

- mysql
- php 5
- apache
- [Smarty](https://www.smarty.net/)

### Structure

- `/www` fichiers statiques
- `/templates` gabaris HTML
- `/includes` classes métiers
- `/lib*` librairies tierces
- `/static` fichiers statiques, ressources publiques à exposer en http

## Utilisation

### Installation

- Copier les fichiers dans un répertoire de votre serveur web
- Exposez le répertoire statique dans /www/ si vous n'avez pas de domaine séparé
- Importez le fichier de structure initial init.sql.sample
- Configurez votre environnement dans le fichier global.inc.php.sample

## Licence

Ce projet est distribué sous la licence [GPL v3](https://www.gnu.org/licenses/gpl-3.0.html) (ou toute autre version ultérieure). Vous pouvez redistribuer et/ou modifier ce logiciel selon les termes de cette licence.

### Utilisation de bibliothèques sous licence GPL

Ce projet utilise les bibliothèques suivantes, qui sont sous licence [GPL] :

- **[ExtJS 3](http://www.extjs.com/products/license.php)** : bibliothèque JavaScript pour interfaces utilisateur.
- **[Smarty PHP](https://www.smarty.net/)** : moteur de templates PHP.
- **[Apache HTTP Server](https://httpd.apache.org/)** : serveur web sous licence Apache 2.0.
- **[MySQL 5](https://www.mysql.com/)** : système de gestion de bases de données relationnelles.
- **[PHP 5](https://www.php.net/releases/)** : langage de programmation utilisé dans ce projet.
- **[PHPExcel](https://github.com/PHPOffice/PHPExcel)** : bibliothèque pour la manipulation de fichiers Excel.
- **[Artichow PHP5](http://www.artichow.org/)** : bibliothèque pour la génération de graphiques en PHP.
- **[FPDF](http://www.fpdf.org/)** : bibliothèque pour la génération de PDF.

En conséquence, le code source complet de ce projet est également distribué sous la même licence (GPL v3).

### Notes sur les licences

Pour les bibliothèques qui ne sont pas sous GPL mais sous d'autres licences open source compatibles (comme FPDF ou Artichow PHP5), assurez-vous de respecter leurs termes respectifs.

### Obtenir le code source

Le code source complet de ce projet est disponible publiquement sur ce dépôt. Si vous avez besoin de plus d'informations concernant les droits ou obligations liés à la licence GPL, veuillez consulter la [documentation officielle](https://www.gnu.org/licenses/gpl-3.0.html).

### Contributions

Les contributions à ce projet doivent également respecter les termes de la licence GPL v3.

### Crédits

Un grand merci aux développeurs des bibliothèques listées en paragraphe précédent!
