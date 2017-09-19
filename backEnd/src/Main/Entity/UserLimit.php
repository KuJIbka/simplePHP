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

    /** @Column(name="login_try_count", type="smallint", nullable=false, options={"unsigned": true, "default": 0} ) */
    private $loginTryCount = 0;

    /** @Column(name="login_try_count_time",
     *          type="integer",
     *          nullable=false,
     *          options={"unsigned": true, "default": 0}
     *  )
     */
    private $loginTryCountTime = 0;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getLoginTryCount(): int
    {
        return $this->loginTryCount;
    }

    public function setLoginTryCount(int $loginTryCount): self
    {
        $this->loginTryCount = $loginTryCount;
        return $this;
    }

    public function getLoginTryCountTime(): int
    {
        return $this->loginTryCountTime;
    }

    public function setLoginTryCountTime(int $loginTryCountTime): self
    {
        $this->loginTryCountTime = $loginTryCountTime;
        return $this;
    }
}
