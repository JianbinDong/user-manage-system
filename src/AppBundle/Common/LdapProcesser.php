<?php

namespace AppBundle\Common;

use Biz\BizKernel;
use AppBundle\Security\LdapClient;
use AppBundle\Common\ArrayToolkit;

class LdapProcesser
{
    private $ldap;
    private $baseDn;
    private $searchDn;
    private $searchPassword;
    private $defaultRoles;
    private $defaultSearch;
    private $biz;
    private $completeAttrs;
    private $infoKeys = array('rank', 'trueName', 'phone', 'email', 'gender');

    public function __construct($biz, $uidKey = 'uid', $filter = '({uid_key}={username})')
    {
        $this->biz = $biz;
        $host = $this->biz->getParameter('ldap_host');
        $this->baseDn = $this->biz->getParameter('base_dn');
        $this->searchDn = $this->biz->getParameter('search_dn');
        $this->searchPassword = $this->biz->getParameter('search_password');
        $this->ldap = new LdapClient($host);
        $this->defaultRoles = $this->biz->getParameter('default_roles');
        $this->defaultSearch = str_replace('{uid_key}', $uidKey, $filter);
        $this->completeAttrs = array('mail', 'mobile');
    }

    public function updateAllUserLdapInfo()
    {
        $users = $this->getUserService()->searchAllUsers(
            array(),
            array('number', 'ASC'),
            0,
            10
        );
        
        $this->ldap->bind($this->searchDn, $this->searchPassword);
        $baseDn = $this->baseDn;
        foreach ($users as $key => $user) {
            $username = $this->ldap->escape($user['username'], '', LDAP_ESCAPE_FILTER);
            $query = str_replace('{username}', $username, $this->defaultSearch);
            $search = $this->ldap->find($this->baseDn, $query);
            if ($search['count'] == 1) {
                $fields = $this->processData($user);
                $this->ldap->updateUser($baseDn, $query, $fields);
            }
        }
    }

    public function updateUserLdapInfo($userId)
    {
        $user = $this->getUserService()->getCompleteinfo($userId);
        $user = array_merge($user['user'], $user['basic']);
        $this->ldap->bind($this->searchDn, $this->searchPassword);
        $username = $this->ldap->escape($user['username'], '', LDAP_ESCAPE_FILTER);
        $query = str_replace('{username}', $username, $this->defaultSearch);
        $search = $this->ldap->find($this->baseDn, $query);
        if ($search['count'] == 1) {
            $fields = $this->processData($user);
            $this->ldap->updateUser($this->baseDn, $query, $fields);
        }
    }

    public function processData($user)
    {
        $infoKeys = $this->infoKeys;
        $datas = ArrayToolkit::parts($user, $infoKeys);
        $map = array(
            'email' => 'mail',
            'phone' => 'mobile'
        );
        $datas = ArrayToolkit::rename($datas, $map);
        return ArrayToolkit::parts($datas, $this->completeAttrs);
    }

    protected function getUserService()
    {
        return $this->biz['user_service'];
    }
}