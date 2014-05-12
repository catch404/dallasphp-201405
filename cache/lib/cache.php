<?php

class Cache {
/*//
this is a super simple singleton style global cache manager. its purpose is to
demonstrate how easy it is to build a quick cache interface to bolt onto an
existing application in a hurry. it is lightweight and simple.
//*/

	///////////////////////////////////////////////////////////////////////////
	// singleton handling /////////////////////////////////////////////////////

	static $Main = null;
	/*//
	@type object
	a singleton instance holder for global cache access.
	//*/

	static function Create() {
	/*//
	@return bool
	create a the global instance. call this early in your application during
	configuration or there abouts.
	//*/

		if(!static::HasValidInstance()) {
			static::$Main = new static;
			return true;
		}

		return false;
	}

	static function HasValidInstance($throw=false) {
	/*//
	@argv bool ThrowException
	@return bool
	check if there is a valid singleton instance in the static property
	Main already. if the argument is true, it will throw an exception stating
	this. else it will return a boolean.
	//*/

		$result = true;

		if(!is_object(static::$Main)) $result = false;
		if(!is_a(static::$Main,get_called_class())) $result = false;

		if($throw && !$result) throw new Exception('Use Cache::Create() once early to initialise the cache.');
		else return $result;
	}

	///////////////////////////////////////////////////////////////////////////
	// cache access ///////////////////////////////////////////////////////////

	protected $Appcache;
	/*//
	@type array
	the storage of the local appcache. data is just stored as an assoc array
	as is.
	//*/

	protected $Memcache;
	/*//
	@type object
	the connection resource to the Memcached pool of servers.
	//*/

	static $MemcacheConfig = [
		'localhost:11211'
	];
	/*//
	@type array
	the list of servers for memcached to pool together.
	//*/

	////////////////
	////////////////

	public function __construct() {
		$this->Init_Appcache();
		$this->Init_Memcache();
		return;
	}

	protected function Init_Appcache() {
		$this->Appcache = [];
		return;
	}

	protected function Init_Memcache() {
		$this->Memcache = new Memcache;

		foreach(static::$MemcacheConfig as $hostnport) {
			list($host,$port) = explode(':',$hostnport);
			$this->Memcache->addServer($host,$port);
		}

		return;
	}

	////////////////
	////////////////

	static function Get($key) {
	/*//
	@argv string Key
	@return mixed or null
	get data from the cache stored under the specified key. returns hard null
	if cache was not found.
	//*/

		static::HasValidInstance(true);

		$result = static::$Main->Get_Appcache($key);
		if($result !== null) return $result;

		$result = static::$Main->Get_Memcache($key);
		if($result !== null) return $result;

		return;
	}

	protected function Get_Appcache($key) {
		if(array_key_exists($key,$this->Appcache)) return $this->Appcache[$key];
		else return null;
	}

	protected function Get_Memcache($key) {
		$result = $this->Memcache->get($key);

		if($result !== false) return $result;
		else return null;
	}

	////////////////
	////////////////

	static function Set($key,$value) {
	/*//
	@argv string Key, mixed Data
	store data in the cache under the specified key. will overwrite if there is
	already data under that key.
	//*/

		static::HasValidInstance(true);
		static::$Main->Set_Appcache($key,$value);
		static::$Main->Set_Memcache($key,$value);
		return;
	}

	protected function Set_Appcache($key,$value) {
		$this->Appcache[$key] = $value;
		return;
	}

	protected function Set_Memcache($key,$value) {
		$this->Memcache->set($key,$value);
		return;
	}

	////////////////
	////////////////

	static function Drop($key) {
	/*//
	@argv string Key
	inform the caches to invalidate the data under the specified key.
	//*/

		static::HasValidInstance(true);
		static::$Main->Drop_Appcache($key);
		static::$Main->Drop_Memcache($key);
		return;
	}

	protected function Drop_Appcache($key) {
		if(array_key_exists($key,$this->Appcache))
		unset($this->Appcache[$key]);

		return;

	}

	protected function Drop_Memcache($key) {
		$this->Memcache->delete($key);
		return;
	}

	////////////////
	////////////////

	static function Flush() {
	/*//
	inform all the caches to drop all the things.
	//*/

		static::HasValidInstance(true);
		static::$Main->Flush_Appcache();
		static::$Main->Flush_Memcache();
		return;
	}

	protected function Flush_Appcache() {
		$this->Appcache = [];
		return;
	}

	protected function Flush_Memcache() {
		$this->Memcache->flush();
		return;
	}

}
