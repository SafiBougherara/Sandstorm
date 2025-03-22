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
        $category = $this->categoryModel->getBySlug($slug, true);
        if (!$category) {
            header('HTTP/1.0 404 Not Found');
            // TODO: Create 404 template
            exit('Category not found');
        }

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $listings = $this->listingModel->getByCategory($category->id, $page);
        
        $data = [
            "title" => $category->name,
            "category" => $category,
            "listings" => $listings,
            "current_page" => $page
        ];

        $this->render("category.html.twig", $data);
    }
}
