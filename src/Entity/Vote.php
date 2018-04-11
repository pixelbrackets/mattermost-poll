<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $uid;

    /**
     * Many-To-One, Unidirectional
     * @ORM\ManyToOne(targetEntity="Answer")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="uid")
     */
    private $answer;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $user;

    /**
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param Answer $answer
     */
    public function setAnswer(Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
