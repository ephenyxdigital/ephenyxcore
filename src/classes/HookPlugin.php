<?php

namespace EphenyxDigital\QuantumCore;


/**
 * Class HookPlugin
 *
 * @since 1.9.1.0
 */
class HookPlugin extends PhenyxObjectModel {

    public $require_context = false;

    public $id_plugin;

    public $id_hook;

    public $position;

    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'hook_plugin',
        'primary' => 'id_hook_plugin',
        'fields'  => [
            'id_plugin' => ['type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 64],
            'id_hook'   => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'position'  => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
        ],
    ];

    public function __construct($id = null, $idLang = null) {

        parent::__construct($id, $idLang);

    }

    public function add($autoDate = true, $nullValues = false, $position = null) {

        if (is_null($position)) {
            $this->position = $this->getNewLastPosition();
        } else {
            $this->adjustPosition($position);
            $this->position = $position;
        }

        return parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = true, $init = true) {

        $oldMenu = new HookPlugin($this->id);

        if ($this->position != $oldMenu->position) {
            $this->adjustPosition($this->position);
        }

        if (parent::update($nullValues)) {
            return true;

        }

    }

    /**
     * Decale d'un rang les greffes situees a $position et au-dela, pour faire de
     * la place a celle qu'on insere.
     *
     * ─── LE FILTRE PORTAIT SUR LE MAUVAIS CHAMP ───
     *
     * La requete etait bornee par « id_plugin = <ce plugin> » au lieu de
     * « id_hook = <cette ancre> ». Comme un plugin est greffe sur plusieurs
     * ancres — souvent des dizaines —, chaque insertion ou deplacement dans UNE
     * ancre repoussait les positions de ce meme plugin dans TOUTES les autres.
     * L'ordre d'affichage du front derivait donc a chaque manipulation, sans que
     * rien ne le signale, et l'ancre reellement modifiee etait la seule a ne pas
     * bouger.
     *
     * L'origine est un copier-coller depuis une classe de menu : le reste de la
     * methode parle encore de « $menus », et update() nomme sa copie « $oldMenu ».
     * Les deux autres methodes de position de cette classe,
     * getNewLastPosition() et cleanPositions(), sont bien bornees par id_hook —
     * celle-ci etait la seule intruse.
     *
     * @param int $position
     *
     * @return void
     */
    public function adjustPosition($position) {

        $grafts = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('t.`id_hook_plugin`, t.`position`')
                ->from('hook_plugin', 't')
                ->where('t.`id_hook` = ' . (int) $this->id_hook)
                ->where('t.`id_hook_plugin` != ' . (int) $this->id)
                ->where('t.`position` >= ' . (int) $position)
                ->orderBy('t.`position` ASC')
        );

        if (!is_array($grafts)) {
            return;
        }

        $i = (int) $position + 1;

        foreach ($grafts as $graft) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET `position` = ' . (int) $i
                . ' WHERE `id_hook_plugin` = ' . (int) $graft['id_hook_plugin']
            );
            $i++;
        }

    }

    public function delete() {

        if (parent::delete()) {

            return $this->cleanPositions();
        }

        return false;
    }

    public function getNewLastPosition() {

        return (Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('IFNULL(MAX(`position`), 0) + 1')
                ->from('hook_plugin')
                ->where('`id_hook` = ' . (int) $this->id_hook)
        ));
    }

    public function cleanPositions() {

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('`id_hook_plugin`')
                ->from('hook_plugin')
                ->where('`id_hook` = ' . (int) $this->id_hook)
                ->orderBy('`position`')
        );

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            Db::getInstance()->update(
                'hook_plugin',
                [
                    'position' => (int) $i,
                ],
                '`id_hook` = ' . (int) $this->id_hook . ' AND `id_hook_plugin` = ' . (int) $result[$i]['id_hook_plugin']
            );
        }

        return true;
    }

}
