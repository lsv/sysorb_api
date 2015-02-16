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
class ErrorEntity
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
}
