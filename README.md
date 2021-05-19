# patotools
Class, traits and some tools i use !

## host file

127.0.0.1 barrecode.pat

## VirtualHost

```
<VirtualHost *:80>
    ServerAdmin patotools@barrecode.pat
    DocumentRoot "D:/LocalWebSites/patobeur/barrecode/public"
    ServerName barrecode.pat
    ServerAlias www.barrecode.pat
    <Directory "D:/LocalWebSites/patobeur/barrecode/public">
        Order allow,deny
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
```

## login & password

admin1 : 

patobeur@patobeur.org/patobeur

admin2 : 

test@patobeur.org/test

tout est dans private/definitions.php

```
define('INSTALLATION', [
	'active' => true, // installation auto activée
	'delete' => true, // en cas de manque de tables la base peut elle etre delete ?
	'redirect' => true,
	'maxtry' => 1, // installation auto activée
	'delay' => 15,
	'nom' => "Adminpatobeur", // admin
	'email' => "patobeur@patobeur.org",
	'password' => md5('patobeur'),
	'accred' => "99999",
	'nom2' => "AdminPat",
	'email2' => "test@patobeur.org",
	'password2' => md5('test'),
	'accred2' => "55555",
]);
```

user1 : 

laura@patobeur.pat/laura

lison@patobeur.pat/lison

tout est dans checkbdd_datas.php ( tout en bas)

## droits d'écriture utiles pour les dossier logs

chown -R www-data:www-data private/logs

## quelques codes barre user

```
(1,1,'000000000000000001','Etlardons','Patobeur','Formateur','0000'),
(2,2,'000000000000000002','EtFromage','Pat','Formateur','0000'),
(3,3,'000000000000000003','Léponge','Bob','Formateur','0000'),
(4,4,'000000000000000004','Zeublouz','Agathe','Administratif','0000'),
(5,5,'000000000000000005','VIP','Formateur','Formateur','0000')
```

## quelques codes barre articles

```
(NULL,'100000000000000001','PAT-T1000','null','null','null','1'),
(NULL,'100000000000000002','PAT-EE-8eu','null','null','null','1'),
(NULL,'100000000000000003','PAT-HATE-CHAUDE','null','null','null','1')
```