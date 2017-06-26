<?php



/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK
include_once(__DIR__ . '/../../config/Twilio/autoload.php');
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class UsersController
{

    public $_GB;
    public $Groups;

    public function __construct($_GB, $Groups)
    {
        $this->_GB = $_GB;
        $this->_Group = $Groups;
    }


    public function SignIn($phone, $countryName)
    {
        $code = rand(100000, 999999);
        $res = $this->createUser($phone, $code, $countryName);
        return $res;
    }


    public function sendMessageThroughTwilio($to_number, $code)
    {

        $otp_prefix = ':';
        $app_name = $this->_GB->getSettings('app_name');
        //Your authentication key
        $authKey = $this->_GB->getSettings('sms_authentication_key');

        //Sender ID,While
        $account_sid = $this->_GB->getSettings('account_sid');

        //Your message to send, Add URL encoding here.
        $message = "Hello,Welcome to $app_name. Your Verification code is $otp_prefix $code ";

        // Step 3: instantiate a new Twilio Rest Client
        $client = new Client($account_sid, $authKey);

        // the number that the sms will come from
        $from_number = $this->_GB->getSettings('phone_number');

        // boom, send the sms message
        $client->messages->create(
        // the number that is receiving the sms
            $to_number,
            array(
                'from' => $from_number,
                'body' => $message)
        );

    }


    public function createUser($phone, $code, $countryName)
    {
        $phone = $this->_GB->_DB->escapeString($phone);
        $countryName = $this->_GB->_DB->escapeString($countryName);

        $app_name = $this->_GB->getSettings('app_name');
        $smsVerification = $this->_GB->getSettings('sms_verification');
        if ($smsVerification == 1) {
            $smsVerification = true;
        } else {
            $smsVerification = false;
        }
        if (!$this->UserExist($phone, $countryName)) {
            // Generating API key
            $auth_token = $this->generateApiKey();


            $arrayData = array(
                'phone' => $phone,
                'auth_token' => $auth_token,
                'status' => 'Hey i am using ' . $app_name . ' and love it!',
                'status_date' => time(),
                'country' => $countryName,
                'is_activated' => 0,
                'has_backup' => 0,
                'backup_hash' => null

            );
            $result = $this->_GB->_DB->insert('users', $arrayData);
            $newUserID = $this->_GB->_DB->last_Id();
            $this->insertDefaultStatus($newUserID);
            // check if row inserted or not
            if ($result) {
                $IDResult = $this->_GB->_DB->select('users', '*', "  `phone` = '{$phone}'");
                if ($this->_GB->_DB->numRows($IDResult) > 0) {
                    $fetch = $this->_GB->_DB->fetchAssoc($IDResult);
                    $res = $this->createCode($fetch['id'], $code);
                    if ($res) {
                        // successfully inserted into database
                        if ($smsVerification == true) {
                            $this->sendMessageThroughTwilio($phone, $code);
                        }
                        $array = array(
                            'success' => true,
                            'message' => 'SMS request is initiated! You will be receiving it shortly.',
                            'mobile' => $phone,
                            'smsVerification' => $smsVerification,
                            'code' => $code,
                            'hasBackup' => $fetch['has_backup'] == 1 ? true : false
                        );
                        return $array;
                    } else {
                        // Failed to create user
                        $array = array(
                            'success' => false,
                            'message' => 'Sorry! Error occurred in registration.',
                            'mobile' => null,
                            'smsVerification' => $smsVerification,
                            'code' => null,
                            'hasBackup' => false
                        );
                        return $array;
                    }
                }

            } else {
                // Failed to create user
                $array = array(
                    'success' => false,
                    'message' => 'Sorry! Error occurred in registration.',
                    'mobile' => null,
                    'smsVerification' => $smsVerification,
                    'code' => null,
                    'hasBackup' => false
                );
                return $array;

            }
        } else if ($this->UserExist($phone, $countryName)) {
            // User with same phone already existed in the database

            // Generating API key
            $auth_token = $this->generateApiKey();

            $fields = "`auth_token` = '" . $auth_token . "'";
            $fields .= ",`is_activated` = '" . 0 . "'";
            $result = $this->_GB->_DB->update('users', $fields, "`phone` = {$phone}");

            // check if row inserted or not
            if ($result) {
                $IDResult = $this->_GB->_DB->select('users', '*', "  `phone` = '{$phone}'");
                if ($this->_GB->_DB->numRows($IDResult) > 0) {
                    $fetch = $this->_GB->_DB->fetchAssoc($IDResult);
                    $res = $this->createCode($fetch['id'], $code);
                    if ($res) {
                        // successfully inserted into database
                        // send sms
                        if ($smsVerification == true) {
                            $this->sendMessageThroughTwilio($phone, $code);
                        }
                        $array = array(
                            'success' => true,
                            'message' => 'SMS request is initiated! You will be receiving it shortly.',
                            'mobile' => $phone,
                            'smsVerification' => $smsVerification,
                            'code' => $code,
                            'hasBackup' => $fetch['has_backup'] == 1 ? true : false
                        );
                        return $array;

                    } else {
                        // Failed to create user
                        $array = array(
                            'success' => false,
                            'message' => 'Sorry! Error occurred in registration.',
                            'mobile' => null,
                            'smsVerification' => true,
                            'code' => null,
                            'hasBackup' => false
                        );
                        return $array;

                    }
                }

            } else {
                // Failed to create user
                $array = array(
                    'success' => false,
                    'message' => 'Sorry! Error occurred in registration.',
                    'mobile' => null,
                    'smsVerification' => $smsVerification,
                    'code' => null,
                    'hasBackup' => false
                );
                return $array;

            }
        } else {
            $array = array(
                'success' => false,
                'message' => 'Sorry! Mobile number is not valid or missing.',
                'mobile' => null,
                'smsVerification' => $smsVerification,
                'code' => null,
                'hasBackup' => false
            );
            return $array;
        }
    }

    /**
     * Function to  check if th user is already exist.
     * @param $phone
     * @param $country
     * @return bool
     * @internal param $UserName
     */
    public function UserExist($phone, $country)
    {
        $phone = $this->_GB->_DB->escapeString($phone);
        $country = $this->_GB->_DB->escapeString($country);

        $query = $this->_GB->_DB->select('users', '`id`', "`phone` LIKE '%{$phone}%' AND `country` LIKE '%{$country}%'");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
        $this->_GB->_DB->free();
    }

    public function initDeleteAccount($userID, $phone, $country)
    {
        $code = rand(100000, 999999);
        $res = $this->DeleteAccount($userID, $phone, $code, $country);
        return $res;
    }

    public function DeleteAccount($userID, $phone, $code, $country)
    {

        $phone = $this->_GB->_DB->escapeString($phone);
        $userID = $this->_GB->_DB->escapeString($userID);
        $smsVerification = $this->_GB->getSettings('sms_verification');
        if ($smsVerification == 1) {
            $smsVerification = true;
        } else {
            $smsVerification = false;
        }

        if ($this->UserExist($phone, $country)) {
            $res = $this->createCode($userID, $code);
            if ($res) {
                // successfully inserted into database
                if ($smsVerification == true) {
                    $this->sendMessageThroughTwilio($phone, $code);
                }
                $array = array(
                    'success' => true,
                    'message' => 'SMS request is initiated! You will be receiving it shortly.',
                    'mobile' => $phone,
                    'smsVerification' => $smsVerification,
                    'code' => $code
                );
                return $array;
            } else {
                // Failed to delete account
                $array = array(
                    'success' => false,
                    'message' => 'Sorry! Error occurred while deleting your account.',
                    'mobile' => null,
                    'smsVerification' => $smsVerification,
                    'code' => null
                );
                return $array;
            }
        } else {
            // Failed to create user
            $array = array(
                'success' => false,
                'message' => 'Sorry! Error occurred while deleteing your account.',
                'mobile' => null,
                'smsVerification' => $smsVerification,
                'code' => null
            );
            return $array;
        }
    }

    public function deleteAccountConfirmation($code)
    {
        $code = $this->_GB->_DB->escapeString($code);

        $query = ("SELECT  U.id,
                           U.username,
                           U.phone,
                           U.auth_token,
                           U.is_activated
                           FROM prefix_users U, prefix_sms_codes S
                           WHERE S.code = {$code}
                           AND S.UserID = U.id ");
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $queryGroup = " SELECT G.id , G.date AS CreatedDate,
                                  G.name AS GroupName ,
                                  G.image AS GroupImage
                          FROM prefix_users U,prefix_groups G,prefix_group_members GM
                          WHERE 
                          CASE
                          WHEN GM.userID = U.id
                          THEN GM.userID = {$fetch['id']} 
                          END
                          AND GM.groupID = G.id
                           AND U.is_activated = '1'
                           AND (GM.role = 'admin' OR GM.role = 'member')
                          GROUP BY G.id  ORDER BY G.id ASC ";
            $queryGroup = $this->_GB->_DB->MySQL_Query($queryGroup);

            if ($this->_GB->_DB->numRows($queryGroup) != 0) {
                while ($fetchGroup = $this->_GB->_DB->fetchAssoc($queryGroup)) {
                    $this->_Group->exitGroup($fetch['id'], $fetchGroup['id']);
                    $fields = "`Deleted` = '" . 1 . "'";
                    $this->_GB->_DB->update('group_members', $fields, " `groupID` = '{$fetchGroup['id']}' AND `userID` = {$fetch['id']}");
                }
            }
            $this->_GB->_DB->delete('sms_codes', " `UserID` =  {$fetch['id']} ");
            $is_activated = 0;

            $fields = "`is_activated` = '" . $is_activated . "'";
            $fields .= ",`image` = '" . null . "'";
            $delete = $this->_GB->_DB->update('users', $fields, "`id`= {$fetch['id']}");
            if ($delete) {
                $array = array(
                    'success' => true,
                    'message' => 'Your account has been deleted successfully'
                );
                $this->_GB->Json($array);
            } else {
                $array = array(
                    'success' => false,
                    'message' => 'Failed to delete your account'
                );
                $this->_GB->Json($array);
            }
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to delete your account'
            );
            $this->_GB->Json($array);
        }

    }


    public function insertDefaultStatus($userID)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $app_name = $this->_GB->getSettings('app_name');

        $arrayStatus = array("Only Emergency calls", "Busy", "At work", "in a meeting", "Available", "Playing football", "Hey i am using $app_name enjoy it");
        $lastElement = end($arrayStatus);
        foreach ($arrayStatus as $status) {
            if ($status == $lastElement) {
                $addDefaultStatus = array(
                    'status' => $status,
                    'userID' => $userID,
                    'current' => 1
                );
            } else {
                $addDefaultStatus = array(
                    'status' => $status,
                    'userID' => $userID,
                    'current' => 0
                );
            }
            $this->_GB->_DB->insert('status', $addDefaultStatus);
        }
    }

    /**
     * Function to  check if th user is already exist.
     * @param $phone
     * @return bool
     * @internal param $UserName
     */
    public function UserLinked($phone)
    {
        $phone = $this->_GB->_DB->escapeString($phone);

        $query = $this->_GB->_DB->select('users', '`id`', "`phone` LIKE '%{$phone}%'  ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to  check if th user is activate.
     * @param $phone
     * @return bool
     * @internal param $UserName
     */
    public function UserActivate($phone)
    {
        $phone = $this->_GB->_DB->escapeString($phone);

        $activated = 1;
        $query = $this->_GB->_DB->select('users', '`id`', "`phone` LIKE '%{$phone}%' AND `is_activated` = '{$activated}' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function ResendCode($phone)
    {
        $phone = $this->_GB->_DB->escapeString($phone);
        $code = rand(100000, 999999);

        $IDResult = $this->_GB->_DB->select('users', '*', "  `phone` = '{$phone}'");
        if ($this->_GB->_DB->numRows($IDResult) > 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($IDResult);
            $res = $this->createCode($fetch['id'], $code);
            if ($res) {
                // successfully inserted into database
                // send sms
                $this->sendMessageThroughTwilio($phone, $code);
                $array = array(
                    'success' => true,
                    'message' => 'SMS request is Resending! You will be receiving it shortly.',
                );
                $this->_GB->Json($array);
            } else {

                $array = array(
                    'success' => false,
                    'message' => 'Sorry! Error occurred .',
                );
                $this->_GB->Json($array);
            }

        }


    }

    public function createCode($UserID, $code)
    {
        $UserID = $this->_GB->_DB->escapeString($UserID);

        // delete the old otp if exists
        $this->_GB->_DB->delete('sms_codes', "`UserID`= {$UserID}");
        $array = array(
            'UserID' => $UserID,
            'code' => $code,
            'status' => 0
        );
        $result = $this->_GB->_DB->insert('sms_codes', $array);
        return $result;
    }


    public function activateUser($code)
    {
        $code = $this->_GB->_DB->escapeString($code);

        $query = (" SELECT  U.id,
                           U.username,
                           U.phone,
                           U.auth_token,
                           U.is_activated,
                           U.has_backup
                           FROM prefix_users U, prefix_sms_codes S
                           WHERE S.code = {$code}
                           AND S.UserID = U.id ");
        $query = $this->_GB->_DB->MySQL_Query($query);
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $is_activated = 1;
            $this->_GB->_DB->update('users', "`is_activated` = '{$is_activated}' ", "`id`='{$fetch['id']}'");
            $this->_GB->_DB->update('sms_codes', "`status` = '{$is_activated}' ", "`UserID`='{$fetch['id']}'");

            $array = array(
                'success' => true,
                'message' => 'Your account has been created successfully.',
                'userID' => $fetch['id'],
                'token' => $fetch['auth_token'],
                'hasBackup' => $fetch['has_backup'] == 1 ? true : false
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to activate your account try again or resend sms to get new code.',
                'userID' => null,
                'token' => null,
                'hasBackup' => false
            );
            $this->_GB->Json($array);
        }

    }


    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }


    public function comparePhoneNumbers($array)
    {
        $contactsModelList = $array['contactsModelList'];
        $resultFinal = array();
        for ($i = 0; $i < count($contactsModelList); $i++) {
            $phone = $this->_GB->_DB->escapeString($contactsModelList[$i]['phone']);
            $phoneTmp = $this->_GB->_DB->escapeString($contactsModelList[$i]['phoneTmp']);
            $username = $this->_GB->_DB->escapeString($contactsModelList[$i]['username']);
            $image = $this->_GB->_DB->escapeString($contactsModelList[$i]['image']);
            $contactID = $this->_GB->_DB->escapeString($contactsModelList[$i]['contactID']);

            if ($this->UserLinked($phoneTmp)) {
                $result = $this->_GB->_DB->select('users', '*', "  `phone` LIKE '%{$phoneTmp}%'");
                if ($this->_GB->_DB->numRows($result) != 0) {
                    $fetch = $this->_GB->_DB->fetchAssoc($result);
                    if ($this->UserActivate($fetch['phone'])) {
                        $fetch['contactID'] = $contactID;
                        $fetch['username'] = (empty($fetch['username'])) ? $username : $fetch['username'];
                        $fetch['Linked'] = true;
                        $fetch['Activate'] = true;
                        $fetch['Exist'] = true;
                        $fetch['phone'] = (empty($fetch['phone'])) ? $phone : $phone;
                        $fetch['image'] = (empty($fetch['image'])) ? null : $fetch['image'];
                        $fetch['status_date'] = (empty($fetch['status_date'])) ? null : $this->_GB->Date($fetch['status_date']);
                        unset ($fetch['auth_token']);
                        $resultFinal [] = $fetch;
                    } else {
                        $fetch['contactID'] = $contactID;
                        $fetch['username'] = (empty($fetch['username'])) ? $username : $fetch['username'];
                        $fetch['Linked'] = true;
                        $fetch['Activate'] = false;
                        $fetch['Exist'] = true;
                        $fetch['phone'] = (empty($fetch['phone'])) ? $phone : $phone;
                        $fetch['image'] = (empty($fetch['image'])) ? null : $fetch['image'];
                        $fetch['status_date'] = (empty($fetch['status_date'])) ? null : $this->_GB->Date($fetch['status_date']);
                        unset ($fetch['auth_token']);
                        $resultFinal [] = $fetch;
                    }
                }

/*
                if (!$this->ContactExist($phone)) {
                    $arrayData = array(
                        'phone' => $phone,
                        'registered' => 1,
                        'username' => $username
                    );
                    $this->_GB->_DB->insert('contacts', $arrayData);
                } else {
                    $unRegistered = 0;
                    $fields = "`registered` = '" . 1 . "'";
                    $this->_GB->_DB->update('contacts', $fields, "`phone`  LIKE '%{$phone}%' AND `registered` = {$unRegistered}");
                }*/

            } else {
                $fetch = array(
                    'id' => $contactID,
                    'contactID' => $contactID,
                    'Linked' => false,
                    'Activate' => false,
                    'Exist' => true,
                    'status' => $phone,
                    'phone' => $phone,
                    'image' => $image,
                    'username' => $username);
                $resultFinal [] = $fetch;

               /* if (!$this->ContactExist($phone)) {
                    $arrayData = array(
                        'phone' => $phone,
                        'registered' => 0,
                        'username' => $username
                    );
                    $this->_GB->_DB->insert('contacts', $arrayData);
                }*/
            }
        }
        $this->_GB->Json($resultFinal);

    }

    /**
     * Function to  check if th user is already exist.
     * @param $phone
     * @return bool
     * @internal param $UserName
     */
 /*   public function ContactExist($phone)
    {
        $phone = $this->_GB->_DB->escapeString($phone);
        //  $registered = 0 ;
        $query = $this->_GB->_DB->select('contacts', '`id`', "`phone` LIKE '%{$phone}%' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }*/

    public function getSessionToken($auth_token)
    {
        $auth_token = $this->_GB->_DB->escapeString($auth_token);
        $query = $this->_GB->_DB->select('users', 'auth_token', "`auth_token`= '{$auth_token}'  ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserIdByToken($auth_token)
    {
        $auth_token = $this->_GB->_DB->escapeString($auth_token);
        $query = $this->_GB->_DB->select('users', 'id', "`auth_token`= '{$auth_token}'");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            return $fetch['id'];
        } else {
            return 0;
        }
    }

    public function getContactInfo($userID)
    {
        $userID = $this->_GB->_DB->escapeString($userID);

        $query = $this->_GB->_DB->select('users', '*', "`id` = '{$userID}' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
            $fetch['username'] = (empty($fetch['username'])) ? null : $fetch['username'];
            $fetch['phone'] = (empty($fetch['phone'])) ? null : $fetch['phone'];
            $fetch['image'] = (empty($fetch['image'])) ? null : $fetch['image'];
            $fetch['Linked'] = $this->UserLinked($fetch['phone']);
            $fetch['Activate'] = $this->UserActivate($fetch['phone']);
            $fetch['status'] = (empty($fetch['status'])) ? null : $fetch['status'];
            $fetch['status_date'] = (empty($fetch['status_date'])) ? null : $this->_GB->Date($fetch['status_date']);
            unset($fetch['auth_token'], $fetch['is_activated'], $fetch['created_at'], $fetch['country']);
            $this->_GB->Json($fetch);

        } else {
            $this->_GB->Json(null);
        }
    }

    public function getRecipientInfo($userID)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $query = $this->_GB->_DB->select('users', '*', "`id` = '{$userID}' ");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $fetch['id'] = (empty($fetch['id'])) ? null : $fetch['id'];
            $fetch['username'] = (empty($fetch['username'])) ? null : $fetch['username'];
            $fetch['phone'] = (empty($fetch['phone'])) ? null : $fetch['phone'];
            $fetch['image'] = (empty($fetch['image'])) ? null : $fetch['image'];
            $fetch['Linked'] = $this->UserLinked($fetch['phone']);
            $fetch['Activate'] = $this->UserActivate($fetch['phone']);
            $fetch['status'] = (empty($fetch['status'])) ? null : $fetch['status'];
            $fetch['status_date'] = (empty($fetch['status_date'])) ? null : $this->_GB->Date($fetch['status_date']);
            unset($fetch['auth_token'], $fetch['is_activated'], $fetch['created_at'], $fetch['country']);
            return $fetch;

        } else {
            return null;
        }
    }

    public function getStatus($query)
    {
        if ($this->_GB->_DB->numRows($query) != 0) {
            $status = array();
            while ($fetch = $this->_GB->_DB->fetchAssoc($query)) {

                $fetch['currentStatusID'] = is_numeric($fetch['currentStatus']) ? null : $fetch['currentStatusID'];
                $fetch['currentStatus'] = is_numeric($fetch['currentStatus']) ? null : $fetch['currentStatus'];
                unset($fetch['userid'], $fetch['username'], $fetch['image'], $fetch['phone'], $fetch['auth_token'], $fetch['is_activated'], $fetch['created_at']);
                $status[] = $fetch;

            }
            $this->_GB->Json($status);
        } else {
            $this->_GB->Json(null);
        }
    }

    public function editStatus($newStatus, $userID, $statusID)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $statusID = $this->_GB->_DB->escapeString($statusID);
        $newStatus = $this->_GB->_DB->escapeString($newStatus);

        $fields = "`status` = '" . $newStatus . "'";
        $result = $this->_GB->_DB->update('status', $fields, "`id` = {$statusID} AND `userID` = {$userID}");


        // check if row inserted or not
        if ($result) {

            $fields .= ",`status_date` = '" . time() . "'";
            $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");
            $array = array(
                'success' => true,
                'message' => 'Status is updated successfully '
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to update status '
            );
            $this->_GB->Json($array);
        }
    }

    public function existStatus($userID, $status)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $status = $this->_GB->_DB->escapeString($status);
        $query = $this->_GB->_DB->select('status', '*', "`status` = '{$status}' AND `userID` = {$userID}");
        if ($this->_GB->_DB->numRows($query) != 0) {
            return true;
        } else {
            return false;
        }

    }

    public function insertStatus($userID, $status)
    {
        $status = $this->_GB->_DB->escapeString($status);
        $userID = $this->_GB->_DB->escapeString($userID);


        if (strpos($status, '\'') !== false) {
            $status = str_replace('\'', "\\'", $status);
        }

        if ($this->existStatus($userID, $status)) {
            $array = array(
                'success' => true,
                'message' => 'Status already exist '
            );
            $this->_GB->Json($array);
        } else {
            $fields = "`current` = '" . 0 . "'";
            $this->_GB->_DB->update('status', $fields, "`userID` = {$userID}");

            $addNewStatus = array(
                'status' => $status,
                'userID' => $userID,
                'current' => 1
            );

            $insert = $this->_GB->_DB->insert('status', $addNewStatus);


            if ($insert) {
                $fields = "`status` = '" . $status . "'";
                $fields .= ",`status_date` = '" . time() . "'";
                $result = $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");

                // check if row inserted or not
                if ($result) {
                    $array = array(
                        'success' => true,
                        'message' => 'Status is updated successfully '
                    );
                    $this->_GB->Json($array);
                } else {
                    $array = array(
                        'success' => false,
                        'message' => 'Failed to update status '
                    );
                    $this->_GB->Json($array);
                }
            } else {
                $array = array(
                    'success' => false,
                    'message' => 'Failed to insert status '
                );
                $this->_GB->Json($array);
            }
        }
    }

    public function updateStatus($userID, $statusID)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $statusID = $this->_GB->_DB->escapeString($statusID);


        $status = null;
        $query = $this->_GB->_DB->select('status', '*', "`id` = {$statusID} AND `userID` = {$userID}");
        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $status = $this->_GB->_DB->escapeString($fetch['status']);
            $field1 = "`current` = '" . 0 . "'";
            $field2 = "`current` = '" . 1 . "'";
            $this->_GB->_DB->update('status', $field1, "`userID` = {$userID}");
            $this->_GB->_DB->update('status', $field2, "`id` = {$statusID} AND `userID` = {$userID}");

            if (strpos($status, '\'') !== false) {
                $status = str_replace('\'', "\\'", $status);
            }
            $fields = "`status` = '" . $status . "'";
            $fields .= ",`status_date` = '" . time() . "'";
            $result = $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");

            // check if row inserted or not
            if ($result) {
                $array = array(
                    'success' => true,
                    'message' => 'Status is updated successfully '
                );
                $this->_GB->Json($array);
            } else {
                $array = array(
                    'success' => false,
                    'message' => 'Failed to update status '
                );
                $this->_GB->Json($array);
            }
        }
    }

    public function DeleteStatus($userID, $status)
    {
        $userID = $this->_GB->_DB->escapeString($userID);
        $status = $this->_GB->_DB->escapeString($status);

        $delete = $this->_GB->_DB->delete('status', "`status`= '{$status}' AND `userID`= {$userID}");
        if ($delete) {
            $array = array(
                'success' => true,
                'message' => 'Status has been deleted successfully '
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to delete status '
            );
            $this->_GB->Json($array);
        }
    }


    public function DeleteAllStatus($delete)
    {
        if ($delete) {
            $array = array(
                'success' => true,
                'message' => 'All Status have been deleted successfully '
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to delete status '
            );
            $this->_GB->Json($array);
        }
    }


    public function editName($name, $userID)
    {
        $name = $this->_GB->_DB->escapeString($name);
        $userID = $this->_GB->_DB->escapeString($userID);

        $fields = "`username` = '" . $name . "'";
        $result = $this->_GB->_DB->update('users', $fields, "`id` = {$userID} ");

        // check if row inserted or not
        if ($result) {
            $array = array(
                'success' => true,
                'message' => 'Name has been updated successfully '
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to update name '
            );
            $this->_GB->Json($array);
        }
    }

    public function updateBackup($backupHash, $userID)
    {
        $hasBackup = 1;
        $fields = "`backup_hash` = '" . $backupHash . "'";
        $fields .= ",`has_backup` = '" . $hasBackup . "'";
        $result = $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");
        if ($result) {
            $array = array(
                'success' => true,
                'message' => 'the backup stored successfully '
            );
            $this->_GB->Json($array);
        } else {
            $array = array(
                'success' => false,
                'message' => 'Failed to store the backup '
            );
            $this->_GB->Json($array);
        }
    }


    public function getBackupUrl($userID)
    {
        $query = $this->_GB->_DB->select('users', 'backup_hash', "`id` = {$userID}");

        if ($this->_GB->_DB->numRows($query) != 0) {
            $fetch = $this->_GB->_DB->fetchAssoc($query);
            $resultHash = $fetch['backup_hash'];
            if ($resultHash != null) {
                $hasBackup = 0;
                $fields = "`backup_hash` = '" . null . "'";
                $fields .= ",`has_backup` = '" . $hasBackup . "'";
                $this->_GB->_DB->update('users', $fields, "`id` = {$userID}");

                $array = array(
                    'success' => true,
                    'message' => $resultHash
                );
                $this->_GB->Json($array);


            } else {
                $array = array(
                    'success' => false,
                    'message' => null
                );
                $this->_GB->Json($array);
            }
        }

    }
    /****************************
     * functions for admins
     ****************************/

   
    /**
     * Function for admin login
     * @param $username
     * @param $password
     */
    public
    function adminLogin($username, $password)
    {

        $username = trim($this->_GB->_DB->escapeString($username));
        $password = trim($this->_GB->_DB->escapeString($password));
        $adminPassword = md5($password);
        $query = $this->_GB->_DB->select('admins', '*', "`username` = '{$username}' AND `password` = '{$adminPassword}'");
        $fetch = $this->_GB->_DB->fetchAssoc($query);
        if (empty($username) || empty($password)) {
            echo $this->_GB->ErrorDisplay('All fields are required');
        } else if ($this->_GB->_DB->numRows($query) <= 0) {
            echo $this->_GB->ErrorDisplay('Login failed please try again later');
        } else {
            $this->_GB->setSession('admin', $fetch['id']);
            $this->_GB->setSession('adminName', $fetch['username']);
            header("Refresh: 1; url=index.php");
            echo $this->_GB->ErrorDisplay('Logged in successfully.', 'yes');
        }
        $this->_GB->_DB->free($query);
    }

}
