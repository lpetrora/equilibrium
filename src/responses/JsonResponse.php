<?php 
namespace equilibrium\responses;

class HttpResponse extends AbstratHttpResponse {
    protected $payload = '';
    
    /**
     * Set json payload
     * @param mixed $v
     * @return \equilibrium\responses\HttpResponse
     */
    public function setPayload($v) {
        $this->payload = $v;
        return $this;
    }
    
    /**
     * Return json payload
     * @return string
     */
    public function getPayload() {
        return $this->payload;
    }
    
    public function execute()
    {
        parent::execute();
        header('Content-type: application/json');
        echo json_encode($this->body);
    }
}