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


        $gameOne = new Game();
        $gameOne->setWeekNumber(1);
        $gameOne->setDate(new \DateTime('2021-09-09 20:20:00'));
        $gameOne->setLocation('TIAA Bank Field');
        $gameOne->setHomeTeam($teamOne);
        $gameOne->setAwayTeam($teamTwo);
        $manager->persist($gameOne);


        $user = new User();
        $user->setUsername('testuser');
        $password = $this->passwordEncoder->hashPassword($user, 'password');
        $user->setPassword($password);
        $user->setEmail('test@test.com');
        $user->setFavTeam($teamOne);
        $user->setCreatedAt(new \DateTime());
        $user->setRoles(['USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
?>