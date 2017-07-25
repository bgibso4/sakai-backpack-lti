<?php

/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/25/2017
 * Time: 9:30 AM
 */
class obf_issuer
{

    /**
     * @var string The name of the issuer
     */
    private $name = null;
    /**
     * @var string The email of the issuer
     */
    private $email= null;
    /**
     * @var string The URL of the issuer
     */
    private $url= null;

    /**
     * obf_issuer constructor.
     * @param string $_name Name
     * @param string $_email Email
     * @param string $_url Url
     */
    public function __construct($_name, $_email, $_url)
    {
        $this->name= $_name;
        $this->email= $_email;
        $this->url= $_url;
    }

    /**
     * @return string Returns the name of the issuer
     */
    public function getName(){
        return $this->name;
    }
    /**
     * @return string Returns the email of the issuer
     */
    public function getEmail(){
        return $this->email;
    }
    /**
     * @return string Returns the URl link of the issuer
     */
    public function getUrl(){
        return $this->url;
    }
}