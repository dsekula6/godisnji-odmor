<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $project_name = null;

    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'team_projects')]
    private Collection $project_teams;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?User $project_lead = null;

    #[ORM\Column]
    private ?bool $active = null;

    public function __construct()
    {
        $this->project_teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->project_name;
    }

    public function setProjectName(string $project_name): static
    {
        $this->project_name = $project_name;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getProjectTeams(): Collection
    {
        return $this->project_teams;
    }

    public function addProjectTeam(Team $projectTeam): static
    {
        if (!$this->project_teams->contains($projectTeam)) {
            $this->project_teams->add($projectTeam);
        }

        return $this;
    }

    public function removeProjectTeam(Team $projectTeam): static
    {
        $this->project_teams->removeElement($projectTeam);

        return $this;
    }

    public function getProjectLead(): ?User
    {
        return $this->project_lead;
    }

    public function setProjectLead(?User $project_lead): static
    {
        $this->project_lead = $project_lead;

        return $this;
    }
    public function __toString(): string
    {
        return $this->project_name;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
