<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParentController extends AbstractController
{
    protected $session;
    
    public function __construct() {
        $this->session = new Session();
        
    }
    
    
}
