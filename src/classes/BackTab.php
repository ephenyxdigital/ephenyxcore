<?php



namespace EphenyxDigital\EphenyxCore;



use Language;





/**

 * Class BackTab

 *

 * @since 1.9.1.0

 */

class BackTab extends PhenyxObjectModel {



    protected static $_getIdFromClassName = null;



    protected static $_getIdFromFuncAndClassName = null;



    protected static $_cache_back_tab = [];



    protected static $_tabAccesses = [];



    public $generated;



    public $name;



    public $function;



    public $plugin;



    public $fa_duatone = null;



    public $class_name = null;



    public $id_parent;



    public $position;



    public $has_divider;



    public $active = true;



    public $master;



    public $is_global;



    public $is_specific;



    public $accesses;



    public $parent_class;

    

    public $common_function = null;



    protected static $instance;



    /**

     * @see PhenyxObjectModel::$definition

     */

    public static $definition = [

        'table'     => 'back_tab',

        'primary'   => 'id_back_tab',

        'multilang' => true,

        'fields'    => [

            'class_name'        => ['type' => self::TYPE_STRING, 'size' => 64],

            'id_parent'         => ['type' => self::TYPE_INT, 'validate' => 'isInt'],

            'position'          => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],

            'function'          => ['type' => self::TYPE_STRING, 'size' => 64],

            'plugin'            => ['type' => self::TYPE_STRING],

            'fa_duatone'        => ['type' => self::TYPE_STRING],

            'has_divider'       => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            'is_global'         => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            'is_specific'       => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            'common_function'   => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            'active'            => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            'master'            => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],



            /* Lang fields */

            'generated'         => ['type' => self::TYPE_BOOL, 'lang' => true],

