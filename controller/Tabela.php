<?php
class Tabela
{
  public function __construct()
  {
    Transaction::open();
  }
  public function controller()
  {
    try {
      Transaction::get();
      $crud = new Crud();
      $consult = $crud -> select("users");
      if (!$consult["erro"]) {
        $tabela = new Template("view/tabela.html");
        $cons = $consult["msg"];
        if(count($cons) > 0){
          $tabela -> set("linha", $cons);
        }
        $retorno["msg"] = $tabela -> saida(); 
      }else{
        $retorno[" msg"] = $consult[" msg"];
        $msg = new Template("view/msg.html");
        $msg -> set("cor", $retorno["erro"]?"danger":"success");
        $msg -> set("msg", $retorno["msg"]);
        $retorno["msg"] = $msg -> saida();
      } 
    }catch(Exception $e) {
      $retorno["msg"] = "Ocorreu um erro! " . $e->getMessage();
      $retorno["erro"] = TRUE;
      Transaction::rollback();
    }
    return $retorno;
  }
  public function remover()
  {
    if(isset($_GET["id"])){
      try {
        $conexao = Transaction::get();
        $id = $conexao->quote($_GET["id"]);
        $crud = new Crud();
        $retorno = $crud->delete("users","id={$id}");

      }catch (Exception $e) {
        $retorno["msg"] = "Ocorreu um erro! " . $e->getMessage();
        $retorno["erro"] = TRUE;
        Transaction::rollback();
      }
    } else {
      $retorno["msg"] = "Faltando parâmetro! ";
      $retorno["erro"] = TRUE;
    }
    $msg = new Template(" view / msg. html ") ;
    $msg -> set("cor", $retorno["erro"]?"danger":"success");
    $msg -> set("msg", $retorno["msg"]);
    $retorno["msg"] = $msg -> saida();
    return $retorno;
  }
  public function editar()
  {
    if (isset($_GET["id"])) {
      try {
        $conexao = Transaction::get();
        $id = $conexao->quote($_GET["id"]);
        $crud = new Crud();
        $consult = $crud -> select("users", "*", "id ={$id}");
        $cons = $consult["msg"];
        if(count($cons)==1) {
          $form = new Template("view/form.html");
          $form -> set("id" , $cons[0]["id"]);
          foreach ($cons[0] as $cod => $c){
            $form->set($cod, $c);
          }
          $retorno["msg"] = $form->saida();
        }else{
          $msg = new Template("view/msg.html");
          $msg -> set("cor" , "danger");
          $msg -> set("msg" , $retorno["msg"]);
          $retorno["msg"] = $msg -> saida();
        }
        $retorno["erro"] = $consult["erro"];
      } catch (Exception $e) {
        $retorno["msg"] = "Ocorreu um erro! " . $e->getMessage();
        $retorno["erro"] = TRUE;
        Transaction::rollback();
      }
    }else{
      $msg = new Template("view/msg.html");
      $msg -> set("cor", "danger");
      $msg -> set("msg", "Faltando parâmetro!");
      $retorno["msg"] = $msg -> saida();
    }
    return $retorno;
  }
  public function __destruct()
  {
    Transaction::close();
  }
}