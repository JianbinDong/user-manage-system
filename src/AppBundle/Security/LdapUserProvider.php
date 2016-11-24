<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapClientInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Biz\BizKernel;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use AppBundle\Security\CurrentUser;

class LdapUserProvider implements UserProviderInterface
{
    private $ldap;
    private $baseDn;
    private $searchDn;
    private $searchPassword;
    private $defaultRoles;
    private $defaultSearch;
    private $biz;

    public function __construct(LdapClientInterface $ldap, $baseDn, $searchDn = null, $searchPassword = null, array $defaultRoles = array(), $uidKey = 'sAMAccountName', $filter = '({uid_key}={username})', $biz)
    {
        $this->ldap = $ldap;
        $this->baseDn = $baseDn;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
        $this->defaultRoles = $defaultRoles;
        $this->defaultSearch = str_replace('{uid_key}', $uidKey, $filter);
        $this->biz = $biz;
    }

    public function loadUserByUsername($username)
    {
        try {
            $this->ldap->bind($this->searchDn, $this->searchPassword);
            $username = $this->ldap->escape($username, '', LDAP_ESCAPE_FILTER);
            $query = str_replace('{username}', $username, $this->defaultSearch);
            $search = $this->ldap->find($this->baseDn, $query);
        } catch (ConnectionException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username), 0, $e);
        }
        if (!$search || $search['count'] == 0) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        if ($search['count'] > 1) {
            throw new UsernameNotFoundException('More than one user found');
        }

        $ldapUser = $search[0];

        $user = $this->getUserService()->getUserByUsername($username);
            
        if (empty($user)) {
            if (empty($ldapUser['mail']) || empty($ldapUser['initials'])) {
                throw new BadCredentialsException('ldap信息错误，请联系王建平.');
            }
            $password = $this->generateRandomChars();
            $user = array(
                'email' => $ldapUser['mail'][0],
                'username' => $username,
                'password' => $password,
                'number' => $ldapUser['initials'][0],
                'roles' => array('ROLE_USER'),
            );

            $user = $this->getUserService()->register($user);
        }

        $basic = $this->biz['user_service']->getBasic($user['id']);

        $department = $this->biz['department_service']->getDepartment($basic['departmentId']);
        $user['trueName'] = $basic['trueName'];
        $user['department'] = $department['name'];

        $currentUser = new CurrentUser($user);

        $this->biz->setUser($currentUser);

        return new CurrentUser($user);
    }

    public function loadUser($username, $user)
    {
        if (!$user instanceof CurrentUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return new User($user->getUsername(), null, $user->getRoles());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

    protected function getUserService()
    {
        return $this->biz['user_service'];
    }

    protected function generateRandomChars($maxLoop = 100)
    {
        for ($i = 0; $i < $maxLoop; $i++) {
            $password = 'user-'.substr(mt_rand(), 0, 6);
        }

        return $password;
    }
}
