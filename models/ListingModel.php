<?php
namespace Models;

use PDO;

class ListingModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'listings');
    }

    /**
     * Create a new listing with images
     */
    public function createListing(array $data, array $images = []): ?int
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (title, description, price, location, user_id, category_id, status) 
                                      VALUES (:title, :description, :price, :location, :user_id, :category_id, :status)");
            
            $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':price' => $data['price'],
                ':location' => $data['location'],
                ':user_id' => $data['user_id'],
                ':category_id' => $data['category_id'],
                ':status' => $data['status'] ?? 'active'
            ]);

            $listingId = $this->db->lastInsertId();

            if ($listingId && !empty($images)) {
                $stmt = $this->db->prepare("INSERT INTO listing_images (listing_id, image_path, is_primary) VALUES (:listing_id, :image_path, :is_primary)");
                foreach ($images as $index => $image) {
                    $stmt->execute([
                        ':listing_id' => $listingId,
                        ':image_path' => $image,
                        ':is_primary' => $index === 0 ? 1 : 0
                    ]);
                }
            }

            $this->db->commit();
            return $listingId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get all listings with pagination and sorting
     */
    public function getAllListings(int $page = 1, int $perPage = 12, string $sort = 'newest'): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Count total records for pagination
        $countStmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'active'");
        $total = $countStmt->fetchColumn();
        
        // Determine sort order
        $orderBy = match($sort) {
            'price_low' => 'l.price ASC',
            'price_high' => 'l.price DESC',
            default => 'l.created_at DESC'
        };

        $query = "SELECT l.*, u.username, c.name as category_name, c.slug as category_slug,
                        (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as image
                 FROM {$this->table} l
                 JOIN users u ON l.user_id = u.id
                 JOIN categories c ON l.category_id = c.id
                 WHERE l.status = 'active'
                 ORDER BY {$orderBy}
                 LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_OBJ),
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get recent listings with their primary images
     */
    public function getRecentListings(int $limit = 8): array
    {
        $query = "SELECT l.*, u.username, c.name as category_name, c.slug as category_slug,
                        (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as image
                 FROM {$this->table} l
                 JOIN users u ON l.user_id = u.id
                 JOIN categories c ON l.category_id = c.id
                 WHERE l.status = 'active'
                 ORDER BY l.created_at DESC
                 LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Search listings with filtering and pagination
     */
    public function searchListings(string $query, ?int $categoryId = null, ?float $minPrice = null, ?float $maxPrice = null, int $page = 1, int $perPage = 12, string $sort = 'newest'): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = ["l.status = 'active'"];

        if (!empty($query)) {
            $conditions[] = "(l.title LIKE :query OR l.description LIKE :query)";
            $params[':query'] = "%{$query}%";
        }

        if ($categoryId) {
            $conditions[] = "l.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($minPrice !== null) {
            $conditions[] = "l.price >= :min_price";
            $params[':min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $conditions[] = "l.price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        // Count total results
        $countQuery = "SELECT COUNT(*) FROM {$this->table} l WHERE " . implode(' AND ', $conditions);
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        // Determine sort order
        $orderBy = match($sort) {
            'price_low' => 'l.price ASC',
            'price_high' => 'l.price DESC',
            default => 'l.created_at DESC'
        };

        // Main query
        $query = "SELECT l.*, u.username, c.name as category_name, c.slug as category_slug,
                        (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as image
                 FROM {$this->table} l
                 JOIN users u ON l.user_id = u.id
                 JOIN categories c ON l.category_id = c.id
                 WHERE " . implode(' AND ', $conditions) . "
                 ORDER BY {$orderBy}
                 LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_OBJ),
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get listing details with images and seller info
     */
    public function getListingDetails(int $id): ?object
    {
        $query = "SELECT l.*, u.username, u.created_at as user_created_at,
                        c.name as category_name, c.slug as category_slug,
                        (SELECT COUNT(*) FROM listings WHERE user_id = l.user_id AND status = 'active') as seller_listing_count,
                        (SELECT ROUND(AVG(rating), 1) FROM reviews WHERE reviewed_id = l.user_id) as seller_rating
                 FROM {$this->table} l
                 JOIN users u ON l.user_id = u.id
                 JOIN categories c ON l.category_id = c.id
                 WHERE l.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $listing = $stmt->fetch(PDO::FETCH_OBJ);

        if ($listing) {
            // Get all images
            $imageStmt = $this->db->prepare("SELECT * FROM listing_images WHERE listing_id = :listing_id ORDER BY is_primary DESC");
            $imageStmt->execute([':listing_id' => $id]);
            $listing->images = $imageStmt->fetchAll(PDO::FETCH_OBJ);
        }

        return $listing;
    }

    /**
     * Get user's listings
     */
    public function getUserListings(int $userId, string $status = 'active'): array
    {
        $query = "SELECT l.*, c.name as category_name,
                        (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as image
                 FROM {$this->table} l
                 JOIN categories c ON l.category_id = c.id
                 WHERE l.user_id = :user_id";
        
        if ($status !== 'all') {
            $query .= " AND l.status = :status";
        }
        
        $query .= " ORDER BY l.created_at DESC";

        $stmt = $this->db->prepare($query);
        $params = ['user_id' => $userId];
        if ($status !== 'all') {
            $params['status'] = $status;
        }
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
