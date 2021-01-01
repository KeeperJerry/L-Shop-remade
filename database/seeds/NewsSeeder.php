<?php
declare(strict_types = 1);

use app\Entity\News;
use app\Repository\News\NewsRepository;
use app\Repository\User\UserRepository;
use Faker\Generator;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(NewsRepository $newsRepository, UserRepository $userRepository, Generator $faker): void
    {
        $newsRepository->deleteAll();

        for ($i = 0; $i < 30; $i++) {
            $news = new News(
                __('seeding.news.title', ['number' => mt_rand(1, 1000)]),
                $faker->text(1024),
                $userRepository->findByUsername('admin')
            );
            $newsRepository->create($news);
        }
    }
}
