test-dev
========

Un stagiaire à créer le code contenu dans le fichier src/Controller/Home.php

Celui permet de récupérer des urls via un flux RSS ou un appel à l’API NewsApi.
Celles ci sont filtrées (si contient une image) et dé doublonnées.
Enfin, il faut récupérer une image sur chacune de ces pages.

Le lead dev n'est pas très satisfait du résultat, il va falloir améliorer le code.

Pratique :
1. Revoir complètement la conception du code (découper le code afin de pouvoir ajouter de nouveaux flux simplement)

Questions théoriques :
1. Que mettriez-vous en place afin d'améliorer les temps de réponses du script
2. Comment aborderiez-vous le fait de rendre scalable le script (plusieurs milliers de sources et images)

### Sur la partie SYSTEM
- Mettre en place une tache CRON qui lance un script pour mettre à jour les nouvelles images en cache
- Mettre le CDM sur un serveur dit à la demande (AWS, Google Cloud) qui permettra d'avoir plus d'instance en fonction du nombre de personne
- Découper en plusieurs services, une instance front qui fera le lien avec une API qui appellera un CDN.

### Sur la partie BACK

 - Mettre en place du cache PHP qui ferais juste un appel sur la date pour avoir les nouvelles images des articles ou du flux RSS.
 - Mettre en place un cache des ressources (CDN)
 - Optimisez les images en les redimensionnant et en les compressant

### Sur la partie FRONT

 - Mettre en place du cache pour la page HTML
 - Minifier le fichier CSS et le mettre en cache
 - Mettre en place un chargement progressif des images
