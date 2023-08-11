<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'project_teams')]
    private Collection $team_projects;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TeamMember::class)]
    private Collection $team_members;

    public function __construct()
    {
        $this->team_projects = new ArrayCollection();
        $this->team_members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }

    public function setTeamName(string $team_name): static
    {
        $this->team_name = $team_name;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getTeamProjects(): Collection
    {
        return $this->team_projects;
    }

    public function addTeamProject(Project $teamProject): static
    {
        if (!$this->team_projects->contains($teamProject)) {
            $this->team_projects->add($teamProject);
            $teamProject->addProjectTeam($this);
        }

        return $this;
    }

    public function removeTeamProject(Project $teamProject): static
    {
        if ($this->team_projects->removeElement($teamProject)) {
            $teamProject->removeProjectTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TeamMember>
     */
    public function getTeamMembers(): Collection
    {
        return $this->team_members;
    }

    public function addTeamMember(TeamMember $teamMember): static
    {
        if (!$this->team_members->contains($teamMember)) {
            $this->team_members->add($teamMember);
            $teamMember->setTeam($this);
        }

        return $this;
    }

    public function removeTeamMember(TeamMember $teamMember): static
    {
        if ($this->team_members->removeElement($teamMember)) {
            // set the owning side to null (unless already changed)
            if ($teamMember->getTeam() === $this) {
                $teamMember->setTeam(null);
            }
        }

        return $this;
    }
}
