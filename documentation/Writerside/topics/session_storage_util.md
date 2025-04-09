# La class SessionStorage

> Cet utilitaire utilisé par le framework et principalement par la class <code>Request</code> permet d'intéragir avec la session.

## Séparation des données

Le framework sépare les données stockées en session via un tableau avec les clés suivantes :

- <code>FOR_USER</code> clé des données fournies par les fonctions utilisateur
- <code>FOR_FLASH</code> données flash
- <code>FOR_FRAMEWORK</code> données utiles au framework
- <code>FOR_CSRF_TOKEN</code> token csrf

<warning>L'utilisateur peut définir des clés au même niveau dans la session, mais il est important de ne pas modifier ces clés ainsi que les données qu'elles contiennent manuellement</warning>

## Fonctions de l'utilitaire

- <code>store</code> permet de stocker une donnée utilisateur
- <code>storeFlash</code> stocke une donnée flash
    <note>Les données flash sont des données ayant une durée de vie plus limitée que la durée de la session. Elles ont un temps d'expiration associé à leur création ainsi qu'un nombre de rafraichissements avant d'être supprimées.</note>
- <code>storeFramework</code> permet de stocker une donnée framework
  <warning>L'utilisation de cette fonction est réservée au framework</warning>
- <code>getValue</code> récupère une donnée stockée par la fonction <code>store</code>
- <code>getFrameworkValue</code> récupère une donnée stockée par la fonction <code>storeFramework</code>
    <warning>L'utilisation de cette fonction est réservée au framework</warning>
- <code>getFlashValue</code> récupère une donnée flash
- <code>delete</code> supprime une donnée stockée par la fonction <code>store</code>
- <code>deleteInFramework</code> supprime une donnée stockée par la fonction <code>storeFramework</code>
  <warning>L'utilisation de cette fonction est réservée au framework</warning>
- <code>deleteInFlash</code> supprime une donnée flash
- <code>manageFlashDatas</code> fonction utilisée pour gérer la durée de vie et de rafraichissement des données flash par le framework
- <code>storeCsrf</code> permet de stocker un token csrf
- <code>getCsrfFrom</code> permet de récupérer une instance de <code>CsrfManager</code> en faisant une correspondance entre le token fourni et un token stocké
- <code>deleteCsrf</code> supprime un token csrf
- <code>create</code> crée une instance <code>SessionStorage</code>