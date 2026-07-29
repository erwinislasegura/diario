<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Controller; use App\Models\Analytics; use App\Models\Category; use App\Models\Post; use App\Models\Setting; use App\Models\Video;
final class SiteController extends Controller
{
    public function home(): void
    {
        $posts = Post::published(28);
        $featured = array_shift($posts);
        $categories = Category::topLevel();
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[$category['slug']] = (int) $category['id'];
        }

        Analytics::record('/', 'home', null, 'Portada');
        $this->render('site/home', [
            'title' => 'La voz de nuestra gente',
            'featured' => $featured,
            'posts' => $posts,
            'communityPosts' => Post::published(6, $categoryIds['comunidad'] ?? 0),
            'businessPosts' => Post::published(6, $categoryIds['guia-local'] ?? 0),
            'eventPosts' => Post::published(6, $categoryIds['eventos'] ?? 0),
            'categories' => $categories,
            'weather' => Setting::weather(),
            'videos' => Video::published(),
        ]);
    }
    public function article(string $slug): void { $post=Post::findBySlug($slug); if(!$post){http_response_code(404);$this->render('site/404',['title'=>'Noticia no encontrada']);return;} Analytics::record('/noticia/'.$slug,'article',(int)$post['id'],$post['title']); $this->render('site/article',['title'=>$post['title'],'post'=>$post,'related'=>Post::published(4,(int)$post['category_id']),'categories'=>Category::topLevel()]); }
    public function video(int $id): void { $video=Video::findPublished($id); if(!$video){http_response_code(404);$this->render('site/404',['title'=>'Video no encontrado','categories'=>Category::topLevel()]);return;} Analytics::record('/video/'.$id,'video',$id,$video['title']); $related=array_values(array_filter(Video::published(5),fn(array $item):bool=>(int)$item['id']!==$id)); $this->render('site/video',['title'=>$video['title'],'video'=>$video,'related'=>array_slice($related,0,3),'categories'=>Category::topLevel()]); }
    public function videos(): void { Analytics::record('/videos','videos',null,'Pulso Angelino TV'); $this->render('site/videos',['title'=>'Pulso Angelino TV','videos'=>Video::published(60),'categories'=>Category::topLevel(),'communes'=>Video::COMMUNES,'formats'=>Video::FORMATS]); }
    public function events(): void { $category=Category::findBySlug('eventos'); Analytics::record('/eventos','events',$category?(int)$category['id']:null,'Agenda y eventos'); $this->render('site/events',['title'=>'Agenda y eventos','category'=>$category,'posts'=>$category?Post::published(60,(int)$category['id']):[],'categories'=>Category::topLevel()]); }
    public function category(string $slug): void {
        if ($slug === 'vecinoss-tv') { $this->videos(); return; }
        if ($slug === 'eventos') { $this->events(); return; }
        $category=Category::findBySlug($slug);
        if(!$category){http_response_code(404);$this->render('site/404',['title'=>'Categoría no encontrada','categories'=>Category::topLevel()]);return;}
        Analytics::record('/categoria/'.$slug,'category',(int)$category['id'],$category['name']);
        $this->render('site/category',['title'=>$category['name'],'category'=>$category,'posts'=>Post::published(24,(int)$category['id']),'categories'=>Category::topLevel()]);
    }
    public function notFound(): void { $this->render('site/404',['title'=>'Página no encontrada','categories'=>Category::topLevel()]); }
}
