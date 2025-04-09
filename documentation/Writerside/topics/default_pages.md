# Pages par défaut

> Dans certaines étapes de la phase de routing, certaines erreurs ou évènements peuvent se produire, pour les traiter 3 fichiers html par défaut sont définis. Ces fichiers sont des fichiers html statiques sans accès aux ressources du site ni valeurs dynamiques pour garantir leur affichage sans soucis. 

Ces fichiers se situent dans le répertoire <code>src/views/default-pages</code>

- <code>internal-error.html</code> affiché en cas d'erreur interne durant la phase de routing ou d'exception non traitée par le code utilisateur
- <code>maintenance.html</code> affiché lorsque l'application est en état de maintenance
- <code>not-found.html</code> affiché quand le lien saisi n'est pas trouvé