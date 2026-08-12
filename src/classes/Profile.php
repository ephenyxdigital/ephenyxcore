<?php

namespace EphenyxDigital\EphenyxCore;


/**
 * Class ProfileCore
 *
 * @since 1.9.1.0
 */
class Profile extends PhenyxObjectModel {

    const PERMISSION_VIEW = 'view';
    const PERMISSION_ADD = 'add';
    const PERMISSION_EDIT = 'edit';
    const PERMISSION_DELETE = 'delete';
    
    public $require_context = false;
    // @codingStandardsIgnoreStart
    protected static $_cache_accesses = [];

    protected static $_cache_employee_accesses = [];

    /*
     * Table id_back_tab => id_parent, chargee en une requete. Sert a
     * isOutOfMatrixTab() ; voir le bloc de getProfileAccess().
     */
    protected static $_cache_tab_parents = null;
    public $generated;
    /** @var string Name */
    public $name;
    // @codingStandardsIgnoreEnd

    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'profile',
        'primary'   => 'id_profile',
        'multilang' => true,
        'fields'    => [
            /* Lang fields */
            'generated'           => ['type' => self::TYPE_BOOL, 'lang' => true],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
        ],
    ];

    /**
     * Get all available profiles
     *
     * @param $idLang
     *
     * @return array Profiles
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function getProfiles($idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('p.`id_profile`, `name`')
                ->from('profile', 'p')
                ->leftJoin('profile_lang', 'pl', 'p.`id_profile` = pl.`id_profile`')
                ->where('`id_lang` = ' . (int) $idLang)
                ->orderBy('`id_profile` ASC')
        );
    }

    /**
     * Get the current profile name
     *
     * @param int      $idProfile
     * @param int|null $idLang
     *
     * @return string Profile
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function getProfile($idProfile, $idLang = null) {

        if (!$idLang) {
            $idLang = Context::getContext()->phenyxConfig->get('EPH_LANG_DEFAULT');
        }

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
                ->select('`name`')
                ->from('profile', 'p')
                ->leftJoin('profile_lang', 'pl', 'p.`id_profile` = pl.`id_profile`')
                ->where('p.`id_profile` = ' . (int) $idProfile)
                ->where('pl.`id_lang` = ' . (int) $idLang)
        );
    }

    /**
     * @param int $idProfile
     * @param int $idTab
     *
     * @return bool
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    /**
     * ─── LE DEFAUT ETAIT « TOUT REFUSER », Y COMPRIS POUR LES ECRANS QUI NE
     *     FIGURENT PAS DANS LA MATRICE DES DROITS ───
     *
     * BackTab::getIdFromClassName() rend FALSE quand la classe n'a aucune ligne
     * dans back_tab. AdminController::__construct appelle alors cette methode
     * avec $idTab = false : aucune ligne ne correspond, et l'ancien defaut
     * rendait view/add/edit/delete a zero pour tout profil non maitre.
     *
     * Le releve du 2026-08-03 en a chiffre la portee : 77 controleurs
     * d'administration n'ont AUCUNE ligne back_tab, et 38 onglets portent
     * id_parent = -1 — ces derniers ont bien une ligne dans employee_access,
     * creee a zero par initAccess(), mais ne figurent pas dans l'ecran des
     * droits, donc personne ne peut la passer a un. Sur les 67 controleurs
     * dotes d'une garde tabAccess le meme jour, 34 seulement etaient reglables
     * depuis cet ecran. Les autres — AdminEducations, AdminStudentEducations,
     * AdminProductImages, AdminDeclinaisons, AdminSpecificPrices, AdminAgenda,
     * AdminSepa… — devenaient inaccessibles a tout profil non maitre, SANS
     * AUCUN MOYEN de leur accorder le droit depuis l'interface.
     *
     * ─── POURQUOI OUVRIR PLUTOT QUE FERMER ───
     *
     * Ces ecrans ne sont pas des portes d'entree : on n'ouvre pas
     * AdminProductImages sans passer par la fiche produit, ni AdminDeclinaisons
     * sans AdminProducts. Le verrou qui compte est celui de l'ecran parent, qui
     * lui figure dans la matrice. Refuser ici casse des fonctions sans recours ;
     * autoriser ne relache rien par rapport a la situation d'avant les gardes,
     * ou aucune verification n'existait sur ces chemins.
     *
     * ⚠️ C'est aussi la condition pour basculer EPH_AJAX_RIGHTS_MODE en
     * « enforce » : verifierDroitAjax() lit le meme tabAccess et souffre du meme
     * angle mort. Tant que le defaut est « refuser », la bascule couperait la
     * moitie du back-office.
     *
     * ⚠️ L'appel a isOutOfMatrixTab() est place AVANT la lecture des lignes, et
     * non apres. Un onglet id_parent = -1 A une ligne en base : la tester
     * d'abord la rendrait prioritaire et le plein droit ne s'appliquerait
     * jamais.
     */
    public static function getProfileAccess($idProfile, $idTab) {

        $idProfile = (int) $idProfile;
        $horsMatrice = static::isOutOfMatrixTab($idTab);

        if (!$horsMatrice) {
            // getProfileAccesses est mis en cache : pas de fuite de performance.
            $accesses = Profile::getProfileAccesses($idProfile);

            if (isset($accesses[$idTab]) && is_array($accesses[$idTab])) {
                return $accesses[$idTab];
            }

        }

        $perm = static::formatPermissionValue(
            $horsMatrice || $idProfile === (int) _EPH_ADMIN_PROFILE_
        );

        return [
            'id_profile' => $idProfile,
            'id_tab'     => $idTab,
            'class_name' => '',
            'view'       => $perm,
            'add'        => $perm,
            'edit'       => $perm,
            'delete'     => $perm,
        ];
    }

    /**
     * Un onglet est « hors matrice » lorsqu'il ne peut pas etre regle depuis
     * AdminEmployeeAccess. Deux cas, et un troisieme par prudence :
     *
     *   - $idTab vide ou false : la classe n'a aucune ligne back_tab. C'est le
     *     cas des 77 controleurs auxiliaires de plugins.
     *   - id_parent < 0 : onglet technique, volontairement absent du menu et de
     *     l'ecran des droits (AdminProductImages, AdminDeclinaisons, les quinze
     *     AdminChat*…).
     *   - identifiant qui ne correspond a aucune ligne : onglet supprime dont
     *     une reference trainait. Meme traitement.
     *
     * ⚠️ Si back_tab est vide — installation ou reparation en cours — tout est
     * declare hors matrice, donc tout est permis. C'est voulu : sans onglets il
     * n'y a pas de matrice, et refuser bloquerait l'ecran qui sert justement a
     * les reconstruire.
     *
     * @param int|bool|null $idTab
     *
     * @return bool
     */
    public static function isOutOfMatrixTab($idTab) {

        if (empty($idTab)) {
            return true;
        }

        if (static::$_cache_tab_parents === null) {
            static::$_cache_tab_parents = [];

            $rows = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                (new DbQuery())
                    ->select('`id_back_tab`, `id_parent`')
                    ->from('back_tab')
            );

            if (is_array($rows)) {

                foreach ($rows as $row) {
                    static::$_cache_tab_parents[(int) $row['id_back_tab']] = (int) $row['id_parent'];
                }

            }

        }

        if (!isset(static::$_cache_tab_parents[(int) $idTab])) {
            return true;
        }

        return static::$_cache_tab_parents[(int) $idTab] < 0;
    }

    /**
     * Vide les caches de permissions de cette classe.
     *
     * A appeler apres toute ecriture dans employee_access ou dans back_tab au
     * cours d'une meme requete — creation d'onglet par un plugin, matrice
     * reenregistree. Les actions ajax du back-office meurent sur un die() juste
     * apres leur ecriture, elles n'en ont donc pas besoin.
     */
    public static function resetAccessCache() {

        static::$_cache_employee_accesses = [];
        static::$_cache_tab_parents = null;
    }

    /**
     * @param int    $idProfile
     * @param string $type
     *
     * @return bool
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    /**
     * ─── LE CACHE SE VIDAIT LUI-MEME A CHAQUE APPEL ───
     *
     * La premiere instruction du corps etait :
     *
     *     static::$_cache_employee_accesses = [];
     *
     * Le cache statique ne servait donc jamais, alors que le commentaire de
     * getProfileAccess() juste au-dessus affirmait le contraire (« is cached so
     * there is no performance leak »). Chaque appel refaisait un
     * SELECT * FROM employee_access LEFT JOIN back_tab complet — et cette
     * methode est appelee depuis le constructeur de CHAQUE controleur
     * d'administration, dont AdminController::display() instancie toute la pile
     * des onglets ouverts en session.
     *
     * ⚠️ Le vidage n'est pas supprime, il est deplace dans resetAccessCache(),
     * a appeler explicitement quand une ecriture doit etre relue dans la meme
     * requete.
     */
    public static function getProfileAccesses($idProfile, $type = 'id_back_tab') {

		// @codingStandardsIgnoreStart
		if (!in_array($type, ['id_back_tab', 'class_name'])) {
            return false;
        }
		
        if (!isset(static::$_cache_employee_accesses[$idProfile])) {
            static::$_cache_employee_accesses[$idProfile] = [];
        }
		
        if (!isset(static::$_cache_employee_accesses[$idProfile][$type])) {
            static::$_cache_employee_accesses[$idProfile][$type] = [];
			
            if ($idProfile == _EPH_ADMIN_PROFILE_) {
                foreach (BackTab::getBackTabs(Context::getContext()->language->id) as $tab) {
                    static::$_cache_employee_accesses[$idProfile][$type][$tab['id_back_tab']] = [
                        'id_profile' => _EPH_ADMIN_PROFILE_,
                        'id_back_tab'  => $tab['id_back_tab'],
                        'view'       => '1',
                        'add'        => '1',
                        'edit'       => '1',
                        'delete'     => '1',
                    ];
                }

            } else {
                $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                    (new DbQuery())
                        ->select('*')
                        ->from('employee_access', 'a')
                        ->leftJoin('back_tab', 't', 't.`id_back_tab` = a.`id_back_tab`')
                        ->where('`id_profile` = ' . (int) $idProfile)
                );

                foreach ($result as $row) {
                    static::$_cache_employee_accesses[$idProfile][$type][$row[$type]] = $row;
                }

            }

        }

        return static::$_cache_employee_accesses[$idProfile][$type];
    }

    
	
	public static function getProfilePartnerAccesses(License $license, $idProfile, $type = 'id_back_tab') {

      
		
		$accesses = [];
        if ($idProfile == _EPH_ADMIN_PROFILE_) {

        	foreach (BackTab::getBackTabs(Context::getContext()->language->id) as $tab) {
            	$accesses[$idProfile][$type][$tab[$type]] = [
                	'id_profile' => _EPH_ADMIN_PROFILE_,
                    'id_back_tab'  => $tab['id_back_tab'],
                    'view'       => '1',
                    'add'        => '1',
                    'edit'       => '1',
                    'delete'     => '1',
                ];
            }

       } else {
			
			$query =  'SELECT *
			FROM `eph_employee_access` a
			LEFT JOIN `eph_back_tab` `t` ON t.`id_back_tab` = a.`id_back_tab`
			WHERE a.`id_profile` = '. (int) $idProfile;
			
			$result = $license->pushSqlRequest($query, 'executeS');
           	

            foreach ($result as $row) {
				
           		$accesses[$idProfile][$type][$row[$type]] = $row;
            }

        }
        return $accesses[$idProfile][$type];
    }

    /**
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @throws PhenyxException
     */
    /**
     * ─── DEUX TABLES INEXISTANTES ───
     *
     * La premiere requete s'ecrivait :
     *
     *     INSERT INTO <prefixe>access (SELECT <id>, id_back_tab, 0,0,0,0
     *                                  FROM <prefixe>tab)
     *
     * Ni « access » ni « tab » n'existent : les tables sont « employee_access »
     * et « back_tab ». Le reste de cette classe interroge bien les bonnes.
     *
     * La requete echouait donc en silence — DbPDO avale l'exception — et un
     * profil neuf naissait SANS AUCUNE LIGNE DE PERMISSION. C'est la raison
     * d'etre d'AdminEmployeeAccess::initAccess(), qui les fabriquait une par une
     * a chaque ouverture de l'ecran des droits : un SELECT par onglet et par
     * profil, plus un REPLACE quand la ligne manquait. Avec deux cents onglets
     * et cinq profils, un bon millier de requetes a chaque affichage.
     *
     * ⚠️ En prime, « INSERT INTO x (SELECT …) » sans liste de colonnes repose
     * sur l'ordre des colonnes de la table. Le jour ou l'on en ajoute une, la
     * requete continue de « marcher » en rangeant les valeurs de travers. Les
     * colonnes sont donc nommees.
     *
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     */
    public function add($autoDate = true, $nullValues = false) {

        if (!parent::add($autoDate, true)) {
            return false;
        }

        $result = Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'employee_access`
                (`id_profile`, `id_back_tab`, `view`, `add`, `edit`, `delete`)
             SELECT ' . (int) $this->id . ', t.`id_back_tab`, 0, 0, 0, 0
               FROM `' . _DB_PREFIX_ . 'back_tab` t'
        );

        $result = $result && Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'plugin_access`
                (`id_profile`, `id_plugin`, `configure`, `view`, `uninstall`)
             SELECT ' . (int) $this->id . ', p.`id_plugin`, 0, 1, 0
               FROM `' . _DB_PREFIX_ . 'plugin` p'
        );

        return $result;
    }

    /**
     * @return bool
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @throws PhenyxDatabaseExceptionException
     */
    /**
     * ─── LES DEUX GARDE-FOUS SONT ICI, ET NON DANS LE CONTROLEUR ───
     *
     * AdminProfilesController::ajaxProcessDeleteProfile() portait ces deux
     * controles — « on ne supprime pas le profil maitre », « on ne supprime pas
     * un profil utilise ». Cette methode n'est jamais appelee : le bouton de la
     * grille passe par deleteObject(), donc par la suppression generique de
     * PhenyxController. Les protections etaient donc inertes.
     *
     * Les poser ici les rend valables pour TOUS les chemins d'appel, present et
     * a venir. C'est leur vraie place.
     *
     * ─── ET LA TABLE N'ETAIT PAS LA BONNE ───
     *
     * La ligne s'ecrivait :
     *
     *     Db::getInstance()->delete('access', ...)
     *
     * La table des permissions s'appelle « employee_access ». « access » n'existe
     * pas — le reste de cette classe interroge bien employee_access. La
     * suppression echouait donc, ce qui avait deux effets : les permissions du
     * profil restaient en base a jamais, et delete() rendait false ALORS QUE LA
     * LIGNE DU PROFIL ETAIT DEJA SUPPRIMEE par parent::delete(). Un demi-succes
     * annonce comme un echec.
     *
     * ⚠️ Le meme defaut se trouve dans add() juste au-dessus, avec en prime une
     * seconde table inexistante : « INSERT INTO access (… FROM tab) » — la table
     * des onglets s'appelle back_tab. C'est pourquoi un profil neuf n'a aucune
     * ligne de permission, et pourquoi AdminEmployeeAccess::initAccess() doit les
     * fabriquer une par une a chaque ouverture de l'ecran. Non corrige ici :
     * cela change le comportement de la creation de profil, ça se decide et se
     * teste a part.
     *
     * @return bool
     */
    public function delete() {

        if (defined('_EPH_ADMIN_PROFILE_') && (int) $this->id === (int) _EPH_ADMIN_PROFILE_) {
            PhenyxLogger::addLog(
                'Profile::delete refuse : suppression du profil administrateur (id ' . (int) $this->id . ')',
                2, null, 'Profile', (int) $this->id, true
            );

            return false;
        }

        $used = Employee::getEmployeesByProfile((int) $this->id);

        if (is_array($used) && count($used)) {
            PhenyxLogger::addLog(
                'Profile::delete refuse : profil ' . (int) $this->id . ' encore porte par ' . count($used) . ' compte(s)',
                2, null, 'Profile', (int) $this->id, true
            );

            return false;
        }

        if (!parent::delete()) {
            return false;
        }

        return (
            Db::getInstance()->delete('employee_access', '`id_profile` = ' . (int) $this->id)
            && Db::getInstance()->delete('plugin_access', '`id_profile` = ' . (int) $this->id)
        );
    }
    
    public static function isValidPermission($permission)  {
       return $permission && is_string($permission) && in_array($permission, [
           Profile::PERMISSION_VIEW,
           Profile::PERMISSION_DELETE,
           Profile::PERMISSION_ADD,
           Profile::PERMISSION_EDIT,
       ]);
    }
    
    public static function formatPermissionValue($hasPermission) {
        return $hasPermission ? '1' : '0';
    }

}
