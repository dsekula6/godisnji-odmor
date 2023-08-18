<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\User;
use App\Entity\AppRole;
use App\Entity\Project;
use App\Entity\TeamMember;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // APP ROLES
        $role1 = new AppRole();
        $role1->setRoleName('employee');
        $manager->persist($role1);
        
        $role2 = new AppRole();
        $role2->setRoleName('project lead');
        $manager->persist($role2);
        
        $role3 = new AppRole();
        $role3->setRoleName('team lead');
        $manager->persist($role3);

        // $role3 = new AppRole();
        // $role3->setRoleName('approver');
        // $manager->persist($role3);
        
        $role4 = new AppRole();
        $role4->setRoleName('admin');
        $manager->persist($role4);

        // ADMIN user

        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']); // note to self: bez ovoga
        $user->setEmail('dsekula6@gmail.com');
        $user->setFirstName('Daniel');
        $user->setLastName('Sekula');
        $user->setPassword($this->userPasswordHasher->hashPassword($user,'admin'));
        $user->addAppRole($role4);
        $manager->persist($user);

        // Ostali useri

        $user2 = new User();
        $user2->setEmail('team@gmail.com');
        $user2->setFirstName('Team');
        $user2->setLastName('Lead');
        $user2->setPassword($this->userPasswordHasher->hashPassword($user,'team'));
        $user2->addAppRole($role3); // team lead
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('project@gmail.com');
        $user3->setFirstName('Project');
        $user3->setLastName('Lead');
        $user3->setPassword($this->userPasswordHasher->hashPassword($user,'project'));
        $user3->addAppRole($role2); // project lead
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('user1@gmail.com');
        $user4->setFirstName('User1');
        $user4->setLastName('Sekula');
        $user4->setPassword($this->userPasswordHasher->hashPassword($user,'user'));
        $user4->addAppRole($role1); // employee
        $manager->persist($user4);

        $user5 = new User();
        $user5->setEmail('user2@gmail.com');
        $user5->setFirstName('User2');
        $user5->setLastName('Sekula');
        $user5->setPassword($this->userPasswordHasher->hashPassword($user,'user'));
        $user5->addAppRole($role1); // employee
        $manager->persist($user5);

        $user6 = new User();
        $user6->setEmail('user3@gmail.com');
        $user6->setFirstName('User3');
        $user6->setLastName('Sekula');
        $user6->setPassword($this->userPasswordHasher->hashPassword($user,'user'));
        $user6->addAppRole($role1); // employee
        $manager->persist($user6);

        $user7 = new User();
        $user7->setEmail('user4@gmail.com');
        $user7->setFirstName('User4');
        $user7->setLastName('Sekula');
        $user7->setPassword($this->userPasswordHasher->hashPassword($user,'user'));
        $user7->addAppRole($role1); // employee
        $manager->persist($user7);

        // Timovi i projekti

        // tim 1
        $team = new Team();
        $team->setTeamName('backend');

        $teamMember = new TeamMember();
        $teamMember->setUser($user4);
        $teamMember->setTeamRole('member');
        $manager->persist($teamMember);

        $teamMember3 = new TeamMember();
        $teamMember3->setUser($user5);
        $teamMember3->setTeamRole('member');
        $manager->persist($teamMember3);
        
        $teamMember2 = new TeamMember();
        $teamMember2->setUser($user2);
        $teamMember2->setTeamRole('team_lead');
        $manager->persist($teamMember2);

        $team->addTeamMember($teamMember);
        $team->addTeamMember($teamMember2);
        $team->addTeamMember($teamMember3);
        $manager->persist($team);

        // tim 2
        $team2 = new Team();
        $team2->setTeamName('frontend');

        $teamMember4 = new TeamMember();
        $teamMember4->setUser($user6);
        $teamMember4->setTeamRole('member');
        $manager->persist($teamMember4);

        $teamMember5 = new TeamMember();
        $teamMember5->setUser($user7);
        $teamMember5->setTeamRole('member');
        $manager->persist($teamMember5);
        
        $teamMember6 = new TeamMember();
        $teamMember6->setUser($user2);
        $teamMember6->setTeamRole('team_lead');
        $manager->persist($teamMember6);

        $team2->addTeamMember($teamMember4);
        $team2->addTeamMember($teamMember5);
        $team2->addTeamMember($teamMember6);
        $manager->persist($team2);

        $project = new Project();
        $project->setProjectName('izrada aplikacije');
        $project->addProjectTeam($team);
        $project->addProjectTeam($team2);
        $project->setProjectLead($user3);
        $manager->persist($project);


        $manager->flush();
    }
}
