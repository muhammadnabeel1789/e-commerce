<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category); 
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        $products = $query->with(['images', 'category', 'variants'])->paginate(12);
        
        $categories = Category::withCount(['products' => function($q) {
                $q->where('is_active', true); 
            }])->get();
            
        $brands = Brand::all(); 
        $totalProducts = Product::where('is_active', true)->count();

        $hasFilters = $request->filled('category') || $request->filled('brand') || $request->filled('search');

        return view('customer.products.index', compact('products', 'categories', 'brands', 'totalProducts', 'hasFilters'));
    }

    public function show($id)
    {
        // Tambah variants.image agar foto per varian bisa ditampilkan
        $product = Product::with([
                'images', 'category', 'brand', 'variants.image',
                'reviews' => fn($q) => $q->where('is_approved', true)->with(['user', 'images']),
            ])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
      
        $relatedProducts = Product::with(['images', 'variants'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:user_addresses,id',
            'courier' => 'required|in:jne,jnt,sicepat',
            'payment_method' => 'required|in:cod,bank_transfer,ewallet',
        ]);

        $user = Auth::user();
        
        $orderItemsData = [];
        $subtotal = 0;
        $isDirectBuy = false; 

        if (Session::has('direct_buy')) {
            $isDirectBuy = true;
            $sessionData = Session::get('direct_buy');
            
            $product = Product::findOrFail($sessionData['product_id']);
            $variantId = $sessionData['variant_id'] ?? null;
            $variant = null;
            $price = $product->hasDiscount() ? $product->discount_price : $product->getRawOriginal('price');

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $price += $variant->additional_price;
                }
            }

            $itemObj = new \stdClass();
            $itemObj->product_id = $product->id;
            $itemObj->product_variant_id = $variantId;
            $itemObj->product = $product;
            $itemObj->variant = $variant;
            $itemObj->quantity = $sessionData['quantity'];
            $itemObj->price = $price;
            
            $orderItemsData[] = $itemObj;
            $subtotal = $price * $sessionData['quantity'];

        } else {
            $cart = Cart::where('user_id', $user->id)
                ->with(['items.product', 'items.variant'])
                ->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
            }
            
            $orderItemsData = $cart->items;
            
            foreach ($cart->items as $item) {
                $basePrice = $item->product->hasDiscount() ? $item->product->discount_price : $item->product->getRawOriginal('price');
                $itemPrice = $basePrice + ($item->variant->additional_price ?? 0);
                $subtotal += $itemPrice * $item->quantity;
            }
        }

        $address = $user->addresses()->find($request->shipping_address_id);

        $tokoCity     = 'Jakarta Pusat'; 
        $tokoProvince = 'DKI Jakarta';   
        
        $baseOngkir = 0;
        if (strcasecmp($address->city, $tokoCity) == 0) {
            $baseOngkir = 9000; 
        } elseif (strcasecmp($address->province, $tokoProvince) == 0) {
            $baseOngkir = 15000;
        } else {
            $baseOngkir = 35000; 
        }

        $courierName = '';
        $shippingCost = 0;

        switch ($request->courier) {
            case 'jne': $courierName = 'JNE Reguler'; $shippingCost = $baseOngkir; break;
            case 'jnt': $courierName = 'J&T Express'; $shippingCost = $baseOngkir + 2000; break;
            case 'sicepat': $courierName = 'SiCepat REG'; $shippingCost = $baseOngkir - 1000; break;
        }

        $total = $subtotal + $shippingCost;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                
                'recipient_name' => $address->recipient_name ?? $user->name,
                'recipient_phone' => $address->phone,
                'shipping_address' => $address->address,
                'province' => $address->province,
                'city' => $address->city,
                'district' => $address->district,
                'postal_code' => $address->postal_code,
                'courier_name' => $courierName, 
            ]);

            foreach ($orderItemsData as $item) {
                $variantInfo = '';
                if (isset($item->variant) && $item->variant) {
                    $variantInfo = trim(($item->variant->color ?? '') . ' ' . ($item->variant->size ?? ''));
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id ?? null,
                    'product_name' => $item->product->name,
                    'variant_info' => $variantInfo,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                }
                
                if (isset($item->product_variant_id) && $item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->decrement('stock', $item->quantity);
                    }
                }
            }

            if ($request->payment_method != 'cod') {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number,
                        'gross_amount' => (int) $total,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $address->phone,
                    ],
                ];

                if ($request->payment_method == 'bank_transfer') {
                    $params['enabled_payments'] = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'];
                } elseif ($request->payment_method == 'ewallet') {
                    $params['enabled_payments'] = ['gopay', 'shopeepay', 'qris'];
                }

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->snap_token = $snapToken;
                $order->save();
            }

            if ($isDirectBuy) {
                Session::forget('direct_buy');
            } else {
                if (isset($cart)) {
                    $cart->items()->delete();
                }
            }

            DB::commit();

            if ($request->payment_method == 'cod') {
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silahkan siapkan uang pas untuk COD.');
            } else {
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silahkan selesaikan pembayaran.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}