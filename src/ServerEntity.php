<?php
/**
 * This file is part of the Lsv\SysorbApi
 */
namespace Lsv\SysorbApi;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Server entity, holds a server status and errors
 *
 * @author Martin Aarhof <martin.aarhof@gmail.com>
 */
class ServerEntity
{

    /**
     * Unknown status
     */
    const UNKNOWN_STATUS = 0;

    /**
     * Blank status
     */
    const BLANK_STATUS = 1;

    /**
     * OK status
     */
    const OK_STATUS = 2;

    /**
     * Warning status
     */
    const WARNING_STATUS = 4;

    /**
     * Error status
     */
    const ERROR_STATUS = 8;

    /**
     * Server name
     * @var string
     */
    private $name;

    /**
     * Network status
     * @var int
     */
    private $networkStatus;

    /**
     * Checkin status
     * @var int
     */
    private $checkinStatus;

    /**
     * Agent status
     * @var int
     */
    private $agentStatus;

    /**
     * Has the server error
     * @var bool
     */
    private $hasError = false;

    /**
     * Holds the errors
     * @var ErrorEntity|ArrayCollection
     */
    private $errors;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    /**
     * Gets the server name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the server name
     *
     * @param string $name : Name of the server
     * @return ServerEntity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get network status
     *
     * @return int
     */
    public function getNetworkStatus()
    {
        return $this->networkStatus;
    }

    /**
     * Set network status
     *
     * @param int $networkStatus : Network status
     * @return ServerEntity
     */
    public function setNetworkStatus($networkStatus)
    {
        $this->networkStatus = $networkStatus;
        if ($networkStatus > self::OK_STATUS) {
            $this->hasError = true;
        }
        return $this;
    }

    /**
     * Get checkin status
     *
     * @return int
     */
    public function getCheckinStatus()
    {
        return $this->checkinStatus;
    }

    /**
     * Set checkin status
     *
     * @param int $checkinStatus : Checkin status
     * @return ServerEntity
     */
    public function setCheckinStatus($checkinStatus)
    {
        $this->checkinStatus = $checkinStatus;
        if ($checkinStatus > self::OK_STATUS) {
            $this->hasError = true;
        }
        return $this;
    }

    /**
     * Get agent status
     *
     * @return int
     */
    public function getAgentStatus()
    {
        return $this->agentStatus;
    }

    /**
     * Set agent status
     *
     * @param int $agentStatus : Agent status
     * @return ServerEntity
     */
    public function setAgentStatus($agentStatus)
    {
        $this->agentStatus = $agentStatus;
        if ($agentStatus > self::OK_STATUS) {
            $this->hasError = true;
        }
        return $this;
    }

    /**
     * Get all the errors
     *
     * @return ErrorEntity[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set errors
     *
     * @param null|array $errors : Array of ErrorEntity
     * @return ServerEntity
     */
    public function setErrors(array $errors = null)
    {
        $this->errors = new ArrayCollection();
        if ($errors !== null) {
            foreach ($errors as $e) {
                $this->addError($e);
            }
        }
        return $this;
    }

    /**
     * Add error
     *
     * @param ErrorEntity $error : Add a error
     * @return $this
     */
    public function addError(ErrorEntity $error)
    {
        $this->errors->add($error);
        return $this;
    }

    /**
     * Has the server an error
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }
}
