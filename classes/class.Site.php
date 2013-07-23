<?php

class Site
{

    var $username;
    var $pass;
    var $name;
    var $wpUrl;
    var $initialUrl;
    var $baseImageslUrl;
    var $category;
    var $imageSearch;

    function __construct()
    {}

    /**
     * @param String $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return String $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param String $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return String $pass
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return String $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $url
     */
    public function setInitialUrl($url)
    {
        $this->initialUrl = $url;
    }

    /**
     * @return String $url
     */
    public function getInitialUrl()
    {
        return $this->initialUrl;
    }
    
    /**
     * @param String $url
     */
    public function setBaseImagesUrl($url)
    {
        $this->baseImageslUrl = $url;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return String $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Strin $wpUrl
     */
    public function setWpUrl($wpUrl)
    {
        $this->wpUrl = $wpUrl;
    }

    /**
     * @return String $wpUrl
     */
    public function getWpUrl()
    {
        return $this->wpUrl;
    }

    /**
     * Load Categories config and add Pages
     */
    public function loadCategories()
    {
        if (($handle = fopen("archives/{$this->getName()}/categories.csv", "r")) !== FALSE) {
            
            $x = 1;

            while (($data = fgetcsv($handle, 1024, ",")) !== FALSE) {
                $name = $data[0];
                $tags = $data[1];
                $url = $data[2];
                $title = $data[3];
                $metaDesc = $data[4];
                $parseDOM = false;

                if (isset($data[5])) {
                    $parseDOM = $data[5];
                }
                
                $imageUrl = null;

                if($x <= 5 && $this->category->getImageUrl() != ''){
                    $imageUrl = $this->category->getImageUrl();
                }

                $page = new Page($name, $tags, $url, $title, $metaDesc, $parseDOM, $imageUrl);
                $page->replaceKeys($this->category);
                $this->category->addPage($page);
                
                $x++;
            }
            fclose($handle);
        }
    }
    
    public function setImageSearch($search = false)
    {
        $this->imageSearch = $search;
    }
}