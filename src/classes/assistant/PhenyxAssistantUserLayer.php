<?php

namespace EphenyxDigital\QuantumCore;

/**
 * OBSOLÈTE depuis le 2026-07-21 — renommée en PhenyxAssistantCoreLayer
 * (cf. classes/assistant/PhenyxAssistantCoreLayer.php dans ce même dossier)
 * car cette couche ne se limite plus au client (entity_class 'User') mais
 * répond désormais à tous les topics "core" du wiki (CMS, Content Anywhere,
 * Plugins, ...) sans filtre entity_class.
 *
 * Ce fichier n'est plus référencé nulle part (aliases-map.php pointe
 * maintenant sur PhenyxAssistantCoreLayer, PhenyxAssistant::registerCoreLayers()
 * instancie PhenyxAssistantCoreLayer) — laissé en place uniquement parce que
 * l'outillage de cette session ne peut pas supprimer de fichier sur le
 * répertoire connecté de Jeff. À supprimer manuellement sur le VPS lors du
 * prochain déploiement (vendor/ephenyxdigital/quantumcore/src/classes/assistant/
 * PhenyxAssistantUserLayer.php), puis penser à `composer dump-autoload -o`.
 */
