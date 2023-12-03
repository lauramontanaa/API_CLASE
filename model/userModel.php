<?php
require_once "ConDB.php";
class UserModel {
    static public function createUser($data) {
        $cantMail = self::getMail($data["use_mail"]);
        if ($cantMail == 0) {
            $query = "INSERT INTO users (use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status) 
                     VALUES (:use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, 1)";
            $cryptedPss = md5($data['use_pss']);
            $statement = Connection::connection()->prepare($query);
            $statement->bindParam(":use_mail", $data["use_mail"], PDO::PARAM_STR);
            $statement->bindParam(":use_pss", $cryptedPss, PDO::PARAM_STR);
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
        $stament = Conection::connection()->prepare($query);
        $stament->execute();
        $result = $stament->fetchAll(PDO::FETCH_ASSOC);
        return $result;
     }
     
    static public function login($data){
        $user = $data['use_mail'];
        $pss = md5($data['use_pss']);
        if (!empty($user) && !empty($pss)){
            $query="SELECT us_identifier, us_key, use_id FROM users WHERE use_mail = '$user' and use_pss='$pss' and us_status='1'";
            return $query;
            $statement = Connection::connection()->prepare($query);
            $statement-> execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } else {
            $mensaje = array(
                "COD" => "001",
                "MENSAJE" => ("Error en credenciales")
            );
            return $mensaje;
        }
        $query="";
    }

    static public function getUserAuth(){
        $query="";
        $query = "SELECT us_identifier, us_key FROM users WHERE us_status = '1';";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //update y delete

    static public function updateUser($id, $data) {
        $pss = md5($data['use_pss']);
        $query = "UPDATE users SET use_email='".$data['use_email']."',use_pss='".$pss."' WHERE use_id = ".$id.";";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $mensaje = array(
            "mensaje"=>"Usuario actualizado"
        );
        return $mensaje;
    }

    static private function getStatus($id){
        $query = "SELECT use_status FROM users WHERE use_id = '$id'";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['use_status'];
    }

    static public function deleteUser($id) {
        $status = self::getStatus($id);
        $updatedStatus = ($status == 0) ? 1 : 0;
        $query = "UPDATE users SET use_status='".$updatedStatus."' WHERE use_id = ".$id.";";
        $statement = Connection::connection()->prepare($query);
        $statement->execute();
        $mensaje = array(
            "mensaje"=>"Usuario Eliminado"
        );
        return $mensaje;
    }
     
}
?>
