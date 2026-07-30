<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Post
{
    private const SELECT = "SELECT p.*, c.name category_name, c.slug category_slug, parent.name parent_category_name, u.name author_name, GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') tag_names FROM posts p JOIN categories c ON c.id=p.category_id LEFT JOIN categories parent ON parent.id=c.parent_id JOIN users u ON u.id=p.user_id LEFT JOIN post_tags pt ON pt.post_id=p.id LEFT JOIN tags t ON t.id=pt.tag_id";
    public static function published(int $limit=12, ?int $categoryId=null): array { $sql=self::SELECT." WHERE p.status='published' AND p.published_at<=CURRENT_TIMESTAMP".($categoryId?' AND (p.category_id=? OR c.parent_id=?)':'').' GROUP BY p.id ORDER BY p.published_at DESC,p.id DESC LIMIT '.(int)$limit; $s=Database::connection()->prepare($sql); $s->execute($categoryId?[$categoryId,$categoryId]:[]); return $s->fetchAll(); }
    public static function publishedToday(int $limit=10): array { $s=Database::connection()->query("SELECT title,slug,published_at FROM posts WHERE status='published' AND published_at BETWEEN CURRENT_DATE AND CURRENT_TIMESTAMP ORDER BY published_at DESC,id DESC LIMIT ".(int)$limit);return $s->fetchAll(); }
    public static function featured(): ?array { $s=Database::connection()->query(self::SELECT." WHERE p.status='published' AND p.published_at<=CURRENT_TIMESTAMP GROUP BY p.id ORDER BY p.featured DESC,p.published_at DESC LIMIT 1"); return $s->fetch() ?: null; }
    public static function findBySlug(string $slug): ?array { $s=Database::connection()->prepare(self::SELECT." WHERE p.slug=? AND p.status='published' GROUP BY p.id LIMIT 1");$s->execute([$slug]);return $s->fetch()?:null; }
    public static function search(string $query, int $limit=60): array {
        $terms=array_values(array_filter(preg_split('/\s+/u',trim($query))?:[],fn(string $term):bool=>$term!==''));
        if(!$terms)return [];
        $where=[];$values=[];
        foreach($terms as $term){
            $like='%'.str_replace(['\\','%','_'],['\\\\','\\%','\\_'],$term).'%';
            $where[]="(p.title LIKE ? ESCAPE '\\\\' OR p.excerpt LIKE ? ESCAPE '\\\\' OR p.body LIKE ? ESCAPE '\\\\' OR c.name LIKE ? ESCAPE '\\\\' OR EXISTS (SELECT 1 FROM post_tags search_pt JOIN tags search_t ON search_t.id=search_pt.tag_id WHERE search_pt.post_id=p.id AND search_t.name LIKE ? ESCAPE '\\\\'))";
            array_push($values,$like,$like,$like,$like,$like);
        }
        $sql=self::SELECT." WHERE p.status='published' AND p.published_at<=CURRENT_TIMESTAMP AND ".implode(' AND ',$where).' GROUP BY p.id ORDER BY p.published_at DESC,p.id DESC LIMIT '.max(1,min(100,$limit));
        $s=Database::connection()->prepare($sql);$s->execute($values);return $s->fetchAll();
    }
    public static function allAdmin(?int $categoryId=null): array { $sql=self::SELECT.($categoryId?' WHERE p.category_id=? OR c.parent_id=?':'').' GROUP BY p.id ORDER BY p.created_at DESC';$s=Database::connection()->prepare($sql);$s->execute($categoryId?[$categoryId,$categoryId]:[]);return $s->fetchAll(); }
    public static function find(int $id): ?array { $s=Database::connection()->prepare("SELECT p.*, GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') tags FROM posts p LEFT JOIN post_tags pt ON pt.post_id=p.id LEFT JOIN tags t ON t.id=pt.tag_id WHERE p.id=? GROUP BY p.id");$s->execute([$id]);return $s->fetch()?:null; }
    public static function save(array $d, ?int $id=null): void {
        $db=Database::connection(); $values=[$d['category_id'],$d['user_id'],$d['title'],$d['slug'],$d['excerpt'],$d['body'],$d['image'],$d['status'],$d['featured'],$d['published_at']];
        if($id){$values[]=$id;$sql='UPDATE posts SET category_id=?,user_id=?,title=?,slug=?,excerpt=?,body=?,image=?,status=?,featured=?,published_at=? WHERE id=?';}else{$sql='INSERT INTO posts(category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at) VALUES(?,?,?,?,?,?,?,?,?,?)';}
        $db->prepare($sql)->execute($values); $postId=$id??(int)$db->lastInsertId(); $db->prepare('DELETE FROM post_tags WHERE post_id=?')->execute([$postId]);
        foreach(array_unique(array_filter(array_map('trim',explode(',',(string)($d['tags']??''))))) as $tag){$slug=self::tagSlug($tag);if($slug==='')continue;$db->prepare('INSERT INTO tags(name,slug) VALUES(?,?) ON DUPLICATE KEY UPDATE name=VALUES(name)')->execute([mb_substr($tag,0,80),$slug]);$s=$db->prepare('SELECT id FROM tags WHERE slug=?');$s->execute([$slug]);$db->prepare('INSERT IGNORE INTO post_tags(post_id,tag_id) VALUES(?,?)')->execute([$postId,(int)$s->fetchColumn()]);}
    }
    public static function delete(int $id): void { Database::connection()->prepare('DELETE FROM posts WHERE id=?')->execute([$id]); }
    private static function tagSlug(string $text): string { $ascii=iconv('UTF-8','ASCII//TRANSLIT',$text)?:$text;return trim((string)preg_replace('/[^a-z0-9]+/','-',strtolower($ascii)),'-'); }
}
