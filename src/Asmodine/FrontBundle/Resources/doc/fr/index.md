# FrontBundle

## Données issues du projet Asmodine Common

Les morphotype et morphoprofile sont calculés à partir grâce au projet Asmodine Common.
La documentation associée se trouvent dans ce projet.

## Fonctionnement général

AsmodineFront a pour rôles principales de parcourir les produits (whishlist, recommendations, navigation, ...) et de mettre à jour le profil de l'utilisateur.

Ce projet inclut donc principalement des formulaires et des templates. Tout les calculs et recherches sont externalisés (AsmodineBack / Elasticsearch).


Doctrine est utilisé pour persister les données. Contrairement au Front, ce choix a été fait d'utiliser l'**ORM** parce que :
 - On ne traite qu'un (ou peu) d'enregistrements à la fois (Utilisation de DBAL pour les gros traitements)
 - Simplification du code
 - Simplification avec les Bundles ApiPlatform ou FOSUser
 - Plus simple d'utilisation avec les formulaires 

Le **routing** est déclaré au niveau des actions des Controller.

Les **templates** suivent les noms et l'arboresence des Controller.

Les **fragments** ont été utilisés pour ne pas surcharger inutilement les controllers et permettre au Cache (Navigateur et/ou Varnish) de garder les résultats en Cache.

Les **JS** et **CSS** sont tous déclarés dans [app/config/assetic.php](../../../../../../app/config/assetic.yml)


## Paricularités

L'envoi des profils utilisateurs sur le back est réalisé grâce à un listener : [PhysicalProfileListener.php](../../../Entity/Listener/PhysicalProfileListener.php) et grâce au Service [BackApiService](../../../Service/BackApiService.php)

Les styles sont en scss *(écrit en sass à l'origine)* pour utiliser une syntaxe récente et pouvoir la compiler automatiquement grâce à la librairie ScssPhp.

JWT n'est pas utilisé pour l'instant.