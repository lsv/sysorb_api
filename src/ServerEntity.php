<?php
namespace Lsv\SysorbApi;

use Doctrine\Common\Collections\ArrayCollection;

class ServerEntity
{

    const UNKNOWN_STATUS = 0;
    const BLANK_STATUS = 1;
    const OK_STATUS = 2;
    const WARNING_STATUS = 4;
    const ERROR_STATUS = 8;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $networkStatus;

    /**
     * @var int
     */
    private $checkinStatus;

    /**
     * @var int
     */
    private $agentStatus;

    /**
     * @var ErrorEntity|ArrayCollection
     */
    private $errors;

    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ServerEntity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getNetworkStatus()
    {
        return $this->networkStatus;
    }

    /**
     * @param int $networkStatus
     * @return ServerEntity
     */
    public function setNetworkStatus($networkStatus)
    {
        $this->networkStatus = $networkStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getCheckinStatus()
    {
        return $this->checkinStatus;
    }

    /**
     * @param int $checkinStatus
     * @return ServerEntity
     */
    public function setCheckinStatus($checkinStatus)
    {
        $this->checkinStatus = $checkinStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getAgentStatus()
    {
        return $this->agentStatus;
    }

    /**
     * @param int $agentStatus
     * @return ServerEntity
     */
    public function setAgentStatus($agentStatus)
    {
        $this->agentStatus = $agentStatus;
        return $this;
    }

    /**
     * @return ErrorEntity[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
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
     * @param ErrorEntity $error
     * @return $this
     */
    public function addError(ErrorEntity $error)
    {
        $this->errors->add($error);
        return $this;
    }

}
