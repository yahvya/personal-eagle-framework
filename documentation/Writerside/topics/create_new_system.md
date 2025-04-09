# Créer un nouveau système

> Le framework arrive par défaut avec un système permettant de gérer le système MYSQL. Toutefois, il est possible d'intégrer un nouveau système en l'implément ou en intégrant un paquet externe.

<procedure title="Etapes de création d'un système">
    <step>
        Créer une class qui extends <code>SaboCore\Database\Providers\DatabaseProvider</code>. 
        Cette class permettra a minima d'initialiser le système dans le cycle de vie du framework via la méthode abstraite <code>initDatabase</code>.
        Cette méthode reçoit en paramètre une configuration provenant de l'environnement qui sera traité plus bas.
        <note>Il est recommandé d'implémenter cette class en utilisant le pattern <a href="https://fr.wikipedia.org/wiki/Singleton_(patron_de_conception)">singleton</a>.</note>
    </step>
    <step>
        Enregistrer le système dans l'environnement ainsi que son mode de configuration.
        Pour ce faire:
        <ul>
            <li>Fournir une instance du provider sur la clé <code>DatabaseConfig::PROVIDER->value</code></li>
            <li>Remplir la configuration attendue par le système dans la méthode <code>initDatabase</code> via la clé <code>DatabaseConfig::PROVIDER_CONFIG->value</code></li>
        </ul>
    </step>
    <step>
        La suite de l'implémentation est libre en fonction du fonctionnement, toutefois des class et interfaces de structure sont prévus dans le namespace <code>SaboCore\Database\System</code> dans le chemin de même nom.
        <ul>
            <li><code>DatabaseModel</code> permettant de définir la structure d'un model ainsi que des méthodes natives attendues</li>
            <li><code>DatabaseCondition|DatabaseCondSeparator|DatabaseComparator</code> permettant l'écriture de conditions de recherche via méthodes.</li>
            <li><code>DatabaseActions</code> énumération des différents évènements possibles.</li>
            <li><code>DatabaseActionException</code> exception liée à un évènement.</li>
        </ul>
    </step>
</procedure>

## Exemple d'implémentation

Cet exemple va se baser sur un système fonctionnant à base d'un seul document <code>JSON</code> sans toutefois aller jusqu'à l'implémentation des conditions dynamiques ...

<warning>L'exemple est à but représentatif</warning>

### Implémentation du provider

<code-block lang="php">
class JsonSystemProvider extends DatabaseProvider{
    protected static array|null $jsonDocument = null;
    protected static string|null $documentPathFromStorage = null; 
    #[Override]
    public function initDatabase(Config $providerConfig):void{
        $providerConfig->checkConfigs("documentPathFromStorage");
        self::$documentPathFromStorage = $providerConfig->getConfig(name: "documentPathFromStorage");
        $fileContent = @file_get_contents(filename: AppStorage::buildStorageCompletePath(pathFromStorage: self::$documentPathFromStorage) );
        if($fileContent == null)
            return;
        self::$jsonDocument = json_decode(json: $fileContent,associative: true);
    }
    #[Override]
    public function getCon():array|null{
        return self::$jsonDocument;
    }
}
</code-block>

### Enregistrement dans l'environnement

<code-block lang="php">
->setConfig(
    name: EnvConfig::DATABASE_CONFIG->value,
    value: Config::create()
        ->setConfig(name: DatabaseConfig::INIT_APP_WITH_CONNECTION->value,value: true)
        ->setConfig(name: DatabaseConfig::PROVIDER->value,value: new JsonSystemProvider() )
        ->setConfig(
            name: DatabaseConfig::PROVIDER_CONFIG->value,
            value: Config::create()
                ->setConfig(name: "documentPathFromStorage",value: "/database/json-doc.json")
        )
)
</code-block>

> On pourrait dans ce cas imaginer des systèmes de modèles représentant un format d'une partie du document ...