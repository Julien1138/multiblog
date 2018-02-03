<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Blog;
use App\Article;

use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Suppression du contenu des tables de la base de donnée principale
        DB::table('users')->delete();
        DB::table('blogs')->delete();
        DB::table('websites')->delete();
        DB::table('hostnames')->delete();
        DB::table('customers')->delete();

        // Suppression de toutes les bases de données dont le nom commence par "multiblog_"
        $databases = DB::connection()->select('SELECT TABLE_SCHEMA FROM information_schema.tables WHERE TABLE_SCHEMA LIKE "' . config('app.name') . '_%"');
        foreach ($databases as $database) {
            DB::connection()->statement('DROP DATABASE IF EXISTS `' . $database->TABLE_SCHEMA . '`');
        }

        // On crée un faux utilisateur
        $fakeUser = User::create(array(
            'name'      => 'FakeUser',
            'email'     => 'fake@user.com',
            'password'  => bcrypt('secret')
        ));

        // On crée un host correspondant au site web par défaut
        $hostname = new Hostname;
        $hostname->fqdn = config('tenancy.hostname.default');
        app(HostnameRepository::class)->create($hostname);

        // On créer un premier blog
        $blog = Blog::create(array(
            'name'      => 'Premier Blog',
        ));

        // On switch sur le tenant 'Premier Blog'...
        $tenancy = app(Environment::class);
        $tenancy->hostname($blog->website()->hostnames[0]);

        //... afin d'y créer un premier article
        $article = Article::create(array(
            'title'     => 'Premier Article',
            'content'   => 'Contenu du premier article',
        ));
    }
}
