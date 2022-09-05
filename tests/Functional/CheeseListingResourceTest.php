<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/cheeses');
        $this->assertResponseStatusCodeSame(401);

        $user = new User();
        $user
            ->setEmail('cheeseplease@example.com')
            ->setUsername('cheeseplease')
            ->setPassword('$2y$13$ZsjXfUKgrDd/d2vqcBwLcuDnq46RgKFb.zi81zT0QnMNHNCJvJsmC');

        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $client->request('POST', '/login', [
            'json' => [
                'email' => $user->getEmail(),
                'password' => 'foo',
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);
    }
}
