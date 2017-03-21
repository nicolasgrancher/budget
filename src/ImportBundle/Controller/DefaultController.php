<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace ImportBundle\Controller;

use BudgetBundle\Entity\Account;
use BudgetBundle\Entity\Operation;
use BudgetBundle\Entity\OperationPattern;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package ImportBundle\Controller
 */
class DefaultController extends Controller
{
    const TEMPLATE_LBP = 0;
    const TEMPLATE_CE = 1;

    /**
     * @var EntityManager
     */
    protected $em;

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
            ->in($statementsFolder);

        $this->em = $this->get('doctrine')->getManager();

        foreach ($finder as $file) {
            $this->log("processing {$file->getFilename()}...");

            if (preg_match('/^[0-9]{7}[A-Z][0-9]{16}\.csv$/', $file->getFilename())) {
                $this->log("La Banque Postale");

                $this->parseCsvFile($file, self::TEMPLATE_LBP);

            } elseif (preg_match('/^telechargement\.csv$/', $file->getFilename())) {
                $this->log("La Caisse d'Epargne");

                $this->parseCsvFile($file, self::TEMPLATE_CE);
            } else {
                $this->log("...unsupported template");
            }
        }
    }

    /**
     * @param $message
     */
    protected function log($message)
    {
        echo "[" . date('Y-m-d H:i:s') . "] $message<br>";
    }

    /**
     * @param SplFileInfo $file
     * @param $template
     */
    protected function parseCsvFile(SplFileInfo $file, $template)
    {
        if (($handle = fopen($file->getRealPath(), "r")) !== false) {
            $this->log('opening file');

            $accountImportDate = null;

            /** @var Account $account */
            $account = null;
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== false) {
                switch ($template) {
                    case self::TEMPLATE_LBP :
                        $this->parseLBPTemplate($i, $data, $account, $accountImportDate);
                        break;
                    case self::TEMPLATE_CE :
                        $this->parseCETemplate($i, $data, $account, $accountImportDate);
                        break;
                }

                $i++;
            }
            $this->log("$i lines");
            fclose($handle);

            $this->em->flush();
            $this->log('...saving ok');
        } else {
            $this->log("error while opening {$file->getFilename()}");
        }
    }

    /**
     * @param int $lineNumber
     * @param array $data
     * @param Account $account
     * @param \DateTime $accountImportDate
     */
    protected function parseLBPTemplate($lineNumber, array $data, Account &$account = null, \DateTime &$accountImportDate = null)
    {
        if ($lineNumber == 0) {
            $accountNumber = $data[1];

            $account = $this->findAccount($accountNumber);
            $this->em->persist($account);
        } elseif ($lineNumber == 3) {
            $accountImportDate = $this->toDate($data[1]);
        } elseif ($lineNumber == 4) {
            $balance = $this->toFloat($data[1]);

            if (is_null($account->getImportDate()) || $accountImportDate > $account->getImportDate()) {
                $account->setBalance($balance);
                $account->setImportDate($accountImportDate);
            }
        } elseif ($lineNumber >= 8) {

            $operation = new Operation();
            $operation->setAccount($account);

            $operation->setDate($this->toDate($data[0]));
            $operation->setLabel(trim($data[1]));
            $operation->setAmount($this->toFloat($data[2]));

            $operation->setSignature();
            $operation->setImportDate(new \DateTime());

            $this->persist($operation);
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
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) {
            $date = \DateTime::createFromFormat('d/m/Y', $val);
        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $val)) {
            $date = \DateTime::createFromFormat('d/m/y', $val);
        } else {
            throw new \Exception("wrong date format : $val");
        }

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

    /**
     * @param Operation $operation
     */
    protected function persist(Operation $operation)
    {
        $operationExists = $this->findExistingOperation($operation->getSignature());
        if (is_null($operationExists)) {
            // auto affect category
            $this->affectCategory($operation);

            $this->em->persist($operation);
        } elseif (is_null($operationExists->getCategory())) {
            // auto affect category
            $this->affectCategory($operationExists);

            $this->em->persist($operationExists);

            unset($operation);
        } else {
            unset($operation);
            unset($operationExists);
        }
    }

    /**
     * @param string $signature
     * @return null|Operation
     */
    protected function findExistingOperation($signature)
    {
        return $this->get('doctrine')->getRepository('BudgetBundle:Operation')->findOneBy([
            'signature' => $signature,
        ]);
    }

    /**
     * @param Operation $operation
     */
    protected function affectCategory(Operation &$operation)
    {
        $patterns = $this->get('doctrine')->getRepository('BudgetBundle:OperationPattern')->findByPattern($operation->getLabel());
        if (sizeof($patterns) === 1) {
            /** @var OperationPattern $pattern */
            $pattern = $patterns[0];
            $operation->setCategory($pattern->getCategory());
        }
    }

    /**
     * @param $lineNumber
     * @param array $data
     * @param Account|null $account
     * @param \DateTime|null $accountImportDate
     */
    protected function parseCETemplate($lineNumber, array $data, Account &$account = null, \DateTime &$accountImportDate = null)
    {
        if ($lineNumber == 0) {
            preg_match('/\d{2}\/\d{2}\/\d{4}/', $data[3], $matches);
            $accountImportDate = $this->toDate($matches[0]);
        } elseif ($lineNumber == 1) {
            preg_match('/\d{11}/', $data[0], $matches);
            $accountNumber = $matches[0];

            $account = $this->findAccount($accountNumber);
            $this->em->persist($account);
        } elseif ($lineNumber == 3) {
            $balance = $this->toFloat($data[4]);

            if (is_null($account->getImportDate()) || $accountImportDate > $account->getImportDate()) {
                $account->setBalance($balance);
                $account->setImportDate($accountImportDate);
            }
        } elseif ($lineNumber >= 5) {
            if (!preg_match('/\d{2}\/\d{2}\/\d{2}/', $data[0]))
                return;

            $operation = new Operation();
            $operation->setAccount($account);

            $operation->setDate($this->toDate($data[0]));
            $operation->setLabel(trim($data[2]));
            $operation->setAmount($this->toFloat($data[3]) + $this->toFloat($data[4]));
            $operation->setDescription($data[5]);

            $operation->setSignature();
            $operation->setImportDate(new \DateTime());

            $this->persist($operation);
        }
    }
}
