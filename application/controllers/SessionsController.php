<?php


class SessionsController
{

    public $_GB;

    function __construct($_GB)
    {
        $this->_GB = $_GB;
    }


    /**
     * Function to  get the user id
     * @param $userID
     * @param $token
     * @return int
     */
    public
    function getSessionToken($userID, $token)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $token = $this->_GB->_DB->escapeString($token);
        
        $query = $this->_GB->_DB->select('sessions', 'token', "`token`= '{$token} ' AND `userID`= '{$userID}' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to set sessions
     * @param $key
     * @param $value
     */
    function SetSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Function to get sessions
     * @param $key
     * @return bool
     */
    function GetSession($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    /**
     * Function to unset session
     * @param $key
     * @return bool
     */
    function UnsetSession($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        } else {
            return false;
        }
    }

}