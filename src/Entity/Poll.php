<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PollRepository")
 */
class Poll
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * User who started the poll
     *
     * @ORM\Column(type="string", length=64)
     */
    private $creator;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modificationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visibility = true;

    /**
     * One-To-Many, Unidirectional
     * @ORM\ManyToMany(targetEntity="Answer")
     * @ORM\JoinTable(name="poll_answers",
     *      joinColumns={@ORM\JoinColumn(name="poll_id", referencedColumnName="uid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="answer_id", referencedColumnName="uid", unique=true)}
     * )
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     */
    public function setCreationDate()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return \DateTime
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param \DateTime $modificationDate
     */
    public function setModificationDate(\DateTime $modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param boolean $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return array
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
    }
}
