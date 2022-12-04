# Créateur de OpenSim Manager Web - OSMW depuis 2007
-------------------------------------------------------------------------------

L’outil de gestion de tous vos simulateurs Opensim au travers de votre navigateur web.
Il vous simplifie la gestion quotidienne de vos simulateurs, vous supervisez vos régions d’un coup d’œil.
Un outil simple à l’utiliser, intégrer à vos hébergement et personnalisable si possible.

Pour Opensimulator:
- Gestion de vos régions 
- Gestion de vos terrains
- Gestion des visiteurs sur vos terrains
- Gestion de vos sauvegardes
- ...

-------------------------------------------------------------------------------
Configuration requise pour OpenSimulator Manager Web (OSMW):
	--  Apache / Mysql / TMUX / LibSSH2
	
Fonctionnement:
	-- OSMW envoi des commandes au simulateur via Remote Admin sauf pour le START (Demarrage en console TMUX)
	-- Le simulateur est lancé par le fichier batch "RunOpensim.sh"
	-- Certains fichiers doivent avoir les droits 777 pour pouvoir etre modifier par OSMW (LINUX)
	
	-- ATTENTION aux droits d'accés aux fichiers et le format des données saisie dans vos fichiers INI
		--> Régions.ini (droits écriture) / OpensimDefaults.ini , etc.. qui doivent etre accessible
		--> Préférer l'utilisation de fichier de config dans addon-modules/NameGrid/config/NameGrid.ini
		
Gestion des Utilisateurs:
	=> 4 Niveaux d'accés sont autorisés
	-- Administrateurs 
	-- Gestionnaires de sauvegardes
	-- Invités et/ou Compte privatif par moteur opensimulator
	-- 1 compte root
