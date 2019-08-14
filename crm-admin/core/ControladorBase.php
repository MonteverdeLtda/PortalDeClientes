<?php 
/* *******************************
 *
 * Developer by FelipheGomez
 *
 * Git: https://github.com/Feliphegomez/crm-crud-api-php
 * *******************************
 */


class ControladorBase {
	private $thisClassName;
	public $post;
	public $get;
	public $put;
	public $folders;
	public $protocol;
	public $host;
	public $port;
	public $path;
	private $request;
	private $apiCore;
	private $response;
	public $api;
	private $this_file;
	public $sections;
	public $status;
	public $enlace_actual;
	private $session;
	public $userData;
	public $modules;
	private $theme;
	private $template;
	private $vista_actual;
	public $datos;
	private $thisModule;
 
    public function __construct() {
		require_once(folder_admin . '/config/database.php');
		require_once(folder_admin . '/core/ui.php');
		require_once 'Conectar.php';
		require_once('api.php');
        require_once 'EntidadBase.php';
        require_once 'ModeloBase.php';
        require_once 'MenuBase.php';
		$this->modules = $this->getModules();
		$this->post = $_POST;
		$this->get = $_GET;
		$this->userData = new stdClass();
		$this->sections = array();
		$this->enlace_actual = $_SERVER['REQUEST_URI'];
		
        //Incluir todos los modelos del sistema
        foreach(glob(folder_admin . "/model/*.php") as $file){ require_once $file; };
        //Incluir todos los modelos de los modulos
		$directorio = opendir(folder_content . "/modules"); //ruta actual
		while ($nombreModulo = readdir($directorio)) 
		{
			//verificamos si es o no un directorio
			if (is_dir(folder_content . "/modules/" . $nombreModulo))
			{
				foreach(glob(folder_content . "/modules/{$nombreModulo}/models/*.php") as $file){
					require_once $file;
				};
			}
		}
		
		$this->setServer();
		$this->folders = new stdClass();
		$this->folders->principal = folder_principal;
		$this->folders->admin = folder_admin;
		$this->theme = TEMA_DEFECTO;
		
		$request_headers = getallheaders();
		if((
			isset($request_headers['X-CORE']) && $request_headers['X-CORE'] == 'api') 
			|| (isset($this->sections[0]) && $this->sections[0] == 'api') 
			|| (isset($this->sections[0]) && $this->sections[0] == 'openapi') 
			|| (isset($this->sections[0]) && $this->sections[0] == 'login') 
			// || (isset($this->sections[0]) && $this->sections[0] == 'login') 
			// || (isset($this->sections[0]) && $this->sections[0] == 'logout') 
			|| (isset($this->sections[0]) && $this->sections[0] == 'records') 
			|| (isset($_GET['core']) && $_GET['core'] == 'api')
		){
			global $response;
			$reponse = ResponseUtils::output($response, false);
			if(!isset($reponse->code) || $reponse->code !== 1011){
				echo $reponse;
				exit();
			}
		}
		$this->status = (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] > 0) ? 'connected' : 'disconnect';
		
		// Agregar el login si no esta `connected`
		if(REDIRECT_LOGIN === true){
			if($this->status === 'disconnect'){
				if(!isset($this->get['controller']) || $this->get['controller'] != 'Login'){
					header("Location: /index.php?controller=Login");
				}
			} else if ($this->status === 'login_fail'){
				if(!isset($this->get['controller']) || $this->get['controller'] != 'Login'){
					header("Location: /index.php?controller=Login&action=error&message=".base64_encode($this->api->message));
				}
				echo "Fail Login";
				exit();
			}
		}
		$this->session = $this->validateSession();
		$this->userData = (isset($this->session['user'])) ? $this->getLoadProfile($this->session['user']['id']) : $this->getProfileDefault();
		
