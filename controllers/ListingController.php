<?php
namespace Controllers;

use Models\ListingModel;
use Models\CategoryModel;

class ListingController extends Controller
{
    private ListingModel $listingModel;
    private CategoryModel $categoryModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->listingModel = new ListingModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    /**
     * Browse and search listings
     */
    public function browse()
    {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $sort = $_GET['sort'] ?? 'newest';
        
        // Search and filter parameters
        $query = $_GET['q'] ?? '';
        $categoryId = !empty($_GET['category']) ? intval($_GET['category']) : null;
        $minPrice = !empty($_GET['min_price']) ? floatval($_GET['min_price']) : null;
        $maxPrice = !empty($_GET['max_price']) ? floatval($_GET['max_price']) : null;

        // Get listings with search/filters
        $listings = $this->listingModel->searchListings($query, $categoryId, $minPrice, $maxPrice, $page, 12, $sort);
        $categories = $this->categoryModel->getAllWithCounts();

        $data = [
            "title" => empty($query) ? "Browse Listings" : "Search Results for '{$query}'",
            "listings" => $listings['items'],
            "total_pages" => $listings['total_pages'],
            "current_page" => $page,
            "categories" => $categories,
            "sort" => $sort,
            "search" => [
                "query" => $query,
                "category_id" => $categoryId,
                "min_price" => $minPrice,
                "max_price" => $maxPrice
            ]
        ];

        $this->render("listing/browse.html.twig", $data);
    }

    /**
     * View listing details
     */
    public function view(int $id)
    {
        $listing = $this->listingModel->getListingDetails($id);
        
        if (!$listing) {
            header('HTTP/1.0 404 Not Found');
            echo 'Listing not found';
            exit;
        }

        $data = [
            "title" => $listing->title,
            "listing" => $listing
        ];

        $this->render("listing/view.html.twig", $data);
    }

    /**
     * Display listing creation form
     */
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        $categories = $this->categoryModel->getAllWithCounts();
        
        $data = [
            "title" => "Create Listing",
            "categories" => $categories
        ];

        $this->render("listing/create.html.twig", $data);
    }

    /**
     * Handle listing creation
     */
    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $location = $_POST['location'] ?? '';
            $categoryId = $_POST['category_id'] ?? '';

            $errors = [];
            if (empty($title)) $errors[] = "Title is required";
            if (empty($description)) $errors[] = "Description is required";
            if (empty($price) || !is_numeric($price)) $errors[] = "Valid price is required";
            if (empty($location)) $errors[] = "Location is required";
            if (empty($categoryId)) $errors[] = "Category is required";

            if (empty($errors)) {
                $images = [];
                if (!empty($_FILES['images'])) {
                    $uploadDir = 'uploads/listings/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $filename = uniqid() . '_' . $_FILES['images']['name'][$key];
                            $filepath = $uploadDir . $filename;
                            
                            if (move_uploaded_file($tmp_name, $filepath)) {
                                $images[] = $filepath;
                            }
                        }
                    }
                }

                try {
                    $listingId = $this->listingModel->createListing([
                        'title' => $title,
                        'description' => $description,
                        'price' => $price,
                        'location' => $location,
                        'category_id' => $categoryId,
                        'user_id' => $_SESSION['user_id']
                    ], $images);

                    if ($listingId) {
                        header('Location: /Sandstorm/listing/' . $listingId);
                        exit;
                    } else {
                        $errors[] = "Failed to create listing";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error creating listing: " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                $data = [
                    "title" => "Create Listing",
                    "categories" => $this->categoryModel->getAllWithCounts(),
                    "errors" => $errors,
                    "old" => $_POST
                ];
                $this->render("listing/create.html.twig", $data);
                return;
            }
        }

        header('Location: /Sandstorm/sell');
        exit;
    }
}
