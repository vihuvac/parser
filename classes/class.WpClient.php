<?php

require 'class-IXR.php';

class WpClient
{

    var $client;
    var $site;
    static private $instance = null;

    /**
     * @param Site $site
     */
    static public function getInstance($site)
    {
        if (null === self::$instance) {
            self::$instance = new self($site);
        }
        return self::$instance;
    }

    function __construct($site)
    {
        $this->site = $site;
        $this->client = new IXR_Client($this->site->getWpUrl() . '/xmlrpc.php');
    }

    /**
     * @return Array $catNames
     */
    function getWpCategoriesNames()
    {
        if (!$this->client->query('wp.getCategories', '', $this->site->username,
                $this->site->pass)) {
            echo('\nError geting WP categories list: ' . $this->client->getErrorCode() . ":" . $this->client->getErrorMessage());
            die;
        }

        $response = $this->client->getResponse();
        $catNames = array();
        foreach ($response as $category) {
            $catNames[$category['categoryName']] = $category['categoryId'];
        }
        return $catNames;
    }

    /**
     * @param String $name
     * @param Int $parentId
     * @return Int $catId
     */
    function createWpCategory($name, $parentId = null)
    {
        $category = array(
            'name' => $name,
            'slug' => null,
            'parent_id' => $parentId,
            'description' => null
        );

        $params = array(
            0,
            $this->site->username,
            $this->site->pass,
            $category
        );

        if (!$this->client->query('wp.newCategory', $params)) {
            echo('\nError creating WP category: ' . $this->client->getErrorCode() . ":" . $this->client->getErrorMessage());
            die;
        }

        $catId = $this->client->getResponse();
        return $catId;
    }

    /**
     * @param Array $article
     */
    function postArticle($article)
    {

        $postId = $this->createPost($article);

        $imageId = null;
        if ($postId && $article['image_url'] != '') {
            $imageId = $this->uploadImage($postId, $article['title'],
                $article['image_url']);
        } else if ($postId && $this->site->imageSearch) {
            require_once 'tools/gis.php';
            echo "\nSearching images for 'panoramio {$this->site->category->name} {$article['page_name']}'";
            $images = googleImageSearch("panoramio {$this->site->category->name} {$article['page_name']}");
            echo "\n" . count($images) . " images found";
            foreach ($images as $image) {
                $imageId = $this->uploadImage($postId, $article['title'],
                    $image->source);
            }
            if ($imageId) {
                $this->setFeaturedImage($postId, $imageId);
            }
        }

        return $postId;
    }

    /**
     * @param Array $article
     */
    protected function createPost($article)
    {
        $post = array(
            'title' => $article['title'],
            'categories' => array($article['category']),
            'mt_keywords' => $article['tags'],
            'description' => $article['content'],
            'wp_slug' => $article['title'],
            'mt_excerpt' => $article['post_excerpt'],
        );

        $params = array(
            0,
            $this->site->username,
            $this->site->pass,
            $post,
            'publish'
        );

        if (!$this->client->query(
                'metaWeblog.newPost', $params
        )) {
            echo('\nError [' . $this->client->getErrorCode() . ']: ' . $this->client->getErrorMessage());
            exit();
        }
        echo "\nCreating post";

        return $this->client->getResponse();
    }

    /**
     * @param Int $postId
     * @param String $imageName
     * @param String $imageUrl
     */
    protected function uploadImage($postId, $imageName, $imageUrl)
    {
        $handle = fopen($imageUrl, 'r');
        if (!$handle) {
            echo "couldn't get image";
            return false;
        }
        $filedata = stream_get_contents($handle);
        fclose($handle);

        $data = array(
            'name' => "{$imageName}.jpg",
            'type' => 'image/jpg',
            'bits' => new IXR_Base64($filedata),
            'post' => $postId,
            false // overwrite
        );

        echo "\n-Uploading image";
        if (!$this->client->query(
                'metaWeblog.newMediaObject', $postId, $this->site->username,
                $this->site->pass, $data
        )) {
            echo('\nError [' . $this->client->getErrorCode() . ']: ' . $this->client->getErrorMessage());
            exit();
        } else {
            
        }
        $image = $this->client->getResponse();

        return $image['id'];
    }

    /**
     * @param Int $postId
     * @param Int $imageID
     */
    protected function setFeaturedImage($postId, $imageId)
    {
        $post = array(
            'wp_post_thumbnail' => $imageId,
        );

        $params = array(
            $postId,
            $this->site->username,
            $this->site->pass,
            $post,
            'publish'
        );

        if (!$this->client->query(
                'metaWeblog.editPost', $params
        )) {
            echo('\nError [' . $this->client->getErrorCode() . ']: ' . $this->client->getErrorMessage());
            exit();
        }
        echo "\n-Setting featured image";

        return $this->client->getResponse();
    }
}