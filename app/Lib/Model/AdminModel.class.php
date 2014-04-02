<?php

class AdminModel extends Model {
    public function adminLogin($userId, $pwdEncode) {
        return $this->where("id = $userId AND password = '$pwdEncode'")->find();
    }
}