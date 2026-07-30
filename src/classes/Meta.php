<?php

namespace EphenyxDigital\QuantumCore;

use Language;
use ReflectionClass;


/**
 * Class Meta
 *
 * @since 1.9.1.0
 */
class Meta extends PhenyxObjectModel {

    protected static $instance;
    // @codingStandardsIgnoreStart
    public $page;
    public $controller;
    public $plugin;
    public $configurable = 1;
    public $generated;
    public $title;
    public $description;
    public $keywords;
    public $url_rewrite;
    // @codingStandardsIgnoreEnd

    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'meta',
        'primary'   => 'id_meta',
        'multilang' => true,
        'fields'    => [
            'page'         => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true, 'size' => 64],
            'controller'   => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 12],
            'plugin'       => ['type' => self::TYPE_STRING],
            'configurable' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],

            /* Lang fields */
            'generated'    => ['type' => self::TYPE_BOOL, 'lang' => true],
            'title'        => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128],
            'description'  => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'keywords'     => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'url_rewrite'  => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 255],
        ],
    ];

    public function __construct($id = null, $idLang = null) {

        parent::__construct($id, $idLang);
    }
    
    public static function buildObject( $id, $idLang = null, $className = null) {
        
        $objectData = parent::buildObject( $id, $idLang, $className);
		       
        return Tools::jsonDecode(Tools::jsonEncode($objectData));
    }

    public static function getInstance($id = null, $idLang = null) {

        if (!isset(static::$instance)) {
            static::$instance = new Meta($id, $idLang);
        }

        return static::$instance;
    }

    public function add($autoDate = true, $nullValues = true) {

        $success = parent::add($autoDate, $nullValues);

        if ($success) {

            if ($this->context->cache_enable && is_object($this->context->cache_api)) {
                $this->context->cache_api->cleanByStartingKey('metaGetPages_');
            }

        }

        return $success;
    }

    public function update($nullValues = false) {

        $result = parent::update(true);

        if ($result) {

            if ($this->context->cache_enable && is_object($this->context->cache_api)) {
                $this->context->cache_api->cleanByStartingKey('metaGetPages_');
            }

            Tools::generateHtaccess();
        }

        return $result;
    }

    /**
     * Proprietes par defaut d'un controleur deja resolu par l'autoloader
     * (classe nativement chargeable ou plugin installe - PhenyxAutoload
     * indexe class_index.php sur ces seuls cas, cf. never_require_plugin_controller).
     * Remplace le duo class_exists()+ReflectionClass repete plus bas.
     *
     * @param string $className
     *
     * @return array
     */
    protected static function getInstalledControllerProperties($className) {

        if (!class_exists($className)) {
            return [];
        }

        return (new ReflectionClass($className))->getDefaultProperties();
    }

    public static function getPages($excludeFilled = false, $addPage = false, $pageExludes = true) {

        $context = Context::getContext();

        $plugins = Plugin::getPluginsInstalled();

        $selectedPages = [];
        $pluginFiles = [];
        $adminPluginFiles = [];
        $adminFiles = [];
        $extraAdminFiles = [];

        if ($pageExludes) {
            $exludePages = [
                'cms',
                'footer',
                'header',
                'pfgmodel',
            ];
            $extraPages = $context->_hook->exec('actionMetaGetExtraPages', [], null, true);

            if (is_array($extraPages) && count($extraPages)) {

                foreach ($extraPages as $plugin => $pages) {

                    if (is_array($pages) && count($pages)) {

                        foreach ($pages as $key => $value) {
                            $exludePages[] = $value;
                        }

                    }

                }

            }

            if ($addPage) {

                $exludePages = array_merge(
                    $exludePages,
                    self::getReferentPages()
                );

            }

        } else {
            $exludePages = [];
        }

        if (!$files = Tools::scandir(_EPH_CORE_DIR_ . DIRECTORY_SEPARATOR . '/includes/controllers/front' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan front root directory'));
        }

        if (!$extraFrontFiles = Tools::scandir(_EPH_CORE_DIR_ . DIRECTORY_SEPARATOR . 'includes/specific_controllers/front' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan specific front controllers directory'));
        }

        if (is_array($extraFrontFiles) && count($extraFrontFiles)) {
            $files = array_values(array_unique(array_merge($files, $extraFrontFiles)));
        }

        if (!$adminFiles = Tools::scandir(_EPH_CORE_DIR_ . DIRECTORY_SEPARATOR . 'includes/controllers/backend' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan admin root directory'));
        }

        if (!$extraAdminFiles = Tools::scandir(_EPH_CORE_DIR_ . DIRECTORY_SEPARATOR . 'includes/specific_controllers/backend' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan specific controllers directory'));
        }

        if (is_array($extraAdminFiles) && count($extraAdminFiles)) {
            $adminFiles = array_values(array_unique(array_merge($adminFiles, $extraAdminFiles)));
        }

        foreach ($adminFiles as $file) {

            if ($file != 'index.php' && !in_array(strtolower(str_replace('Controller.php', '', $file)), $exludePages)) {
                $className = str_replace('.php', '', $file);
                $properties = Meta::getInstalledControllerProperties($className);

                if (isset($properties['php_self'])) {
                    $selectedPages['admin']['native'][] = $properties['php_self'];
                } else

                if (preg_match('/^[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages['admin']['native'][] = strtolower(str_replace('Controller.php', '', $file));
                } else

                if (preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages['admin']['native'][] = strtolower(str_replace('Controller.php', '', basename($file)));
                }

            }

        }

        foreach ($plugins as $plugin) {

            if (is_dir(_EPH_PLUGIN_DIR_ . $plugin['name'])) {

                foreach (glob(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/controllers/admin/*.php') as $file) {
                    $file = str_replace(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/controllers/admin/', '', $file);

                    if ($file == 'index.php') {
                        continue;
                    }

                    $className = str_replace('.php', '', $file);
                    $properties = Meta::getInstalledControllerProperties($className);

                    if (isset($properties['php_self']) && !in_array($properties['php_self'], $exludePages)) {
                        $selectedPages['admin']['plugin'][$plugin['name']][] = $properties['php_self'];
                    }

                }
                
                foreach (glob(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/controllers/front/*.php') as $file) {
                    $file = str_replace(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/controllers/front/', '', $file);

                    if ($file == 'index.php') {
                        continue;
                    }

                    $className = str_replace('.php', '', $file);
                    $properties = Meta::getInstalledControllerProperties($className);

                    // Page de reference d'un objet affiche (canonicalRedirection) :
                    // ce n'est pas une page meta configurable independamment.
                    if (!empty($properties['canonical'])) {
                        continue;
                    }

                    if (isset($properties['php_self']) && !in_array($properties['php_self'], $exludePages)) {
                        $selectedPages['front']['plugin'][$plugin['name']][] = $properties['php_self'];
                    }

                }


            }

            if (is_dir(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'])) {

                foreach (glob(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/controllers/admin/*.php') as $file) {
                    $file = str_replace(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/controllers/admin/', '', $file);

                    if ($file == 'index.php') {
                        continue;
                    }

                    $className = str_replace('.php', '', $file);
                    $properties = Meta::getInstalledControllerProperties($className);

                    if (isset($properties['php_self']) && !in_array($properties['php_self'], $exludePages)) {
                        $selectedPages['admin']['plugin'][$plugin['name']][] = $properties['php_self'];
                    }

                }
                
                foreach (glob(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/controllers/front/*.php') as $file) {
                    $file = str_replace(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/controllers/front/', '', $file);

                    if ($file == 'index.php') {
                        continue;
                    }

                    $className = str_replace('.php', '', $file);
                    $properties = Meta::getInstalledControllerProperties($className);

                    // Page de reference d'un objet affiche (canonicalRedirection) :
                    // ce n'est pas une page meta configurable independamment.
                    if (!empty($properties['canonical'])) {
                        continue;
                    }

                    if (isset($properties['php_self']) && !in_array($properties['php_self'], $exludePages)) {
                        $selectedPages['front']['plugin'][$plugin['name']][] = $properties['php_self'];
                    }

                }

            }

        }

       

        if (!$overrideFiles = Tools::scandir(_EPH_CORE_DIR_ . DIRECTORY_SEPARATOR . 'includes/override' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR, 'php', '', true)) {
            die(Tools::displayError('Cannot scan "override" directory'));
        }

        if (is_array($overrideFiles) && count($overrideFiles)) {
            $files = array_values(array_unique(array_merge($files, $overrideFiles)));
        }

        // Exclude pages forbidden

        foreach ($files as $file) {

            if ($file != 'index.php' && !in_array(strtolower(str_replace('Controller.php', '', $file)), $exludePages)) {
                $className = str_replace('.php', '', $file);
                $properties = Meta::getInstalledControllerProperties($className);

                // Page de reference d'un objet affiche (canonicalRedirection) :
                // ce n'est pas une page meta configurable independamment.
                // Cas concret : PfgController ('pfg') est canonical=true mais
                // n'etait pas dans la liste $exludePages codee en dur ci-dessus.
                if (!empty($properties['canonical'])) {
                    continue;
                }

                if (isset($properties['php_self'])) {
                    $selectedPages['front']['native'][] = $properties['php_self'];

                } else

                if (preg_match('/^[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages['front']['native'][] = strtolower(str_replace('Controller.php', '', $file));
                } else

                if (preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file)) {
                    $selectedPages['front']['native'][] = strtolower(str_replace('Controller.php', '', basename($file)));
                }

            }

        }

        // Add plugins controllers to list (this function is cool !)

        // NB : $excludeFilled est accepte pour compatibilite d'appel (2
        // consommateurs, cf. AdminMetaController / AdminPhenyxMediasController)
        // mais ne fait plus rien : le bloc precedent comparait un $meta['page']
        // (string) a $selectedPages, qui est imbrique depuis le passage a la
        // structure ['front'|'admin']['native'|'plugin'][...] - in_array()/
        // array_search() ne pouvaient donc jamais matcher. Le retirer ne
        // change aucun comportement observable (verifie), et evite une
        // requete Meta::getMetas() inutile a chaque rendu du formulaire.
        // L'exclusion des pages deja configurees, elle, fonctionne bel et
        // bien via self::getReferentPages() ci-dessus quand $addPage est vrai
        // (creation) - ne pas la reimplementer ici sans exclure la page de
        // l'objet en cours d'edition, sous peine de la faire disparaitre du
        // select au moment ou on la modifie.

        // Add selected page : on exige une string non-vide, sinon on aurait
        // $selectedPages[true => true] qui devient $selectedPages[1 => true]
        // et fait planter les consommateurs qui font foreach (cf AdminMetaController).

        if (is_string($addPage) && $addPage !== '') {
            $name = $addPage;

            if (preg_match('#plugin-([a-z0-9_-]+)-([a-z0-9]+)$#i', $addPage, $m)) {
                $addPage = $m[1] . ' - ' . $m[2];
            }

            $selectedPages[$addPage] = $name;
        }

        ksort($selectedPages);
        
        return $selectedPages;
    }
	
	/**
	 * Liste les pages (php_self) exposees par les controleurs front et admin
	 * des plugins presents sur le disque mais NON installes.
	 *
	 * Ces classes ne figurent pas dans class_index.php - PhenyxAutoload
	 * n'indexe que les plugins actifs - donc elles ne peuvent etre ni
	 * autoloadees, ni instanciees. Tout est fait en analyse statique du code
	 * source. Les controleurs des plugins installes sont quand meme parcourus,
	 * mais uniquement pour servir de table de resolution des heritages.
	 *
	 * Les controleurs qui declarent `$canonical = true` sont exclus : leur URL de
	 * reference est celle de l'objet affiche, pas une page meta configurable.
	 *
	 * @return array [pluginName => [php_self, ...]]
	 */
	public static function getPluginPages() {

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

				foreach (['admin', 'front'] as $controllerType) {

					foreach (glob($pluginDir . '/controllers/' . $controllerType . '/*.php') as $file) {

						if (basename($file) === 'index.php') {
							continue;
						}

						$definition = Meta::parseControllerFile($file);

						// Fichier sans declaration de classe (scripts legacy
						// type franfinance/controllers/front/validation.php).
						if (!$definition) {
							continue;
						}

						$definition['type'] = $controllerType;
						$definitions[$definition['class']] = $definition;

						if (!$isInstalled) {
							$notInstalled[$plugin][] = $definition;
						}

					}

				}

			}

		}

		$selectedPages = [];

		foreach ($notInstalled as $plugin => $pluginDefinitions) {

			$pages = [];

			foreach ($pluginDefinitions as $definition) {

				// Controleur canonique : sa page n'est pas une page meta a part
				// entiere, l'URL de reference est celle de l'objet affiche
				// (canonicalRedirection). On ne la propose pas.
				if ($definition['canonical']) {
					continue;
				}

				$phpSelf = Meta::resolvePhpSelf($definition, $definitions);

				if ($phpSelf) {
					$pages[] = $phpSelf;
				}

			}

			if ($pages) {
				$pages = array_values(array_unique($pages));
				sort($pages);
				$selectedPages[$plugin] = $pages;
			}

		}

		ksort($selectedPages);

		return $selectedPages;
	}

	/**
	 * Extrait, sans charger le fichier, le nom de la classe declaree, sa classe
	 * parente et la valeur litterale de $php_self.
	 *
	 * On passe par token_get_all() et non par une preg_match sur le source :
	 * un `$php_self` cite dans un commentaire ou dans une chaine produirait
	 * sinon un faux positif.
	 *
	 * ATTENTION : ne jamais require_once le fichier. Un controleur de plugin
	 * declare <Nom>Core ; c'est PhenyxAutoload qui fabrique l'alias <Nom> a
	 * partir de l'index. Un require manuel ne declare que la classe Core, et
	 * expose a un "Cannot redeclare class" si deux plugins declarent le meme
	 * nom (cf. FrontLink / AdminPhlinks).
	 *
	 * @param string $file
	 *
	 * @return array|false ['class' => ..., 'parent' => ..., 'php_self' => ..., 'canonical' => bool]
	 */
	protected static function parseControllerFile($file) {

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
		$phpSelf = null;
		$canonical = null;
		$count = count($tokens);

		for ($i = 0; $i < $count; $i++) {

			if (!is_array($tokens[$i])) {
				continue;
			}

			// Declaration de la classe (la premiere du fichier).
			if ($class === null && $tokens[$i][0] === T_CLASS) {

				// Ecarte les `Foo::class`.
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

			// Proprietes de classe qui nous interessent : $php_self (chaine) et
			// $canonical (booleen). On exige une vraie declaration de propriete,
			// c'est-a-dire un modificateur de visibilite devant la variable :
			// sinon une variable locale homonyme serait prise pour la propriete
			// (ph_blog fait `$canonical = $this->context->_link->getPluginLink()`
			// dans une methode, et plusieurs controleurs ont un parametre
			// `function buildJsonLd($category, $canonical)`).
			if ($tokens[$i][0] === T_VARIABLE && in_array($tokens[$i][1], ['$php_self', '$canonical'], true)) {

				if (!Meta::isPropertyDeclaration($tokens, $i)) {
					continue;
				}

				$literal = Meta::readPropertyLiteral($tokens, $i, $count);

				// null = propriete sans valeur par defaut, ou valeur non litterale
				// (constante, expression) : rien d'exploitable sans executer le code.
				if ($literal === null) {
					continue;
				}

				if ($tokens[$i][1] === '$php_self') {

					if ($phpSelf === null && is_string($literal)) {
						$phpSelf = $literal;
					}

				} else

				if ($canonical === null) {
					$canonical = (bool) $literal;
				}

			}

		}

		if ($class === null) {
			return false;
		}

		return [
			'class'     => $class,
			'parent'    => $parent,
			'php_self'  => $phpSelf,
			// Toujours presente et toujours booleenne : la propriete absente,
			// nulle ou fausse donne false, l'appelant n'a pas d'isset() a faire.
			'canonical' => (bool) $canonical,
			'file'      => $file,
		];
	}

	/**
	 * La variable rencontree est-elle une declaration de propriete de classe,
	 * et non une variable locale ou un parametre de methode ?
	 *
	 * On remonte les tokens significatifs : un modificateur de visibilite
	 * (public/protected/private/var/static/readonly) valide la declaration, un
	 * token de type le fait traverser (`public bool $canonical`,
	 * `public ?string $php_self`), tout le reste l'invalide - notamment `,` et
	 * `(` d'une liste de parametres, `;` et `{` d'un corps de methode.
	 *
	 * @param array $tokens
	 * @param int   $index Position du T_VARIABLE
	 *
	 * @return bool
	 */
	protected static function isPropertyDeclaration(array $tokens, $index) {

		$modifiers = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_STATIC];

		if (defined('T_READONLY')) {
			$modifiers[] = T_READONLY;
		}

		$typeTokens = [T_STRING, T_ARRAY, T_CALLABLE];

		if (defined('T_NAME_QUALIFIED')) {
			$typeTokens[] = T_NAME_QUALIFIED;
			$typeTokens[] = T_NAME_FULLY_QUALIFIED;
		}

		for ($p = $index - 1; $p >= 0; $p--) {

			if (!is_array($tokens[$p])) {

				// `?` d'un type nullable, `|` d'un type union.
				if ($tokens[$p] === '?' || $tokens[$p] === '|') {
					continue;
				}

				return false;
			}

			if (in_array($tokens[$p][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
				continue;
			}

			if (in_array($tokens[$p][0], $modifiers, true)) {
				return true;
			}

			if (in_array($tokens[$p][0], $typeTokens, true)) {
				continue;
			}

			return false;
		}

		return false;
	}

	/**
	 * Valeur par defaut litterale d'une propriete : chaine, entier, true ou
	 * false. Retourne null quand il n'y a pas de valeur par defaut, ou quand
	 * elle n'est pas un litteral (constante, concatenation, appel) : rien
	 * d'exploitable sans executer le code.
	 *
	 * @param array $tokens
	 * @param int   $index Position du T_VARIABLE
	 * @param int   $count
	 *
	 * @return string|int|bool|null
	 */
	protected static function readPropertyLiteral(array $tokens, $index, $count) {

		$seenEqual = false;

		for ($j = $index + 1; $j < $count; $j++) {

			if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
				continue;
			}

			if (!$seenEqual) {

				if ($tokens[$j] === '=') {
					$seenEqual = true;
					continue;
				}

				// Propriete declaree sans valeur par defaut.
				return null;
			}

			if (!is_array($tokens[$j])) {
				return null;
			}

			if ($tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING) {
				return trim($tokens[$j][1], '\'"');
			}

			if ($tokens[$j][0] === T_LNUMBER) {
				return (int) $tokens[$j][1];
			}

			// true, false et null sont des T_STRING pour le tokenizer.
			if ($tokens[$j][0] === T_STRING) {

				$lower = strtolower($tokens[$j][1]);

				if ($lower === 'true') {
					return true;
				}

				if ($lower === 'false') {
					return false;
				}

			}

			return null;
		}

		return null;
	}

	/**
	 * Determine le php_self d'un controleur non installe.
	 *
	 * 1. valeur litterale trouvee dans le fichier ;
	 * 2. sinon on remonte l'heritage : d'abord dans les autres controleurs du
	 *    meme plugin, puis - si la classe parente est, elle, resoluble par
	 *    l'autoloader (classe du core ou plugin actif) - via les proprietes par
	 *    defaut, sans instanciation ;
	 * 3. sinon on derive du nom de la classe, comme le fait getPages() pour les
	 *    controleurs front natifs.
	 *
	 * @param array $definition
	 * @param array $definitions Toutes les classes du plugin, indexees par nom
	 *
	 * @return string|false
	 */
	protected static function resolvePhpSelf(array $definition, array $definitions) {

		if (!empty($definition['php_self'])) {
			return $definition['php_self'];
		}

		$parent = $definition['parent'];
		$seen = [];

		while ($parent && !isset($seen[$parent])) {

			$seen[$parent] = true;

			// Classe parente declaree dans le plugin lui-meme.
			if (isset($definitions[$parent])) {

				if (!empty($definitions[$parent]['php_self'])) {
					return $definitions[$parent]['php_self'];
				}

				$parent = $definitions[$parent]['parent'];
				continue;
			}

			// La meme classe, suffixee Core, dans le plugin.
			if (isset($definitions[$parent . 'Core'])) {

				if (!empty($definitions[$parent . 'Core']['php_self'])) {
					return $definitions[$parent . 'Core']['php_self'];
				}

				$parent = $definitions[$parent . 'Core']['parent'];
				continue;
			}

			// Classe parente connue de l'autoloader : on lit les proprietes par
			// defaut, sans instancier (le constructeur d'un AdminController
			// exige un contexte admin complet).
			try {

				if (class_exists($parent)) {
					$properties = (new ReflectionClass($parent))->getDefaultProperties();

					if (!empty($properties['php_self'])) {
						return $properties['php_self'];
					}

				}

			} catch (\Throwable $e) {
				// Chaine d'heritage incomplete : on tombe sur le repli ci-dessous.
			}

			break;
		}

		$name = $definition['class'];

		if (substr($name, -4) === 'Core') {
			$name = substr($name, 0, -4);
		}

		if (substr($name, -10) === 'Controller') {
			$name = substr($name, 0, -10);
		}

		return $name === '' ? false : strtolower($name);
	}

    public static function cleanPluginMeta() {

        $tools = new PhenyxTools();

        $metas = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('id_meta, plugin')
                ->from('meta')
                ->where('`plugin` != \'\'')
        );

        if (is_array($tools->plugins)) {

            foreach ($metas as $meta) {

                if (array_key_exists($meta['plugin'], $tools->plugins)) {
                    continue;
                }

                $meta = new Meta((int) $meta['id_meta']);
                $meta->delete();
            }

        }

    }

    public static function getPluginControllerPage($pluginName, $controller = null) {

        $context = Context::getContext();
        $adminPluginFiles = [];
        $plugin = Plugin::getInstanceByName($pluginName);

        if (is_dir(_EPH_PLUGIN_DIR_ . $plugin->name)) {
            $local_path = _EPH_PLUGIN_DIR_ . $plugin->name . '/';
        } else

        if (is_dir(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin->name)) {
            $local_path = _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin->name . '/';
        }

        if (is_null($controller)) {

            foreach (glob($local_path . 'controllers/admin/*.php') as $file) {
                $filename = basename($file, '.php');

                if ($filename == 'index') {
                    continue;
                }

                $adminPluginFiles[] = str_replace('Controller', '', basename($filename));
            }

        } else {

            if (file_exists($local_path . 'controllers/admin/' . $controller . '.php')) {
                $adminPluginFiles[] = str_replace('Controller.php', '', $controller);
            }

        }

        if (count($adminPluginFiles)) {
            $metas = Meta::getMetas();

            foreach ($metas as $meta) {

                if (in_array($meta['page'], array_map('strtolower', $adminPluginFiles))) {
                    unset($adminPluginFiles[array_search($meta['page'], $adminPluginFiles)]);
                }

            }

        }

        if (count($adminPluginFiles)) {

            foreach ($adminPluginFiles as $pluginController) {
                $id_tab = (int) BackTab::getIdFromClassName($pluginController);

                if ($id_tab > 0) {
                    $tab = new BackTab($id_tab);
                    $link_rewrite = Tools::str2url($tab->name[$context->language->id]);
                    $meta = new Meta();
                    $meta->controller = 'admin';
                    $meta->page = strtolower($pluginController);
                    $meta->plugin = $plugin->name;

                    foreach (Language::getLanguages(false) as $language) {
                        $meta->title[$language['id_lang']] = $tab->name[$language['id_lang']];
                        $meta->url_rewrite[$language['id_lang']] = Tools::str2url($tab->name[$language['id_lang']]);
                    }

                    $meta->add();
                }

            }

        }

        return true;
    }

    public static function getMetas() {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('*')
                ->from('meta')
                ->orderBy('`page` ASC')
        );
    }

    public static function getIdMetaByPage($page) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('id_meta')
                ->from('meta')
                ->where('page LIKE \'' . pSQL($page) . '\'')
        );
    }

    public static function getLinkRewrite($page, $idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('ml.url_rewrite')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta` AND ml.`id_lang` = ' . (int) $idLang)
                ->where('page = \'' . pSQL($page) . '\'')
        );
    }

    public static function getTitle($page, $idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('ml.title')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta` AND ml.`id_lang` = ' . (int) $idLang)
                ->where('page = \'' . pSQL($page) . '\'')
        );
    }

    public static function getDescription($page, $idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('ml.description')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta` AND ml.`id_lang` = ' . (int) $idLang)
                ->where('page = \'' . pSQL($page) . '\'')
        );
    }

    public static function getMetasByIdLang($idLang, $type = null, $configurable = null) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('*')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta`')
                ->where('ml.`id_lang` = ' . (int) $idLang)
                ->where($type ? 'm.`controller` LIKE "' . $type . '"' : '1')
                ->where(!is_null($configurable) ? 'm.`configurable` = ' . $configurable . '' : '1')
                ->orderBy('`page` ASC')
        );
    }

    public static function getEquivalentUrlRewrite($newIdLang, $idLang, $urlRewrite) {

        $metaSql = (new DbQuery())
            ->select('`id_meta`')
            ->from('meta_lang')
            ->where('`url_rewrite` = \'' . pSQL($urlRewrite) . '\'')
            ->where('`id_lang` = ' . (int) $idLang);

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('url_rewrite')
                ->from('meta_lang')
                ->where('id_meta = (' . $metaSql->build() . ')')
                ->where('`id_lang` = ' . (int) $newIdLang)
        );
    }

    public static function getMetaTags($idLang, $pageName, $title = '') {
        $allowed = false;

        if (!empty(Context::getContext()->phenyxConfig->get('EPH_MAINTENANCE_IP'))) {
            $allowed = in_array(Tools::getRemoteAddr(), explode(',', Context::getContext()->phenyxConfig->get('EPH_MAINTENANCE_IP')));
        }

        if (!(!Context::getContext()->phenyxConfig->get('EPH_SHOP_ENABLE') && !$allowed)) {
            $tags = Context::getContext()->_hook->exec('actiongetMetaTags', ['idLang' => $idLang, 'pageName' => $pageName, 'title' => $title]);

            if ($pageName == 'cms' && ($idCms = Tools::getValue('id_cms'))) {
                return Meta::getCmsMetas($idCms, $idLang, $pageName);
            }

        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    public static function getAdminControllers() {

        $return = [];

        $controllers = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('class_name')
                ->from('back_tab')
        );

        foreach ($controllers as $controller) {

            if (!is_null($controller['class_name'])) {
                $return[] = $controller['class_name'];
            }

        }

        return $return;

    }

    public static function completeMetaTags($metaTags, $defaultValue, $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        if (empty($metaTags['meta_title'])) {
            $metaTags['meta_title'] = $defaultValue . ' - ' . Context::getContext()->phenyxConfig->get('EPH_SHOP_NAME');
        }

        if (empty($metaTags['meta_description'])) {
            $metaTags['meta_description'] = Context::getContext()->phenyxConfig->get('EPH_META_DESCRIPTION', $context->language->id) ? Context::getContext()->phenyxConfig->get('EPH_META_DESCRIPTION', $context->language->id) : '';
        }

        if (empty($metaTags['meta_keywords'])) {
            $metaTags['meta_keywords'] = Context::getContext()->phenyxConfig->get('EPH_META_KEYWORDS', $context->language->id) ? Context::getContext()->phenyxConfig->get('EPH_META_KEYWORDS', $context->language->id) : '';
        }

        return $metaTags;
    }

    public static function getHomeMetas($idLang, $pageName) {

        $metas = Meta::getMetaByPage($pageName, $idLang);
        $ret['meta_title'] = (isset($metas['title']) && $metas['title']) ? $metas['title'] . ' - ' . Context::getContext()->phenyxConfig->get('EPH_SHOP_NAME') : Context::getContext()->phenyxConfig->get('EPH_SHOP_NAME');
        $ret['meta_description'] = (isset($metas['description']) && $metas['description']) ? $metas['description'] : '';
        $ret['meta_keywords'] = (isset($metas['keywords']) && $metas['keywords']) ? $metas['keywords'] : '';

        return $ret;
    }

    public static function getMetaByPage($page, $idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
                ->select('*')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta`')
                ->where('m.`page` = \'' . pSQL($page) . '\' OR m.`page` = \'' . pSQL(str_replace('_', '', strtolower($page))) . '\'')
                ->where('ml.`id_lang` = ' . (int) $idLang)
        );
    }

    public static function getMetaById($idMeta, $idLang) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
                ->select('*')
                ->from('meta', 'm')
                ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta`')
                ->where('m.`id_meta` = ' . (int) $idMeta)
                ->where('ml.`id_lang` = ' . (int) $idLang)
        );
    }

    public static function getCmsMetas($idCms, $idLang, $pageName) {

        if ($row = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
            ->select('`meta_title`, `meta_description`, `meta_keywords`')
            ->from('cms_lang')
            ->where('`id_lang` = ' . (int) $idLang)
            ->where('`id_cms` = ' . (int) $idCms)

        )) {
            $row['meta_title'] = $row['meta_title'] . ' - ' . Context::getContext()->phenyxConfig->get('EPH_SHOP_NAME');

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    public static function getCmsCategoryMetas($idCmsCategory, $idLang, $pageName) {

        if ($row = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
            ->select('`meta_title`, `meta_description`, `meta_keywords`')
            ->from('cms_category_lang')
            ->where('`id_lang` = ' . (int) $idLang)
            ->where('`id_cms_category` = ' . (int) $idCmsCategory)

        )) {
            $row['meta_title'] = $row['meta_title'] . ' - ' . Context::getContext()->phenyxConfig->get('EPH_SHOP_NAME');

            return Meta::completeMetaTags($row, $row['meta_title']);
        }

        return Meta::getHomeMetas($idLang, $pageName);
    }

    public function deleteSelection($selection) {

        if (!is_array($selection)) {
            die(Tools::displayError());
        }

        $result = true;

        foreach ($selection as $id) {
            $this->id = (int) $id;
            $result = $result && $this->delete();
        }

        return $result && Tools::generateHtaccess();
    }

    public function delete() {

        if (!parent::delete()) {
            return false;
        }

        return Tools::generateHtaccess();
    }

    public static function getReferentPages() {

        $return = [];
        $pages = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('`page`')
                ->from('meta')
                ->orderBy('page')
        );

        foreach ($pages as $page) {
            $return[] = $page['page'];
        }

        return $return;

    }
    
    public static function getSynchMetas() {
        
        $synchMetas = [];
        $metas = new PhenyxCollection('Meta');
        foreach($metas as $meta) {
            $synchMetas[] = Meta::buildObject($meta->id);
        }
        
        return $synchMetas;
        
    }

}
