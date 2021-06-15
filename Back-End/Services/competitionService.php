<?php
    include_once("../Config/db.php");
    include_once('../middleware/genericMethod.php');
    include_once('../Config/constant.php');
    include_once('../Helpers/Security/Securitypass.php');
    class Result{}
    class Competition extends genericMethod{
        private $id;
        private $idUser;
        private $idSubject;
        private $description;
        private $creationDate;
        private $finalDate;
        private $state;
        private $idPostion;

        public function __construct ($id, $idUser,$idSubject, $description, $creationDate, $finalDate, $state, $idPosition){
            #region initial
            $this->id = $id;
            $this->idUser = $idUser;
            $this->idSubject = $idSubject;
            $this->description = $description;
            $this->creationDate = $creationDate;
            $this->finalDate = $finalDate;
            $this->state = $state;
            $this->idPostion = $idPosition;
            #endregion
        }

        //method     
        public static function Get(){             
            $cnn = Connection();
            $query = "select *, sub.name, sub.img from competitions comp inner join subjects sub on comp.idSubject = sub.id where comp.state = 1 ORDER BY comp.id DESC";
            $users = mysqli_query($cnn,$query);
            $userList = [];

            while($reg = mysqli_fetch_array($users,MYSQLI_ASSOC)){
                $userList[] = $reg;
            }

            $finalList = json_encode($userList);
            echo $finalList;
        }
        public static function GetById($_id){
            $cnn = Connection();
            $user = mysqli_query($cnn,"select * from competitions where state = 1 and id =".$_id);
            $usersingle = "";

            while($reg = mysqli_fetch_array($user,MYSQLI_ASSOC)){
                $usersingle = $reg;
            }
            $single = json_encode($usersingle);
            echo $single;
        }
        public function Post(){
            try{
                $cnn = Connection();

                $this->ValidateParameter('idSubject', $this->idSubject, INTEGER);
                $this->checkNonerepeat('competitions', 'idSubject', $this->idSubject, "Ya tiene un concurso vigente para esa materia");
                
                $result = mysqli_query($cnn,"insert into competitions (idSubject, description, state, idUser, idPostion, creationDate, finalDate) 
                values($this->idSubject , '$this->description' , 1 , $this->idUser, $this->idPostion, $this->creationDate, $this->finalDate)");
                if($result){
                    $this->ReturnReponse(SUCCESS_RESPONSE, "El concurso fue guardado con exito.");
                }
                else{
                    $this->ReturnReponse(ERROR_RESPONSE, "El concurso no fue guardado con exito.");
                }
            }
            catch(\Exception $e){
                $this->throwError(REQUEST_NOT_VALID, $e->getMessage()); 
            }  
        }

        public function Put($id){
            $cnn = Connection();
            $this->ValidateParameter('materia', $this->idSubject, INTEGER); 
           
            $result = mysqli_query($cnn,"update competitions set idSubject = $this->idSubject,
                                    description='$this->description', idUser= $this->idUser
                                    where id =".$id);
            if($result){
                $this->ReturnReponse(SUCCESS_RESPONSE, "El concurso fue modificado con exito.");
            }
            else{
                $this->ReturnReponse(ERROR_RESPONSE, "El concurso no fue modificado con exito.");
            }    
        }
        public static function Delete($id){ 
            $cnn = Connection();   
            $response = new Result();
            $result = mysqli_query($cnn,"update competitions set state = 2 where id =".$id);

            if($result){
                $response->result = 'Ok';
                $response->message="El concurso fue eliminado con exito";
            }
            else{
                $response->result = 'Error';
                $response->message="El concurso no fue eliminado con exito";
            }
            echo json_encode($response);
        }         
    }
?>