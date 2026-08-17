<?php

namespace EphenyxDigital\EphenyxCore;

use AdminController;
use PluginAdminController;


/**
 * Class HelperUploader
 *
 * @since 1.8.1.0
 */
class HelperUploader extends PhenyxUploader {

    const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/uploader';
    const DEFAULT_TEMPLATE = 'simple.tpl';
    const DEFAULT_AJAX_TEMPLATE = 'ajax.tpl';

    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

    // @codingStandardsIgnoreStart
    private $_context;
    private $_drop_zone;
    private $_id;
    private $_files;
    private $_name;
    private $_max_files;
    private $_multiple;
    private $_post_max_size;
    protected $_template;
    private $_template_directory;
    private $_title;
    private $_url;
    private $_use_ajax;
    // @codingStandardsIgnoreEnd

    /**
     * @param Context $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setContext($value) {

        $this->_context = $value;

        return $this;
    }

    /**
     * @return Context
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getContext() {

        if (!isset($this->_context)) {
            $this->_context = Context::getContext();
        }

        return $this->_context;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setDropZone($value) {

        $this->_drop_zone = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getDropZone() {

        if (!isset($this->_drop_zone)) {
            $this->setDropZone("$('#" . $this->getId() . "-add-button')");
        }

        return $this->_drop_zone;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setId($value) {

        $this->_id = (string) $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId() {

        if (!isset($this->_id) || trim($this->_id) === '') {
            $this->_id = $this->getName();
        }

        return $this->_id;
    }

    /**
     * @param string[] $value
     *
     * @return $this
     */
    public function setFiles($value) {

        $this->_files = $value;

        return $this;
    }

    /**
     * @return string[]
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getFiles() {

        if (!isset($this->_files)) {
            $this->_files = [];
        }

        return $this->_files;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setMaxFiles($value) {

        $this->_max_files = isset($value) ? (int) ($value) : $value;

        return $this;
    }

    /**
     * @return int
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getMaxFiles() {

        return $this->_max_files;
    }

    /**
     * @param bool $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setMultiple($value) {

        $this->_multiple = (bool) $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setName($value) {

        $this->_name = (string) $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getName() {

        return $this->_name;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setPostMaxSize($value) {

        $this->_post_max_size = $value;
        $this->setMaxSize($value);

        return $this;
    }

    /**
     * @return int
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getPostMaxSize() {

        if (!isset($this->_post_max_size)) {
            $this->_post_max_size = parent::getPostMaxSize();
        }

        return $this->_post_max_size;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTemplate($value) {

        $this->_template = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplate() {

        if (!isset($this->_template)) {
            $this->setTemplate(static::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTemplateDirectory($value) {

        $this->_template_directory = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplateDirectory() {

        if (!isset($this->_template_directory)) {
            $this->_template_directory = static::DEFAULT_TEMPLATE_DIRECTORY;
        }

        return $this->_normalizeDirectory($this->_template_directory);
    }

    /**
     * Resout le chemin du gabarit d'upload, du plus specifique au plus general.
     *
     * ⚠️ CORRIGE LE 2026-08-16 — cette methode retournait `null`.
     *
     * L'ancienne redaction enchainait les candidats en `if / else if`, avec le
     * cas « controleur de plugin » en TETE de chaine :
     *
     *     if ($controleur instanceof PluginAdminController) {
     *         if (file_exists(<gabarit du plugin>)) { return <gabarit du plugin>; }
     *     } else if (…) { … } else { return <recours>; }
     *
     * Des lors que le controleur appelant appartenait a un plugin ET que le
     * plugin ne fournissait pas son propre `views/templates/admin/helpers/
     * uploader/simple.tpl` — c'est-a-dire toujours, aucun plugin n'en fournit —
     * la premiere branche etait prise, son `file_exists` echouait, et la
     * fonction sortait par le bas SANS `return`. Les trois candidats suivants,
     * dont celui du socle (`content/backoffice/backend/helpers/uploader/`) qui
     * existe bel et bien, etaient inatteignables : ils pendaient au `else` du
     * premier `if`.
     *
     * `render()` passait donc `null` a `Smarty::createTemplate()`, d'ou le
     * « Deprecated: strpos(): Passing null » (Smarty.php:986) puis l'exception
     * « Source: Missing name » (Template/Source.php:146). Concretement, tout
     * champ `'type' => 'file'` d'un formulaire servi par un
     * PluginAdminController etait mort — constate sur
     * AdminPieceStatusesController (champ « Icone »), mais le defaut etait
     * general : il touchait les 60+ controleurs admin de plugins.
     *
     * La reecriture empile les candidats dans une liste ordonnee et ne teste
     * `file_exists` qu'a la fin : ajouter un candidat ne peut plus couper les
     * suivants, et il y a toujours un `return`.
     *
     * @param string $template
     *
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplateFile($template) {

        $context = $this->getContext();
        $controller = $context->controller;
        $directory = $this->getTemplateDirectory();
        $candidates = [];

        /* 1. Surcharge portee par le plugin proprietaire du controleur.
              ⚠️ PluginAdminController::getTemplatePath() ne prend PAS
              d'argument, contrairement a Plugin::getTemplatePath($template) :
              l'argument que passait l'ancienne redaction etait ignore. */

