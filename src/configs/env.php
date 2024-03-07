<?php

use SaboCore\Config\Config;

/**
 * @brief Fichier d'environnement du framework
 * @return Config les variables d'environnement
 */
return Config::create()
    // nom de l'application
    ->setConfig("applicationName","Sabo framework")
    // lien de l'application (au format lien/)
    ->setConfig("applicationLink","https://sabo-final.local/")
    // configuration de connexion à la base de donnée
    ->setConfig(
        "database",
        Config::create()
            ->setConfig("initWithConnection",true)
            ->setConfig("host","")
            ->setConfig("user","")
            ->setConfig("password","")
            ->setConfig("dbname","")
    );
