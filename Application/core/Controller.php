<?php
namespace Application\core;

//use Application\models\Users;

/*
Esta classe é responsável por instanciar um model e chamar a view correta
passando os dados que serão usados.
*/
class Controller {
  /*
  Este método é responsável por chamar uma determinada view (página).
  */
  public function model($model) {
    require '../Application/models/' . $model . '.php';
    $classe = 'Application\\models\\' . $model;
    return new $classe();
  }

  /*
  Este método é responsável por chamar uuma determinada view (página).
  */
  public function view(string $view, $data = [])
  {
    require '../Application/views/' . $view . '.php';
  }

  /*
  Este método é herdado para todas as classes filhas que o chamaram quando
  o método ou classe informada pelo usuário nçao forem encontrados.
  */
  public function noMethodFound() {
    $this->view('noMethodFound');
  }
}
