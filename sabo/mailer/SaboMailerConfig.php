<?php

namespace Sabo\Mailer;

/**
 * configuration du mailer
 */
enum SaboMailerConfig:int{
    /**
     * email envoyeur (non requis configuration par défaut utilisé)
     */
    case FROM_EMAIL = 1;

    /**
     * nom de l'envoyeur (non requis configuration par défaut utilisé)
     */
    case FROM_NAME = 2;
}