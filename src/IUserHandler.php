<?php
	namespace equilibrium;
	
	interface IUserHandler
	{
		/**
		 * Returns true if the current user is guest
		 * @return boolean
		 */
		public function isGuest();
		
		/**
		 * Returns true if the current user is authenticated
		 * @return boolean
		 */
		public function isAuthenticated();
		
		/**
		 * Returns an array with user roles
		 * @return array
		 */
		public function getRoles ();
	}