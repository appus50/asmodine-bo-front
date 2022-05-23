<?php

namespace Asmodine\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FragmentControllerTest extends WebTestCase
{
    public function testCategories()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fragment/categories');
    }

    public function testFooter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fragment/footer');
    }

    public function testSocial()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fragment/social');
    }
}
