<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($j=1; $j<=mt_rand(4, 6); $j++)
        {
            $category = new Category;
            $category->setTitle($this->faker->sentence())
                    ->setDescription($this->faker->paragraph());
            $manager->persist($category);
            for($i=1; $i<=mt_rand(5,7); $i++)
            {
                $article = new Article;
                $article->setTitle($this->faker->sentence(6))
                        ->setContent($this->faker->paragraph(250))
                        ->setImage($this->faker->imageUrl( 640, 480))
                        ->setCreatedAt($this->faker->dateTimeBetween('-10 months'))
                        ->setCategory($category);
                $manager->persist($article);

                for($k=1; $k<=mt_rand(8, 10); $k++)
                {
                    $comment = new Comment;

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . ' days';

                    $comment->setAuthor($this->faker->name)
                        ->setContent($this->faker->paragraph())
                        ->setCreatedAt($this->faker->dateTimeBetween($minimum))
                        ->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }

        

        $manager->flush();
    }
}
