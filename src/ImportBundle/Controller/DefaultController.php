<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace ImportBundle\Controller;

use BudgetBundle\Entity\Account;
use BudgetBundle\Entity\Operation;
use BudgetBundle\Entity\OperationPattern;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package ImportBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $statementsFolder = $this->getParameter('statements_folder');

        $this->importCsvStatements($statementsFolder);

        return $this->render('ImportBundle:Default:index.html.twig', []);
    }

    /**
     * @param $statementsFolder
     */
    protected function importCsvStatements($statementsFolder)
    {
        $finder = new Finder();
        $finder->files()
            ->in($statementsFolder)
            ->name('*.csv');

        $em = $this->get('doctrine')->getManager();

        foreach ($finder as $csv) {
            if (($handle = fopen($csv->getRealPath(), "r")) !== false) {
                $accountNumber = null;
                $balance = null;
                $accountImportDate = null;
                $operations = [];

                /** @var Account $account */
                $account = null;
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== false) {
                    if ($i == 0) {
                        $accountNumber = $data[1];

                        $account = $this->findAccount($accountNumber);
                        $em->persist($account);
                    } elseif ($i == 3) {
                        $accountImportDate = $this->toDate($data[1]);
                    } elseif ($i == 4) {
                        $balance = $this->toFloat($data[1]);

                        if (is_null($account->getImportDate()) || $accountImportDate > $account->getImportDate()) {
                            $account->setBalance($balance);
                            $account->setImportDate($accountImportDate);
                        }
                    } elseif ($i >= 8) {
                        $operations[] = $data;

                        $operation = new Operation();
                        $operation->setAccount($account);

                        $operation->setDate($this->toDate($data[0]));
                        $operation->setLabel(trim($data[1]));
                        $operation->setAmount($this->toFloat($data[2]));

                        $operation->setSignature();
                        $operation->setImportDate(new \DateTime());

                        $operationExists = $this->get('doctrine')->getRepository('BudgetBundle:Operation')->findOneBy([
                            'signature' => $operation->getSignature(),
                        ]);
                        if (is_null($operationExists)) {
                            // auto affect category
                            $patterns = $this->get('doctrine')->getRepository('BudgetBundle:OperationPattern')->findByPattern($operation->getLabel());
                            if (sizeof($patterns) === 1) {
                                /** @var OperationPattern $pattern */
                                $pattern = $patterns[0];
                                $operation->setCategory($pattern->getCategory());
                            }

                            $em->persist($operation);
                        } elseif (is_null($operationExists->getCategory())) {
                            // auto affect category
                            $patterns = $this->get('doctrine')->getRepository('BudgetBundle:OperationPattern')->findByPattern($operation->getLabel());
                            if (sizeof($patterns) === 1) {
                                /** @var OperationPattern $pattern */
                                $pattern = $patterns[0];
                                $operationExists->setCategory($pattern->getCategory());
                                $em->persist($operationExists);
                            } else {
                                unset($operation);
                                unset($operationExists);
                            }
                        } else {
                            unset($operation);
                            unset($operationExists);
                        }
                    }

                    $i++;
                }
                fclose($handle);

                $em->flush();
            }
        }
    }

    /**
     * @param string $number
     * @return Account
     */
    protected function findAccount($number)
    {
        $account = $this->get('doctrine')->getRepository('BudgetBundle:Account')->findOneBy([
            'number' => $number,
        ]);

        if (is_null($account)) {
            $account = new Account();
            $account->setNumber($number);
            $account->setName($number);
        }

        return $account;
    }

    /**
     * @param string $val
     * @return bool|\DateTime
     */
    protected function toDate($val)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $val);
        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @param string $val
     * @return float
     */
    protected function toFloat($val)
    {
        $val = str_replace(',', '.', $val);

        return floatval($val);
    }
}
