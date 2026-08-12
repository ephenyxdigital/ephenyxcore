<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistantQuery
 *
 * Value-object représentant une question posée à l'assistant back-office,
 * enrichie du contexte BO courant (contrôleur, entité éditée, employé).
 *
 * Volontairement permissif (propriétés publiques, pas de getters/setters) pour
 * rester dans le style du reste du framework (cf. PhenyxController, PhenyxObjectModel
 * qui exposent aussi leurs propriétés directement).
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantQuery {

    /** @var string Texte brut saisi par l'employé */
    public $text;

    /** @var int|null Employé courant (Context::getContext()->employee->id) */
    public $idEmployee;

    /** @var int|null Profil courant (droits) */
    public $idProfile;

    /** @var string|null Nom du contrôleur BO courant (ex: 'AdminProducts') */
    public $controllerName;

    /** @var string|null 'admin' | 'pluginadmin' (cf. PhenyxController::$controller_type) */
    public $controllerType;

    /** @var string|null Nom du plugin propriétaire du contrôleur courant, si pluginadmin */
    public $pluginName;

    /** @var string|null Action AJAX en cours (Tools::getValue('action')) le cas échéant */
    public $action;

    /** @var string|null Classe PhenyxObjectModel de l'entité actuellement affichée/éditée */
    public $entityClass;

    /** @var int|null Identifiant de l'entité actuellement affichée/éditée */
    public $entityId;

    /** @var string Code langue courant (iso_code) */
    public $isoCode;

    /**
     * Sac fourre-tout pour tout contexte additionnel fourni par l'appelant
     * (ex: widget front du chatbot) sans devoir toucher cette classe à chaque besoin.
     *
     * @var array
     */
    public $extra = [];

    public function __construct($text, array $extra = []) {

        $this->text = trim((string) $text);
        $this->extra = $extra;
    }

    /**
     * Construit une query pré-remplie à partir du Context courant.
     * Reste volontairement défensif : l'assistant doit pouvoir être appelé tôt
     * dans le cycle de vie (avant que tout le contexte ne soit peuplé).
     *
     * @param string $text
     * @param array  $extra
     * @return static
     */
    public static function fromContext($text, array $extra = []) {

        $query = new static($text, $extra);
        $context = Context::getContext();

        if (isset($context->employee) && is_object($context->employee)) {
            $query->idEmployee = (int) $context->employee->id;
            $query->idProfile = isset($context->employee->id_profile) ? (int) $context->employee->id_profile : null;
        }

        if (isset($context->controller) && is_object($context->controller)) {
            $query->controllerName = $context->controller->controller_name;
            $query->controllerType = $context->controller->controller_type;
            $query->pluginName = $context->controller->plugin_name;
        }

        if (isset($context->language) && is_object($context->language)) {
            $query->isoCode = $context->language->iso_code;
        }

        if (isset($context->_tools)) {
            $action = $context->_tools->getValue('action');

            if (!empty($action)) {
                $query->action = $action;
            }

        }

        return $query;
    }

}
