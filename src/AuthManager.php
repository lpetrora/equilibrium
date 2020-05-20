<?php
	namespace equilibrium;
	use Equilibrium;
	use equilibrium\IUserHandler;
	
	class AuthManager
	{
		protected static $rules = null;
		public static $lastCallback = '';
		public static $lastAllow = false;
		
		static public function isAllowed($controller, $action, IUserHandler &$user)
		{
			//Si el archivo no es legible, entonces no funciona el módulo de autorización
			if (! is_readable(Equilibrium::$appPath.'/config/auth.php')) return true;
			if (static::$rules == null) static::$rules = include Equilibrium::$appPath.'/config/auth.php';
			
			$rules = static::$rules;
			$allow = isset($rules['global']['allow'])?$rules['global']['allow']:false;
			$callback = isset($rules['global']['callback'])?$rules['global']['callback']:false;
			if (!isset($rules['rules'])) $rules['rules'] = [];
			foreach ($rules['rules'] as $rule)
			{
				if (
						//Controlador
						in_array($rule['controller'], [$controller, '*'])
						
						//Accion
						&& ( in_array( '*', $rule['actions']) ||  in_array($action, $rule['actions']))
						
						//Roles
						&& ( 
								//Cualquier rol
								in_array( '*', $rule['roles'])
								//Visitante
								|| (in_array( '?', $rule['roles']) && $user->isGuest())
								//Autenticado
								|| (in_array( '@', $rule['roles']) && $user->isAuthenticated())
								//Cumple un rol específico
								|| self::someone_in_array($user->getRoles(), $rule['roles'])
								)
						)
				{
					$allow = isset ($rule['allow'])?$rule['allow']:$allow;
					$callback = isset ($rule['callback'])?$rule['callback']:$callback;
					break;
				}
			}
			static::$lastCallback = $callback;
			static::$lastAllow = $allow;
			return $allow;
		}
		
		protected static function someone_in_array($needles, $haystack)
		{
			foreach ($needles as $needle)
			{
				$res = in_array($needle, $haystack);
				if ($res) return true;
			}
			return false;
		}
	}