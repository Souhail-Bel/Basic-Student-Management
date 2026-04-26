<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Section;
use App\Entity\Student;
use App\Enum\UserRole;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // 1. Create Sections
        $sections = [];
        foreach (['GL', 'RT', 'IIA', 'IMI'] as $dsg) {
            $section = new Section();
            $section->setDesignation($dsg);
            $sections[] = $section;
            $manager->persist($section);
        }

        // 2. Create Admin & Normal Users
        $admin = new User();
        $admin->setUsername('talel zighni')->setRole(UserRole::Admin)
        ->setPassword($this->hasher->hashPassword($admin, '123'));
        $manager->persist($admin);

        // 3. Create Students
        for ($i = 0; $i < 10; $i++) {
            $student = new Student();
            $student->setName($faker->name)
            ->setDateDeNaissance(new \DateTimeImmutable($faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d H:i:s')))
            ->setImgUrl("https://ui-avatars.com/api/?name=Student")
            ->setSection($faker->randomElement($sections));
            $manager->persist($student);
        }

        $manager->flush();
    }
}
