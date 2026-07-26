<?php

namespace EphenyxDigital\QuantumCore;


/**
 * @since 1.9.1.0
 */
class MailTemplate extends PhenyxObjectModel {

    
    // @codingStandardsIgnoreStart
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'mail_template',
        'primary' => 'id_mail_template',
        'multilang' => true,
        'fields'  => [
            'template' => ['type' => self::TYPE_STRING, 'required' => true],                   
            'plugin'         => ['type' => self::TYPE_STRING, 'validate' => 'isTabName', 'size' => 64],
            
            'generated' => ['type' => self::TYPE_BOOL, 'lang' => true],
            'target'   => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],     
            'name'     => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
        ],
    ];
    public $template;
    public $target;
    public $plugin;
    
    public $generated;
    public $name;
    // @codingStandardsIgnoreEnd
    public $content;
    
    public $template_path;

    /**
     * GenderCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($id = null, $idLang = null) {

        parent::__construct($id, $idLang);

        if ($this->id) {
            $this->content = $this->getTemplateContent();
            $this->getTemplatePath();
        }

    }
    
    public static function buildObject($id, $id_lang = null, $className = null) {

        $objectData = parent::buildObject($id, $id_lang, $className);
        $objectData['content'] = self::getStaticTemplateContent($objectData);
        $objectData['template_path'] = self::getStaticTemplatePath($objectData);

        return Tools::jsonDecode(Tools::jsonEncode($objectData));
    }

    /**
     * Resolve the on-disk path of a plugin's mail template, checking the
     * site-specific override directory (_EPH_SPECIFIC_PLUGIN_DIR_) first,
     * then falling back to the standard plugin directory (_EPH_PLUGIN_DIR_).
     * A plugin can exist purely under specific_plugins (no counterpart in
     * plugins/) or be overridden there while also shipping in plugins/ —
     * in both cases the specific_plugins copy is the one that should win,
     * consistent with Translate::loadPluginTranslations() and the
     * PhenyxAutoload override resolution.
     *
     * @return string|null Absolute path if found in either location, else null.
     */
    public static function resolvePluginTemplatePath($plugin, $template) {

        if (empty($plugin) || empty($template)) {
            return null;
        }

        $specificPath = _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . '/views/mails/' . $template;

        if (file_exists($specificPath)) {
            return $specificPath;
        }

        $pluginPath = _EPH_PLUGIN_DIR_ . $plugin . '/views/mails/' . $template;

        if (file_exists($pluginPath)) {
            return $pluginPath;
        }

        return null;
    }

    public function getTemplatePath() {

        if (empty($this->plugin) && file_exists(_EPH_MAIL_DIR_ . $this->template)) {

            $this->template_path = _EPH_MAIL_DIR_ . $this->template;
        } else {
            $pluginPath = self::resolvePluginTemplatePath($this->plugin, $this->template);

            if ($pluginPath !== null) {
                $this->template_path = $pluginPath;
            }

        }

    }

    public static function getStaticTemplatePath($mailtemplate) {

        $template_path = null;

        if (empty($mailtemplate['plugin']) && file_exists(_EPH_MAIL_DIR_ . $mailtemplate['template'])) {

            $template_path = _EPH_MAIL_DIR_ . $mailtemplate['template'];
        } else {
            $template_path = self::resolvePluginTemplatePath($mailtemplate['plugin'], $mailtemplate['template']);
        }

        return $template_path;

    }

    public function getTemplateContent() {

        $content = '';

        if (empty($this->plugin) && file_exists(_EPH_MAIL_DIR_ . $this->template)) {
            $tpl = str_replace('.tpl', '', $this->template);

            $content = file_get_contents(_EPH_MAIL_DIR_ . $this->template);
            $content = $this->context->_tools->parseEmailContent($content, $tpl);
        } else {
            $pluginPath = self::resolvePluginTemplatePath($this->plugin, $this->template);

            if ($pluginPath !== null) {
                $tpl = str_replace('.tpl', '', $this->template);
                $content = file_get_contents($pluginPath);
                $content = $this->context->_tools->parseEmailContent($content, $tpl, $this->plugin);
            }

        }

        return $content;

    }

    public static function getStaticTemplateContent($mailtemplate) {

        $_tools = PhenyxTool::getInstance();
        $content = '';

        if (empty($mailtemplate['plugin']) && file_exists(_EPH_MAIL_DIR_ . $mailtemplate['template'])) {
            $tpl = str_replace('.tpl', '', $mailtemplate['template']);

            $content = file_get_contents(_EPH_MAIL_DIR_ . $mailtemplate['template']);
            $content = $_tools->parseEmailContent($content, $tpl);
        } else {
            $pluginPath = self::resolvePluginTemplatePath($mailtemplate['plugin'], $mailtemplate['template']);

            if ($pluginPath !== null) {
                $tpl = str_replace('.tpl', '', $mailtemplate['template']);
                $content = file_get_contents($pluginPath);
                $content = $_tools->parseEmailContent($content, $tpl, $mailtemplate['plugin']);
            }

        }

        return $content;

    }

    public static function getObjectByTemplateName($template) {

        return Db::getInstance()->getValue(
            (new DbQuery())
                ->select('`id_mail_template`')
                ->from('mail_template')
                ->where('`template` LIKE  \'' . $template . '\'')
        );
    }

    
    public static function getMalPlugins() {
        
        $plugins = [];
        $request = Db::getInstance()->executeS(
            (new DbQuery())
                ->select('DISTINCT(`plugin`)')
                ->from('mail_template')
        );
        foreach($request as $plugin) {
            if(!empty($plugin['plugin'])) {
                $plugins[$plugin['plugin']] = $plugin['plugin'];
            }
            
        }
        
        return $plugins;
    }
}
