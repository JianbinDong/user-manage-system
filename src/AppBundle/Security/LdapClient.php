<?php

namespace AppBundle\Security;

use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\LdapClientInterface;
use AppBundle\Common\ArrayToolkit;

class LdapClient implements LdapClientInterface
{
    private $host;
    private $port;
    private $version;
    private $useSsl;
    private $useStartTls;
    private $optReferrals;
    private $connection;

    public function __construct($host = null, $port = 389, $version = 3, $useSsl = false, $useStartTls = false, $optReferrals = false)
    {
        if (!extension_loaded('ldap')) {
            throw new LdapException('The ldap module is needed.');
        }

        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        $this->useSsl = (bool) $useSsl;
        $this->useStartTls = (bool) $useStartTls;
        $this->optReferrals = (bool) $optReferrals;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function bind($dn = null, $password = null)
    {
        if (!$this->connection) {
            $this->connect();
        }

        if (false === @ldap_bind($this->connection, $dn, $password)) {
            throw new ConnectionException(ldap_error($this->connection));
        }
    }

    public function find($dn, $query, $filter = '*')
    {
        if (!$this->connection) {
            $this->connect();
        }
        if (!is_array($filter)) {
            $filter = array($filter);
        }

        $search = ldap_search($this->connection, $dn, $query, $filter);

        if (false === $search) {
            throw new LdapException(ldap_error($this->connection));
        }

        $infos = ldap_get_entries($this->connection, $search);

        if (false === @ldap_free_result($search)) {
            throw new LdapException(ldap_error($this->connection));
        }

        if (0 === $infos['count']) {
            return;
        }
        return $infos;
    }

    public function escape($subject, $ignore = '', $flags = 0)
    {
        $value = ldap_escape($subject, $ignore, $flags);

        if ((int) $flags & LDAP_ESCAPE_DN) {
            if (!empty($value) && $value[0] === ' ') {
                $value = '\\20'.substr($value, 1);
            }
            if (!empty($value) && $value[strlen($value) - 1] === ' ') {
                $value = substr($value, 0, -1).'\\20';
            }
            $value = str_replace("\r", '\0d', $value);
        }

        return $value;
    }

    public function updateUser($baseDn, $query, $fields)
    {
        $search = $this->find($baseDn, $query);
        if ($search['count'] != 1) {

        } else {
            $user = $search[0];
            $keys = array_keys($user);
            foreach ($keys as $index => $key) {
                if (is_numeric($key)) {
                    unset($keys[$index]);
                }
            }

            $updateFields = ArrayToolkit::parts($fields, $keys);
            $this->updateAttrs($baseDn, $query, $updateFields);
            
            $createFields = array_diff($fields, $updateFields);
            $this->addAttrs($baseDn, $query, $createFields);
        }
    }

    public function updateAttrs($baseDn, $query, $fields)
    {
        $search = $this->find($baseDn, $query);
        if ($search['count'] != 1) {

        } else {
            $user = $search[0];
            var_dump(array_keys($user));exit();
            ldap_modify($this->connection, $user['dn'], $fields);
        }
    }

    public function addAttrs($baseDn, $query, $fields)
    {
        $search = $this->find($baseDn, $query);
        if ($search['count'] != 1) {

        } else {
            $user = $search[0];
            ldap_mod_add($this->connection, $user['dn'], $fields);
        }
    }

    private function connect()
    {
        if (!$this->connection) {
            $host = $this->host;

            if ($this->useSsl) {
                $host = 'ldaps://'.$host;
            }

            $this->connection = ldap_connect($host, $this->port);

            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, $this->version);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, $this->optReferrals);

            if ($this->useStartTls) {
                ldap_start_tls($this->connection);
            }
        }
    }

    private function disconnect()
    {
        if ($this->connection && is_resource($this->connection)) {
            ldap_unbind($this->connection);
        }

        $this->connection = null;
    }


}