<?php

namespace EphenyxDigital\EphenyxCore;


/**
 * Class Page
 *
 * @since 1.9.1.0
 */
class Page extends PhenyxObjectModel {

    public $require_context = false;
    // @codingStandardsIgnoreStart

    public $id_page_type;
    public $id_object;

    // @codingStandardsIgnoreEnd
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'page',
        'primary' => 'id_page',
        'fields'  => [
            'id_page_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_object'    => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
        ],
    ];

    public static function getCurrentId() {

        $controller = Performer::getInstance()->getController();
        $pageTypeId = Page::getPageTypeByName($controller);

        $specialArray = [
            'pfg' => 'id_pfg',
        ];
        $extraSpecialArrays = Context::getContext()->_hook->exec('actionGetSpecialArrays', [], null, true);

        if (is_array($extraSpecialArrays) && count($extraSpecialArrays)) {

            foreach ($extraSpecialArrays as $plugin => $pages) {

                if (is_array($pages) && count($pages)) {

                    foreach ($pages as $key => $value) {
                        $specialArray[] = $value;
                    }

                }

            }

        }

        $where = '';
        $insertData = [
            'id_page_type' => $pageTypeId,
        ];

        if (array_key_exists($controller, $specialArray)) {
            $objectId = Tools::getValue($specialArray[$controller], null);
            $where = ' AND `id_object` = ' . (int) $objectId;
            $insertData['id_object'] = (int) $objectId;
        }

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
                ->select('`id_page`')
                ->from('page')
                ->where('`id_page_type` = ' . (int) $pageTypeId . $where)
        );

        if (!empty($result) && $result['id_page']) {
            return $result['id_page'];
        }

        Db::getInstance()->insert('page', $insertData, true);

        return Db::getInstance()->Insert_ID();
    }

    public static function getPageTypeByName($name) {

        if (empty($name)) {
            return;
        }

        if ($value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
            ->select('`id_page_type`')
            ->from('page_type')
            ->where('`name` = \'' . pSQL($name) . '\'')
        )) {
            return $value;
        }

        $type = new PageType();
        $type->name = $name;
        return $type->add();

    }

    /**
     * Incremente le compteur de vues d'une page pour la periode courante.
     *
     * ═══════════════════════════════════════════════════════════════════
     *  UN SEUL ORDRE, PARCE QUE DEUX ORDRES SE COURENT APRES
     * ═══════════════════════════════════════════════════════════════════
     *
     * Cette methode faisait : UPDATE, puis « si aucune ligne touchee, INSERT ».
     * Deux visiteurs qui ouvrent la meme page au meme instant passent tous les
     * deux par l'UPDATE — qui ne touche rien puisque la ligne n'existe pas
     * encore — puis tous les deux par l'INSERT. Le second recoit
     * « Duplicate entry '18-1776' for key 'PRIMARY' », et l'alerte part.
     *
     * ⚠️ CE N'EST PAS UN CAS RARE, C'EST LE CAS NORMAL AU CHANGEMENT DE
     * PERIODE. `id_date_range` change a chaque nouvelle plage : la premiere
     * ligne de chaque page doit alors etre creee, et c'est precisement le seul
     * moment ou la fenetre de course est ouverte. Plus il y a de trafic, plus
     * elle est probable.
     *
     * Un `INSERT ... ON DUPLICATE KEY UPDATE` fait le travail en UN ordre, que
     * la base execute sous verrou. La cle primaire (id_page, id_date_range)
     * garantit qu'il n'y a rien a arbitrer.
     *
     * ⚠️ Db::insert() avec ON_DUPLICATE_KEY ne convient PAS ici : il reecrit
     * chaque colonne avec la valeur fournie, donc `counter` = 1. Il remettrait
     * le compteur a un a chaque visite au lieu de l'incrementer. D'ou le SQL
     * ecrit a la main.
     *
     * @param int $idPage
     *
     * @return void
     */
    public static function setPageViewed($idPage) {

        $idPage      = (int) $idPage;
        $idDateRange = (int) DateRange::getCurrentRange();

        /*
         * Sans page ni periode, il n'y a rien a compter. La garde evite
         * d'accumuler des lignes de clef 0 que personne ne lira jamais et qui
         * fausseraient les totaux par periode.
         */

        if ($idPage <= 0 || $idDateRange <= 0) {
            return;
        }

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'page_viewed` (`id_page`, `id_date_range`, `counter`)
             VALUES (' . $idPage . ', ' . $idDateRange . ', 1)
             ON DUPLICATE KEY UPDATE `counter` = `counter` + 1'
        );
    }

}
