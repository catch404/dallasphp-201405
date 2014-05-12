dallasphp-201405
================

code from the may dallasphp meeting. a lightweight and simple code demo of how
you can easily throw a cache into your application before throwing money at
your database cluster. it implements a barebones api access to two layers of
cache, local appcache and access to memcached, handled automatically.

the cache/lib/cache.php file is the Cache class you can drop into your app
if you want to give it a run. edit the static $MemcacheConfig array in that
class to change or add servers to the pool. remove all the entries (but leave
the array) if you only want to use appcache.

call Cache::Create() early on in the app once during app config to initalise
the caches. then you can call Cache::Get() and Cache::Set() all you want.

the cache/cache-test.php file is just a small cli script i used to test that
the demo was working as i expected.
