<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Article;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //suppression du contenu des tables
        DB::table('users')->delete();
        DB::table('articles')->delete();

        $fakeUser = User::create(array(
            'name'      => 'FakeUser',
            'email'     => 'fake@user.com',
            'password'  => bcrypt('secret')
        ));

        $article = Article::create(array(
            'title'     => 'Premier Article',
            'content'   => 'Contenu du premier article',
        ));
    }
}
