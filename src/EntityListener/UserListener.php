<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserListener 
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user, PrePersistEventArgs $event)
    {
        $this->encodePassword($user);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        $this->encodePassword($user);
    }

    /**
     * Encode password based on plain password
     *
     * @param User $user
     * @return void
     */
    public function encodePassword(User $user)
    {
        if ($user->getPlainPassword() === null) {
            return;
        }

        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );

        $user->setPlainPassword(null);
    }
}
