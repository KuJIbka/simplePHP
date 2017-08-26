<?php

namespace Main\Entity;

/**
 * @Entity(repositoryClass="Main\Repository\UserLimitRepository")
 * @Table(name="user_limit")
 */
class UserLimit
{
    /**
     * @Id
     * @OneToOne(targetEntity="User", inversedBy="userLimit")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /** @Column(name="login_try_count", type="integer") */
    private $loginTryCount = 0;

    /** @Column(name="login_try_count_time", type="integer") */
    private $loginTryCountTime = 0;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoginTryCount(): int
    {
        return $this->loginTryCount;
    }

    /**
     * @param int $loginTryCount
     */
    public function setLoginTryCount(int $loginTryCount): self
    {
        $this->loginTryCount = $loginTryCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoginTryCountTime(): int
    {
        return $this->loginTryCountTime;
    }

    /**
     * @param int $loginTryCountTime
     */
    public function setLoginTryCountTime(int $loginTryCountTime): self
    {
        $this->loginTryCountTime = $loginTryCountTime;
        return $this;
    }
}
