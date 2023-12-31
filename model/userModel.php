<?php
require_once "ConDB.php";
class UserModel {
    static public function createUser($data) {
        $cantMail = self::getMail($data["use_mail"]);
        if ($cantMail == 0) {
            $query = "INSERT INTO users (use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status) 
                     VALUES (:use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, 1)";
            $statement = Connection::connection()->prepare($query);
            $statement->bindParam(":use_mail", $data["use_mail"], PDO::PARAM_STR);
            $statement->bindParam(":use_pss", $data["use_pss"], PDO::PARAM_STR);
            $statement->bindParam(":use_dateCreate", $data["use_dateCreate"], PDO::PARAM_STR);
            $statement->bindParam(":us_identifier", $data["us_identifier"], PDO::PARAM_STR);
            $statement->bindParam(":us_key", $data["us_key"], PDO::PARAM_STR);
            $message = $statement->execute() ? "ok" : implode(" ", $statement->errorInfo());
            $statement -> closeCursor();
            $statement = null;
            $query = "";
        } else {
            $message = "Usuario ya registrado";
        }
        return $message;
    }

    static private function getMail($mail) {
        $query = "SELECT use_mail FROM users WHERE use_mail = :mail";
        $statement = Connection::connection()->prepare($query);
        $statement->bindParam(":mail", $mail, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->rowCount();
        return $result;
    }

    static public function getUsers($id){
        $query = "";
        $id = is_numeric($id) ? $id : 0;
        $query = "SELECT use_id, use_mail, use_dateCreate FROM users";
        $query.=($id > 0) ? " WHERE users.use_id = '$id' AND " : "";
        $query.=($id > 0) ? " us_status='1';" : " WHERE us_status = '1';"; 
        $stament = Connection::connection()->prepare($query);
        $stament->execute();
        $result = $stament->fetchAll(PDO::FETCH_ASSOC);
        return $result;
     }
     
     static public function login($data){
        $user = $data['use_mail'];
        $pass = md5($data['use_pss']);
        if (!empty($user) && !empty($pass)){
            $query="SELECT use_id, us_identifier, us_key FROM users WHERE use_mail = '$user' and use_pss='$pass' and us_status='1'";
            $statement = Connection::connection()->prepare($query);
            $statement-> execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }else{
            return "No ha ingresado sus credenciales. Por favor, ingrese su correo y contraseña.";
        }
    }

    static public function getUserAuth($identifier,$key){
        $query="";
        $query="SELECT use_id FROM users WHERE us_identifier = '$identifier' and us_key='$key' and us_status='1'";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //update y delete

    static public function updateUser($id, $data) {
        $pss = md5($data['use_pss']);
        $query = "UPDATE users SET use_mail='".$data['use_mail']."',use_pss='".$pss."' WHERE use_id = ".$id.";";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $mensaje = array(
            "mensaje"=>"Usuario actualizado"
        );
        return $mensaje;
    }

    static private function getStatus($id){
        $query = "SELECT us_status FROM users WHERE use_id = '$id'";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['us_status'];
    }

    static public function deleteUser($id) {
        $status = self::getStatus($id);
        $updatedStatus = ($status == 0) ? 1 : 0;
        $query = "UPDATE users SET us_status='".$updatedStatus."' WHERE use_id = ".$id.";";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $mensaje = array(
            "mensaje"=>"Usuario desactivado"
        );
        return $mensaje;
    }
     
}
?>
