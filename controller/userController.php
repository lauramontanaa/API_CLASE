<?php

class UserController {
    private $_method; 
    private $_complement; 
    private $_data; 

    function __construct($method, $complement, $data) {
        $this->_method = $method;
        $this->_complement = $complement;
        $this->_data = $data !== 0 ? $data : "";
    }

    public function index() {
        switch ($this->_method) {
            case "GET":
                if($this->_complement == 0){
                    $user = UserModel::getUsers(0);
                    $json = $user;
                    echo json_encode($json, true);
                    return;
                }else{
                    $user = UserModel::getUsers($this->_complement);
                    $json = $user;
                    echo json_encode($json, true);
                    return;
                }
            case "POST":
                $createUser = UserModel::createUser($this->generateSalting());
                $json = array(
                    "response" => $createUser
                );
                echo json_encode($json, true);
                return;
            case "PUT":
                $createUser = UserModel::updateUser($this->_complement,$this->_data);
                $json = array(
                    "response: "=>$createUser
                );
                echo json_encode($json,true);
                return;
            case "DELETE":
                $createUser = UserModel::deleteUser($this->_complement);
                $json = array(
                    "response: "=>$createUser
                );
                echo json_encode($json,true);
                return;
        }
    }

    private function generateSalting(){
        $trimmed_data="";
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/';
        if (preg_match($pattern, $this->_data['use_pss'])) {
            if(($this->_data !="") || (!empty($this->_data))){
                $trimmed_data = array_map('trim', $this->_data);
                $trimmed_data['use_pss'] = md5($trimmed_data['use_pss']);
                $identifier = str_replace("$", "ue56", crypt($trimmed_data['use_mail'], 'ue56'));
                $key = str_replace("$", "ue23", crypt($trimmed_data['use_pss'], 'ue23'));
                $trimmed_data['us_identifier'] = $identifier;
                $trimmed_data['us_key'] = $key;
                return $trimmed_data;
            }
        } else {
            $message = "La contraseña debe contener una mayúscula, una minúscula y un caracter especial";
            die($message);
        }
    }
}

?>
