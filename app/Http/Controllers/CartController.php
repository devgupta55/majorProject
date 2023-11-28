<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineSlotItem;
use App\Models\Promotion;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request, $id)
    {
        $promotions = Promotion::where('display_on_machine', 1)->get();
        $machine = Machine::with('slots.items')->where('id', $id)->first();
        return view('productsNew', compact('machine', 'promotions'));
    }

    public function cart()
    {
        return view('cart');
    }

    public function addToCart($id)
    {
        $product = MachineSlotItem::findOrFail($id);

        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->title,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart');
        $lessQuantity = false;
        $avialableQuantity = 0;
        $product = '';
        foreach($cart as $id => $details) {
            $quantity = MachineSlotItem::find($id)->quantity;
            if ($quantity < (int)$details['quantity']) {
                $lessQuantity = true;
                $avialableQuantity = $quantity;
                $product = $details['name'];
                break;
            }
        }
        if ($lessQuantity) {
            return redirect()->back()->with('danger',"There is a minimum quantity of $avialableQuantity items for $product. Please check your basket");
        }

        foreach($cart as $id => $details) {
            $quantity = MachineSlotItem::find($id)->quantity;
            MachineSlotItem::where('id', $id)->update([
                'quantity' => $quantity - (int)$details['quantity']
            ]);
        }
        session()->put('cart', []);
        return redirect()->back()->with('success',"Order placed successfully");
    }
}
