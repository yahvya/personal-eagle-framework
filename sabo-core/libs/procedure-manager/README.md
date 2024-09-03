# Procedure manager

> Utilitaire de gestion de procédure

## Classes 

- **Procedure** : class abstraite visant à être étendu pour définir une procédure
- **ProcedureStep** : interface de description d'une étape de la procédure

## Exemple d'utilisation

Dans cet exemple, nous allons implémenter une procédure d'inscription.

```
<?php

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;

/**
 * @brief Etape d'enregistrement des données utilisateur
 * @step 1
 */
class RegisterUserDatasStep implements ProcedureStep{
    /**
     * @param array $userDatas données utilisateurs
     */
    public function __construct(protected array $userDatas = []){}

    public function canAccessNext(Procedure $procedure, ...$args): bool{
        if(empty($_POST["email"]) || empty($_POST["password"]))
            return false;

        $this->userDatas = [
            "email" => $_POST["email"],
            "password" => $_POST["password"]
        ];

        return $this->sendVerificationMailTo($this->userDatas["email"]);
    }

    /**
     * @return string|null le code de vérification
     */
    public function getCode():?string{
        return $this->userDatas["verificationCode"] ?? null;
    }

    /**
     * @brief Envoi le mail de vérification à l'utilisateur
     * @param string $userEmail mail de l'utilisateur
     * @return bool si l'envoi du mail de vérification réussi
     */
    protected function sendVerificationMailTo(string $userEmail):bool{
        // envoi du mail de vérification

        $this->userDatas["verificationCode"] = "code_de_verification";
        return true;
    }
}

/**
 * @brief Etape de vérification du code reçu par mail et enregistrement de l'utilisateur
 * @step 2
 */
class VerifyUserCodeStep implements ProcedureStep{
    public function canAccessNext(Procedure $procedure, ...$args): bool{
        if(empty($_POST["code"]))
            return false;

        // récupération du code de vérification
        $verificationCode = $procedure
            ->getStep(stepNumber: 1) // récupération de l'étape 1 de type RegisterUserDatasStep
            ->getCode();

        return $verificationCode === $_POST["code"] && $this->registerUser();
    }

    /**
     * @return bool enregistre l'utilisateur
     */
    public function registerUser():bool{
        return true;
    }
}

/**
 * @brief Procédure d'inscription
 */
class RegistrationProcedure extends Procedure{
    public function getSteps(): array{
        return [
            new RegisterUserDatasStep,
            new VerifyUserCodeStep
        ];
    }
}

// sur chaque route liée à l'inscription

$registrationProcedure = RegistrationProcedure::loadFrom(from: $_SESSION["registration.procedure"] ?? null);

if(!$registrationProcedure->next())
    erreurDansLetapeCourante();

if($registrationProcedure->isFinished())
    actionDeFin();

```