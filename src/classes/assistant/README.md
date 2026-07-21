# Assistant IA back-office — squelette QuantumCore

Squelette initial de l'assistant décrit dans les instructions du projet : un ChatBot natif au code source de `vendor/ephenyxdigital/quantumcore/`, avec une couche par plugin à forte présence BO. Écrit le 2026-07-21.

## Fichiers

- `PhenyxAssistant.php` — orchestrateur central (singleton). Point d'entrée : `PhenyxAssistant::getInstance()->ask($texte)`.
- `PhenyxAssistantLayer.php` — classe abstraite qu'un plugin étend pour exposer sa couche BO. Auto-description des entités via `PhenyxObjectModel::getDefinition()`, pas de doc à maintenir à la main.
- `PhenyxAssistantQuery.php` / `PhenyxAssistantAnswer.php` — value-objects question/réponse.
- `PhenyxAssistantProviderInterface.php` — le point de bascule vers un LLM. Une seule méthode (`generate()`) à réimplémenter le jour où le modèle local est prêt.
- `PhenyxAssistantRuleProvider.php` — implémentation par défaut, sans LLM, sans dépendance : choisit la meilleure réponse candidate remontée par les plugins. Reste utile comme fallback même après le passage au LLM.
- `PhenyxAssistantLog.php` — `PhenyxObjectModel` classique pour journaliser chaque échange (table `eph_phenyx_assistant_log`).
- `sql/phenyx_assistant.sql` — création de la table de log, conventions identiques au reste du schéma (`eph_` prefix, InnoDB, utf8mb3).

## Comment un plugin se branche

Deux façons, au choix de l'auteur du plugin, agrégées de la même manière par `PhenyxAssistant` :

**1. Couche explicite** (état/contexte riche à réutiliser) :

```php
// dans le constructeur du plugin ou d'un contrôleur admin
PhenyxAssistant::getInstance()->registerLayer(new PhEcommerceAssistantLayer('ph_ecommerce'));
```

```php
class PhEcommerceAssistantLayer extends PhenyxAssistantLayer {
    protected $entities = ['Product', 'Category', 'Order'];

    public function answer(PhenyxAssistantQuery $query) {
        if (stripos($query->text, 'facture') !== false) {
            return PhenyxAssistantAnswer::text(
                'Pour créer une facture, ouvre Comptabilité > Factures puis "Nouvelle facture".',
                $this->pluginName,
                0.8
            );
        }
        return null; // rien de pertinent : PhenyxAssistant continue avec les autres sources
    }
}
```

**2. Hook générique** (plus léger, cohérent avec le reste du framework) :

```php
// dans install() du plugin
$this->registerHook('actionAssistantAnswer');

// méthode hookActionAssistantAnswer($params) sur la classe du plugin
public function hookActionAssistantAnswer($params) {
    $query = $params['query']; // PhenyxAssistantQuery
    // ... même logique que ci-dessus, renvoie une PhenyxAssistantAnswer ou null
}
```

Aucune ligne à ajouter à la main dans `eph_hook` : `Plugin::registerHook()` la crée automatiquement.

## Ce qui n'est PAS encore fait (volontairement)

- Pas de contrôleur admin (`AdminAssistantController`) ni de widget front pour l'instant — le squelette se limite à l'orchestration métier, testable indépendamment de l'UI.
- Pas de couche concrète pour `ph_ecommerce` ou un autre plugin — à écrire une fois le squelette validé, en suivant l'exemple ci-dessus.
- Pas de provider LLM — `PhenyxAssistantRuleProvider` fait tourner le pipeline de bout en bout dès aujourd'hui (utile pour valider l'architecture avant d'investir dans la machine GPU). Le jour venu, une nouvelle classe `PhenyxAssistantLlmProvider implements PhenyxAssistantProviderInterface` suffit, branchée via `PhenyxAssistant::getInstance()->setProvider(...)`.
- Le `sql/phenyx_assistant.sql` n'est rattaché à aucun mécanisme d'install automatique (le squelette vit dans le core, pas dans un plugin avec son propre cycle `Plugin::install()`) — à exécuter manuellement pour l'instant.

## Point de vigilance

D'après l'exploration du dump `ephenyx_io (4).sql` : le framework a **trois copies** de son cœur dans le repo (`vendor/ephenyxdigital/quantumcore` pour le site principal, deux autres sous `forum/vendor/`, jamais synchronisées suite à un renommage de package). Ce squelette ne cible que `vendor/ephenyxdigital/quantumcore` — à garder en tête si l'assistant doit un jour couvrir aussi le forum.

## Note d'accès (2026-07-21)

Ce sous-dossier a reçu une ACL dédiée (`setfacl -m u:ubuntu:rwx`, propriétaire/groupe restés `ephenyx.io_djdgdojkuqw:psacln` comme le reste du vhost) pour permettre à l'assistant Claude/Cowork d'y écrire sans élargir les droits sur le reste du site. Si de nouveaux sous-dossiers sont créés en dehors de cette arborescence, la même ACL devra y être répliquée.
