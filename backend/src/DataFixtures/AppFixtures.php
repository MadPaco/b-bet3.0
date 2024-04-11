<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Chatroom;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\NflTeam;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
    $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $chatroom = new Chatroom();
        $chatroom->setName('Public Chatroom');
        $manager->persist($chatroom);

        $teamOne = new NflTeam();
        $teamOne->setName('Arizona Cardinals');
        $teamOne->setLocation('Phoenix');
        $teamOne->setShorthandName('ARI');
        $teamOne->setLogo('ARI.png');
        $teamOne->setDivision('West');
        $teamOne->setConference('NFC');
        $teamOne->setPrimaryColor('red');
        $manager->persist($teamOne);

        $teamTwo = new NflTeam();
        $teamTwo->setName('Atlanta Falcons');
        $teamTwo->setLocation('Atlanta');
        $teamTwo->setShorthandName('ATL');
        $teamTwo->setLogo('ATL.png');
        $teamTwo->setDivision('South');
        $teamTwo->setConference('NFC');
        $teamTwo->setPrimaryColor('black');
        $manager->persist($teamTwo);

        $teamThree = new NflTeam();
        $teamThree->setName('Baltimore Ravens');
        $teamThree->setLocation('Baltimore');
        $teamThree->setShorthandName('BAL');
        $teamThree->setLogo('BAL.png');
        $teamThree->setDivision('North');
        $teamThree->setConference('AFC');
        $teamThree->setPrimaryColor('purple');
        $manager->persist($teamThree);

        $teamFour = new NflTeam();
        $teamFour->setName('Buffalo Bills');
        $teamFour->setLocation('Buffalo');
        $teamFour->setShorthandName('BUF');
        $teamFour->setLogo('BUF.png');
        $teamFour->setDivision('East');
        $teamFour->setConference('AFC');
        $teamFour->setPrimaryColor('blue');
        $manager->persist($teamFour);

        $gameOne = new Game();
        $gameOne->setWeekNumber(1);
        $gameOne->setDate(new \DateTime('2021-09-09 20:20:00'));
        $gameOne->setLocation('Statefarm Stadium');
        $gameOne->setHomeTeam($teamOne);
        $gameOne->setAwayTeam($teamTwo);
        $manager->persist($gameOne);

        $gameTwo = new Game();
        $gameTwo->setWeekNumber(1);
        $gameTwo->setDate(new \DateTime('2021-09-09 20:20:00'));
        $gameTwo->setLocation('M&T Bank Stadium');
        $gameTwo->setHomeTeam($teamThree);
        $gameTwo->setAwayTeam($teamFour);
        $manager->persist($gameTwo);

        


        $user = new User();
        $user->setUsername('testuser');
        $password = $this->passwordEncoder->hashPassword($user, 'password');
        $user->setPassword($password);
        $user->setEmail('test@test.com');
        $user->setFavTeam($teamOne);
        $user->setCreatedAt(new \DateTime());
        $user->setRoles(['USER']);
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('admin');
        $password = $this->passwordEncoder->hashPassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setEmail('admin@test.com');
        $admin->setFavTeam($teamTwo);
        $admin->setCreatedAt(new \DateTime());
        $admin->setRoles(['USER', 'ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
?>