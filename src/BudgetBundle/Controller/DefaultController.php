<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Controller;

use BudgetBundle\Entity\Account;
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
        return $this->render('BudgetBundle:Default:index.html.twig', []);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $accounts = $this->get('doctrine')->getRepository('BudgetBundle:Account')->findAll();

        return $this->render('BudgetBundle:Default:list.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportingAction()
    {
        $accounts = $this->get('doctrine')->getRepository('BudgetBundle:Account')->findAll();

        $operations = [];
        /** @var Account $account */
        foreach ($accounts as $account) {
            $operations[$account->getId()] = $this->get('doctrine')->getRepository('BudgetBundle:Category')->findAmountByMonth($account);
        }

        $monitorings = $this->get('doctrine')->getRepository('BudgetBundle:Monitoring')->findMonthlyCostByMonitoring();

        return $this->render('BudgetBundle:Default:reporting.html.twig', [
            'accounts' => $accounts,
            'operations' => $operations,
            'timeline' => $this->buildTimeline($operations),
            'monitorings' => $monitorings,
        ]);
    }

    /**
     * @param array $operations
     * @return array
     */
    protected function buildTimeline(array $operations)
    {
        $minYear = 9999;
        $minMonth = 9999;
        $maxYear = 0;
        $maxMonth = 0;

        foreach ($operations as $operation) {
            foreach ($operation as $category) {
                foreach ($category['data'] as $data) {
                    if ($data['year'] <= $minYear) {
                        if ($data['year'] < $minYear) {
                            $minYear = $data['year'];
                            $minMonth = 9999;
                        }

                        if ($data['month'] < $minMonth) {
                            $minMonth = $data['month'];
                        }
                    }

                    if ($data['year'] >= $maxYear) {
                        if ($data['year'] > $maxYear) {
                            $maxYear = $data['year'];
                            $maxMonth = 0;
                        }

                        if ($data['month'] > $maxMonth) {
                            $maxMonth = $data['month'];
                        }
                    }
                }
            }
        }

        $timeline = [];
        for ($i = $minYear; $i <= $maxYear; $i++) {
            if ($i == $minYear) {
                for ($j = $minMonth; $j <= 12; $j++) {
                    $timeline[] = $i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT);
                }
            } elseif ($i == $maxYear) {
                for ($j = 1; $j <= $maxMonth; $j++) {
                    $timeline[] = $i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT);
                }
            } else {
                for ($j = 1; $j <= 12; $j++) {
                    $timeline[] = $i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT);
                }
            }
        }

        return $timeline;
    }
}