            'name'              => ['type' => self::TYPE_STRING, 'lang' => true],

        ],

    ];



    public function __construct($id = null, $full = true, $idLang = null) {



        parent::__construct($id, $idLang);



        if ($this->id) {



            $this->accesses = $this->getAccesses();

            if($this->id_parent > 0) {

                $this->parent_class = $this->getParentClass();

            }

            

        }



    }



    public static function buildObject($id, $idLang = null, $className = null) {



        $objectData = parent::buildObject($id, $idLang, $className);

        if(isset($objectData['id_parent']) && $objectData['id_parent'] > 0) {

            $objectData['parent_class'] = self::getStaticParentClass($objectData['id_parent']);

        }

        



        return Tools::jsonDecode(Tools::jsonEncode($objectData));

    }



    public static function getGlobalTabs() {



        $backTabs = [];

        $tabs = new PhenyxCollection('BackTab');

        $tabs->where('id_back_tab', '>', 1);

        $tabs->where('id_parent', '>', 0);

        $tabs->where('is_specific', '=', 0);



        foreach ($tabs as $tab) {

            $backTabs[] = BackTab::buildObject($tab->id);

        }



        return $backTabs;



    }

	

	/**

	 * Liste les AdminControllers des plugins presents sur le disque mais NON

	 * installes, avec le libelle par defaut (`$this->publicName`) que chaque

	 * controleur s'assigne d'ordinaire dans son constructeur.

	 *

	 * Meme principe que Meta::getPluginPages() (analyse statique du code

	 * source - cf. never_require_plugin_controller), mais uniquement sur

	 * controllers/admin : un tab n'existe que cote back-office.

	 *

	 * @return array [pluginName => [className => publicName|false, ...]]

	 */

	public static function getPluginsTabs() {

		

		$baseDirs = [_EPH_PLUGIN_DIR_, _EPH_SPECIFIC_PLUGIN_DIR_];

		// Table de resolution : toutes les classes de controleur du disque.

		$definitions = [];

		// Ce qu'on restitue : uniquement les plugins non installes.

		$notInstalled = [];

		

		foreach (Plugin::getPluginsDirOnDisk() as $plugin) {



			$isInstalled = Plugin::isInstalled($plugin);



			foreach ($baseDirs as $baseDir) {



				$pluginDir = $baseDir . $plugin;



				if (!is_dir($pluginDir)) {

					continue;

				}



				foreach (glob($pluginDir . '/controllers/admin/*.php') as $file) {



					if (basename($file) === 'index.php') {

						continue;

					}



					$definition = BackTab::parseAdminControllerFile($file);



					// Fichier sans declaration de classe.

					if (!$definition) {

						continue;

					}



					$definitions[$definition['class']] = $definition;



					if (!$isInstalled) {

						$notInstalled[$plugin][] = $definition;

					}



				}





			}



		}



		$pluginTabs = [];



		foreach ($notInstalled as $plugin => $pluginDefinitions) {



			$classes = [];



			foreach ($pluginDefinitions as $definition) {

				// Controleur d'override : la classe ne se termine pas par
				// "Core" et etend directement <MemeNom>Core (ex. AdminPFGController
				// extends AdminPFGControllerCore dans ph_learning). Ce fichier ne
				// personnalise qu'un controleur Core existant - potentiellement
				// d'un autre plugin - ce n'est pas un onglet distinct.
				if (BackTab::isOverrideController($definition)) {
					continue;
				}

				// ⚠️ LA CLE EST LE NOM DE ROUTAGE, PAS LE NOM DE CLASSE DECLARE.
				//
				// C'est cette cle qui devient `$_TABS['...']` dans
				// <plugin>/translations/<iso>/tab.php, et c'est sous cette meme
				// cle que `Plugin::translateWord($s, $lang, 'tab', $source)` ira
				// la relire a l'installation — avec pour `$source` ce que le
				// plugin passe a `installPluginTab()`, c'est-a-dire
				// « AdminBillofPayment », jamais « AdminBillofPaymentControllerCore ».
				//
				// Or un fichier de plugin declare `class XControllerCore`, l'alias
				// `XController` etant fabrique par PhenyxAutoload — et seulement
				// pour un plugin INSTALLE. L'analyse statique lit donc le nom
				// suffixe, et la cle ecrite ne correspondait a rien :
				//
				//   plugin non installe  ->  $_TABS['AdminBillofPaymentControllerCore']
				//   plugin installe      ->  translateWord(..., 'AdminBillofPayment')
				//
				// Consequence : le libelle saisi AVANT l'installation etait perdu
				// A l'installation, l'onglet reprenait sa chaine anglaise, et
				// l'ecran de traduction affichait deux entrees pour un seul
				// onglet — l'une suffixee, l'autre non — selon que le plugin
				// etait installe ou pas.

				$routing = BackTab::routingClassName($definition['class']);

				// Collision : deux fichiers du meme plugin peuvent se reduire au
				// meme nom de routage — un `XControllerCore` et un `XController`
				// qui n'etend pas son Core, donc non ecarte par
				// isOverrideController(). La declaration `Core` est la canonique :
				// c'est elle que PhenyxAutoload aliasera. On ne l'ecrase pas.

				if (isset($classes[$routing]) && substr($definition['class'], -4) !== 'Core') {
					continue;
				}

				$classes[$routing] = BackTab::resolvePublicName($definition, $definitions);

			}



			if ($classes) {

				ksort($classes);

				$pluginTabs[$plugin] = $classes;

			}



		}



		ksort($pluginTabs);



		return $pluginTabs;

	}



	/**

	 * Extrait, sans charger le fichier, le nom de la classe declaree, sa

	 * classe parente et le libelle assigne a `$this->publicName` (litteral ou

	 * enveloppe dans un appel `$this->la(...)`).

	 *

	 * ATTENTION : ne jamais require_once le fichier - cf.

	 * Meta::parseControllerFile(), meme raison (alias fabrique par

	 * PhenyxAutoload, jamais present pour un plugin non installe).

	 *

	 * @param string $file

	 *

	 * @return array|false ['class' => ..., 'parent' => ..., 'publicName' => string|null, 'file' => ...]

	 */

	protected static function parseAdminControllerFile($file) {



		$content = @file_get_contents($file);



		if ($content === false || stripos($content, '<?php') === false) {

			return false;

		}



		$tokens = @token_get_all($content);



		if (!is_array($tokens)) {

			return false;

		}



		$nameTokens = [T_STRING];



		if (defined('T_NAME_QUALIFIED')) {

			$nameTokens[] = T_NAME_QUALIFIED;

			$nameTokens[] = T_NAME_FULLY_QUALIFIED;

		}



		$class = null;

		$parent = null;

		$publicName = null;

		$count = count($tokens);



		for ($i = 0; $i < $count; $i++) {



			if (!is_array($tokens[$i])) {

				continue;

			}



			// Declaration de la classe (la premiere du fichier) - identique a

			// Meta::parseControllerFile().

			if ($class === null && $tokens[$i][0] === T_CLASS) {



				$previous = null;



				for ($p = $i - 1; $p >= 0; $p--) {



					if (is_array($tokens[$p]) && in_array($tokens[$p][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {

						continue;

					}



					$previous = $tokens[$p];

					break;

				}



				if (is_array($previous) && $previous[0] === T_DOUBLE_COLON) {

					continue;

				}



				$expect = 'class';



				for ($j = $i + 1; $j < $count; $j++) {



					if (!is_array($tokens[$j])) {



						if ($tokens[$j] === '{') {

							break;

						}



						continue;

					}



					if ($tokens[$j][0] === T_EXTENDS) {

						$expect = 'parent';

						continue;

					}



					if ($tokens[$j][0] === T_IMPLEMENTS) {

						break;

					}



					if (in_array($tokens[$j][0], $nameTokens, true)) {



						if ($expect === 'class') {

							$class = $tokens[$j][1];

							$expect = null;

						} else



						if ($expect === 'parent') {

							$parent = ltrim($tokens[$j][1], '\\');

							break;

						}



					}



				}



				continue;

			}



			// $this->publicName = '...'; ou $this->publicName = $this->la('...');

			if ($publicName !== null || $tokens[$i][0] !== T_VARIABLE || $tokens[$i][1] !== '$this') {

				continue;

			}



			$p = BackTab::skipInsignificantTokens($tokens, $count, $i + 1);



			if ($p >= $count || !is_array($tokens[$p]) || $tokens[$p][0] !== T_OBJECT_OPERATOR) {

				continue;

			}



			$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);



			if ($p >= $count || !is_array($tokens[$p]) || $tokens[$p][0] !== T_STRING || $tokens[$p][1] !== 'publicName') {

				continue;

			}



			$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);



			if ($p >= $count || $tokens[$p] !== '=') {

				continue;

			}



			$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);

			$literal = BackTab::readLiteralOrLaCall($tokens, $p, $count);



			if ($literal !== null) {

				$publicName = $literal;

			}



		}



		if ($class === null) {

			return false;

		}



		return [

			'class'      => $class,

			'parent'     => $parent,

			'publicName' => $publicName,

			'file'       => $file,

		];

	}



	/**

	 * Position du premier token significatif a partir de $pos (saute

	 * espaces/commentaires).

	 *

	 * @param array $tokens

	 * @param int   $count

	 * @param int   $pos

	 *

	 * @return int

	 */

	protected static function skipInsignificantTokens(array $tokens, $count, $pos) {



		while ($pos < $count && is_array($tokens[$pos]) && in_array($tokens[$pos][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {

			$pos++;

		}



		return $pos;

	}



	/**

	 * Valeur d'une expression assignee a `$this->publicName` : soit une

	 * chaine litterale directe (`'Taxes'`), soit le premier argument litteral

	 * d'un appel `$this->la('...')` (la forme la plus courante, cf. la

	 * convention English-source de feedback_source_strings_english). Toute

	 * autre forme (concatenation, sprintf, constante) retourne null : rien

	 * d'exploitable sans executer le code.

	 *

	 * @param array $tokens

	 * @param int   $pos

	 * @param int   $count

	 *

	 * @return string|null

	 */

	protected static function readLiteralOrLaCall(array $tokens, $pos, $count) {



		if ($pos >= $count) {

			return null;

		}



		if (is_array($tokens[$pos]) && $tokens[$pos][0] === T_CONSTANT_ENCAPSED_STRING) {

			return trim($tokens[$pos][1], '\'"');

		}



		if (!is_array($tokens[$pos]) || $tokens[$pos][0] !== T_VARIABLE || $tokens[$pos][1] !== '$this') {

			return null;

		}



		$p = BackTab::skipInsignificantTokens($tokens, $count, $pos + 1);



		if ($p >= $count || !is_array($tokens[$p]) || $tokens[$p][0] !== T_OBJECT_OPERATOR) {

			return null;

		}



		$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);



		if ($p >= $count || !is_array($tokens[$p]) || $tokens[$p][0] !== T_STRING || $tokens[$p][1] !== 'la') {

			return null;

		}



		$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);



		if ($p >= $count || $tokens[$p] !== '(') {

			return null;

		}



		$p = BackTab::skipInsignificantTokens($tokens, $count, $p + 1);



		if ($p >= $count || !is_array($tokens[$p]) || $tokens[$p][0] !== T_CONSTANT_ENCAPSED_STRING) {

			return null;

		}



		return trim($tokens[$p][1], '\'"');

	}



	/**

	 * Detecte un controleur d'override : sa classe ne porte pas le suffixe

	 * "Core" et etend directement <MemeNom>Core (ex. AdminPFGController

	 * extends AdminPFGControllerCore). Convention PhenyxAutoload : le fichier

	 * "Core" est le controleur d'origine (potentiellement d'un autre plugin

	 * ou du coeur) ; ce fichier-ci ne fait que le personnaliser depuis un

	 * plugin donne, ce n'est pas un onglet distinct a lister.

	 *

	 * @param array $definition ['class' => ..., 'parent' => ..., ...]

	 *

	 * @return bool

	 */

	/**
	 * Nom de ROUTAGE d'un controleur, a partir du nom de classe declare.
	 *
	 * ── LA REGLE, ET POURQUOI ELLE TIENT EN DEUX LIGNES ────────────────────
	 *
	 * Le fork suit une convention unique : un fichier de controleur declare
	 * `class XControllerCore`, et `PhenyxAutoload` fabrique `class XController
	 * extends XControllerCore`. Le nom par lequel on ROUTE, celui que porte
	 * `back_tab.class_name`, que `installPluginTab()` recoit et que
	 * `translateWord(..., 'tab', $source)` cherche, est `X` — sans suffixe.
	 *
	 * Les deux suffixes sont retires dans cet ordre, et l'ordre compte :
	 * retirer `Controller` d'abord laisserait `XCore`.
	 *
	 * Un nom qui ne porte aucun des deux suffixes est rendu tel quel : ce n'est
	 * pas une anomalie a corriger, seulement un controleur nomme autrement.
	 *
	 * @param string $class Nom de classe declare, ex. `AdminBillofPaymentControllerCore`
	 *
	 * @return string Nom de routage, ex. `AdminBillofPayment`
	 */
	public static function routingClassName($class) {

		$class = (string) $class;

		foreach (['ControllerCore', 'Controller'] as $suffixe) {

			$longueur = strlen($suffixe);

			if (strlen($class) > $longueur && substr($class, -$longueur) === $suffixe) {
				return substr($class, 0, -$longueur);
			}

		}

		return $class;
	}

	/**
	 * Cles historiques sous lesquelles un libelle d'onglet a pu etre enregistre.
	 *
	 * ⚠️ INDISPENSABLE POUR NE PAS PERDRE LE TRAVAIL DEJA FAIT. Les fichiers
	 * tab.php ecrits avant la correction du 2026-08-17 portent la cle suffixee.
	 * Sans ce repli, la correction aurait « perdu » toutes les traductions
	 * d'onglets de plugins non installes — techniquement elles seraient encore
	 * dans le fichier, mais plus personne ne les lirait, ce qui revient au meme
	 * pour celui qui les a saisies.
	 *
	 * Le repli est en LECTURE seulement : l'ecriture se fait sous la cle de
	 * routage, si bien qu'un simple enregistrement remet le fichier d'aplomb.
	 *
	 * @param string $routing Nom de routage
	 *
	 * @return array
	 */
	public static function legacyTabKeys($routing) {

		return [$routing . 'ControllerCore', $routing . 'Controller'];
	}

	protected static function isOverrideController(array $definition) {



		$class = $definition['class'];

		$parent = $definition['parent'];



		if ($parent === null || $parent === '') {

			return false;

		}



		if (substr($class, -4) === 'Core') {

			return false;

		}



		return $parent === $class . 'Core';

	}



	/**

	 * Determine le publicName d'un AdminController non installe : litteral

	 * trouve dans le fichier, sinon remontee de l'heritage au sein du meme

	 * plugin (un enfant qui ne redefinit pas son constructeur herite de celui

	 * du parent, donc de son assignation de publicName).

	 *

	 * Contrairement a Meta::resolvePhpSelf(), pas de repli par

	 * ReflectionClass::getDefaultProperties() sur un parent autoloadable :

	 * publicName n'est jamais declaree avec une valeur par defaut litterale,

	 * toujours assignee en corps de constructeur - illisible sans executer le

	 * code. Pas de derivation depuis le nom de classe non plus (aucune valeur

	 * synthetique ne vaudrait un vrai libelle humain) : on retourne false et

	 * on laisse l'appelant decider du repli final (tab.php existant, puis nom

	 * de classe brut en dernier recours).

	 *

	 * @param array $definition

	 * @param array $definitions Toutes les classes du plugin, indexees par nom

	 *

	 * @return string|false

	 */

	protected static function resolvePublicName(array $definition, array $definitions) {



		if ($definition['publicName'] !== null) {

			return $definition['publicName'];

		}



		$parent = $definition['parent'];

		$seen = [];



		while ($parent && !isset($seen[$parent])) {



			$seen[$parent] = true;



			if (isset($definitions[$parent])) {



				if ($definitions[$parent]['publicName'] !== null) {

					return $definitions[$parent]['publicName'];

				}



				$parent = $definitions[$parent]['parent'];

				continue;

			}



			if (isset($definitions[$parent . 'Core'])) {



				if ($definitions[$parent . 'Core']['publicName'] !== null) {

					return $definitions[$parent . 'Core']['publicName'];

				}



				$parent = $definitions[$parent . 'Core']['parent'];

				continue;

			}



			break;

		}



		return false;

	}



    public function getParentClass() {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('class_name')

                ->from('back_tab')

                ->where('id_back_tab = ' . $this->id_parent)

        );

    }



    public static function getStaticParentClass($id_parent) {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('class_name')

                ->from('back_tab')

                ->where('id_back_tab = ' . $id_parent)

        );

    }



    public static function getInstance($id = null, $idLang = null) {



        if (!BackTab::$instance) {

            BackTab::$instance = new BackTab($id, $idLang);

        }



        return BackTab::$instance;

    }



    public function getAccesses() {



        $profiles = Profile::getProfiles($this->context->language->id);

        $accesses = [];



        foreach ($profiles as $profile) {



            if ($profile['id_profile'] == 1) {

                continue;

            }



            $accesses[$profile['id_profile']] = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(

                (new DbQuery())

                    ->select('`view`, `add`, `edit`, `delete`')

                    ->from('employee_access')

                    ->where('`id_profile` = ' . (int) $profile['id_profile'])

                    ->where('`id_back_tab` = ' . (int) $this->id)

            );



        }



        return $accesses;

    }



    public static function getCurrentTabId() {



        $idTab = BackTab::getIdFromClassName(Tools::getValue('controller'));



        if (empty($idTab)) {

            $idTab = BackTab::getIdFromClassName(Tools::getValue('BackTab'));

        }



        return $idTab;

    }



    public static function getIdBackTabByClass($controller) {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('id_back_tab')

                ->from('back_tab')

                ->where('class_name LIKE \'' . pSQL($controller) . '\'')

        );

    }



    public static function getCurrentParentId() {



        $cacheId = 'getCurrentParentId_' . mb_strtolower(Tools::getValue('controller'));



        if (!CacheApi::isStored($cacheId)) {

            $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

                (new DbQuery())

                    ->select('`id_parent`')

                    ->from('back_tab')

                    ->where('LOWER(`class_name`) = \'' . pSQL(mb_strtolower(Tools::getValue('controller'))) . '\'')

            );



            if (!$value) {

                $value = -1;

            }



            CacheApi::store($cacheId, $value);



            return $value;

        }



        return CacheApi::retrieve($cacheId);

    }



    public static function getIdFromClassName($className) {



        if (!is_null($className)) {

            $className = strtolower($className);

        }



        $context = Context::getContext();

        $cache = $context->cache_api;



        if ($context->cache_enable && is_object($context->cache_api)) {

            $value = $cache->getData('idTabfrom_' . $className, 864000);

            $temp = empty($value) ? null : Tools::jsonDecode($value, true);



            if (!empty($temp)) {

                return $temp;

            }



        }



        if (static::$_getIdFromClassName === null) {

            static::$_getIdFromClassName = [];

            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

                (new DbQuery())

                    ->select('`id_back_tab`, `class_name`')

                    ->from('back_tab'),

                true,

                false

            );



            if (is_array($result)) {



                foreach ($result as $row) {

                    static::$_getIdFromClassName[strtolower((string) $row['class_name'])] = $row['id_back_tab'];

                }



            }



        }



        $result = (isset(static::$_getIdFromClassName[$className]) ? (int) static::$_getIdFromClassName[$className] : false);



        if ($context->cache_enable && is_object($context->cache_api)) {

            $temp = $result === null ? null : Tools::jsonEncode($result);

            $cache->putData('idTabfrom_' . $className, $temp);

        }



        return $result;

    }



    public static function getIdFromFuncAndClassName($className, $function) {



        if (!is_null($className)) {

            $className = strtolower($className);

        }



        $key = strtolower($className) . md5(strtolower(str_replace(' ', '', $function)));



        $context = Context::getContext();

        $cache = $context->cache_api;



        if ($context->cache_enable && is_object($context->cache_api)) {

            $value = $cache->getData('idTabfrom_' . $key, 864000);



            if (!empty($value)) {

                return $value;

            }



        }



        if (static::$_getIdFromClassName === null) {

            static::$_getIdFromClassName = [];

            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

                (new DbQuery())

                    ->select('`id_back_tab`, `class_name`, `function`')

                    ->from('back_tab')

                    ->where('`function` != \'\'')

            );



            if (is_array($result)) {



                foreach ($result as $row) {

                    static::$_getIdFromFuncAndClassName[strtolower($row['class_name']) . md5(strtolower(str_replace(' ', '', $row['function'])))] = $row['id_back_tab'];

                }



            }



        }



        $result = (isset(static::$_getIdFromFuncAndClassName[$key]) ? (int) static::$_getIdFromFuncAndClassName[$key] : false);



        if ($context->cache_enable && is_object($context->cache_api)) {

            $cache->putData('idTabfrom_' . $key, $result);

        }



        return $result;

    }



    public function getBrothers() {



        $back_tab = [];

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.id_back_tab, t.position, tl.`name`')

                ->from('back_tab', 't')

                ->leftJoin('back_tab_lang', 'tl', 'tl.`id_lang` = ' . $this->context->language->id . ' AND tl.`id_back_tab` = ' . $this->id_parent)

                ->where('id_parent = ' . $this->id_parent)

                ->orderBy('t.`position` ASC')

        );



        foreach ($result as &$row) {



            $back_tab[] = [

                'position' => $row['position'],

                'name'     => sprintf($this->l('Position (%s) on %s'), $row['position'], $row['name']),

            ];



        }



        return $back_tab;

    }



    public function getChildrens() {



        $back_tab = [];

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.id_back_tab, t.position, tl.`name`')

                ->from('back_tab', 't')

                ->leftJoin('back_tab_lang', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang` = 1')

                ->where('id_parent = ' . $this->id)

                ->orderBy('t.`position` ASC')

        );



        foreach ($result as &$row) {



            $back_tab[] = [

                'position' => $row['position'],

                'name'     => sprintf($this->l('Position (%s)'), $row['position']),

            ];



        }



        return $back_tab;

    }



    public static function getChlidren($idParent) {



        $back_tab = [];

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.id_back_tab, t.id_parent, tl.`name`')

                ->from('back_tab', 't')

                ->leftJoin('back_tab_lang', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang` = 1')

                ->where('id_parent = ' . $idParent)

                ->orderBy('t.`position` ASC')

        );



        foreach ($result as &$row) {

            $row['children'] = self::getChlidren($row['id_back_tab']);

            $back_tab[] = $row;



        }



        return $back_tab;

    }



    public static function buildSelect($back_tab, $idParent) {



        $select = '';



        foreach ($back_tab as $key => $value) {



            foreach ($value['children'] as $child) {

                $select .= '<option value="' . $child['id_back_tab'] . '" ';



                if ($child['id_back_tab'] == $idParent) {

                    $select .= 'selected="selected"';

                }



                $select .= '>' . $value['name'] . ' > ' . $child['name'] . '</option>';



            }



        }



        return $select;

    }



    public function getBackTabSelects($idParent = null) {



        $select = '';



        $select .= '<select name="id_parent" id="id_parent">';

        $select .= '<option value="-1">' . $this->l('Invisible') . '</option>';

        $select .= '<option value="1" ';



        if ($idParent == 1) {

            $select .= 'selected="selected"';

        }



        $select .= '>' . $this->l('Home') . '</option>';

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.id_back_tab, t.id_parent, tl.`name`')

                ->from('back_tab', 't')

                ->leftJoin('back_tab_lang', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang`  = ' . (int) $this->context->language->id)

                ->where('id_parent = 1')

                ->orderBy('t.`position` ASC')

        );



        if (is_array($result)) {



            foreach ($result as &$row) {

                $row['children'] = BackTab::getChlidren($row['id_back_tab']);

                $back_tab[$row['id_back_tab']] = $row;

            }



            foreach ($back_tab as $key => $value) {

                $select .= '<option value="' . $value['id_back_tab'] . '" ';



                if ($value['id_back_tab'] == $idParent) {

                    $select .= 'selected="selected"';

                }



                $select .= '>' . $value['name'] . '</option>';



                foreach ($value['children'] as $child) {

                    $select .= '<option value="' . $child['id_back_tab'] . '" ';



                    if ($child['id_back_tab'] == $idParent) {

                        $select .= 'selected="selected"';

                    }



                    $select .= '>' . $value['name'] . ' > ' . $child['name'] . '</option>';



                    if (is_array($child['children']) && count($child['children'])) {

                        $select .= BackTab::buildSelect($child['children'], $idParent);

                    }



                }



            }



        }



        $select .= '</select>';

        return $select;

    }



    public static function getBackTabs($idLang, $idParent = null, $cache_enable = true) {



        $context = Context::getContext();

        $cache = $context->cache_api;



        if ($cache_enable && $context->cache_enable && is_object($context->cache_api)) {

            $value = $cache->getData('getBckTab_' . $idLang . '_' . $idParent, 864000);

            $temp = empty($value) ? null : Tools::jsonDecode($value, true);



            if (!empty($temp)) {

                return $temp;

            }



        }



        if (!isset(static::$_cache_back_tab[$idLang])) {

            static::$_cache_back_tab[$idLang] = [];



            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

                (new DbQuery())

                    ->select('t.*, tl.`name`')

                    ->from('back_tab', 't')

                    ->leftJoin('back_tab_lang', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang` = ' . (int) $idLang)

                    ->orderBy('t.`position` ASC')

            );



            if (is_array($result)) {



                foreach ($result as $row) {



                    if (!isset(static::$_cache_back_tab[$idLang][$row['id_parent']])) {

                        static::$_cache_back_tab[$idLang][$row['id_parent']] = [];

                    }



                    static::$_cache_back_tab[$idLang][$row['id_parent']][] = $row;



                }



            }



        }

        

         



        if ($idParent === null) {

            $arrayAll = [];



            foreach (static::$_cache_back_tab[$idLang] as $arrayParent) {

                $arrayAll = array_merge($arrayAll, $arrayParent);

            }



            if ($cache_enable && $context->cache_enable && is_object($context->cache_api)) {

                $temp = $arrayAll === null ? null : Tools::jsonEncode($arrayAll);

                $cache->putData('getBckTab_' . $idLang . '_' . $idParent, $temp);

            }

            $hookResult = Hook::getInstance()->exec('actiongetBackTabs', ['tabs' => $result], null, true);

            



            return $arrayAll;

        }



        $result = (isset(static::$_cache_back_tab[$idLang][$idParent]) ? static::$_cache_back_tab[$idLang][$idParent] : []);

        

       

        

        if ($context->cache_enable && is_object($context->cache_api)) {

            $temp = $result === null ? null : Tools::jsonEncode($result);

            $cache->putData('getBckTab_' . $idLang . '_' . $idParent, $temp);

        }



        return $result;



    }



    public static function enablingForPlugin($plugin) {



        $tabs = BackTab::getCollectionFromPlugin($plugin);



        if (!empty($tabs)) {



            foreach ($tabs as $tab) {

                /** @var Tab $tab */

                $tab->active = 1;

                $tab->save();

            }



            return true;

        }



        return false;

    }



    public static function getCollectionFromPlugin($plugin, $idLang = null) {



        if (is_null($idLang)) {

            $idLang = Context::getContext()->language->id;

        }



        if (!Validate::isPluginName($plugin)) {

            return [];

        }



        $tabs = new PhenyxCollection('BackTab', (int) $idLang);

        $tabs->where('plugin', '=', $plugin);



        return $tabs;

    }



    public static function disablingForPlugin($plugin) {



        $tabs = BackTab::getCollectionFromPlugin($plugin);



        if (!empty($tabs)) {



            foreach ($tabs as $tab) {

                /** @var Tab $tab */

                $tab->active = 0;

                $tab->save();

            }



            return true;

        }



        return false;

    }



    public static function getInstanceFromClassName($className, $idLang = null) {



        $idTab = (int) BackTab::getIdFromClassName($className);



        return new BackTab($idTab, $idLang);

    }



    public static function checkTabRights($idTab) {



        $context = Context::getContext();



        // No logged-in employee → no rights. Avoid null property access cascade.

        if (!isset($context->employee) || empty($context->employee->id_profile)) {

            return false;

        }



        if ($context->employee->id_profile == _EPH_ADMIN_PROFILE_) {

            return true;

        }



        $idProfil = (int) $context->employee->id_profile;

        $idTab    = (int) $idTab;



        if (!isset(static::$_tabAccesses[$idProfil][$idTab])) {

            $tabAccesses = Profile::getProfileAccesses($idProfil);



            // Initialize the cache slot in every branch so the return below

            // never hits an undefined key (PHP 8 warning).

            if (is_array($tabAccesses) && isset($tabAccesses[$idTab]['view'])) {

                static::$_tabAccesses[$idProfil][$idTab] = $tabAccesses[$idTab]['view'];

            } else {

                static::$_tabAccesses[$idProfil][$idTab] = false;

            }

        }



        return static::$_tabAccesses[$idProfil][$idTab];

    }



    public static function recursiveTab($idTab, $tabs) {



        $adminTab = BackTab::getTab((int) Context::getContext()->language->id, $idTab);

        $tabs[] = $adminTab;



        if ($adminTab['id_parent'] > 0) {

            $tabs = BackTab::recursiveTab($adminTab['id_parent'], $tabs);

        }



        return $tabs;

    }



    public static function getTab($idLang, $idTab) {



        $cacheId = 'BackTab::getTab_' . (int) $idLang . '-' . (int) $idTab;



        if (!CacheApi::isStored($cacheId)) {

            /* Tabs selection */

            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(

                (new DbQuery())

                    ->select('*')

                    ->from('back_tab', 't')

                    ->leftJoin('back_tab_lang', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang` = ' . (int) $idLang)

                    ->where('t.`id_back_tab` = ' . (int) $idTab)

            );

            CacheApi::store($cacheId, $result);



            return $result;

        }



        return CacheApi::retrieve($cacheId);

    }



    public static function getTabByIdProfile($idParent, $idProfile) {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.`id_back_tab`, t.`id_parent`, tl.`name`, a.`id_profile`')

                ->from('back_tab', 't')

                ->leftJoin('employee_access', 'a', 'a.`id_back_tab` = t.`id_back_tab`')

                ->leftJoin('topba_langr', 'tl', 't.`id_back_tab` = tl.`id_back_tab` AND tl.`id_lang` = ' . (int) Context::getContext()->language->id)

                ->where('a.`id_profile` = ' . (int) $idProfile)

                ->where('t.`id_parent` = ' . (int) $idParent)

                ->where('a.`view` = 1')

                ->where('a.`edit` = 1')

                ->where('a.`delete` = 1')

                ->where('a.`add` = 1')

                ->where('t.`id_parent` != 0')

                ->where('t.`id_parent` != -1')

                ->orderBy('t.`id_parent` ASC')

        );

    }



    public static function getNewLastPosition($idParent) {



        return (Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('IFNULL(MAX(`position`), 0) + 1')

                ->from('back_tab')

                ->where('`id_parent` = ' . (int) $idParent)

        ));

    }



    public static function initAccess(BackTab $Tab, $context = null) {



        if (!$context) {

            $context = Context::getContext();

        }



        $idCurrentProfile = (!$context->employee || !$context->employee->id_profile) ? 0 : (int) $context->employee->id_profile;



        $profiles = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('`id_profile`')

                ->from('profile')

                ->where('`id_profile` != 1')

        );



        $replace = [];

        $replace[] = [

            'id_profile'  => 1,

            'id_back_tab' => (int) $Tab->id,

            'view'        => 1,

            'add'         => 1,

            'edit'        => 1,

            'delete'      => 1,

        ];



        $accesses = $Tab->accesses;



        if (is_array($accesses) && count($accesses)) {



            foreach ($profiles as $profile) {



                if (array_key_exists($profile['id_profile'], $accesses)) {

                    $replace[] = [

                        'id_profile'  => (int) $profile['id_profile'],

                        'id_back_tab' => (int) $Tab->id,

                        'view'        => (int) $accesses[$profile['id_profile']]['view'],

                        'add'         => (int) $accesses[$profile['id_profile']]['add'],

                        'edit'        => (int) $accesses[$profile['id_profile']]['edit'],

                        'delete'      => (int) $accesses[$profile['id_profile']]['delete'],

                    ];

                } else {

                    $rights = ($Tab->id_parent == 0 || (int) $profile['id_profile'] === $idCurrentProfile) ? 1 : 0;

                    $replace[] = [

                        'id_profile'  => (int) $profile['id_profile'],

                        'id_back_tab' => (int) $Tab->id,

                        'view'        => (int) $rights,

                        'add'         => (int) $rights,

                        'edit'        => (int) $rights,

                        'delete'      => (int) $rights,

                    ];

                }



            }



        } else {



            foreach ($profiles as $profile) {

                $replace[] = [

                    'id_profile'  => (int) $profile['id_profile'],

                    'id_back_tab' => (int) $Tab->id,

                    'view'        => 0,

                    'add'         => 0,

                    'edit'        => 0,

                    'delete'      => 0,

                ];

            }



        }



        return Db::getInstance()->insert('employee_access', $replace, false, true, Db::REPLACE);

    }



    public function save($nullValues = false, $autodate = true) {



        static::$_getIdFromClassName = null;



        return parent::save();

    }



    public function cleanPositions() {



        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('`id_back_tab`')

                ->from('back_tab')

                ->where('`id_parent` = ' . (int) $this->id_parent)

                ->orderBy('position')

        );

        $sizeof = count($result);



        for ($i = 0; $i < $sizeof; ++$i) {

            Db::getInstance()->update(

                'back_tab',

                [

                    'position' => $i,

                ],

                '`id_back_tab` = ' . (int) $result[$i]['id_back_tab']

            );

        }



        return true;

    }



    public function move($direction) {



        $nbTabs = BackTab::getNbTabs($this->id_parent);



        if ($direction != 'l' && $direction != 'r') {

            return false;

        }



        if ($nbTabs <= 1) {

            return false;

        }



        if ($direction == 'l' && $this->position <= 1) {

            return false;

        }



        if ($direction == 'r' && $this->position >= $nbTabs) {

            return false;

        }



        $newPosition = ($direction == 'l') ? $this->position - 1 : $this->position + 1;

        Db::getInstance()->execute(

            '

            UPDATE `' . _DB_PREFIX_ . 'tab` t

            SET position = ' . (int) $this->position . '

            WHERE id_parent = ' . (int) $this->id_parent . '

                AND position = ' . (int) $newPosition

        );

        $this->position = $newPosition;



        return $this->update();

    }



    public static function getNbTabs($idParent = null) {



        return (int) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('COUNT(*)')

                ->from('back_tab', 't')

                ->where(!is_null($idParent) ? 't.`id_parent` = ' . (int) $idParent : '')

        );

    }



    public function builTranslateTabs() {



        $tab = '_TABS';



        foreach (Language::getLanguages(true) as $lang) {

            $toInsert = [];

            $iso = $lang['iso_code'];

            $filePath = _EPH_TRANSLATIONS_DIR_ . $iso . '/tabs.php';



            if (file_exists($filePath)) {

                @include $filePath;

                $toInsert = $_TABS;



                if (isset($this->name[$lang['id_lang']])) {



                    $toInsert[$this->class_name] = $this->name[$lang['id_lang']];

                    ksort($toInsert);

                    $file = fopen($filePath, "w");

                    fwrite($file, "<?php\n\nglobal \$" . $tab . ";\n\n");

                    fwrite($file, "$" . $tab . " = [];\n");



                    foreach ($toInsert as $key => $value) {

                        fwrite($file, '$' . $tab . '[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");

                    }



                    fwrite($file, "\n" . 'return $' . $tab . ';' . "\n");

                    fwrite($file, "\n?>");

                    fclose($file);

                }



            }



        }



    }



    public function add($autoDate = true, $nullValues = false, $init = true, $position = null) {



        static::$_cache_back_tab = [];



        $this->cleanPositions();



        if (is_null($position)) {

            $this->position = BackTab::getNewLastPosition($this->id_parent);

        } else {

            $this->adjustPosition($position);

            $this->position = $position;

        }



        if (parent::add($autoDate, $nullValues)) {

            //forces cache to be reloaded

            static::$_getIdFromClassName = null;

            $this->context->_tools->generateTabs(false);

            $this->builTranslateTabs();



            if ($init) {

                return BackTab::initAccess($this);

            }



            $idMeta = Meta::getIdMetaByPage(strtolower($this->class_name));



            if (!$idMeta) {

                $meta = new Meta();

                $meta->controller = 'admin';

                $meta->page = strtolower($this->class_name);

                $meta->plugin = $this->plugin;



                foreach (Language::getLanguages(false) as $language) {

                    $meta->title[$language['id_lang']] = $this->name[$language['id_lang']];

                    $meta->url_rewrite[$language['id_lang']] = Tools::str2url($this->name[$language['id_lang']]);

                }



                $meta->add();

            }



            return true;

        }



        return false;

    }



    public function adjustPosition($position) {



        $menus = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.`id_back_tab`, t.`position`, t.`id_parent`')

                ->from('back_tab', 't')

                ->where('t.`id_parent` = ' . (int) $this->id_parent)

                ->where('t.`position` >= ' . (int) $position)

                ->orderBy('t.`position` ASC')

        );

        $i = $position + 1;



        foreach ($menus as $menu) {

            $sql = 'UPDATE `' . _DB_PREFIX_ . 'back_tab` SET `position` = ' . (int) $i . ' WHERE `id_back_tab` = ' . (int) $menu['id_back_tab'];

            $result = Db::getInstance()->execute($sql);

            $i++;



        }



    }



    public function delete() {



        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'employee_access` WHERE `id_back_tab` = ' . (int) $this->id);



        $result = parent::delete();

        $this->context->_tools->generateTabs(false);

        return $result;

    }



    public function update($nullValues = true, $init = true) {



        static::$_cache_back_tab = [];



        if (parent::update($nullValues)) {

            $this->context->_tools->generateTabs(false);

            $this->builTranslateTabs();



            if ($init) {

                return BackTab::initAccess($this);

            }



            return true;



        }



    }



    public function getLastPosition() {



        return (Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('IFNULL(MAX(`position`), 0)')

                ->from('back_tab')

                ->where('`id_parent` = ' . (int) $this->id_parent)

        ));

    }



    public function updatePosition($way, $position) {



        if (!$res = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

            ->select('t.`id_back_tab`, t.`position`, t.`id_parent`')

            ->from('back_tab', 't')

            ->where('t.`id_parent` = ' . (int) $this->id_parent)

            ->orderBy('t.`position` ASC')

        )) {

            return false;

        }



        foreach ($res as $tab) {



            if ((int) $tab['id_back_tab'] == (int) $this->id) {

                $movedTab = $tab;

            }



        }



        if (!isset($movedTab) || !isset($position)) {

            return false;

        }



        $result = (Db::getInstance()->update(

            'back_tab',

            [



                'position' => ['type' => 'sql', 'value' => '`position` ' . ($way ? '- 1' : '+ 1')],

            ],

            '`position` ' . ($way ? '> ' . (int) $movedTab['position'] . ' AND `position` <= ' . (int) $position : '< ' . (int) $movedTab['position'] . ' AND `position` >= ' . (int) $position) . ' AND `id_parent`=' . (int) $movedTab['id_parent']

        )

            && Db::getInstance()->update(

                'back_tab',

                [

                    'position' => (int) $position,

                ],

                '`id_parent` = ' . (int) $movedTab['id_parent'] . ' AND `id_back_tab`=' . (int) $movedTab['id_back_tab']

            ));



        return $result;

    }



    public static function getPluginTabList() {



        $list = [];



        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('t.`class_name`, t.`plugin`')

                ->from('back_tab', 't')

                ->where('t.`plugin` IS NOT NULL')

                ->where('t.`plugin` != ""')

        );



        if (is_array($result)) {



            foreach ($result as $detail) {

                $list[strtolower($detail['class_name'])] = $detail;

            }



        }



        return $list;

    }



    public static function getTabPluginsList($idTab) {



        $pluginsList = ['default_list' => [], 'slider_list' => []];

        $xmlTabPluginsList = false;



        if (file_exists(_EPH_ROOT_DIR_ . Plugin::CACHE_FILE_TAB_PLUGINS_LIST)) {

            $xmlTabPluginsList = @simplexml_load_file(_EPH_ROOT_DIR_ . Plugin::CACHE_FILE_TAB_PLUGINS_LIST);

        }



        $className = null;

        $displayType = 'default_list';



        if ($xmlTabPluginsList) {



            foreach ($xmlTabPluginsList->tab as $tab) {



                foreach ($tab->attributes() as $key => $value) {



                    if ($key == 'class_name') {

                        $className = (string) $value;

                    }



                }



                if (BackTab::getIdFromClassName((string) $className) == $idTab) {



                    foreach ($tab->attributes() as $key => $value) {



                        if ($key == 'display_type') {

                            $displayType = (string) $value;

                        }



                    }



                    foreach ($tab->children() as $plugin) {

                        $pluginsList[$displayType][(int) $plugin['position']] = (string) $plugin['name'];

                    }



                    ksort($pluginsList[$displayType]);

                }



            }



        }



        return $pluginsList;

    }



    public static function getClassNameById($idTab) {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

            (new DbQuery())

                ->select('`class_name`')

                ->from('back_tab')

                ->where('`id_back_tab` = ' . (int) $idTab)

        );

    }



    public static function getmetroTabColors() {



        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(

            (new DbQuery())

                ->select('*')

                ->from('tabmetro_color')

        );

    }



}

