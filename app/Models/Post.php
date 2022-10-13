<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post
{

    public $title;

    public $exerpt;

    public $date;

    public $body;

    public $slug;

    public function __construct($title, $exerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->exerpt = $exerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function all()
    {
        //To check caching, use cmd "php artisan tinker" and then use "cache('posts.all')", and to clear it use cache()->forget('posts.all')  
        return cache()->rememberForever('posts.all', function () {
            return collect(File::files(resource_path('/posts')))
            //find all of the posts in the post directory, map over them, parse into a document
            ->map(fn ($file) => YamlFrontMatter::parseFile($file))
            //map over documents to use post object
            ->map(fn ($document) => new Post(
                $document->title,
                $document->excerpt,
                $document->date,
                $document->body(),
                $document->slug
            ))
            ->sortbyDesc('date');
        });
        
    }

    public static function find($slug)
    {
        return static::all()->firstWhere('slug', $slug);
    }
}
