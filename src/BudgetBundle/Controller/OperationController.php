<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController as Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OperationController
 * @package BudgetBundle\Controller
 */
class OperationController extends Controller
{
    /**
     * @param int $id
     * @param Request $request
     * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postOperationAction(int $id, Request $request)
    {
        $operation = $this->get('doctrine')->getRepository('BudgetBundle:Operation')->find($id);

        if (empty($operation)) {
            return $this->createNotFoundException();
        }

        $categoryId = $request->get('category');
        if (empty($categoryId)) {
            $operation->setCategory(null);
        } else {
            $category = $this->get('doctrine')->getRepository('BudgetBundle:Category')->find($categoryId);

            if (empty($category)) {
                return $this->createNotFoundException();
            }

            $operation->setCategory($category);
        }

        $this->get('doctrine')->getManager()->flush();

        return $this->view($operation);
    }
}
