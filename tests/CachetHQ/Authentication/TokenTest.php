<?php

namespace TonicHealthCheck\Test\CachetHQ\Authentication;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use TonicHealthCheck\CachetHQ\Authentication\Token;

/**
 * Authenticate a PSR-7 Request using a token.
 */
class TokenTest extends PHPUnit_Framework_TestCase
{
    const DEFAULT_TOKEN = 'xsf322tg2r14tf1wfwff';
    /**
     * @var string
     */
    private $tokenString = self::DEFAULT_TOKEN;

    /**
     * @var Token
     */
    private $token;

    /**
     * Test that Authenticate set header
     */
    public function testAuthenticate()
    {
        $this->setToken(new Token($this->getTokenString()));
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();

        $request
            ->expects($this->once())
            ->method('withHeader')
            ->with(Token::COOKIE_CACHET_TOKEN, $this->equalTo($this->getTokenString()));

        $this->getToken()->authenticate($request);
    }

    /**
     * @return string
     */
    protected function getTokenString()
    {
        return $this->tokenString;
    }

    /**
     * @param string $tokenString
     */
    protected function setTokenString($tokenString)
    {
        $this->tokenString = $tokenString;
    }

    /**
     * @return Token
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    protected function setToken(Token $token)
    {
        $this->token = $token;
    }
}
