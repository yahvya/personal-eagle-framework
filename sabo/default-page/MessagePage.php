<?php

namespace Sabo\DefaultPage;

/**
 * représente une page html affichant un message
 */
class MessagePage implements Showable{
    /**
     * message affiché
     */
    private string $message;
    /**
     * titre de la page
     */
    private string $title;

    /**
     * @param title le titre de la page html
     * @param message le message à afficher dans la page
     */
    public function __construct(string $title,string $message){
        $this->title = $title;
        $this->message = $message;
    }

    public function show():void{
        die(<<<HTML
            <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>{$this->title}</title>
                </head>
                <body>
                    <p style="font-size: large; font-family: Arial;text-align:center;font-weight: bold">{$this->message}</p>
                </body>
                </html>
        HTML);
    }
}