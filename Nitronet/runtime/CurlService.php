<?php
namespace Nitronet\runtime;



class CurlService
{
    protected $proxyUser;

    protected $proxyPassword;

    protected $proxyHost;

    public function __construct($proxyHost = null, $proxyUser = null,
        $proxyPass = null
    ) {
        $this->proxyHost = $proxyHost;
        $this->proxyUser = $proxyUser;
        $this->proxyPassword = $proxyPass;
    }

    public function request($targetUrl, $timeout = 20)
    {
        $ch = curl_init($targetUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if  (isset($this->proxyHost) && !empty($this->proxyHost)) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);

            if (!empty($this->proxyUser)) {
               $ident = $this->proxyUser;
               if (!empty($this->proxyPassword)) {
                   $ident .= ":". $this->proxyPassword;
               }

               curl_setopt($ch, CURLOPT_PROXYUSERPWD, $ident);
            }
        }

        if (stripos($targetUrl, "https", 0) !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            throw new \ErrorException(sprintf("cURL Request failed - http code: %s", $httpCode));
        }
        elseif (curl_errno($ch) != 0) {
            throw new \ErrorException(sprintf("cURL Request failed: %s", curl_error($ch)));
        }

        return $output;
    }

    /**
     *
     * @param string $targetUrl
     * @param string $destination 
     * 
     * @return string 
     */
    public function download($targetUrl, $destination = null, $timeout = 300)
    {
        if (null === $destination) {
            $destination = tempnam(sys_get_temp_dir(), 'fwkdl-');
        }
        
        file_put_contents($destination, $this->request($targetUrl, $timeout));
        
        return $destination;
    }
    
    public function getProxyUser()
    {
        return $this->proxyUser;
    }

    public function setProxyUser($proxyUser)
    {
        $this->proxyUser = $proxyUser;
    }

    public function getProxyPassword()
    {
        return $this->proxyPassword;
    }

    public function setProxyPassword($proxyPassword)
    {
        $this->proxyPassword = $proxyPassword;
    }

    public function getProxyHost()
    {
        return $this->proxyHost;
    }

    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;
    }
}