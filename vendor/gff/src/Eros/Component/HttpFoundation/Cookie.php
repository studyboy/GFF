<?php namespace Eros\Component\HttpFoundation;

class Cookie {
	
	protected $name;
	protected $value;
	protected $domain;
	protected $path;
	protected $expire;
	protected $secure;
	protected $httpOnly;
	
	
	public function __construct($name, $value = NULL, $expire = 0, $path = '/', $domain = NULL, $secure = false, $httpOnly = true){
		//匹配字符情況
		if(preg_match('/[=,; \t\r\n\013\014]/', $name)){
			 throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
		}
		
		if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty.');
        }

        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTime) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);

            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }
        
		$this->name = $name;
		$this->value = $value;
		$this->expire = $expire;
		$this->path = empty($path) ? '/' : $path;
		$this->domain = $domain;
		$this->secure = (bool) $secure;
		$this->httpOnly = (bool)$httpOnly;
	}
	
	public function __toString(){
		
		$str = urlencode($this->getName()).'=';
		
		if('' == $this->getValue()){
			$str .= 'deleted; expires='.gmdate("D, d-M-Y H:i:s T", time() - 31536001);
		}else{
			
			$str .= urlencode($this->getValue());
			
			if($this->getExpiresTime() !== 0){
				$str .= '; expires='.gmdate("D, d-M-Y H:i:s T", $this->getExpiresTime());
			}
		}
		
		if ($this->path) {
            $str .= '; path='.$this->path;
        }

        if ($this->getDomain()) {
            $str .= '; domain='.$this->getDomain();
        }

        if (true === $this->isSecure()) {
            $str .= '; secure';
        }

        if (true === $this->isHttpOnly()) {
            $str .= '; httponly';
        }

        return $str;
	}
	
    /** Gets the name of the cookie.
     *
     * @return string
     *
     * @api
     */
    public function getName(){
    	
        return $this->name;
    }

    /**
     * Gets the value of the cookie.
     *
     * @return string
     *
     * @api
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets the domain that the cookie is available to.
     *
     * @return string
     *
     * @api
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Gets the time the cookie expires.
     *
     * @return int
     *
     * @api
     */
    public function getExpiresTime()
    {
        return $this->expire;
    }

    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     *
     * @api
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Checks whether the cookie should only be transmitted over a secure HTTPS connection from the client.
     *
     * @return bool
     *
     * @api
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @return bool
     *
     * @api
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Whether this cookie is about to be cleared.
     *
     * @return bool
     *
     * @api
     */
    public function isCleared()
    {
        return $this->expire < time();
    }
}