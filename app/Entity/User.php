<?php
declare(strict_types = 1);

namespace app\Entity;

use app\Exceptions\InvalidArgumentException;
use app\Services\Auth\Acl\HasPermissions;
use app\Services\Auth\Acl\HasRoles;
use app\Services\Auth\Acl\PermissionTrait;
use app\Services\Auth\Acl\RoleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Illuminate\Support\Facades\DB;

/**
 * Represents a user in the system.
 *
 * @ORM\Entity
 * @ORM\Table(name="users", indexes={
 *     @ORM\Index(name="username_idx", columns={"username"}),
 *     @ORM\Index(name="email_idx", columns={"email"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class User implements HasRoles, HasPermissions
{
    use RoleTrait, PermissionTrait;

    /**
     * User identifier.
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * User's username.
     *
     * @ORM\Column(name="username", type="string", length=32, unique=true)
     */
    private $username;

    /**
     * User's email.
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * Hashed user password.
     *
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * The amount of funds on the balance of the user.
     *
     * @ORM\Column(name="balance", type="float", nullable=false, unique=false)
     */
    private $balance = 0;

    /**
     * User's UUID.
     *
     * @ORM\Column(name="uuid", type="guid", unique=true)
     */
    private $uuid;

    /**
     * This column is used by sashok724's launcher.
     *
     * @ORM\Column(name="access_token", type="string", length=36, nullable=true, options={"fixed" = true})
     */
    private $accessToken;

    /**
     * This column is used by sashok724's launcher.
     *
     * @ORM\Column(name="server_id", type="string", length=41, nullable=true)
     */
    private $serverId;

    /**
     * SHA1 skin file hash eq. filename.
     *
     * @ORM\Column(name="skin_hash", type="string", length=40, nullable=true)
     */
    private $skinHash;

    /**
     * SHA1 cloak file hash eq. filename.
     *
     * @ORM\Column(name="cloak_hash", type="string", length=40, nullable=true)
     */
    private $cloakHash;

    /**
     * Roles that the user has.
     *
     * @ORM\ManyToMany(targetEntity="app\Entity\Role", mappedBy="users", cascade={"persist", "merge"})
     */
    private $roles;

    /**
     * Permissions that the user has.
     *
     * @ORM\ManyToMany(targetEntity="app\Entity\Permission", mappedBy="users")
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity="app\Entity\Activation", mappedBy="user", cascade={"remove"})
     */
    private $activations;

    /**
     * @ORM\OneToMany(targetEntity="app\Entity\Persistence", mappedBy="user", cascade={"remove"})
     */
    private $persistences;

    /**
     * @ORM\OneToMany(targetEntity="app\Entity\Ban", mappedBy="user", cascade={"persist", "merge", "remove"})
     */
    private $bans;

    /**
     * @ORM\OneToMany(targetEntity="app\Entity\Reminder", mappedBy="user", cascade={"remove"})
     */
    private $reminders;

    /**
     * @ORM\OneToMany(targetEntity="app\Entity\Purchase", mappedBy="user")
     */
    private $purchases;

    /**
     * Date and time when the user was created.
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function __construct(string $username, string $email, string $password)
    {
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->activations = new ArrayCollection();
        $this->persistences = new ArrayCollection();
        $this->bans = new ArrayCollection();
        $this->reminders = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        // Так а чё, так нельзя что ли было сделать?
        return DB::table('users')->where('id', $this->id)->value('balance');
    }

    /**
     * It is not recommended to use this method directly. Instead, use {@see Transactor}.
     *
     * @param float $balance
     *
     * @return User
     */
    public function setBalance(float $balance): User
    {
        if ($balance < 0) {
            throw new InvalidArgumentException('The argument $balance must be greater than or equal to zero');
        }
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return Uuid::fromString($this->uuid);
    }

    /**
     * @return string
     */
    public function getSkinHash(): string
    {
        return DB::table('users')->where('id', $this->id)->value('skin_hash') ?? '';
    }

    /**
     * @return string
     */
    public function getCloakHash(): string
    {
        return DB::table('users')->where('id', $this->id)->value('cloak_hash') ?? '';
    }

    /**
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @return Collection
     */
    public function getActivations(): Collection
    {
        return $this->activations;
    }

    /**
     * @return Collection
     */
    public function getPersistences(): Collection
    {
        return $this->persistences;
    }

    /**
     * @return Collection
     */
    public function getBans(): Collection
    {
        return $this->bans;
    }

    /**
     * @return Collection
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    /**
     * @return Collection
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function generateCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return string Object string representation.
     * @example app\Entity\User(id=1, username="Admin", email="admin@example.com", balance=1000)
     */
    public function __toString(): string
    {
        return sprintf(
            '%s(id=%d, username="%s", email="%s", balance=%F)',
            self::class,
            $this->getId(),
            $this->getUsername(),
            $this->getEmail(),
            $this->getBalance()
        );
    }
}
