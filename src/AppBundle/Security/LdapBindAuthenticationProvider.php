<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Ldap\LdapClientInterface;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class LdapBindAuthenticationProvider extends UserAuthenticationProvider
{
    private $userProvider;
    private $ldap;
    private $dnString;
    private $encoderFactory;
    private $baseDn;

    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, LdapClientInterface $ldap, $dnString = '{username}', $hideUserNotFoundExceptions = true, EncoderFactory $encoderFactory, $baseDn)
    {
        parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);

        $this->userProvider = $userProvider;
        $this->ldap = $ldap;
        $this->dnString = $dnString;
        $this->encoderFactory = $encoderFactory;
        $this->baseDn = $baseDn;
    }

    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        if ('NONE_PROVIDED' === $username) {
            throw new UsernameNotFoundException('Username can not be null');
        }

        return $this->userProvider->loadUserByUsername($username);
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $username = $token->getUsername();
        $password = $token->getCredentials();
        if ('' === $password) {
            throw new BadCredentialsException('The presented password must not be empty.');
        }
        $ldapUser = $this->ldap->find($this->baseDn, 'uid='.$username);

        if ($ldapUser['count'] > 0) {
            try {
                $username = $this->ldap->escape($username, '', LDAP_ESCAPE_DN);
                $dn = $ldapUser[0]['dn'];
                $this->ldap->bind($dn, $password);
            } catch (ConnectionException $e) {
                throw new BadCredentialsException('The presented password is invalid.');
            }
        } else {

            $currentUser = $token->getUser();
            if ($currentUser instanceof UserInterface) {
                if ($currentUser->getPassword() !== $user->getPassword()) {
                    throw new BadCredentialsException('The credentials were changed from another session.');
                }
            } else {
                if ('' === ($presentedPassword = $token->getCredentials())) {
                    throw new BadCredentialsException('The presented password cannot be empty.');
                }

                if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $presentedPassword, $user->getSalt())) {
                    throw new BadCredentialsException('The presented password is invalid.');
                }
            }
        }

    }
}
