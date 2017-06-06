<?php

	use InoOicClient\Flow\Basic;
	use InoOicClient\Http;
	use InoOicClient\Client;
	use InoOicClient\Oic\Token;

	class ClaveUnicaController extends BaseController{

       protected $authConfig;
	   
	   public function __construct(){
	       $this->authConfig = array(
	           'client_info' => array(
	               'client_id' => $_ENV['claveunica_client_id'],
	               'redirect_uri' => URL::to('/claveunica/validar'),
	               'authorization_endpoint' => 'https://www.claveunica.gob.cl/openid/authorize',
	               'token_endpoint' => 'https://www.claveunica.gob.cl/openid/token',
	               'user_info_endpoint' => 'https://www.claveunica.gob.cl/openid/userinfo',
	               'authentication_info' => array(
	                   'method' => 'client_secret_post',
	                   'params' => array(
	                       'client_secret' => $_ENV['claveunica_secret_id']
	                   )
	               )
	           )
	       );
	   }
		
		public function autenticar(){
			$flow = new Basic($this->authConfig);
		    if (! isset($_GET['redirect'])) {
		        try {
		            $uri = $flow->getAuthorizationRequestUri('openid nombre');
		            return Redirect::to($uri);
		        } catch (\Exception $e) {
		            printf("Exception during authorization URI creation: [%s] %s", get_class($e), $e->getMessage());
		        }
		    } else {
		        try {
		            $userInfo = $flow->process();
		        } catch (\Exception $e) {
		            printf("Exception during user authentication: [%s] %s", get_class($e), $e->getMessage());
		        }
		    }

		}

		public function validar(){
			$flow = new Basic($this->authConfig);
	        $token = $flow->getAccessToken($_GET['code']);
	        $infoPersonal = $flow->getUserInfo($token);
	        $rut = $infoPersonal['RUT'];
	        $rut = str_replace(".", "", $rut);
	        $user = Usuario::where('rut',$rut)->first();
	        if($user){
	        	Auth::login($user);
	        	return Redirect::to(URL::to('controles'));	
	        }else{
	        	return Redirect::to(URL::to('/'));
	        }
		}

		public function logout(){
			if(\Auth::check()){
				\Auth::logout();
			}
			Session::flush();
			return \Redirect::to('/');
		}

		public function login(){
			if(\Auth::check()){
				return \Redirect::to('controles');
			}else{
				return \Redirect::to('/');
			}
		}
}


?>