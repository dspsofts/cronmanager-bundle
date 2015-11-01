<?php

/**
 * CronTaskController test class.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 26/04/2015 10:38
 */

namespace DspSofts\CronManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CronTaskControllerTest extends WebTestCase
{
	/** @var Client */
	private $client;

	public function setUp()
	{
		$this->client = static::createClient();
	}

	public function testIndex()
	{
		$crawler = $this->client->request('GET', '/crontasks/list');

		$response = $this->client->getResponse();

		fwrite(STDERR, $response->getContent());
		$this->assertTrue($response->isSuccessful());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Cron task list")')->count());
	}
}
