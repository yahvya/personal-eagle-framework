<?php

namespace SaboCore\Utils\Sse;

use Throwable;

/**
 * @brief Utilitaire d'évènement sse
 * @author yahaya bathily https://github.com/yahvya.com
 */
class SaboSse{
    /**
     * @var ResourceManager Gestionnaire de ressource de l'évènement
     */
    protected ResourceManager $resourceManager;

    /**
     * @var int Temps de pause dans l'exécution
     */
    protected int $sleepTimeSec;

    /**
     * @param ResourceManager|null Gestionnaire de ressource
     * @param int $defaultSleepTimeSec temps de pause par défaut en secondes
     */
    public function __construct(?ResourceManager $resourceManager = null,int $defaultSleepTimeSec = 1){
        $this->resourceManager = $resourceManager ?? new ResourceManager();
        $this->sleepTimeSec = $defaultSleepTimeSec;
    }

    /**
     * @return ResourceManager le gestionnaire de ressource
     */
    public function getResourceManager():ResourceManager{
        return $this->resourceManager;
    }

    /**
     * @brief Lance l'exécution sse
     * @param Closure|array $executor fonction de gestions des actions à exécuter dans la boucle d'évènement, appelé à chaque tour, reçoit en argument $this
     * @param Closure|array|null $stopVerifier fonction à retour booléen , si true renvoi l'évènement de fermeture et stoppe l'exécution , reçoit en argument $this
     * @param string $stopEventName nom de l'évènement renvoyé à la fermeture par condition
     * @return $this après stoppage de l'exécution par l'utilisateur ou par condition
     * @notice Vous pouvez redéfinir la méthode setup afin d'ajouter du paramétrage supplémentaire
     */
    public function launch(Callable $executor,?Callable $stopVerifier = null,string $stopEventName = "close"):SaboSse{
        $this->setup();

        while(true){
        
            // exécution utilisateur
            call_user_func_array(callback: $executor,args: [$this]);

            // vérification d'arrêt par condition
            if($stopVerifier !== null && call_user_func_array(callback: $stopVerifier,args: [$this])){
                $this->sendEvent(eventName: $stopEventName,eventDatas: []);
                break;
            }
            
            if(connection_aborted())
                break;

            sleep(seconds: $this->sleepTimeSec);
        }

        return $this;
    }

    /**
     * @brief Envoi un évènement au format json 
     * @param array $eventDatas contenu du message
     * @param array|Closure|null $onError action à faire en cas d'erreur d'envoi, reçoit en argument $this
     * @return $this
     */
    public function sendEvent(string $eventName,array $eventDatas,?Callable $onError = null):SaboSse{
        try{
            $encodedDatas = @json_encode(value: $eventDatas);

            // gestion de l'échec d'encodage
            if($encodedDatas === false){
                if($onError !== null)
                    call_user_func_array(callback: $onError,args: [$this]);

                return $this;
            }

            // envoi du message
            echo "event: $eventName" . PHP_EOL;
            echo "data: $encodedDatas";
            echo PHP_EOL . PHP_EOL;
            ob_flush();
            flush();
        }
        catch(Throwable){
            if($onError !== null)
                call_user_func_array(callback: $onError,args: [$this]);
        }

        return $this;
    }

    /**
     * @brief Met à jour le temps de pause dans l'exécution
     * @param int $sleepTimeSec temps de pause entre chaque tour en secondes
     * @return $this
     */
    public function setSleepTimeSec(int $sleepTimeSec):SaboSse{
        $this->sleepTimeSec = $sleepTimeSec;

        return $this;
    }

    /**
     * @return int le temps de pause entre chaque tour en secondes
     */
    public function getSleepTimeSec():int{
        return $this->sleepTimeSec;
    }

    /**
     * @brief Configure l'exécution pour sse
     * @return $this
     */
    protected function setup():SaboSse{
        session_write_close();
        ignore_user_abort(enable: true);
        header(header: "Content-Type: text/event-stream");
        header(header: "Cache-Control: no-cache");
        ob_flush();
        flush();

        return $this;
    }
}
