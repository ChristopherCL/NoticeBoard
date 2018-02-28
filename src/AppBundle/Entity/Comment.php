<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\Column(name="commentContent", type="text", nullable=true)
     */
    private $commentContent;
    
    /**
     * @ORM\ManyToOne(targetEntity="Advert", inversedBy="comments")
     * @ORM\JoinColumn(name="advert_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $advert;
    
    /**
     * @ORM\Column(name="notified", type="boolean")
     */
    private $notified = false;
    
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
     * Set user
     *
     * @param \AppBundle\Entity\user $user
     *
     * @return Comment
     */
    public function setUser(\AppBundle\Entity\user $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set commentContent
     *
     * @param string $commentContent
     *
     * @return Comment
     */
    public function setCommentContent($commentContent)
    {
        $this->commentContent = $commentContent;

        return $this;
    }

    /**
     * Get commentContent
     *
     * @return string
     */
    public function getCommentContent()
    {
        return $this->commentContent;
    }

    /**
     * Set advert
     *
     * @param \AppBundle\Entity\Advert $advert
     *
     * @return Comment
     */
    public function setAdvert(\AppBundle\Entity\Advert $advert = null)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \AppBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set notified
     *
     * @param boolean $notified
     *
     * @return Comment
     */
    public function setNotified($notified)
    {
        $this->notified = $notified;

        return $this;
    }

    /**
     * Get notified
     *
     * @return boolean
     */
    public function isNotified()
    {
        return $this->notified;
    }
}
