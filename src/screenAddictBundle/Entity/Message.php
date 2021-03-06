<?php

namespace screenAddictBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use screenAddictBundle\Entity\User;

/**
 * Message
 *
 * @ORM\Table(name="Message")
 * @ORM\Entity(repositoryClass="screenAddictBundle\Repository\messageRepository")
 */
class Message
{

    /**
     * Many Messages have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     * @ORM\JoinColumn(name="id_author", referencedColumnName="id")
     */
     private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_author", type="integer")
     */
    private $idAuthor;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="usernameAuthor", type="string", length=32)
	 */
	private $usernameAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_post", type="datetime")
     */
    private $datePost;

    public function __construct() {
        $this->content = "";
        $this->datePost = new \DateTime();
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
     * Set idAuthor
     *
     * @param integer $idAuthor
     *
     * @return message
     */
    public function setIdAuthor($idAuthor)
    {
        $this->idAuthor = $idAuthor;

        return $this;
    }

    /**
     * Get idAuthor
     *
     * @return int
     */
    public function getIdAuthor()
    {
        return $this->idAuthor;
    }

	/**
     * Set usernameAuthor
     *
     * @param string $usernameAuthor
     *
     * @return message
     */
	 
    public function setUsernameAuthor($usernameAuthor)
    {
        $this->usernameAuthor = $usernameAuthor;

        return $this;
    }

    /**
     * Get usernameAuthor
     *
     * @return string
     */
    public function getUsernameAuthor()
    {
        return $this->usernameAuthor;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set datePost
     *
     * @param \DateTime $datePost
     *
     * @return message
     */
    public function setDatePost($datePost)
    {
        $this->datePost = $datePost;

        return $this;
    }

    /**
     * Get datePost
     *
     * @return \DateTime
     */
    public function getDatePost()
    {
        return $this->datePost;
    }

	/**
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return User $user
	 */
	public function getUser()
	{
		return $this->user;
	}
}
