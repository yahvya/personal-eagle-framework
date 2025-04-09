# Class de traitement

> Le principe des class de traitement sert à ne pas surcharger les controllers. La logique de fonctionnement MVC attendue par le framework fonctionne ainsi

<code-block lang="d2">
controller_action: Controller{
    S'occupe de l'interception de la requête sur un lien précis et rend le résultat attendu{
        shape: text
    }
}
models_action: Model{
    S'occupe de mapper et d'apporter les fonctionnalités d'interaction avec la base de données{
        shape: text
    }
} 
treatments_actions: Class de traitement{
    Les class de traitement s'occuperaient de la logique de traitement (formulaires, calculs \.\.\.){
        shape: text
    }
}
controller_action &lt;-> treatments_actions
treatments_actions &lt;-> models_action
result_render: Rendu du résultat
controller_action -> result_render
</code-block>

## Utilisation

Les class de traitement se situent par défaut dans <code>src/treatment</code> et peuvent étendre de la classe <code>CustomTreatment</code>.

<note>La logique recommandée est de fonctionner par renvoi d'exceptions pour marquer une erreur</note>

Deux méthodes sont ajoutées par défaut à cet effet, <code>throwModelException</code> et <code>throwException</code>

<procedure title="Exemple d'implémentation">
<step>
<code-block lang="php">
class MyController{
    public function sendContactMessage(Request $request):RedirectResponse{
        try{
            ContactTreatment::sendMessage(request: $request);
            return new RedirectResponse(link: "https://github.com/yahvya");
        }
        catch(TreatmentException $e){
            $request
                ->getSessionStorage()
                ->storeFlash(storeKey: "contact.error",toStore: $e->getErrorMessage());
            return new RedirectResponse(route(requestMethod: "get",routeName: "contact.page"));
        }
    }
}
</code-block>
</step>
<step>
<code-block lang="php">
class ContactTreatment extends CustomTreatment{
    public static function sendMessage(Request $request):void{
        self::throwException(errorMessage: "Erreur de test");
    }
}
</code-block>
</step>
</procedure>

<note>Vous pouvez ajouter des méthodes customisées dans <code>CustomTreatment</code></note>