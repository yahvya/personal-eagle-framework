# Server Send Event

> Le principe d'échange sse (Server Send Event), permet, sans passer par l'utilisation de Web Sockets, d'envoyer des messages du serveur au client.

Ce principe peut être utilisé pour des systèmes de chat, notifications ...

L'utilitaire sse permet offre des fonctions de configuration de d'envoi de message correctement formatée.

## Utilisation

L'utilitaire associé est la class <code>SaboCore\Utils\Sse</code>

*Cet exemple est à but représentatif des possibilités*

<code-block lang="php">
$resourceManager = new ResourceManager();
$resourceManager
    ->setResource(key: "userToNotifyId",resource: 1)
    ->setResource(key: "notifyWith",resource: NotificationConfig::MESSAGE);
$sseManager = new SaboSee(
    resourceManager: $resourceManager,
    defaultSleepTimeSec: 10 # temps entre chaque tour de boucle
);
$sseManager->launch(
    executor: function(SaboSse $manager):void{
        if(condition)
            $manager->sendEvent(eventName: "notification",eventDatas: $manager->getResourceManager()->getResource(key: "userToNotifyId"));
    }, # Callable de gestion à chaque tour de boucle
    stopVerifier: fn(SaboSse $manager):bool => condition_stop, # fonction renvoyant si le sse doit s'arrêter
    stopEventName: "endOfNotifications" # nom de l'évènement envoyé en cas d'arrêt
);
</code-block>

<note>Pour l'exemple des fonctions ont été utilisés, il est toutefois recommandé d'utiliser une class pour gérer un échange.</note>