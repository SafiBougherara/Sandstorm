<?php

namespace Controllers;

use PDO;
use Models\CategoryModel;
use Models\ListingModel;

class HomeController extends Controller
{
    private CategoryModel $categoryModel;
    private ListingModel $listingModel;

    public function __construct(PDO $database)
    {
        parent::__construct($database);
        $this->categoryModel = new CategoryModel($database);
        $this->listingModel = new ListingModel($database);
    }

    public function index()
    {
        $categories = $this->categoryModel->getAllWithCounts();
        $recentListings = $this->listingModel->getRecentListings(8); // Get 8 most recent listings

        $data = [
            "title" => "sandstorm 🌪️ - Find What You Need",
            "categories" => $categories,
            "recent_items" => $recentListings
        ];

        $this->render("home.html.twig", $data);
    }
}
