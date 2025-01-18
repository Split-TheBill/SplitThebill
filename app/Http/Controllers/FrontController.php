<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //

    protected $frontService;

    public function __construct(FrontService $frontService)
    {
        $this->frontService = $frontService;
    }

    public function index()
    {
        $data = $this->frontService->getFrontPageData();
        return view('front.index', $data);
    }

    public function details(Product $product)
    {

        $ppn = 0.12;
        $price = $product->price_per_person;
        $totalPpn = $price * $ppn;
        $grandTotal = $totalPpn + $price;
        return view('front.details', compact('product', 'grandTotal', 'totalPpn'));
    }
}
