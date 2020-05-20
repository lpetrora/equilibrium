<?php
	namespace equilibrium;
	
	use equilibrium\IUserHandler;
	use Equilibrium;
	
	class User implements IUserHandler
	{
		const GUEST = 0;
		const AUTHENTICATED = 1;
		
		protected $_roles = [];
		protected $_status = User::GUEST;
		protected $_username = '';
		protected $_userId = '';
		protected $_screenName = '';
		
		public function __construct()
		{
			$session = Equilibrium::EquilibriumSession();
			if (isset($session->user))
			{
				$user = $session->user;
				$this->_roles = $user['roles'];
				$this->_status = $user['status'];
				$this->_username = $user['username'];
				$this->_userId = $user['userId'];
				$this->_screenName = $user['screenName'];
			}
		}
		
		public function __destruct()
		{
			$session = Equilibrium::EquilibriumSession();
			$user = [
					'roles' => $this->_roles,
					'status' => $this->_status,
					'username' => $this->_username,
					'userId' => $this->_userId,
					'screenName' => $this->_screenName,
			];
			$session->user = $user;
				
		}
		
		/**
		 * Adds a role to the user
		 * @param string $role
		 * @return User
		 */
		public function addRole($role)
		{
			$this->_roles [$role] = $role;
			return $this;
		}
		
		/**
		 * Removes a role from the user
		 * @param string $role
		 * @return User
		 */
		public function removeRole($role)
		{
			unset ($this->_roles[$role]);
			return $this;
		}
		
		/**
		 * Check if user has a role
		 * @param string $role
		 * @return boolean
		 */
		public function hasRole($role)
		{
			return in_array($role, $this->_roles);
		}
		
		/**
		 * {@inheritDoc}
		 * @see \equilibrium\IUserHandler::getRoles()
		 */
		public function getRoles ()
		{
			return $this->_roles;
		}
		
		/**
		 * Clears user roles
		 * @return User
		 */
		public function clearRoles()
		{
			$this->_roles = [];
			return $this;
		}
		
		/**
		 * {@inheritDoc}
		 * @see \equilibrium\IUserHandler::isGuest()
		 */
		public function isGuest()
		{
			return ($this->_status == self::GUEST);	
		}
		
		/**
		 * {@inheritDoc}
		 * @see \equilibrium\IUserHandler::isAuthenticated()
		 */
		public function isAuthenticated()
		{
			return ($this->_status == self::AUTHENTICATED);
		}
		
		/**
		 * Sets user as guest. Clear roles, user id, user name and screen name
		 * @return User
		 */
		public function setGuest()
		{
			$this->clearRoles();
			$this->setScreenName('');
			$this->setUserId('');
			$this->setUsername('');
			$this->_status = User::GUEST;
			return $this;
		}
		
		/**
		 * Sets user as authenticated. Does not clear roles, user id, user name nor screen name
		 * @return User
		 */
		public function setAuthenticated()
		{
			$this->_status = User::AUTHENTICATED;
			return $this;
		}
		
		/**
		 * Sets user ID
		 * @param string $v
		 * @return User
		 */
		public function setUserId($v)
		{
			$this->_userId = $v;
			return $this;
		}
		
		/**
		 * Gets user ID
		 * @return string
		 */
		public function getUserId()
		{
			return $this->_userId;
		}
		
		
		/**
		 * Gets user name (login)
		 * @return string
		 */
		public function getUsername()
		{
			return $this->_username;
		}
		
		/**
		 * Sets user name (login)
		 * @param string $v
		 * @return User
		 */
		public function setUsername($v)
		{
			$this->_username = $v;
			return $this;
		}
		
		/**
		 * Gets user screen name
		 * @return string
		 */
		public function getScreenName()
		{
			return $this->_screenName;
		}
		
		/**
		 * Sets user screen name
		 * @param string $v
		 * @return User
		 */
		public function setScreenName($v)
		{
			$this->_screenName = $v;
			return $this;
		}
	}