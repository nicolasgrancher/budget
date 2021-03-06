<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Entity;

/**
 * Class Operation
 * @package BudgetBundle\Entity
 */
class Operation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var \DateTime
     */
    private $importDate;

    /**
     * @var boolean
     */
    private $manual = false;

    /**
     * @var \BudgetBundle\Entity\Account
     */
    private $account;

    /**
     * @var \BudgetBundle\Entity\Category
     */
    private $category;

    /**
     * @var \BudgetBundle\Entity\Operation
     */
    private $reconciliation;

    /**
     * @var \BudgetBundle\Entity\Operation
     */
    private $reconcialiated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Operation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set signature
     *
     * @return Operation
     */
    public function setSignature()
    {
        $signature = sha1($this->getDate()->format('d/m/Y') . $this->getLabel() . $this->getAmount());

        $this->signature = $signature;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Operation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Operation
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Operation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get importDate
     *
     * @return \DateTime
     */
    public function getImportDate()
    {
        return $this->importDate;
    }

    /**
     * Set importDate
     *
     * @param \DateTime $importDate
     *
     * @return Operation
     */
    public function setImportDate($importDate)
    {
        $this->importDate = $importDate;

        return $this;
    }

    /**
     * Get account
     *
     * @return \BudgetBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set account
     *
     * @param \BudgetBundle\Entity\Account $account
     *
     * @return Operation
     */
    public function setAccount(\BudgetBundle\Entity\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get category
     *
     * @return \BudgetBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param \BudgetBundle\Entity\Category $category
     *
     * @return Operation
     */
    public function setCategory(\BudgetBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get reconciliation
     *
     * @return \BudgetBundle\Entity\Operation
     */
    public function getReconciliation()
    {
        return $this->reconciliation;
    }

    /**
     * Set reconciliation
     *
     * @param \BudgetBundle\Entity\Operation $reconciliation
     *
     * @return Operation
     */
    public function setReconciliation(\BudgetBundle\Entity\Operation $reconciliation = null)
    {
        $this->reconciliation = $reconciliation;

        return $this;
    }

    /**
     * Get reconcialiated
     *
     * @return \BudgetBundle\Entity\Operation
     */
    public function getReconcialiated()
    {
        return $this->reconcialiated;
    }

    /**
     * Set reconcialiated
     *
     * @param \BudgetBundle\Entity\Operation $reconcialiated
     *
     * @return Operation
     */
    public function setReconcialiated(\BudgetBundle\Entity\Operation $reconcialiated = null)
    {
        $this->reconcialiated = $reconcialiated;

        return $this;
    }

    /**
     * Get manual
     *
     * @return boolean
     */
    public function getManual()
    {
        return $this->manual;
    }

    /**
     * Set manual
     *
     * @param boolean $manual
     *
     * @return Operation
     */
    public function setManual($manual)
    {
        $this->manual = $manual;

        return $this;
    }
}
