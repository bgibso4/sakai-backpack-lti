<?php

/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 7/25/2017
 * Time: 9:22 AM
 */
class badge
{
    /**
     * @var obf_issuer The issuer of the badge.
     */
    private $issuer = null;
    /**
     * @var string The id of the badge
     */
    private $id = null;
    /**
     * @var string The name of the badge
     */
    private $name = '';
    /**
     * @var string The badge image in base64
     */
    private $image = null;
    /**
     * @var string The badge description
     */
    private $description = '';
    /**
     * @var int Badge expiration time as an unix-timestamp
     */
    private $expiresby = null;
    /**
     * @var string[] The tags of the badge.
     */
    private $tags = array();
    /**
     * @var int The badge creation time as an unix-timestamp
     */
    private $created = null;
    /**
     * @var string The URL of the badge criteria.
     */
    private $criteriaurl = '';
//    /**
//     * @var string The HTML-markup of badge criteria.
//     */
//    private $criteriahtml = '';
//    /**
//     * @var string The CSS of the badge criteria page.
//     */
//    private $criteriacss = '';
//    /**
//     * @var string[] The categories of the badge.
//     */
//    private $categories = array();

    /**
     * @return string returns the name of the badge
     */
    public function getName(){
        if($this->name != null){
            return $this->name;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $name Name of the badge
     */
    public function setName($name){
        $this->name= $name;
    }

    /**
     * @return obf_issuer Returns the issuer and all of its details
     */
    public function getIssuer(){
        if($this->issuer != null){
            return $this->issuer;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param obf_issuer $_issuer The issuer
     */
    public function setIssuer($_issuer){
        $this->issuer= $_issuer;
    }

    /**
     * @return string Returns the id of the badge
     */
    public function getId(){
        if($this->id != null){
            return $this->id;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $id Id of badge
     */
    public function setId($id){
        $this->id= $id;
    }

    /**
     * @return string Returns the url of the image for the badge
     */
    public function getImage(){
        if($this->image != null){
            return $this->image;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $image Url for the image of badge
     */
    public function setImage($image){
        $this->image= $image;
    }

    /**
     * @return string Returns the description of the badge
     */
    public function getDescription(){
        if($this->description != null){
            return $this->description;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $description Description of badge
     */
    public function setDescription($description){
        $this->description= $description;
    }

    /**
     * @return string returns the expiry date of the badge
     */
    public function getExpiry(){
        if($this->expiresby != null){
            return $this->expiresby;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $expiry Expiry date of badge
     */
    public function setExpiry($expiry){
        $this->expiresby= $expiry;

    }
    /**
     * @param string $timestamp The timestamp to be converted
     * @return string the newly converted timestamp back to Y-m-d format
     */
    public function convertTimestamp($timestamp){
        date_default_timezone_set('UTC');
        return date('Y-m-d',$timestamp);

    }

    /**
     * @return string Returns the creation time of the badge
     */
    public function getCreationTime(){
        if($this->created != null){
            return $this->created;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $created Creation time of badge
     */
    public function setCreationTime($created){
        $this->created= $created;
    }

    /**
     * @return string[] Returns the tags of the badge
     */
    public function getTags(){
        if($this->tags != null){
            return $this->tags;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string[] $tags Tags of badge
     */
    public function setTags($tags){
        $this->tags= $tags;
    }

    /**
     * @return string returns the url to the criteria of the badge
     */
    public function getCriteriaUrl(){
        if($this->criteriaurl != null){
            return $this->criteriaurl;
        }
        throw new InvalidArgumentException();
    }
    /**
     * @param string $criteriaUrl The Url to the criteria of badge
     */
    public function setCriteriaUrl($criteriaUrl){
        $this->criteriaurl= $criteriaUrl;
    }


}
