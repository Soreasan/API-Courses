<?php
/**
 * Created by PhpStorm.
 * User: Joshua
 * Date: 10/7/2015
 * Time: 4:02 PM
 */

namespace TestingCenter\Controllers;
use Firebase\JWT;
use TestingCenter\Http\StatusCodes;
use TestingCenter\Models\Token;


class TokensController
{
    public function post()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $serverConfig = array('host' => '137.190.19.17',
            'FQDN' => 'cs.weber.edu',
            'accountDomainNameShort' => 'apollo',
            'accountCanonicalForm' => '4',
            'baseDn' => 'dc=cs,dc=weber,dc=edu');
        $ldapConnectionString = "ldap://" . $serverConfig["host"];
        $ad = ldap_connect($ldapConnectionString);
        if (is_null($ad)) {
            http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
            die("Unable to connect to LDAP server.");
        }

        ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ad, "{$username}@{$serverConfig["FQDN"]}", $password);

        if (!$bind)
        {
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("Your credentials were rejected by the server.");
        }

       //Check group membership
        $userdn = $this->getDN($ad, $username, $serverConfig['baseDn']);

        if ($userdn == '')
        {
            http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
            die("Failure to query LDAP");
        }

        $role = '';

        if (strpos($userdn, "OU=Students"))
        {
            $role = Token::ROLE_STUDENT;
        }

        if (strpos($userdn, "OU=Faculty"))
        {
            $role = Token::ROLE_FACULTY;
        }

        if (strpos($userdn, "OU=Tech"))
        {
            $role = Token::ROLE_AIDE;
        }

        return (new Token())->buildToken($role, $username);
    }

    /**
     * This function searchs in LDAP tree entry specified by samaccountname and
     * returns its DN or epmty string on failure.
     *
     * @param resource $ad
     *          An LDAP link identifier, returned by ldap_connect().
     * @param string $samaccountname
     *          The sAMAccountName, logon name.
     * @param string $basedn
     *          The base DN for the directory.
     * @return string
     */
   private function getDN($ad, $samaccountname, $basedn)
    {
        $result = ldap_search($ad, $basedn, "(samaccountname={$samaccountname})", array(
            'dn'
        ));
        if (! $result)
        {
            return '';
        }

        $entries = ldap_get_entries($ad, $result);
        if ($entries['count'] > 0)
        {
            return $entries[0]['dn'];
        }

        return '';
    }
}