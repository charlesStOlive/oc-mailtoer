<?php

return [
    'menu' => [
        'title' => 'Contenu',
        'wakamailtos' => 'Mailtos',
        'wakamailtos_description' => 'Gestion des modèles mailtos',
        'settings_category' => 'Wakaari Modèle',
    ],
    'wakamailto' => [
        'name' => 'Nom',
        'template' => "Template HTML compatible bootstrap 3.4.1",
        'path' => 'Fichier source',
        'analyze' => "Log d'analyse des codes du fichier source",
        'has_sectors_perso' => 'Personaliser le contenu en fonction du secteur',
        'data_source' => ' Sources des données',
        'data_source_placeholder' => 'Choisissez une source de données',
        'show' => 'Voir un exemple',
        'check' => 'Vérifier',
        'scopes' => [
            'title' => "limiter le mailto pour une cible",
            'prompt' => 'Ajouter une nouvelle limites',
            'com' => "Vous pouvez décider de n'afficher ce modèle que sous certains critères Attention seul les valeurs id sont accepté",
            'self' => "Fonction de restriction liée à l'id de ce modèle ?",
            'target' => 'Relation de la cible',
            'target_com' => "Ecrire le nom de la relation les relations parentes ne sont pas disponible",
            'id' => 'ID recherché',
            'id_com' => "Vous pouvez ajouter plusieurs ID",
            'conditions' => "Conditions",
        ],
        'subject' => "Sujet de l'email",
        'slug' => "Slug ou code",
        'addFunction' => 'Ajouter une fonction/collection',
        'test' => "Tester",
        'show' => "Voir",
    ],
    "button" => [
        'exemple_download' => "Télecharger un ex.",
        'exemple_inline' => "ex. en ligne",
        'exemple_html' => "Voir HTML",

    ],
];