		# Crear Template
		require_once "TemplateBase.php";
		$themeFileBase = folder_content . "/themes/{$this->theme}/global/template.php";
		if($this->validateFileExist($themeFileBase) == true){
			require_once $themeFileBase;
			$template_name = "Template".ucwords(strtolower($this->theme));
			$template = new $template_name();
			$this->template = $template->getTemplate();
			
		}else{
			echo "Template no encontrado en el tema. {$themeFileBase}";
			exit();
		}
		
		$this->thisClassName = $this->getClassName();
		$this->thisModule = $this->getThisModule();
		
		# incluir solvemedia
	}
	
	public function index(){
		$this->viewSystemInTemplate(
			"index", array(
				"title" => "Titulo",
				"subtitle" => "",
				"description" => "Los datos estan incorrectos intenta nuevamente."
			)
		);
	}
		
	public static function validateSession(){
		
		#echo json_encode($_SESSION);
		#exit();
		return (isset($_SESSION) && isset($_SESSION['user']) && isset($_SESSION['user']['id'])) ? $_SESSION : array();
		
		/*
		if (isset($_SESSION) && isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])){
			$userid = (int) $_SESSION['user']['id'];
		} else if (isset($_SESSION) && isset($_SESSION['user']) && is_object($_SESSION['user']) && isset($_SESSION['user']['id'])) {
			$userid = (int) $_SESSION['user']['id'];
		}else{
			$userid = 0;
		}
		$user = new Usuario();
		$user->getById($userid);
		if(isset($user->id) && $user->id > 0 && isset($user->username)){
			$_SESSION['user'] = array();
			foreach($user as $k=>$v){
				$_SESSION['user'][$k] = $v;
			}
		}
		$r = (isset($_SESSION) && isset($_SESSION['user']) && isset($_SESSION['user']['id'])) ? $_SESSION : array();
		return $r;
		*/
	}
	
	public static function getClassName(){
		return str_replace(array(
			"controller",
			"Controller",
		), array(
			"",
			"",
		), get_called_class());
	}
	
	public static function getModules() : array {
		$Mydir = folder_content . '/modules/';
		$dirs = array();
		foreach(glob($Mydir.'*', GLOB_ONLYDIR) as $dir) {
			$dir = str_replace($Mydir, '', $dir);
			$dirs[] = $dir;
		}
		return $dirs;
	}
	
	public function tableDebug($data){
		$keys = array();
		$values = array();
		$html = "<div class=\"container-debug table table-responsive\" style=\"width:100%\"><table class=\"table table-responsive\">";
		foreach($data as $k => $v){
			$html .= "<tr>";
			
			$html .= "<th>{$k}</th>";
			if(is_array($v) || is_object($v)){
				$html .= "<td>{$this->tableDebug($v)}</td>";
			}else{
				$html .= "<td>".($v)."</td>";
			}
			
			$html .= "</tr>";
		}
		$html .= "</table></div>";
		
		return $html;
	}
	
	public static function getPermissions(){
		$user = ControladorBase::validateSession();
		if(isset($user['user']['permissions']->id) && (int) $user['user']['permissions']->id > 0){
			$permisoID = (int) $user['user']['permissions']->id;
			$perm = new Permiso();
			$perm->getById($permisoID);
		} else if(isset($user['user']['permissions']) && (int) $user['user']['permissions'] > 0){
			$permisoID = (int) $user['user']['permissions'];
			$perm = new Permiso();
			$perm->getById($permisoID);
		} else {
			$perm = new stdClass();
		}
		return ($perm);
	}
	
	public static function validatePermission($module, $action){
		$t = ControladorBase::getPermissions();
		$p = (isset($t->data)) ? $t->data : new stdClass();
		
		if(isset($p->{"isAdmin"}) && $p->{"isAdmin"} == true){
			return true;
		} else {
			if($action == null){
				return (isset($p->{$module})) ? true : false;
			}else{
				return (isset($p->{$module}->{$action}) && $p->{$module}->{$action} == true) ? true : false;
			}
		}
	}
	
	public static function isUser(){
		$array = ControladorBase::validateSession();
		if(isset($array['user']['id']) && $array['user']['id'] > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public static function getLoadProfile($userid) {
		$dataUser = ControladorBase::getProfileDefault();
		$userid = (int) $userid;
		$user = new Usuario();
		$user->getById($userid);
		
		if(isset($user->id) && isset($user->username)){
			$dataUser->userID = (int) $user->id;
			$dataUser->username = $user->username;
			$dataUser->userInfo = $user;
			return ($dataUser);
		}
		return $user;
	}
	
	public static function getProfileDefault() {
		$a = new stdClass();
		$a->userID = 0;
		$a->username = 0;
		$a->userInfo = 0;
		return $a;
	}
	
	function getPath() : string {
		$a = null;
        if (!isset($_SERVER['PHP_SELF'])) {
            $_SERVER['PHP_SELF'] = '/';
        }
		$a = $_SERVER['PHP_SELF'];
		return $a;
	}
	
    function setServer() {
        $this->protocol = @$_SERVER['HTTP_X_FORWARDED_PROTO'] ?: @$_SERVER['REQUEST_SCHEME'] ?: ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https" : "http");
        $this->port = @intval($_SERVER['HTTP_X_FORWARDED_PORT']) ?: @intval($_SERVER["SERVER_PORT"]) ?: (($this->protocol === 'https') ? 443 : 80);
        $this->host = @explode(":", $_SERVER['HTTP_HOST'])[0] ?: @$_SERVER['SERVER_NAME'] ?: @$_SERVER['SERVER_ADDR'];
        $this->port = ($this->protocol === 'https' && $this->port === 443) || ($this->protocol === 'http' && $this->port === 80) ? '' : ':' . $this->port;
        $this->path = @trim(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/openapi')), '/');
        $this->this_file = substr($_SERVER['SCRIPT_NAME'],1);
		
		$this->path = $this->getPath();
		
		$sections = explode('/', $this->path);
		foreach($sections as $section){
			if($section != '' && $section != null && $section != $this->this_file){
				$this->sections[] = $section;
			}
		}
		
		return true;
    }
	
	public static function getSections() {
		$nombreModulo = str_replace(array(
			"controller",
			"Controller",
		), array(
			"",
			"",
		), get_called_class());
		
		$urlInfoModulo = folder_content . "/modules/{$nombreModulo}/{$nombreModulo}.json";
		if(ControladorBase::validateFileExist($urlInfoModulo)){
			$infoModulo = json_decode(@file_get_contents($urlInfoModulo));
		}else{
			$infoModulo = json_decode(json_encode(array(
				'name' => "Modulo {$nombreModulo}",
				"isActive" => false
			)));
		}
		
		return (!isset($infoModulo->sections)) ? array() : $infoModulo->sections;
		
		
	}
	
	public static function defaultInfoModule() {
		
	}
     
    //Plugins y funcionalidades
    public function viewSystem($vista,$datos) {
		/*
		* Este método lo que hace es recibir los datos del controlador en forma de array
		* los recorre y crea una variable dinámica con el indice asociativo y le da el
		* valor que contiene dicha posición del array, luego carga los helpers para las
		* vistas y carga la vista que le llega como parámetro. En resumen un método para
		* renderizar vistas.
		*/
		foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
        }
        require_once folder_admin . '/core/AyudaVistas.php';
        $helper=new AyudaVistas();
		if(isset($vista)){
			$urlVista = folder_admin . '/view/'.$vista.'View.php';
			if(@file_exists($urlVista)){ require_once $urlVista; } 
			else { echo ("<br> Vista: {{$vista}} No encontrada. URL: {$urlVista}<br>"); };
		}
    }
	
    public function view($vista,$datos){
		foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
        }
        require_once folder_admin . '/core/AyudaVistas.php';
        $helper=new AyudaVistas();
		if(isset($vista)){
			$urlVista = folder_content . '/modules/' . $this->getClassName() . '/view/'.$vista.'View.php';
			if(@file_exists($urlVista)){ require_once $urlVista; } 
			else { echo ("<br> Vista: {{$vista}} No encontrada. URL: {$urlVista}<br>"); };
		}
    }
	
	public function viewInTemplate($vista,$datos){
		/*
		* Este método lo que hace es recibir los datos del controlador en forma de array
		* los recorre y crea una variable dinámica con el indice asociativo y le da el
		* valor que contiene dicha posición del array, luego carga los helpers para las
		* vistas y carga la vista que le llega como parámetro con los archivos conjuntos de la plantilla. En resumen un método para
		* renderizar vistas pero con el tema.
		*/
        foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
        }
		$this->vista_actual = $vista;
		$this->datos = $datos;
		echo "<!DOCTYPE html>\n";
		$this->templateToCode($this->template->getBaseCode());
    }
	
	public function viewSystemInTemplate($vista,$datos){
		/*
		* Este método lo que hace es recibir los datos del controlador en forma de array
		* los recorre y crea una variable dinámica con el indice asociativo y le da el
		* valor que contiene dicha posición del array, luego carga los helpers para las
		* vistas y carga la vista que le llega como parámetro con los archivos conjuntos de la plantilla. En resumen un método para
		* renderizar vistas pero con el tema.
		*/
        foreach ($datos as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
        }
		$this->vista_actual = $vista;
		$this->datos = $datos;
		echo "<!DOCTYPE html>\n";
		$this->templateSystemToCode($this->template->getCodeBase());
    }
	
	public function templateToCode($codeTemplate){
		$vista = $this->vista_actual;
		$datos = $this->datos;
		foreach ($datos as $id_assoc => $valor) {
			${$id_assoc}=$valor;
		}
		require_once folder_admin . '/core/AyudaVistas.php';
		$helper=new AyudaVistas();
		if(is_array($codeTemplate)){
			foreach($codeTemplate as $i => $prms){
				if(isset($prms->name)){
					$clss = (isset($prms->class) && $prms->class != "") ? " class=\"{$prms->class}\"" : '';
					if(isset($prms->tag)){
						echo str_repeat("\t", ($i+1))."<{$prms->tag}{$clss}> \n";
					}
					echo str_repeat("\t", ($i+1))."<!-- // ↑ Inicio {$prms->name} --> \n";
					if(isset($prms->function)){
						if(method_exists($this->template, $prms->function)){
							$this->template->{$prms->function}();
						}else{
							echo str_repeat("\t", ($i+1))."function => {$prms->function}::Result - NO encontrada.\n";
						}
						if($prms->function === 'getBody'){
							if(isset($vista)){
								// Se agrega detect en carpeta del tema
								$urlVista = folder_content . "/themes/{$this->theme}/view/{$vista}View.php";
								if(@file_exists($urlVista)){ require_once $urlVista; } else {
									// echo ("{$this->getClassName()}<br> Vista: {{$vista}} No encontrada. {$urlVista}<br>");
									$urlVista = folder_admin . '/view/'.$vista.'View.php';
									if(@file_exists($urlVista)){ require_once $urlVista; } 
									else { echo ("<br> Vista: {{$vista}} No encontrada.<br>"); };
								}
								//  se elimina vista del modulo
								/*$urlVista = folder_content . '/modules/' . $this->getClassName() . '/view/'.$vista.'View.php';
								if(@file_exists($urlVista)){ require_once $urlVista; } 
								else {
									
								};*/
							}
						}
					}
					if(isset($prms->includes) && is_array($prms->includes)){
						$this->templateToCode($prms->includes);
					}
					echo @str_repeat("\t", ($i+1))."<!-- // ↓ Fin {$prms->name} -->\n";
					if(isset($prms->tag)){
						echo @str_repeat("\t", ($i+1))."</{$prms->tag}>\n";
					}
				}
			}
		}
		
		#	return $html;
	}
	
	public function templateSystemToCode($codeTemplate){
		$vista = $this->vista_actual;
		$datos = $this->datos;
		foreach ($datos as $id_assoc => $valor) {
			${$id_assoc}=$valor;
		}
		require_once folder_admin . '/core/AyudaVistas.php';
		$helper=new AyudaVistas();
		if(is_array($codeTemplate)){
			foreach($codeTemplate as $i => $prms){
				if(isset($prms->name)){
					$clss = (isset($prms->class) && $prms->class != "") ? " class=\"{$prms->class}\"" : '';
					if(isset($prms->tag)){
						echo str_repeat("\t", ($i+1))."<{$prms->tag}{$clss}> \n";
					}
					echo str_repeat("\t", ($i+1))."<!-- // ↑ Inicio {$prms->name} --> \n";
					if(isset($prms->function)){
						if(method_exists($this->template, $prms->function)){
							$this->template->{$prms->function}();
						}else{
							echo str_repeat("\t", ($i+1))."function => {$prms->function}::Result - NO encontrada.\n";
						}
						if($prms->function === 'getBody'){
							if(isset($vista)){
								$urlVista = folder_admin . '/view/'.$vista.'View.php';
								if(@file_exists($urlVista)){ require_once $urlVista; } 
								else { echo ("<br> Vista: {{$vista}} No encontrada.<br>"); };
							}
						}
					}
					if(isset($prms->includes) && is_array($prms->includes)){
						$this->templateSystemToCode($prms->includes);
					}
					echo str_repeat("\t", ($i+1))."<!-- // ↓ Fin {$prms->name} -->\n";
					if(isset($prms->tag)){
						echo str_repeat("\t", ($i+1))."</{$prms->tag}>\n";
					}
				}
			}
		}
		
		#	return $html;
	}
	
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO){
        header("Location:index.php?controller=".$controlador."&action=".$accion);
    }
	
	public static function returnParamsUrl($z){
		$a = '';
		if(is_object($z) || is_array($z)){
			foreach($z as $k => $v){
				$a .= $k . '=' . ControladorBase::returnParamsUrl($v);
			}
		} else {
			$a .= $z;
		}
		return $a;
	}
     
    public function linkUrl($controlador=CONTROLADOR_DEFECTO, $accion=ACCION_DEFECTO, $params=null){
		$urlParams = ControladorBase::returnParamsUrl($params);
        return ("index.php?controller={$controlador}&action={$accion}&{$urlParams}");
    }
     
    public function json(){
        echo json_encode($this);
    }
    //Métodos para los controladores
	
	public function formLogout() : string {
		return "<form method=\"post\" action=\"/logout\"><button type=\"submit\">Cerar sesion</button></form>";
	}
	
	public static function validateFileExist($fileUrl) {
		return (!file_exists($fileUrl)) ? false : true;
	}
	
	public static function validateDirExist($dirUrl) {
		return (is_dir($dirUrl)) ? true : false;
	}
	
	/* FUNCIONES PARA LOS MODULOS */
	public static function getThisModule() {
		$nombreModulo = str_replace(array(
			"controller",
			"Controller",
		), array(
			"",
			"",
		), get_called_class());
		
		$urlInfoModulo = folder_content . "/modules/{$nombreModulo}/{$nombreModulo}.json";
		if(ControladorBase::validateFileExist($urlInfoModulo)){
			return json_decode(@file_get_contents($urlInfoModulo));
		}else{
			return json_decode(json_encode(array(
				'name' => "Modulo {$nombreModulo}",
				"isActive" => false,
			)));
		}
	}
	
	public function getCopyright(){
		return base64_decode("RGVzYXJyb2xsYWRvciBwb3IgRmVsaXBoZUdvbWV6");
	}
	
}