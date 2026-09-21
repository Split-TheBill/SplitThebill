<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BookingService;
use App\Services\FrontService;

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

        $amounts = BookingService::calculateAmounts((int) $product->price_per_person);
        $totalPpn = $amounts['admin_fee'];
        $grandTotal = $amounts['total'];

        return view('front.details', compact('product', 'grandTotal', 'totalPpn'));
    }
}
