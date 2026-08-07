<?php

namespace EphenyxDigital\QuantumCore;

use DateTime;
use DateTimeZone;


/**
 * @since 1.9.1.0
 */
class CronJobs extends PhenyxObjectModel {

    public $require_context = false;
    // @codingStandardsIgnoreStart
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'   => 'cronjobs',
        'primary' => 'id_cronjobs',
        'fields'  => [
            'description' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'class_name'  => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'task'        => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'hour'        => ['type' => self::TYPE_INT],
            'day'         => ['type' => self::TYPE_INT],
            'month'       => ['type' => self::TYPE_INT],
            'day_of_week' => ['type' => self::TYPE_INT],
            'custom'      => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'args'        => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'updated_at'  => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'active'      => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            /* Lang fields */

        ],
    ];

    // @codingStandardsIgnoreEnd
    public $description;
    public $class_name;
    public $task;
    public $hour;
    public $day;
    public $month;
    public $day_of_week;
    public $custom;
    public $args;
    public $updated_at;
    public $active;

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

    }

    /**
     * Passe en revue les taches actives et execute celles qui sont dues.
     *
     * ⚠️ CETTE METHODE N'EST APPELEE PAR AUCUN CODE DU SITE. Elle est declenchee
     * de l'exterieur, par une tache cron de l'hebergeur. Sa periodicite plafonne
     * donc la resolution de tout ce qui est regle ici : si le declencheur passe
     * une fois par heure, « toutes les dix minutes » signifie « une fois par
     * heure ». Le savoir evite de chercher un defaut la ou il n'y en a pas.
     *
     * ─── CE QUI A ETE RETIRE ───
     *
     * Sept lignes de debogage ecrivaient le deroulement — identifiants, noms de
     * taches, arguments, messages d'erreur — dans « testrunTasksCrons.txt », a
     * la RACINE du site, donc servi en clair tant que le bloc de securite du
     * .htaccess n'est pas en place. Les erreurs, elles, n'allaient nulle part
     * ailleurs : une tache qui echouait le faisait en silence.
     *
     * Les trois cas d'echec passent desormais par le journal des erreurs, ou
     * l'employe peut les voir — classe introuvable et methode introuvable en
     * gravite 3, car ils denoncent une saisie fautive dans l'ecran ; exception
     * du traitement en gravite 4.
     */
    public static function runTasksCrons() {

        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'cronjobs WHERE `active` = 1';
        $crons = Db::getInstance()->executeS($query);

        if (!is_array($crons) || !count($crons)) {
            return;
        }

        foreach ($crons as $cron) {

            if (!static::shouldBeExecuted($cron)) {
                continue;
            }

            $class_name = $cron['class_name'];

            if (!class_exists($class_name)) {
                PhenyxLogger::addLog(
                    sprintf('CronJobs: classe introuvable « %s » pour la tache « %s »', $class_name, $cron['description']),
                    3, null, 'CronJobs', (int) $cron['id_cronjobs'], true
                );
                continue;
            }

            $instance = new $class_name();
            $task = $cron['task'];

            if (!method_exists($instance, $task)) {
                PhenyxLogger::addLog(
                    sprintf('CronJobs: methode introuvable « %s::%s » pour la tache « %s »', $class_name, $task, $cron['description']),
                    3, null, 'CronJobs', (int) $cron['id_cronjobs'], true
                );
                continue;
            }

            $args = !empty($cron['args']) ? $cron['args'] : null;

            try {
                $instance->{$task}($args);

                /*
                 * ⚠️ La date n'est mise a jour QU'EN CAS DE SUCCES : c'est elle
                 * qui borne la fenetre du prochain passage. Une tache qui a
                 * echoue doit rester due, sans quoi l'echec se transformerait
                 * en silence definitif jusqu'a l'occurrence suivante.
                 */
                Db::getInstance()->execute(
                    'UPDATE ' . _DB_PREFIX_ . 'cronjobs SET `updated_at` = NOW() WHERE `id_cronjobs` = ' . (int) $cron['id_cronjobs']
                );
            } catch (\Throwable $e) {
                PhenyxLogger::addLog(
                    sprintf('CronJobs: la tache « %s » a echoue — %s', $cron['description'], $e->getMessage()),
                    4, null, 'CronJobs', (int) $cron['id_cronjobs'], true
                );
            }

        }

    }

    /**
     * Fenetre de rattrapage, en jours.
     *
     * On ne remonte jamais plus loin : une tache reactivee apres trois mois
     * d'arret ne doit pas se declencher pour toutes les occurrences manquees.
     */
    const RATTRAPAGE_JOURS = 7;

    /**
     * La tache doit-elle s'executer maintenant ?
     *
     * ─── CE QUI A CHANGE, ET POURQUOI ───
     *
     * L'ancienne version comparait « maintenant » a l'instant prevu, au format
     * « Mon 2026-08-02 14 ». Deux consequences, toutes deux graves :
     *
     *  1. ELLE MANQUAIT LA PLUPART DES DECLENCHEMENTS. Le passage est provoque
     *     par une tache cron de l'hebergeur. Si celle-ci passe a 4 h 05 et que
     *     la tache est reglee sur 4 h, la comparaison echouait — puisque l'heure
     *     courante etait bien 04, mais seulement pendant l'heure entiere. En
     *     pratique cela ne fonctionnait que si le declencheur passait au moins
     *     une fois par heure, et jamais deux fois (sinon la tache repartait).
     *
     *  2. ELLE IGNORAIT COMPLETEMENT LE CHAMP « custom ». L'ecran propose
     *     pourtant de saisir une expression du type « astérisque/10 * * * * ».
     *     Rien ne la lisait : le champ etait stocke, affiche, et sans effet.
     *
     * La nouvelle logique renverse la question. Au lieu de demander « sommes-nous
     * a la minute prevue ? », elle demande « une occurrence prevue est-elle
     * passee depuis la derniere execution ? ». C'est la seule formulation
     * robuste quand on ne maitrise pas la frequence du declencheur : aucune
     * occurrence n'est manquee, et aucune n'est rejouee.
     *
     * ⚠️ LA RESOLUTION RESTE PLAFONNEE PAR LE DECLENCHEUR. Ecrire « toutes les
     * dix minutes » ne sert a rien si la tache cron de l'hebergeur ne passe
     * qu'une fois par heure : la tache s'executera une fois par heure. Le champ
     * exprime un souhait, pas une garantie.
     *
     * ⚠️ SEMANTIQUE DU JOUR. On adopte celle des expressions cron standard :
     * quand le jour du mois ET le jour de la semaine sont tous deux contraints,
     * ils se combinent en OU, pas en ET. L'ancienne version faisait un ET —
     * c'est-a-dire que « le 1er » et « le lundi » ensemble ne se declenchaient
     * que les 1ers qui tombaient un lundi. Ce cas est rare et relevait plus
     * souvent de l'erreur de saisie que de l'intention.
     *
     * @param array $cron ligne de la table cronjobs
     *
     * @return bool
     */
    public static function shouldBeExecuted($cron) {

        $expression = static::expressionDe($cron);
        $champs = static::analyserExpression($expression);

        if ($champs === null) {
            return false;
        }

        $maintenant = time();

        /* Debut de la fenetre : la derniere execution, bornee au rattrapage. */
        $plancher = $maintenant - (static::RATTRAPAGE_JOURS * 86400);
        $depuis = $plancher;

        if (!empty($cron['updated_at']) && $cron['updated_at'] !== '0000-00-00 00:00:00') {
            $dernier = strtotime($cron['updated_at']);

            if ($dernier !== false && $dernier > $plancher) {
                $depuis = $dernier;
            }

        }

        /*
         * On balaie minute par minute la fenetre (depuis, maintenant]. Bornee a
         * sept jours, elle represente au pire 10 080 iterations d'une simple
         * comparaison de tableaux — negligeable, et surtout previsible.
         */
        $minute = ((int) floor($depuis / 60) + 1) * 60;
        $fin = (int) floor($maintenant / 60) * 60;

        for (; $minute <= $fin; $minute += 60) {

            if (static::correspond($champs, $minute)) {
                return true;
            }

        }

        return false;
    }

    /**
     * Rend l'expression a cinq champs qui gouverne la tache.
     *
     * Le champ libre l'emporte quand il est renseigne ET analysable — sans quoi
     * une faute de frappe rendrait la tache muette sans aucun signe. Dans ce cas
     * on retombe sur les quatre listes, qui restent le reglage de reference.
     */
    public static function expressionDe($cron) {

        $custom = isset($cron['custom']) ? trim((string) $cron['custom']) : '';

        if ($custom !== '' && static::analyserExpression($custom) !== null) {
            return $custom;
        }

        $partie = function ($valeur) {
            return ((int) $valeur === -1) ? '*' : (string) (int) $valeur;
        };

        /* Les quatre listes decrivent un instant a l'heure pile : minute = 0. */
        return '0 '
        . $partie($cron['hour'] ?? -1) . ' '
        . $partie($cron['day'] ?? -1) . ' '
        . $partie($cron['month'] ?? -1) . ' '
        . $partie($cron['day_of_week'] ?? -1);
    }

    /**
     * Analyse une expression a cinq champs.
     *
     * Accepte par champ : « * », « valeur », « a-b », « a,b,c », « astérisque/n »
     * et « a-b/n ». Rend un tableau de cinq listes de valeurs autorisees, ou
     * null si l'expression n'est pas exploitable.
     *
     * @return array|null [minutes, heures, jours, mois, joursSemaine, contraintes]
     */
    public static function analyserExpression($expression) {

        $expression = trim(preg_replace('/\s+/', ' ', (string) $expression));

        if ($expression === '') {
            return null;
        }

        $morceaux = explode(' ', $expression);

        if (count($morceaux) !== 5) {
            return null;
        }

        $bornes = [[0, 59], [0, 23], [1, 31], [1, 12], [0, 7]];
        $resultat = [];

        foreach ($morceaux as $i => $morceau) {
            $valeurs = static::analyserChamp($morceau, $bornes[$i][0], $bornes[$i][1]);

            if ($valeurs === null) {
                return null;
            }

            $resultat[] = $valeurs;
        }

        /*
         * Dimanche s'ecrit 0 ou 7 : on normalise sur 0 pour comparer a date('w').
         */

        if (in_array(7, $resultat[4], true)) {
            $resultat[4][] = 0;
            $resultat[4] = array_values(array_unique($resultat[4]));
        }

        /* Memoriser si jour du mois et jour de semaine sont contraints. */
        $resultat[5] = [
            'jourContraint'    => ($morceaux[2] !== '*'),
            'semaineContrainte' => ($morceaux[4] !== '*'),
        ];

        return $resultat;
    }

    /**
     * Analyse un champ unique et rend la liste des valeurs autorisees.
     */
    protected static function analyserChamp($morceau, $min, $max) {

        $valeurs = [];

        foreach (explode(',', $morceau) as $bloc) {

            $pas = 1;

            if (strpos($bloc, '/') !== false) {
                list($bloc, $texte) = explode('/', $bloc, 2);

                if (!ctype_digit($texte) || (int) $texte < 1) {
                    return null;
                }

                $pas = (int) $texte;
            }

            if ($bloc === '*') {
                $debut = $min;
                $finale = $max;
            } else

            if (strpos($bloc, '-') !== false) {
                list($a, $b) = explode('-', $bloc, 2);

                if (!ctype_digit($a) || !ctype_digit($b)) {
                    return null;
                }

                $debut = (int) $a;
                $finale = (int) $b;
            } else {

                if (!ctype_digit($bloc)) {
                    return null;
                }

                $debut = $finale = (int) $bloc;
            }

            if ($debut < $min || $finale > $max || $debut > $finale) {
                return null;
            }

            for ($v = $debut; $v <= $finale; $v += $pas) {
                $valeurs[] = $v;
            }

        }

        return empty($valeurs) ? null : array_values(array_unique($valeurs));
    }

    /**
     * L'instant donne correspond-il a l'expression analysee ?
     */
    protected static function correspond(array $champs, $horodatage) {

        if (!in_array((int) date('i', $horodatage), $champs[0], true)) {
            return false;
        }

        if (!in_array((int) date('G', $horodatage), $champs[1], true)) {
            return false;
        }

        if (!in_array((int) date('n', $horodatage), $champs[3], true)) {
            return false;
        }

        $jourOk = in_array((int) date('j', $horodatage), $champs[2], true);
        $semaineOk = in_array((int) date('w', $horodatage), $champs[4], true);

        /* Semantique cron : les deux contraints se combinent en OU. */

        if ($champs[5]['jourContraint'] && $champs[5]['semaineContrainte']) {
            return $jourOk || $semaineOk;
        }

        return $jourOk && $semaineOk;
    }

}
