<?php

interface SocialNetworkConnector
{
    public function logIn(): void;
    public function logOut(): void;
    public function createPost($content): void;
}

abstract class SocialNetworkPoster
{
    abstract public function getSocialNetwork(): SocialNetworkConnector;

    public function post($content): void
    {
        $network = $this->getSocialNetwork();

        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

class FacebookPoster extends SocialNetworkPoster
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new FacebookConnector($this->login, $this->password);
    }
}

class LinkedInPoster extends SocialNetworkPoster
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new LinkedInConnector($this->email, $this->password);
    }
}

class FacebookConnector implements SocialNetworkConnector
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API request to log in user $this->login with " .
             "password $this->password<br>";
    }

    public function logOut(): void
    {
        echo "Send HTTP API request to log out user $this->login<br>";
    }

    public function createPost($content): void
    {
        echo "Send HTTP API requests to create a post '$content' in Facebook timeline.<br>";
    }
}

class LinkedInConnector implements SocialNetworkConnector
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API request to log in user $this->email with " .
             "password $this->password<br>";
    }

    public function logOut(): void
    {
        echo "Send HTTP API request to log out user $this->email<br>";
    }

    public function createPost($content): void
    {
        echo "Send HTTP API requests to create a post '$content' in LinkedIn timeline.<br>";
    }
}

class Client
{
    public function doStuff(SocialNetworkPoster $poster)
    {
        $poster->post("Hello post!");
        $poster->post("Another post!");
    }
}

$client = new Client();
$client->doStuff(new FacebookPoster("john_smith", "******"));
$client->doStuff(new LinkedInPoster("john_smith@example.com", "******"));  
