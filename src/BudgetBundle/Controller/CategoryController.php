<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController as Controller;

/**
 * Class CategoryController
 * @package BudgetBundle\Controller
 */
class CategoryController extends Controller
{
    /**
     * @return \FOS\RestBundle\View\View
     */
    public function getCategoriesAction()
    {
        $categories = $this->get('doctrine')->getRepository('BudgetBundle:Category')->findBy(
            [],
            ['type' => 'asc', 'name' => 'asc']
        );

        return $this->view($categories);
    }
}
