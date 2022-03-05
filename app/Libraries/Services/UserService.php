<?php

namespace App\Libraries\Services;

use App\Libraries\User;
use App\Models\BlackListModel;
use App\Models\UserModel;
use App\Libraries\Services\CategoryService;

$categoryService = new CategoryService();

class UserService
{

    private $userModel;
    private $confirmUserModel;
    private $blackListModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->confirmUserModel = model(ConfirmUserModel::class);
        $this->blackListModel = model(BlackListModel::class);
    }

    private function convertToArrayUser($mysqlObject)
    {
        $convertArrayObject = $mysqlObject->getResultArray();
        $userArray = array();
        $arrayIndex = 0;
        foreach ($convertArrayObject as $object) {
            $userArray[$arrayIndex] = new User(
                $object['user_id'],
                $object['user_name'],
                $object['user_lastname'],
                $object['user_email'],
                $object['user_login'],
                $object['user_password'],
                $object['user_permission'],
            );
            $arrayIndex++;
        }

        return $userArray;
    }

    private function convertToArray($mysqlObject)
    {
        return $mysqlObject->getResultArray();
    }

    public function getSingleUser($userEmail)
    {
        return $this->convertToArrayUser($this->userModel->getSingleUser($userEmail));
    }

    public function getUserPassword($userEmail)
    {
        return $this->getSingleUser($userEmail)[0]->user_password;
    }

    public function registerCheck($userEmail)
    {
        if(isset($this->getSingleUser($userEmail)[0])){
            return true;
        }
        else{
            return false;
        }
    }

    public function getAllConfirmUserStatus()
    {
        return $this->convertToArray($this->confirmUserModel->getAllConfirmUserStatus());
    }

    public function getSingleConfirmUserStatus($userEmail)
    {
        $confirmationStatus = $this->convertToArray($this->confirmUserModel->getSingleConfirmStatus($userEmail));
        if (!isset($confirmationStatus[0]['confirmation_status'])) {
            return false;
        } else if ($confirmationStatus[0]['confirmation_status'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function putNewConfirmStatus($userEmail, $status = 0){
        $this->confirmUserModel->putNewConfirmStatus($userEmail, $status);
    }

    public function getAllBlackListStatus()
    {
        return $this->convertToArray($this->blackListModel->getAllBlackListStatus());
    }

    public function getSingleBlackListStatus($userEmail)
    {
        $blackListStatus = $this->convertToArray($this->blackListModel->getSingleBlackListStatus($userEmail));
        if ($blackListStatus[0]['blacklist_id'] == 0) {
            return false;
        } elseif ($blackListStatus[0]['blacklist_id'] == 1) {
            return true;
        }
    }

    public function putUser($userName, $userLastname, $userEmail, $userLogin, $userPassword){
        $this->userModel->putUser($userName, $userLastname, $userEmail, $userLogin, $userPassword);
    }
}
