<?php
namespace App\Tests\Repository;

use App\Config\CategoryConfig;
use App\DataFixtures\CategoryFixtures;
use App\Repository\CategoryRepository;
use App\DataFixtures\TestFixtures\CategoryTestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class CategoryRepositoryTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testCountAll()
    {
        $this->databaseTool->loadFixtures([CategoryFixtures::class]);
        $categories = self::getContainer()->get(CategoryRepository::class)->findAll();
        $this->assertCount(count(CategoryConfig::LIST), $categories);
    }
}