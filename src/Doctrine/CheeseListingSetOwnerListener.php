<?php

namespace App\Doctrine;

use App\Entity\CheeseListing;
use Symfony\Component\Security\Core\Security;

class CheeseListingSetOwnerListener
{
    public function __construct(private readonly Security $security)
    {
    }

    public function prePersist(CheeseListing $cheeseListing)
    {
        if ($cheeseListing->getOwner()) {
            return;
        }

        if ($user = $this->security->getUser()) {
            $cheeseListing->setOwner($user);
        }
    }
}