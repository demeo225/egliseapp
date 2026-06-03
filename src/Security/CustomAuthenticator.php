<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Security\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use function str_starts_with;

class CustomAuthenticator implements InteractiveAuthenticatorInterface {

    public function supports(Request $request): ?bool {
        return str_starts_with($request->getPathInfo(), '/otherzonearea') && $request->isMethod('POST');
    }

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UserProviderInterface $userProvider) {
                $this->urlGenerator = $urlGenerator;

    }

    public function authenticate(Request $request): PassportInterface {
        $email = $request->server->get('PHP_AUTH_USER', '');
        $pwd = $request->server->get('PHP_AUTH_PW', '');
        return new Passport(
                new UserBadge($email, [$this->userProvider, 'loadUserByIdentifier']),
                new PasswordCredentials($pwd)
        );
    }

     public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
            return null;
    }

//    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
//        // TODO: Implement onAuthenticationSuccess() method.
//    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function isInteractive(): bool {
        // TODO: Implement isInteractive() method.
    }

    public function createAuthenticatedToken(PassportInterface $passport, $firewallName): TokenInterface {
        
    }

}
