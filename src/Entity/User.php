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
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\ManyToMany(targetEntity: AppRole::class)]
    private Collection $app_roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TeamMember::class, orphanRemoval: true)]
    private Collection $teamMembers;

    #[ORM\OneToMany(mappedBy: 'project_lead', targetEntity: Project::class)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: VacationRequest::class)]
    private Collection $vacationRequests;

    public function __construct()
    {
        $this->app_roles = new ArrayCollection();
        $this->teamMembers = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->vacationRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return Collection<int, AppRole>
     */
    public function getAppRoles(): Collection
    {
        return $this->app_roles;
    }

    public function addAppRole(AppRole $appRole): static
    {
        if (!$this->app_roles->contains($appRole)) {
            $this->app_roles->add($appRole);
        }

        return $this;
    }

    public function removeAppRole(AppRole $appRole): static
    {
        $this->app_roles->removeElement($appRole);

        return $this;
    }

    /**
     * @return Collection<int, TeamMember>
     */
    public function getTeamMembers(): Collection
    {
        return $this->teamMembers;
    }

    public function addTeamMember(TeamMember $teamMember): static
    {
        if (!$this->teamMembers->contains($teamMember)) {
            $this->teamMembers->add($teamMember);
            $teamMember->setUser($this);
        }

        return $this;
    }

    public function removeTeamMember(TeamMember $teamMember): static
    {
        if ($this->teamMembers->removeElement($teamMember)) {
            // set the owning side to null (unless already changed)
            if ($teamMember->getUser() === $this) {
                $teamMember->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setProjectLead($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getProjectLead() === $this) {
                $project->setProjectLead(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VacationRequest>
     */
    public function getVacationRequests(): Collection
    {
        return $this->vacationRequests;
    }

    public function addVacationRequest(VacationRequest $vacationRequest): static
    {
        if (!$this->vacationRequests->contains($vacationRequest)) {
            $this->vacationRequests->add($vacationRequest);
            $vacationRequest->setUser($this);
        }

        return $this;
    }

    public function removeVacationRequest(VacationRequest $vacationRequest): static
    {
        if ($this->vacationRequests->removeElement($vacationRequest)) {
            // set the owning side to null (unless already changed)
            if ($vacationRequest->getUser() === $this) {
                $vacationRequest->setUser(null);
            }
        }

        return $this;
    }
}