        if ($controller instanceof PluginAdminController) {
            $pluginPath = $controller->getTemplatePath();

            if (!empty($pluginPath)) {
                $candidates[] = $this->_normalizeDirectory($pluginPath) . $directory . $template;
            }

        }

        /* 2. Surcharge par controleur, sous <tpl0>/controllers/<dossier>/.
              `override_folder` porte la convention reellement en usage dans le
              socle (« piece_statuses/ ») ; l'ancienne heuristique ne retenait
              que le deuxieme mot du nom de classe (« piece/ »), qui ne
              correspond a aucun repertoire existant. On empile les deux : la
              premiere est juste, la seconde preserve l'existant. */

        if ($controller instanceof AdminController) {
            $base = $this->_normalizeDirectory($context->smarty->getTemplateDir(0))
            . 'controllers' . DIRECTORY_SEPARATOR;

            if (!empty($controller->override_folder)) {
                $candidates[] = $base . $this->_normalizeDirectory($controller->override_folder) . $directory . $template;
            }

            if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($controller), $matches) && isset($matches[0][1])) {
                $candidates[] = $base . strtolower($matches[0][1]) . DIRECTORY_SEPARATOR . $directory . $template;
            }

        }

        /* 3. Repertoires de gabarits Smarty : la surcharge (index 1) avant le
              socle (index 0), comme le faisait l'ancienne chaine. */

        foreach ([1, 0] as $index) {
            $dir = $context->smarty->getTemplateDir($index);

            if (!empty($dir)) {
                $candidates[] = $this->_normalizeDirectory($dir) . $directory . $template;
            }

        }

        foreach ($candidates as $candidate) {

            if (file_exists($candidate)) {
                return $candidate;
            }

        }

        /* Dernier recours : chemin relatif, que Smarty resoudra sur ses propres
           repertoires. Jamais `null` — c'est tout l'objet du correctif. */

        return $directory . $template;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTitle($value) {

        $this->_title = $value;

        return $this;
    }

    /**
     * @return mixed
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTitle() {

        return $this->_title;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setUrl($value) {

        $this->_url = (string) $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getUrl() {

        return $this->_url;
    }

    /**
     * @param bool $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setUseAjax($value) {

        $this->_use_ajax = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function isMultiple() {

        return (isset($this->_multiple) && $this->_multiple);
    }

    /**
     * @return mixed
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function render() {

        $adminWebpath = str_ireplace(_SHOP_CORE_DIR_, '', _EPH_ROOT_DIR_);
        $adminWebpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $adminWebpath);
        $boTheme = ((Validate::isLoadedObject($this->getContext()->employee)
            && $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

        if (!file_exists(_EPH_BO_ALL_THEMES_DIR_ . $boTheme . DIRECTORY_SEPARATOR . 'template')) {
            $boTheme = 'default';
        }

        $this->getContext()->controller->addJs(_EPH_JS_DIR_ . 'jquery.iframe-transport.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_ . 'jquery.fileupload.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_ . 'jquery.fileupload-process.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_ . 'jquery.fileupload-validate.js');
        $this->getContext()->controller->addJs('https://cdn.ephenyx.io/vendor/spin.js');
        $this->getContext()->controller->addJs('https://cdn.ephenyx.io/vendor/ladda.js');

        if ($this->useAjax() && !isset($this->_template)) {
            $this->setTemplate(static::DEFAULT_AJAX_TEMPLATE);
        }

        $template = $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        );

        $template->assign(
            [
                'id'            => $this->getId(),
                'name'          => $this->getName(),
                'url'           => $this->getUrl(),
                'multiple'      => $this->isMultiple(),
                'files'         => $this->getFiles(),
                'title'         => $this->getTitle(),
                'max_files'     => $this->getMaxFiles(),
                'post_max_size' => $this->getPostMaxSizeBytes(),
                'drop_zone'     => $this->getDropZone(),
            ]
        );

        return $template->fetch();
    }

    /**
     * @return bool
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function useAjax() {

        return (isset($this->_use_ajax) && $this->_use_ajax);
    }

}
