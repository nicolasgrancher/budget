<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package BudgetBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('BudgetBundle:Default:index.html.twig');
    }
}
