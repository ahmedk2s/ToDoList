<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tache;
use Faker\Factory;
use Faker\Generator;
use App\Entity\User; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
        
    }

    public function load(ObjectManager $manager,): void
    {
        $now = new \DateTime(); // Date et heure actuelles
        $priorites = ['basse', 'moyenne', 'haute'];
        $statuts = ['En cours', 'Terminé', 'En attente'];
        $descriptions = [
            'Préparez une délicieuse tarte aux fruits en mélangeant des fraises juteuses, des framboises sucrées et des bleuets dans un fond de tarte croustillant, le tout garni d\'une délicieuse crème pâtissière légère.',
            'Réorganisez votre espace de travail pour une productivité maximale.',
            'Rédigez un rapport détaillé sur les dernières avancées du projet.',
            'Contactez les clients pour confirmer les détails de la réunion.',
            'Mettez à jour le site web avec les dernières nouvelles de l\'entreprise.',
            'Créez un plan de marketing pour le lancement du nouveau produit.',
            'Testez la nouvelle fonctionnalité du logiciel et signalez les problèmes.',
            'Préparez une présentation pour la réunion d\'équipe de la semaine prochaine.',
            'Effectuez une analyse comparative des concurrents.',
            'Créez des graphiques et des visualisations pour le rapport mensuel.',
            'Développez un script automatisé pour sauvegarder les fichiers importants.',
            'Organisez une séance de brainstorming pour générer de nouvelles idées de projets.',
            'Assistez à une formation en ligne pour améliorer vos compétences professionnelles.',
            'Collaborez avec l\'équipe de conception pour finaliser le design de l\'application.',
        ];

        for ($i = 1; $i <= 30; $i++) {
            $tache = new Tache();
            $tache->setTitre($this->faker->word())
                ->setDateEcheance(clone $now->modify('+ ' . mt_rand(1, 14) . ' days')) // Date aléatoire entre 1 et 14 jours à partir de maintenant
                ->setPriorite($priorites[array_rand($priorites)]) 
                ->setStatut($statuts[array_rand($statuts)]) // Choisit un statut aléatoire
                ->setDescription($descriptions[array_rand($descriptions)]); // Choisit une description aléatoire

            $manager->persist($tache);
        }

        // Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

                $manager->persist(($user));
        }

        $manager->flush();
    }
}
