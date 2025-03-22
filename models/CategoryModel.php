<?php
namespace Models;

use PDO;

class CategoryModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'categories');
    }

    /**
     * Get all categories with their counts
     */
    public function getAllWithCounts(): array
    {
        $query = "SELECT c.*, COUNT(l.id) as count 
                 FROM categories c 
                 LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active'
                 GROUP BY c.id 
                 ORDER BY c.name";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get category by slug
     */
    public function getBySlug(string $slug, bool $withCount = false): ?object
    {
        if ($withCount) {
            $query = "SELECT c.*, COUNT(l.id) as count 
                     FROM categories c 
                     LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active'
                     WHERE c.slug = :slug
                     GROUP BY c.id";
        } else {
            $query = "SELECT * FROM categories WHERE slug = :slug";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Get subcategories of a category
     */
    public function getSubcategories(int $parentId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id = :parent_id ORDER BY name");
        $stmt->execute(['parent_id' => $parentId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
