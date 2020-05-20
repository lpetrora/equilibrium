<?php 
namespace equilibrium\responses;

abstract class AbstratHttpResponse extends AbstractResponse {
    protected $code = 200;
    
    /**
     * set http code
     * @param integer $v
     * @return \equilibrium\responses\HttpResponse
     */
    public function setCode($v) {
        $this->code = $v;
        return $this;
    }
    
    /**
     * Return http code
     * @return number
     */
    public function getCode() {
        return $this->code;
    }
        
    public function execute()
    {
        http_response_code($this->code);
    }

}