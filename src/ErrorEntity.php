<?php
/**
 * This file is part of the Lsv\SysorbApi
 */
namespace Lsv\SysorbApi;

/**
 * Error entity, holds the error for a server
 *
 * @author Martin Aarhof <martin.aarhof@gmail.com>
 */
class ErrorEntity implements \Serializable
{

    /**
     * Error code
     * @var string
     */
    private $code;

    /**
     * Error message
     * @var string
     */
    private $message;

    /**
     * Error status
     * @var string
     */
    private $status;

    /**
     * Construct
     *
     * @param string|null $code : Set the error code
     * @param string|null $message : Set the error message
     * @param string|null $status : Set the serror status
     */
    public function __construct($code = null, $message = null, $status = null)
    {
        $this->setCode($code)
            ->setMessage($message)
            ->setStatus($status)
        ;
    }

    /**
     * Get the error code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the error code
     *
     * @param string $code : The error code
     * @return ErrorEntity
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the error message
     *
     * @param string $message : Error message
     * @return ErrorEntity
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get the error status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the serror status
     *
     * @param string $status : Error status
     * @return ErrorEntity
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Serialize the object
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return \serialize([
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'status' => $this->getStatus()
        ]);
    }

    /**
     * Unserialize the object
     *
     * @param string $serialized The string representation of the object.
     */
    public function unserialize($serialized)
    {
        $unserialized = \unserialize($serialized);
        $this->setCode($unserialized['code'])
            ->setMessage($unserialized['message'])
            ->setStatus($unserialized['status'])
        ;
    }
}
