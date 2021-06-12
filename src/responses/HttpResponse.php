<?php 
namespace equilibrium\responses;

class HttpResponse extends AbstractHttpResponse {
    protected $body = '';
    
    /**
     * Set http body
     * @param string $v
     * @return \equilibrium\responses\HttpResponse
     */
    public function setBody($v) {
        $this->body = $v;
        return $this;
    }
    
    /**
     * Return http body
     * @return string
     */
    public function getBody() {
        return $this->body;
    }
    
    public function execute()
    {
        parent::execute();
        echo $this->body;
    }

}