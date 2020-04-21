<?php

return [
    'menu' => [
        'title' => 'Contenu',
        'wakamailtos' => 'Mailtos',
        'wakamailtos_description' => 'Gestion des modèles mailtos',
        'settings_category' => 'Wakaari Modèle',
    ],
    'wakamailto' => [
        'tab_info' => "Info",
        'tab_edit' => "Edit",
        'tab_scopes' => "Limites",
        'tab_fnc' => "Images et fonctions",
        'name' => 'Nom',
        'template' => "Texte brut",
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
