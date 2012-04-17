<?php

class MC {
    private $mc;
    private $is_compressed = 0;
    // one hour
    private $expire = 3600;
    function __construct(){
        $this->mc = new Memcache;
        $this->mc->connect('localhost', 11211) or die ("Could not connect");
    }

    function set($key, $value){
        $this->mc->set(base64_encode($key), $value, $this->is_compressed, $this->expire );
    }

    function get($key){
        return $this->mc->get(base64_encode($key));
    }
}
?>
