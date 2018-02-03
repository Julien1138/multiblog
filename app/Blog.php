<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Hyn\Tenancy\Traits\UsesSystemConnection;

use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class Blog extends Model
{
    use UsesSystemConnection;

    protected $fillable = ['name', 'slug', 'website_id'];

    public static function create($data){

        // On crée un slug à partir du nom du nouveau blog
        $slug = str_slug($data['name']);

        // On crée le site web
        $website = new Website;
        $website->uuid = config('app.name') . "_" . $slug;
        app(WebsiteRepository::class)->create($website);

        // On crée l'url du nouveau site web et on l'attache à ce dernier
        $hostname = new Hostname;
        $hostname->fqdn =  $slug . '.' . config('tenancy.hostname.default');
        app(HostnameRepository::class)->attach($hostname, $website);

        // On inscrit le nouveau blog dans la base de donnée
        return static::query()->create(array_merge($data, ['slug' => $slug, 'website_id' => $website->id]));
    }

    public function website()
    {
        $website = Website::findOrFail($this->website_id);
        return $website;
    }

    public function url()
    {
        $url = "http://" . $this->website()->hostnames[0]->fqdn;
        return $url;
    }
}
