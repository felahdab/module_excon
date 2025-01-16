<?php

return [
    'name' => 'Excon',
    /**
     * Le seuil d'extrapolation définit la différence de temps entre les timestamps des positions disponibles en base
     * au delà de laquelle on active le calcul d'une position extrapolée.
     * Unité: secondes
     * Valeur par défaut: 15
     */
    'seuil_extrapolation' => 15,

    /**
     * La limite de validite définit le seuil au delà duquel on ne peut plus utiliser une position pour un identifiant
     * soit que cette position soit trop ancienne (par rapport au timestamp demandé), soit qu'elle soit trop récente
     * (toujours par rapport au timestamp demandé)
     * Unité: secondes.
     * Valeur par défaut: 300 (soit 5 minutes)
     */
    'limite_validite' => 300,
];
