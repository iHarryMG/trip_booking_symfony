<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setSession($attr_name, $attr_value)
    {
        $this->session->set($attr_name, $attr_value);
    }
    
    public function getSession($attr_name)
    {
        return $filters = $this->session->get($attr_name);
    }
    
    
}