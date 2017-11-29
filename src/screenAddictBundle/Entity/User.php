<?php

namespace screenAddictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use screenAddictBundle\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="screenAddictBundle\Repository\userRepository")
 */
class User
{
    /**
     * One User has Many Messages.
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     */
    private $messages;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=30, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=30)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fname", type="string", length=30)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @var \Date
     *
     * @ORM\Column(name="bdate", type="date")
     */
    private $bdate;

    /**
     * @var array
     *
     * @ORM\Column(name="friends", type="array")
     */
    private $friends;

    /**
     * @var array
     *
     * @ORM\Column(name="games", type="array")
     */
    private $games;

    /**
     * @var array
     *
     * @ORM\Column(name="series", type="array")
     */
    private $series;

    /**
     * @var array
     *
     * @ORM\Column(name="movies", type="array")
     */
    private $movies;

    public function __construct() {
        $this->messages = new ArrayCollection();
        $this->username = "";
        $this->password = "";
        $this->name = "";
        $this->fname = "";
        $this->mail = "";
        $this->bdate = new \Date();
        $this->movies = new ArrayCollection();
        $this->series = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return user
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return user
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return user
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fname
     *
     * @param string $fname
     *
     * @return user
     */
    public function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return user
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set bdate
     *
     * @param \DateTime $bdate
     *
     * @return user
     */
    public function setBdate($bdate)
    {
        $this->bdate = $bdate;

        return $this;
    }

    /**
     * Get bdate
     *
     * @return \DateTime
     */
    public function getBdate()
    {
        return $this->bdate;
    }

    /**
     * Set friends
     *
     * @param array $friends
     *
     * @return user
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this;
    }

    /**
     * Get friends
     *
     * @return array
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * Set games
     *
     * @param array $games
     *
     * @return user
     */
    public function setGames($games)
    {
        $this->games = $games;

        return $this;
    }

    /**
     * Get games
     *
     * @return array
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set series
     *
     * @param array $series
     *
     * @return user
     */
    public function setSeries($series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * Get series
     *
     * @return array
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Set movies
     *
     * @param array $movies
     *
     * @return user
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;

        return $this;
    }

    /**
     * Get movies
     *
     * @return array
     */
    public function getMovies()
    {
        return $this->movies;
    }
}
