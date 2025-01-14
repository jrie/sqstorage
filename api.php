<?php

/**
 * PHP-CRUD-API v2              License: MIT
 * Maurits van der Schee: maurits@vdschee.nl
 * https://github.com/mevdschee/php-crud-api
 *
 * Dependencies:
 * - vendor/psr/*: PHP-FIG
 *   https://github.com/php-fig
 * - vendor/nyholm/*: Tobias Nyholm
 *   https://github.com/Nyholm
 **/


/**
 *
 *
 *
 *
 *
 *
 *
 *               AAA               PPPPPPPPPPPPPPPPP   IIIIIIIIII        SSSSSSSSSSSSSSS TTTTTTTTTTTTTTTTTTTTTTT         AAA               RRRRRRRRRRRRRRRRR   TTTTTTTTTTTTTTTTTTTTTTT
 *              A:::A              P::::::::::::::::P  I::::::::I      SS:::::::::::::::ST:::::::::::::::::::::T        A:::A              R::::::::::::::::R  T:::::::::::::::::::::T
 *             A:::::A             P::::::PPPPPP:::::P I::::::::I     S:::::SSSSSS::::::ST:::::::::::::::::::::T       A:::::A             R::::::RRRRRR:::::R T:::::::::::::::::::::T
 *            A:::::::A            PP:::::P     P:::::PII::::::II     S:::::S     SSSSSSST:::::TT:::::::TT:::::T      A:::::::A            RR:::::R     R:::::RT:::::TT:::::::TT:::::T
 *           A:::::::::A             P::::P     P:::::P  I::::I       S:::::S            TTTTTT  T:::::T  TTTTTT     A:::::::::A             R::::R     R:::::RTTTTTT  T:::::T  TTTTTT
 *          A:::::A:::::A            P::::P     P:::::P  I::::I       S:::::S                    T:::::T            A:::::A:::::A            R::::R     R:::::R        T:::::T
 *         A:::::A A:::::A           P::::PPPPPP:::::P   I::::I        S::::SSSS                 T:::::T           A:::::A A:::::A           R::::RRRRRR:::::R         T:::::T
 *        A:::::A   A:::::A          P:::::::::::::PP    I::::I         SS::::::SSSSS            T:::::T          A:::::A   A:::::A          R:::::::::::::RR          T:::::T
 *       A:::::A     A:::::A         P::::PPPPPPPPP      I::::I           SSS::::::::SS          T:::::T         A:::::A     A:::::A         R::::RRRRRR:::::R         T:::::T
 *      A:::::AAAAAAAAA:::::A        P::::P              I::::I              SSSSSS::::S         T:::::T        A:::::AAAAAAAAA:::::A        R::::R     R:::::R        T:::::T
 *     A:::::::::::::::::::::A       P::::P              I::::I                   S:::::S        T:::::T       A:::::::::::::::::::::A       R::::R     R:::::R        T:::::T
 *    A:::::AAAAAAAAAAAAA:::::A      P::::P              I::::I                   S:::::S        T:::::T      A:::::AAAAAAAAAAAAA:::::A      R::::R     R:::::R        T:::::T
 *   A:::::A             A:::::A   PP::::::PP          II::::::II     SSSSSSS     S:::::S      TT:::::::TT   A:::::A             A:::::A   RR:::::R     R:::::R      TT:::::::TT
 *  A:::::A               A:::::A  P::::::::P          I::::::::I     S::::::SSSSSS:::::S      T:::::::::T  A:::::A               A:::::A  R::::::R     R:::::R      T:::::::::T
 * A:::::A                 A:::::A P::::::::P          I::::::::I     S:::::::::::::::SS       T:::::::::T A:::::A                 A:::::A R::::::R     R:::::R      T:::::::::T
 *AAAAAAA                   AAAAAAAPPPPPPPPPP          IIIIIIIIII      SSSSSSSSSSSSSSS         TTTTTTTTTTTAAAAAAA                   AAAAAAARRRRRRRR     RRRRRRR      TTTTTTTTTTT
 *
 *
 *
 *
 *
 *
 */
require_once 'vendor/autoload.php';
require_once 'support/dba.php';
require_once 'support/tools.php';
error_reporting(-1);

use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

//var_dump($_SERVER['PATH_INFO']);

$settings['driver'] = 'mysql';
$settings['address'] = $host;
$settings['port'] = $port;
$settings['username'] = DB::$user;
$settings['password'] = DB::$password;
$settings['database'] = $dbName;
$settings['tables'] = 'customfields,fielddata,headCategories,images,items,storages,subCategories';
$settings['debug'] = true;


if ($useRegistration) {
    $settings['middlewares'] = 'dbAuth,authorization';
    $settings["dbAuth.usersTable"] = 'users'; //: The table that is used to store the users in ("users")
    $settings["dbAuth.usernameColumn"] = 'username'; //: The users table column that holds usernames ("username")
    $settings["dbAuth.passwordColumn"] = 'password'; //: The users table column that holds passwords ("password")
    $settings["dbAuth.returnedColumns"] = 'id, username';
    $settings['authorization.tableHandler'] = function ($operation, $tableName) {
        global $ug;
        $ug = 999;
        if (isset($_SESSION['user']['id'])) {
            $ug = fGetUserGroupID($_SESSION['user']['id']);
            $_SESSION['user']['usergroupid'] = $ug;
        } else {
            if (isset($_SESSION['authenticated']))
                $ug = $_SESSION['user']['usergroupid'];
        }
        if ($ug == 2) {
            if ($operation == "list")
                return true;
            if ($operation == "read")
                return true;
            return false;
        }
        if ($ug == 999)
            return false;
        return $tableName != 'users';
    };

    $settings['tables'] .= 'users';
}

$config = new Config($settings);
$request = RequestFactory::fromGlobals();
$api = new Api($config);
$response = $api->handle($request);
ResponseUtils::output($response);

/*
*  Helper
*/

function fGetUserGroupID($userid)
{
    if ($userid < 1)
        return 999;
    $usergroupid = DB::queryFirstField('SELECT usergroupid FROM users_groups WHERE userid = %i', $userid);
    if (!$usergroupid) {
        return 999;
    }
    return $usergroupid;
}
