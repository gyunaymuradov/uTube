<?php

namespace model;

class User {

    private $id;
    private $username;
    private $password;
    private $email;
    private $firstName;
    private $lastName;
    private $userPhotoUrl;



    /**
     * User constructor.
     * @param $username
     * @param $password
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    public function __construct($username, $password, $email, $firstName, $lastName)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getUserPhotoUrl()
    {
        return $this->userPhotoUrl;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



}