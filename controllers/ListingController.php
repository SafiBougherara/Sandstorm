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
     * Add base URL to image paths
     */
    private function processListingImages($listings)
    {
        if (is_array($listings)) {
            foreach ($listings as $listing) {
                if (isset($listing->image) && $listing->image) {
                    $listing->image = '/Sandstorm/uploads/listings/' . $listing->image;
                }
            }
        } elseif (is_object($listings) && isset($listings->image) && $listings->image) {
            $listings->image = '/Sandstorm/uploads/listings/' . $listings->image;
        }
        return $listings;
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
        
        // Process image paths for each listing
        $listings['items'] = $this->processListingImages($listings['items']);
        
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
     * Search endpoint that redirects to browse with search parameters
     */
    public function search()
    {
        $query = $_GET['q'] ?? '';
        $url = '/Sandstorm/browse?' . http_build_query(['q' => $query]);
        header('Location: ' . $url);
        exit;
    }

    /**
     * View listing details
     */
    public function view($id)
    {
        $listing = $this->listingModel->getListingDetails($id);
        
        if (!$listing) {
            header('Location: /Sandstorm/browse');
            exit;
        }

        // Process images
        if ($listing->images) {
            foreach ($listing->images as $image) {
                $image->image_path = '/Sandstorm/uploads/listings/' . $image->image_path;
            }
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

    /**
     * Display user's listings
     */
    public function myListings()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        $listings = $this->listingModel->getUserListings($_SESSION['user_id']);
        $listings = $this->processListingImages($listings);

        $data = [
            "title" => "My Listings",
            "listings" => $listings
        ];

        $this->render("user/my-listings.html.twig", $data);
    }

    /**
     * Delete a listing
     */
    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        // Get the listing
        $listing = $this->listingModel->getListingDetails($id);
        
        // Check if listing exists and belongs to user
        if (!$listing || $listing->user_id != $_SESSION['user_id']) {
            header('Location: /Sandstorm/my-listings');
            exit;
        }

        // Delete the listing's images from disk
        if ($listing->images) {
            $uploadDir = __DIR__ . '/../public/uploads/listings/';
            foreach ($listing->images as $image) {
                $filePath = $uploadDir . basename($image->image_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // Delete the listing from database
        $this->listingModel->deleteListing($id);

        header('Location: /Sandstorm/my-listings');
        exit;
    }

    /**
     * Display listing edit form
     */
    public function edit($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        // Get the listing
        $listing = $this->listingModel->getListingDetails($id);
        
        // Check if listing exists and belongs to user
        if (!$listing || $listing->user_id != $_SESSION['user_id']) {
            header('Location: /Sandstorm/my-listings');
            exit;
        }

        $categories = $this->categoryModel->getAllWithCounts();
        
        $data = [
            "title" => "Edit Listing",
            "listing" => $listing,
            "categories" => $categories
        ];

        $this->render("listing/edit.html.twig", $data);
    }

    /**
     * Handle listing update
     */
    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        // Get the listing
        $listing = $this->listingModel->getListingDetails($id);
        
        // Check if listing exists and belongs to user
        if (!$listing || $listing->user_id != $_SESSION['user_id']) {
            header('Location: /Sandstorm/my-listings');
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
                    $success = $this->listingModel->updateListing($id, [
                        'title' => $title,
                        'description' => $description,
                        'price' => $price,
                        'location' => $location,
                        'category_id' => $categoryId
                    ], $images);

                    if ($success) {
                        header('Location: /Sandstorm/listing/' . $id);
                        exit;
                    } else {
                        $errors[] = "Failed to update listing";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error updating listing: " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                $data = [
                    "title" => "Edit Listing",
                    "listing" => $listing,
                    "categories" => $this->categoryModel->getAllWithCounts(),
                    "errors" => $errors,
                    "old" => $_POST
                ];
                $this->render("listing/edit.html.twig", $data);
                return;
            }
        }

        header('Location: /Sandstorm/listing/' . $id . '/edit');
        exit;
    }
}
