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
}
