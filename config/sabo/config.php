<?php

use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;

// configuration environnement dev|prod
SaboConfig::setBoolConfig(SaboConfigAttributes::DEBUG_MODE,true);

// configuration base de donnée
SaboConfig::setBoolConfig(SaboConfigAttributes::INIT_WITH_DATABASE_CONNEXION,true);

// configuration des extensions twigs
SaboConfig::setUserExtensions([]);

// configuration de maintenance
SaboConfig::setBoolConfig(SaboConfigAttributes::MAINTENANCE_MODE,false);

// configuration du mode de fichier environnement
// SaboConfig::setStrConfig(SaboConfigAttributes::ENV_FILE_TYPE,".env");