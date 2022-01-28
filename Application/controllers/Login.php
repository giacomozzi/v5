<?php
use Application\core\Controller;

class Login extends Controller {
  protected $username;
  protected $password;
  protected $companyDB;
  protected $sessionID;

  public function index() {
    session_start();

    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
      //return true;
      $this->title = 'PeP';
      $this->view('home/index');
    }
    else {
      //return false;
      $this->view('login/index');
    }
  }

  public function doLogin() {
    session_start();

    if ( (isset($_POST['username']) && !empty($_POST['username'])) && (isset($_POST['password']) && !empty($_POST['password'])) )  {
      $params = [
        "UserName" => $_POST['username'],
        "Password" => $_POST['password'],
        "CompanyDB" => COMPANYDB
      ];

      // Efetua o login na service layer
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, HOST . ":" . PORT . "/b1s/v1/Login");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_VERBOSE, 1);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

      $response = curl_exec($curl);

      if ($response === false) {
        echo 'Curl error: ' . curl_error($curl);
      }

      $jsonObj = json_decode($response);
      /*
      var_dump($jsonObj);
      object(stdClass)#4 (4) {
        ["odata.metadata"]=> string(63) "https://bnvcpdssc001:50000/b1s/v1/$metadata#B1Sessions/@Element"
        ["SessionId"]=> string(36) "05c7173c-8030-11ec-8000-0050569d9f2b"
        ["Version"]=> string(6) "930220"
        ["SessionTimeout"]=> int(30) }
      */

      // Se o retorno for um erro retorna para a página de login
      /*
      if ($values = $jsonObj->error) {
        //echo "<script>alert('Seu usuário ou senha estão incorretos.'); window.location.href='index.php';</script>";
        $errorMessage = 'Usuário ou senha estão incorretos!';
        $this->view('error', $errorMessage);
      }
      */
      if (isset($jsonObj->error)) {
        $errorMessage = $jsonObj->error;
        $this->view('error', $errorMessage);
      }
      else {
        // Se o login ocorreu corretamente salva a session
        //$_SESSION['loggedIn'] = $username;
        // Código para sessão ativa
        //$_SESSION['Logged'] = 'true';

        $_SESSION['loggedIn'] = true;
        $json_response = json_decode($response);
        $_SESSION['sessionId'] = $json_response->{'SessionId'};
        $routeId = "";
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $string) use (&$routeId){
          $len = strlen($string);
          if(substr($string, 0, 10) == "Set-Cookie"){
            preg_match("/ROUTEID=(.+);/", $string, $match);
            if(count($match) == 2){
              $_SESSION['$routeId'] = $match[1];
            }
          }
          return $len;
        });
        //curl_exec($curl);
      }
    }
    else {
      $this->username = 'Lorem ipsum';
      $this->view('home/index');
    }
  }

  public function logoff() {
    session_start();
    session_destroy();
    $this->view('login/index');
  }
}
