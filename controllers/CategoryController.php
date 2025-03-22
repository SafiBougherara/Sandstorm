<?php
namespace Controllers;

use Models\CategoryModel;
use Models\ListingModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;
    private ListingModel $listingModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->categoryModel = new CategoryModel($db);
        $this->listingModel = new ListingModel($db);
    }

    /**
     * Display all categories
     */
    public function index()
    {
        $categories = $this->categoryModel->getAllWithCounts();

        $data = [
            "title" => "Categories",
            "categories" => $categories
        ];

        $this->render("categories.html.twig", $data);
    }

    /**
     * Display listings in a category
     */
    public function view(string $slug)
    {
        $category = $this->categoryModel->getBySlug($slug);
        if (!$category) {
            header('HTTP/1.0 404 Not Found');
            echo 'Category not found';
            exit;
        }

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $sort = $_GET['sort'] ?? 'newest';
        $listings = $this->listingModel->getByCategory($category->id, $page);
        
        $data = [
            "title" => $category->name,
            "category" => $category,
            "listings" => $listings['items'],
            "total_pages" => $listings['total_pages'],
            "current_page" => $page,
            "sort" => $sort
        ];

        $this->render("category.html.twig", $data);
    }
}
