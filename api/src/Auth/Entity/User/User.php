<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auth_users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="auth_user_id")
     */
    private Id $id;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column(type="date_immutable")
     */
    private DateTimeImmutable $date;
    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;
    /**
     * @ORM\Column(type="auth_user_role", length=16)
     */
    private Role $role;
    /**
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;
    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;
    /**
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private ?string $hash = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $joinConfirmToken = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $passwordResetToken = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $newEmailToken = null;

    public function __construct(Id $id, Email $email, DateTimeImmutable $date, Status $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->date = $date;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $email, $date, Status::wait());
        $user->hash = $passwordHash;
        $user->joinConfirmToken = $token;

        return $user;
    }

    public static function joinByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new self($id, $email, $date, Status::wait());
        $user->networks->add(new UserNetwork($user, $identity));
        return $user;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var UserNetwork $existing */
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualsTo($identity)) {
                throw new DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new UserNetwork($this, $identity));
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active');
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new DomainException('Email is already in use.');
        }
        if (null !== $this->newEmailToken && !$this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Changing is already requested.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->newEmail || null === $this->newEmailToken) {
            throw new DomainException('Changing is not requested.');
        }
        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->joinConfirmToken) {
            throw new DomainException('Confirmation is not required.');
        }
        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if (null === $this->passwordResetToken) {
            throw new DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->hash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (null === $this->hash) {
            throw new DomainException('User does not have an old password.');
        }
        if (!$hasher->validate($current, $this->hash)) {
            throw new DomainException('Incorrect current password.');
        }
        $this->hash = $hasher->hash($new);
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function remove(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Unable to remove active user.');
        }
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * @return NetworkIdentity[]
     */
    public function getNetworks(): array
    {
        return $this->networks->map(static function(UserNetwork $network) {
            return $network->getNetwork();
        })->toArray();
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @ORM\PostLoad
     */
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}
