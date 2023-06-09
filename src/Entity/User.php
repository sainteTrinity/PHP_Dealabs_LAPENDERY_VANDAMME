<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Deal::class)]
    private Collection $deals;

    #[ORM\ManyToMany(targetEntity: Deal::class, inversedBy: 'likedUsers')]
    private Collection $likedDeals;

    #[ORM\ManyToMany(targetEntity: Badge::class)]
    private Collection $Badges;

    #[ORM\ManyToMany(targetEntity: Deal::class, mappedBy: 'votedUsers')]
    private Collection $votedDeals;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->deals = new ArrayCollection();
        $this->likedDeals = new ArrayCollection();
        $this->Badges = new ArrayCollection();
        $this->votedDeals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return Collection<int, Deal>
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    public function addDeal(Deal $deal): self
    {
        if (!$this->deals->contains($deal)) {
            $this->deals->add($deal);
            $deal->setCreator($this);
        }

        return $this;
    }

    public function removeDeal(Deal $deal): self
    {
        if ($this->deals->removeElement($deal)) {
            // set the owning side to null (unless already changed)
            if ($deal->getCreator() === $this) {
                $deal->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Deal>
     */
    public function getLikedDeals(): Collection
    {
        return $this->likedDeals;
    }

    public function addLikedDeal(Deal $likedDeal): self
    {
        if (!$this->likedDeals->contains($likedDeal)) {
            $this->likedDeals->add($likedDeal);
        }

        return $this;
    }

    public function removeLikedDeal(Deal $likedDeal): self
    {
        $this->likedDeals->removeElement($likedDeal);

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->Badges;
    }

    public function addBadge(Badge $badge): static
    {
        if (!$this->Badges->contains($badge)) {
            $this->Badges->add($badge);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): static
    {
        $this->Badges->removeElement($badge);

        return $this;
    }

    /**
     * @return Collection<int, Deal>
     */
    public function getVotedDeals(): Collection
    {
        return $this->votedDeals;
    }

    public function addVotedDeal(Deal $votedDeal): static
    {
        if (!$this->votedDeals->contains($votedDeal)) {
            $this->votedDeals->add($votedDeal);
            $votedDeal->addVotedUser($this);
        }

        return $this;
    }

    public function removeVotedDeal(Deal $votedDeal): static
    {
        if ($this->votedDeals->removeElement($votedDeal)) {
            $votedDeal->removeVotedUser($this);
        }

        return $this;
    }
}
