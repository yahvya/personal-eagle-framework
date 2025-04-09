# Gestion de la maintenance

> La mise à jour de l'état de maintenance se gère [via la configuration d'environnement](configuration_elements.md#env-php)


## Etat de l'application durant une maintenance

> Durant une maintenance comme expliqué [sur le schéma du processus routing](routing.md#etapes-de-routing), toute ressource hormis la page d'affichage de maintenance est bloqué.

## Accès à l'application

Dans la configuration d'environnement la ligne <code>->setConfig(name: MaintenanceConfig::SECRET_LINK->value,value: "/maintenance/dev/access/")</code> permet de définir un lien secret d'accès pouvant être changé. 

Ce lien donne mène à l'appel d'un controller permettant la gestion de l'accès à la maintenance 

<procedure title="Définition d'un controller de gestion de maintenance">
<step>Créer une class</step>
<step>Extends la class <code>SaboCore\Controller\MaintenanceController</code></step>
<step>Implémenter les méthodes requises : <code>showMaintenancePage</code> affiche la page de saisie des identifiants - <code>verifyLogin</code> vérifie si l'accès est autorisé (notamment par le traitement du formulaire reçu)</step>
<step>Enregistrer le gestionnaire dans l'environnement via la ligne <code>->setConfig(name: MaintenanceConfig::ACCESS_MANAGER->value,value: MyCustomManager::class)</code></step>
</procedure>

Le framework arrive avec un processus d'accès par défaut pré-défini via les éléments suivant :

- <code>src/controllers/DefaultMaintenanceController</code> ce controller par défaut implémente l'affichage d'une page et la vérification via code secret d'accès
- <code>src/views/maintenance/authentication.blade.php</code> vue de saisie de code d'accès
- <code>src/storage/maintenance/maintenance.secret</code> fichier contenant un mot de passe haché. Par défaut <code>motdepasse</code>
    <code-block lang="php">echo password_hash(password: "votre_mot_de_passe",algo: PASSWORD_BCRYPT);</code-block>
    <warning>Il est important de modifier ce mot de passe avant déploiement</warning>
<procedure title="Processus d'accès">
<step>Se rendre sur le lien secret</step>
<step>Le controller par défaut affiche la vue d'authentification</step>
<step>À la validation du formulaire la vérification via <code>verifyLogin</code> va comparer le mot de passe fourni à celui contenu dans le fichier <code>maintenance.secret</code></step>
<step>Si conforme return true, et débloque l'accès via session sinon false</step>
</procedure>