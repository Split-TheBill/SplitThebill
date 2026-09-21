<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreCheckBookingRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Services\BookingService;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    //

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function booking(Product $product)
    {
        $amounts = BookingService::calculateAmounts((int) $product->price_per_person);
        $totalTaxAmount = $amounts['admin_fee'];
        $grandTotalAmount = $amounts['total'];

        return view('booking.booking', compact('product', 'totalTaxAmount', 'grandTotalAmount'));
    }

    public function bookingStore(Product $product, StoreBookingRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->bookingService->storeBookingInSession($product, $validated);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Data pesanan gagal disimpan. Silakan coba lagi.']);
        }

        return redirect()->route('front.payment');
    }

    public function payment()
    {

        $data = $this->bookingService->payment();

        if (! $data) {
            return redirect()
                ->route('front.index')
                ->withErrors(['error' => 'Sesi pemesanan telah berakhir. Silakan pilih kembali layananmu.']);
        }

        return view('booking.payment', $data);
    }

    public function paymentStore(StorePaymentRequest $request)
    {
        $validated = $request->validated();
        $bookingTransactionId = $this->bookingService->paymentStore($validated);

        if ($bookingTransactionId) {
            return redirect()->to(URL::temporarySignedRoute(
                'front.booking_finished',
                now()->addMinutes(30),
                ['productSubscription' => $bookingTransactionId],
            ));
        }

        return redirect()->route('front.index')->withErrors(['error' => 'Pembayaran gagal dikirim. Silakan coba lagi.']);
    }

    public function bookingFinished(ProductSubscription $productSubscription)
    {
        return view('booking.booking_finished', compact('productSubscription'));
    }

    public function checkBooking()
    {
        return view('booking.check_booking');
    }

    public function checkBookingDetails(StoreCheckBookingRequest $request)
    {
        $validated = $request->validated();

        $bookingData = $this->bookingService->getBookingDetailsWithGroupAndCapacity($validated);

        if ($bookingData) {
            return view('booking.check_booking_details', $bookingData);
        }

        return redirect()->route('front.check_booking')->withErrors([
            'error' => 'Pesanan tidak ditemukan. Periksa kembali kode booking dan nomor WhatsApp.',
        ]);
    }
}
