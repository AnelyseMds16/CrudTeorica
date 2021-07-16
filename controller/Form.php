<?php

  class Form {
    public function __construct(){
      Transaction::open();
    }

    public function controller(){
      try{
        $conexao = Transaction::get();
        $crud = new Crud();
        $consult = $crud -> select("users");
        //print_r($consult);
        if(!$consult["erro"] || $consult["msg"] == "Nenhum registro encontrado!") {
          $form = new Template("view/form.html");
          $form -> set("id", "");
          $form -> set("name", "");
          $form -> set("email", "");
          $form -> set("gender", "");
          $retorno["msg"] = $form -> saida();
        }else{
          $msg = new Template("view/msg.html");
          $msg -> set("cor", $consult["erro"]?"danger":"success");
          $msg -> set("msg", $consult["msg"]);
          $retorno["msg"] = $msg -> saida();
        }
      }catch (Exception $e){
        $retorno["msg"] = "Ocorreu um erro!".$e -> getMessage();
        $retorno["erro"] = TRUE;
      }
      return $retorno;
    }

    public function salvar () {
      if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST ["gender"])){
        try {
          $conexao = Transaction::get();
          $crud = new Crud();
          $name = $conexao -> quote($_POST["name"]);
          $email = $conexao -> quote($_POST["email"]);
          $gender = $conexao -> quote($_POST["gender"]);
         /*  $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : NULL;
          $birthdate = ConverteData($birthdate); */

          if(empty($_POST["id"])){
            $retorno = $crud -> insert("users", "name, email, gender","{$name}, {$email}, {$gender}");
          }else{
            $id = $conexao -> quote($_POST["id"]);
            $retorno = $crud -> update("users", "name={$name}, email={$email}, gender={$gender}", "id={$id}");
          }
        }catch(Exception $e){
          $retorno["msg"] = "Ocorreu um erro!".$e -> getMessage();
          $retorno["erro"] = TRUE;
        }
      }else{
        $retorno["msg"] = "Preencha todos os campos!";
        $retorno["erro"] = TRUE;
      }
      $msg = new Template("view/msg.html");
      $msg -> set("cor", $retorno["erro"]?"danger":"success");
      $msg -> set("msg", $retorno["msg"]);
      $retorno["msg"] = $msg -> saida();
      return $retorno;
    }

    public function __destruct(){
      Transaction::close();
    }
  }
?>
