<?php
$Checkbdd_datas = [
	'articles' => [
		"SET AUTOCOMMIT = 0;"
		,"START TRANSACTION;"
		,'SET time_zone = "+00:00";'
		,"CREATE TABLE #TABLENAME# ("
			."`id` int(3) UNSIGNED NOT NULL,"
			."`barrecode` varchar(50) NOT NULL,"
			."`nom_article` varchar(255) NOT NULL,"
			."`os` varchar(255) NOT NULL,"
			."`cpumhz` varchar(255) NOT NULL,"
			."`comment` varchar(255) NOT NULL,"
			."`valide` int(1) UNSIGNED NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=" . DB['charset']." COMMENT='la liste des articles en locations ou pas.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);"
		,"ALTER TABLE #TABLENAME# MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`id`, `barrecode`, `nom_article`, `os`, `cpumhz`, `comment`, `valide`) VALUES
			(NULL,'100000000000000001','PAT-T1000','null','null','null','1'),
			(NULL,'100000000000000002','PAT-EE-8eu','null','null','null','1'),
			(NULL,'100000000000000003','PAT-HATE-CHAUDE','null','null','null','1');"]
	,'contents' => [
		"CREATE TABLE #TABLENAME# ("
			."`id` int(10) UNSIGNED NOT NULL,"
			."`language` int(1) UNSIGNED NOT NULL,"
			."`logo` varchar(255) NOT NULL,"
			."`logotitle` varchar(255) NOT NULL,"
			."`logoalt` varchar(255) NOT NULL,"
			."`logourl` varchar(255) NOT NULL"
			 .") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des données modifiable du site. Images, textes et autres.';"
		 ,"INSERT INTO #TABLENAME# (`id`, `language`, `logo`, `logotitle`, `logoalt`, `logourl`) VALUES "
			 ."(1, 1, 'logo.png', 'Barrecode Logo Title', 'Barrecode Logo Alt', 'index.php');",
		 "ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);",
		 "ALTER TABLE #TABLENAME# MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;"]
	,'formdatas' => [
		"CREATE TABLE #TABLENAME# ("
			."`datasID` int(11) UNSIGNED NOT NULL,"
			."`groupename` varchar(100) NOT NULL,"
			."`pageid` int(10) UNSIGNED NOT NULL,"
			."`inputtitle` varchar(100) NOT NULL,"
			."`islabel` int(1) UNSIGNED NOT NULL DEFAULT '0',"
			."`isnotnull` int(1) UNSIGNED NOT NULL DEFAULT '0',"
			."`inputname` varchar(100) NOT NULL,"
			."`type` varchar(100) NOT NULL,"
			."`content` varchar(500),"
			."`pardefaut` int(2) UNSIGNED NOT NULL,"
			."`placeholder` varchar(255),"
			."`ordre` int(2) UNSIGNED NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des champs de formulaires affichables dans les pages.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`datasID`), ADD UNIQUE KEY `inputname` (`inputname`);"
		,"ALTER TABLE #TABLENAME# MODIFY `datasID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`datasID`, `groupename`, `pageid`, `inputtitle`,  `islabel`, `isnotnull`, `inputname`, `type`, `content`, `pardefaut`, `placeholder`, `ordre`) VALUES"
			." (1, 'retardcommentaire', 500, 'Raison du retard', 1, 0, 'raisonretard', 'select', '', 1, '', 1),"
			." (2, 'retardcommentaire', 500, 'Commentaires sur le retard', 1, 0, 'commentaire', 'textearea', '', 0, 'Votre commentaire ici ...', 2),"
			." (3, 'retardcommentaire', 500, 'Commentaires2 sur le retard', 1, 0, 'commentaire2', 'textearea', '', 0, 'Votre commentaire ici ...', 3);"]
	,'formgroups' => [
		"CREATE TABLE #TABLENAME# ("
			."`id` int(10) UNSIGNED NOT NULL,"
			." `groupname` varchar(255) NOT NULL,"
			." `title` varchar(255) NOT NULL"
		.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des groupe de formulaires.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);"
		,"ALTER TABLE #TABLENAME# MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`id`, `groupname`, `title`) VALUES (1, 'retardcommentaire', 'Un commentaire sur le retard de ');"]
	,'formdatas_pivot' => [
		"CREATE TABLE #TABLENAME# ("
			."`pivotid` int(10) UNSIGNED NOT NULL,"
			." `parentid` int(10) UNSIGNED NOT NULL,"
			." `formdatasid` int(10) UNSIGNED NOT NULL,"
			." `parenttablename` varchar(50) NOT NULL,"
			." `formdatascontent` varchar(500) NOT NULL,"
			." `parentcolname` varchar(50) NOT NULL"
		.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='Pivot en formdatas et retards.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`pivotid`);"
		,"ALTER TABLE #TABLENAME# MODIFY `pivotid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`pivotid`, `parentid`, `formdatasid`, `parenttablename`, `formdatascontent`, `parentcolname`) VALUES "
			."(1, 1, 1, 'retards', '{\"0\": \"Selectionnez un motif de retard !\",\"1\": \"Retard sans justificatif\",\"2\": \"Problème familial/perso\",\"3\": \"Convocation administrative avec justificatif\",\"4\": \"Rendez-vous médical avec justificatif\",\"5\": \"Transport avec justificatif\",\"6\": \"Intempéries\",\"7\": \"Convocation administrative sans justificatif\",\"8\": \"Rendez-vous médical sans justificatif\",\"9\": \"Transport sans justificatif\",\"10\": \"Panne de réveil\",\"11\": \"Autres...\"}', 'raisonretard'),"
			."(2, 1, 2, 'retards', '', 'commentaire'),"
			."(3, 1, 3, 'retards', '', 'commentaire2');"]
	,'incidents' => [
		"CREATE TABLE #TABLENAME# ("
			."`id` int(3) UNSIGNED NOT NULL,"
			."`barrecode` varchar(50) NOT NULL,"
			."`dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
			."`date` date NOT NULL,"
			."`qui` varchar(50) NOT NULL,"
			."`commentaires` varchar(600) NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des incidents.';"
		,"INSERT INTO #TABLENAME# (`id`, `barrecode`, `dateheure`, `date`, `qui`, `commentaires`) VALUES (1, '201810473120011820', '2018-10-19 15:13:29', '2018-10-19', '222', '201810473120011820');"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);"
		,"ALTER TABLE #TABLENAME# MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;"]
	,'lastdate' => [
		"CREATE TABLE #TABLENAME# ("
			."`id` int(3) UNSIGNED NOT NULL,"
			."`dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
			."`membres_id` int(3) UNSIGNED NOT NULL,"
			."`currentpage` varchar(500) NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des derniers click par date et membres_id.';"
		,"INSERT INTO #TABLENAME# (`id`, `dateheure`, `membres_id`, `currentpage`) VALUES (1, '2018-10-19 15:13:29', 1, 'actions');"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);"
		,"ALTER TABLE #TABLENAME# MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;"]
	,'locations' => [
		"CREATE TABLE #TABLENAME# ("
				."`id` int(3) UNSIGNED NOT NULL,"
				."`barrecode` varchar(50) NOT NULL,"
				."`dateheure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
				."`date` date NOT NULL,"
				."`action` varchar(3) NOT NULL,"
				."`qui` varchar(50) NOT NULL,"
				."`commentaires` varchar(1000) NOT NULL"
				.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des entrés sorties des articles.';"
			,"ALTER TABLE #TABLENAME# ADD UNIQUE KEY `id` (`id`);"
			,"ALTER TABLE #TABLENAME# MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
			,"INSERT INTO #TABLENAME# (`id`, `barrecode`, `dateheure`, `date`, `action`, `qui`, `commentaires`) VALUES (1, 'R90PHLN5', '".Fun::dateDuJour('classic')."', '".Fun::dateDuJour('dayfull')."', 'OUT', '201899990003540000', 'beta demo');"]
	,'membres' => [
		"CREATE TABLE #TABLENAME# ("
			."`membre_id` int(3) UNSIGNED NOT NULL,"
			."`id` int(3) UNSIGNED NOT NULL COMMENT 'A quoi sert ce champs ??',"
			."`barrecode` varchar(50) NOT NULL,"
			."`nom` varchar(255) NOT NULL,"
			."`prenom` varchar(255) NOT NULL,"
			."`section` varchar(55) NOT NULL,"
			."`annee` varchar(4) NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des membres, élèves ou adhérents.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`membre_id`), ADD UNIQUE KEY `barrecode` (`barrecode`);"
		,"ALTER TABLE #TABLENAME# MODIFY `membre_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`membre_id`,`id`, `barrecode`, `nom`, `prenom`, `section`, `annee`) VALUES 
		(1,1,'000000000000000001','Etlardons','Patobeur','Formateur','0000'),
		(2,2,'000000000000000002','EtFromage','Pat','Formateur','0000'),
		(3,3,'000000000000000003','Léponge','Bob','Formateur','0000'),
		(4,4,'000000000000000004','Zeublouz','Agathe','Administratif','0000'),
		(5,5,'000000000000000005','VIP','Formateur','Formateur','0000');"
		]
	,'pages' => [
		"CREATE TABLE #TABLENAME# ("
			."`id_page` int(10) UNSIGNED NOT NULL,"
			."`type` varchar(12) NOT NULL,"
			."`accred` varchar(5) NOT NULL DEFAULT '11111',"
			."`ismenu` tinyint(1) DEFAULT '0',"
			."`name` varchar(25) NOT NULL,"
			."`auth` int(1) NOT NULL,"
			."`author` varchar(255) NOT NULL DEFAULT '{\"tokenCode\":true,\"userStatus\":true}',"
			."`title` varchar(255) NOT NULL,"
			."`parent` int(10) UNSIGNED NOT NULL,"
			."`active` int(1) DEFAULT NULL,"
			."`content` varchar(255) NOT NULL,"
			."`url` varchar(255) NOT NULL,"
			."`urltitle` varchar(255) NOT NULL,"
			."`classsup` varchar(255) NOT NULL,"
			."`ordre` int(2) UNSIGNED NOT NULL"
			." ) ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des pages affichables du site.';"
		,'ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id_page`);'
		,"INSERT INTO #TABLENAME# (`id_page`, `type`, `accred`, `ismenu`, `name`, `auth`, `author`, `title`, `parent`, `active`, `content`, `url`, `urltitle`, `classsup`, `ordre`) VALUES"
			." (1, 'page', '00009', 0, 'accueil', 1, '', 'Codebarre App', 0, 1, '<i class=\"fas fa-home\"></i>', '', 'Test Page accueil', 'homelink', 0),"
			." (8, 'page', '00009', 1, 'pages', 0, '', 'Pages', 0, 1, '<i class=\"far fa-file\"></i>', '', '', '', 0),"
			." (500, 'page', '00000', 1, 'actions', 0, '{\"tokenCode\":true,\"userStatus\":true}', 'Actions - Codebarre App', 0, 1, '<i class=\"fas fa-barcode\"></i>', '', '', 'navlink', 0),"
			." (662, 'page', '00009', 1, 'profil', 0, '{\"tokenCode\":true}', 'Profil - Codebarre App', 0, 1, '<i class=\"fas fa-user-circle\"></i>', '', '', '', 0),"
			." (663, 'page', '00009', 1, 'glpi', 0, '{\"tokenCode\":true}', 'Glpi - Codebarre App', 0, 1, '<i class=\"fas fa-code-branch\"></i>', '', 'Glpi Utilitaires', '', 0),"
			." (664, 'page', '00006', 1, 'editpages', 0, '{\"tokenCode\":true,\"userStatus\":true}', 'Edit les pages', 0, 1, '<i class=\"fas fa-tools\"></i>', '', 'Test edition', '', 0),"
			." (665, 'page', '00000', 1, 'login', 1, '{\"tokenCode\":false}', 'Login - Codebarre App', 0, 1, '<i class=\"fas fa-sign-in-alt\"></i>', '', '', '', 0),"
			." (666, 'page', '00000', 1, 'exit', 0, '{\"tokenCode\":true}', 'Exit - Codebarre App', 0, 1, '<i class=\"fas fa-door-closed o\"></i><i class=\"fas fa-door-open x\"></i>', '', '', '', 0),"
			." (700, 'page', '00006', 1, 'importsql', 0, '{\"tokenCode\":true,\"userStatus\":true}', 'Liste import sql', 0, 1, '<i class=\"fas fa-tools o\"></i>', '', 'Test edition', '', 0),"
			." (701, 'page', '00006', 1, 'readersql', 0, '{\"tokenCode\":true,\"userStatus\":true}', 'Lecteur SQL !', 0, 1, '<i class=\"fas fa-tools o\"></i>', '', 'Lecteur SQL !', '', 0)"
			.";"
		,'ALTER TABLE #TABLENAME# MODIFY `id_page` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=700;']
	,'retards' => [
		"CREATE TABLE #TABLENAME# ("
			."`id` int(10) UNSIGNED NOT NULL,"
			."`id_membres` int(11) NOT NULL,"
			."`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
			."`action` int(1) NOT NULL,"
			."`barrecode` varchar(64) NOT NULL,"
			."`commentaire` text NOT NULL,"
			."`commentaire2` text NOT NULL,"
			."`raisonretard` int(2) NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset']." COMMENT='la liste des retards par membre barrecode.';"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`id`);"
		,"ALTER TABLE #TABLENAME# MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`id`, `id_membres`, `date`, `action`, `barrecode`, `commentaire`, `commentaire2`, `raisonretard`) VALUES"
			." (NULL, '1', '2021-03-30 19:16:07', '1', '201899990003540000', '', '', NULL);"]
	,'users' => [
		"CREATE TABLE #TABLENAME# ("
			."`userID` int(11) UNSIGNED NOT NULL,"
			."`userName` varchar(100) NOT NULL,"
			."`userEmail` varchar(100) NOT NULL,"
			."`userPass` varchar(64) NOT NULL,"
			."`userStatus` int(1) NOT NULL,"
			."`tokenCode` varchar(100) NOT NULL,"
			."`accred` varchar(5) NOT NULL DEFAULT '00000',"
			."`lastconnect` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
			."`userip` varchar(255) NOT NULL,"
			."`glpi_user` varchar(40) NOT NULL,"
			."`glpi_app` varchar(40) NOT NULL"
			.") ENGINE=".DB['engine']." CHARSET=".DB['charset'].";"
		,"ALTER TABLE #TABLENAME# ADD PRIMARY KEY (`userID`), ADD UNIQUE KEY `userEmail` (`userEmail`);"
		,"ALTER TABLE #TABLENAME# MODIFY `userID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;"
		,"INSERT INTO #TABLENAME# (`userID`, `userName`, `userEmail`, `userPass`, `userStatus`, `tokenCode`, `accred`, `lastconnect`, `userip`, `glpi_user`, `glpi_app`) VALUES"
			." (null, '" . INSTALLATION['nom'] . "', '" . INSTALLATION['email'] . "', '" . INSTALLATION['password'] . "', '1', '', '".INSTALLATION['accred']."', '".Fun::dateDuJour('classic')."', '".Fun::get_ip_address()."', '', ''),"
			." (null, '" . INSTALLATION['nom2'] . "', '" . INSTALLATION['email2'] . "', '" . INSTALLATION['password2'] . "', '1', '', '".INSTALLATION['accred2']."', '".Fun::dateDuJour('classic')."', '".Fun::get_ip_address()."', '', ''),"
			." (null, 'Laura', 'laura@patobeur.pat', '".md5("laura")."', '1', '', '10000', '".Fun::dateDuJour('classic')."', '".Fun::get_ip_address()."', '', ''),"
			." (null, 'Lison', 'lison@patobeur.pat', '".md5("lison")."', '1', '', '10000', '".Fun::dateDuJour('classic')."', '".Fun::get_ip_address()."', '', '');"
		,"COMMIT;"]
];