<?php

namespace screenAddictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use screenAddictBundle\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="screenAddictBundle\Repository\userRepository")
 * @UniqueEntity(
 * fields={"mail", "username"},
 * errorPath="username",
 * message="Quelqu'un d'autre utilise déjà cet email/nom d'utilisateur"
 *)
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
     * @ORM\Column(name="username", type="string", length=32, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fname", type="string", length=64)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, unique=true)
     */
    private $mail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bdate", type="date")
     */
    private $bdate;

    /**
     * @var array
     *
     * @ORM\Column(name="friends", type="array", nullable=true)
     */
    private $friends;

    /**
     * @var array
     *
     * @ORM\Column(name="movies", type="array", nullable=true)
     */
    private $movies;

    /**
     * @var array
     *
     * @ORM\Column(name="series", type="array", nullable=true)
     */
    private $series;


    public function __construct()
    {
        $this->username = "";
        $this->password = "";
        $this->name = "";
        $this->fname = "";
        $this->mail = "";
        $this->friends = new ArrayCollection();
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Set messages
     *
     * @param array $messages
     *
     * @return User
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set movies
     *
     * @param array $movies
     *
     * @return User
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

    /**
     * Set series
     *
     * @param array $series
     *
     * @return User
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
}
