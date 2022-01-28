<?php
namespace Application\core;
/*
Classe responsável por obter da URL o controller, método (ação), os parâmetros e verificar a existência dos mesmos.
*/
class App {
  /*
  Os atributos padrão são o controller Login e método index
  Ao inicializar o objeto será executado o controller Login e seu método index
  É importante no Apache configurar o DocumentRoot para apontar par a pasta da aplicação
  */
  protected $controller = 'Login';
  protected $method = 'index';
  protected $page404 = false;
  protected $params = [];

  public function __construct() {
    $URI_ARRAY = $this->parseURI();
    $this->getControllerFromURI($URI_ARRAY);
    $this->getMethodFromURI($URI_ARRAY);
    $this->getParamsFromURI($URI_ARRAY);

    // Chama um método de uma classe passando os parâmetros
    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  /*
  Este método pega as informações da URI e retorna esses dados
  */
  private function parseURI() {
    $REQUEST_URI = explode('/', substr(filter_input(INPUT_SERVER, 'REQUEST_URI'), 1));
    return $REQUEST_URI;
  }

  /*
  Este método verifica se o array informado possui dados na posição 0 (controlador)
  caso positivo verifica se existe um arquivo com aquele nome no diretório Application/controllers
  e instancia um objeto contido no arquivo.
  */
  private function getControllerFromURI($url) {
    if ( !empty($url[0]) && isset($url[0]) ) {
      if ( file_exists('../Application/controllers/' . ucfirst($url[0])  . '.php') ) {
        $this->controller = ucfirst($url[0]);
      } else {
        $this->page404 = true;
      }
    }
    require '../Application/controllers/' . $this->controller . '.php';
    $this->controller = new $this->controller();
  }

  /*
  Este método verifica se o array informado possui dados na posição 1 (método)
  caso positivo verifica se o método existe naquele determinado controlador
  e atribui a variável $method da classe.
  */
  private function getMethodFromURI($url) {
    if ( !empty($url[1]) && isset($url[1]) ) {
      if ( method_exists($this->controller, $url[1]) && !$this->page404) {
        $this->method = $url[1];
      } else {
        /*
        Caso a classe ou o método informado não exista, o método noMethodFound do Controller é chamado.
        */
        $this->method = 'noMethodFound';
      }
    }
  }

  /*
  Este método verifica se o array informador possui a quantidade de elementos maior que 2
  ($url[0] é o controller e $url[1] o método/ação a executar), caso seja, é atrbuido
  a variável $params da classe um novo array  apartir da posição 2 do $url
  */
  private function getParamsFromURI($url) {
    if (count($url) > 2) {
      $this->params = array_slice($url, 2);
    }
  }
}
