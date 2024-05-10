<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use stdClass;

class CartController extends Controller
{
    private function data() : \Illuminate\Support\Collection
    {
        if (!Session::has('cart')) {
            return collect([]);
        }

        $data = Session::get('cart');

        foreach ($data as $key => $d) {
            $d['item'] = DB::table('product')
                ->where('id', '=', $d['id'])
                ->first();
            $d['subtotal'] = $d['item']->price * $d['total'];

            $data[$key] = $d;
        }

        return collect($data);
    }

    private function dataPush(array $d)
    {
        $data = $this->data();
        if ($data->where('id', '=', $d['id'])->count() > 0)
        {
            foreach ($data as $k => $e) {
                if ($e['id'] == $d['id'])
                {
                    $e['total'] = $e['total'] + $d['total'];
                    // This is hacky, but this works
                    $data[$k] = $e;
                }
            }
        }
        else
        {
            $data[] = $d;
        }
        Session::put('cart', $data);

        return $data;
    }

    private function calculateTotal()
    {
        $total = 0;
        $Datas = $this->data();

        foreach ($Datas as $d) {

            $total +=   $d['subtotal'];
        }

        Session::put('cart', $Datas);

        return $total;

    }

    public function Index()
    {
        $data = $this->data();
        $cart = new stdClass();
        $cart->grandTotal = $this->calculateTotal();

        //dd($data);
        return view('cart', [
            "data" => $data,
            "cart" => $cart
        ]);
    }

    public function CartAddAction(Request $request,int $id)
    {
        $quantity =  $request->input('quantity',1);
        $d = DB::table('product')
            ->where('id', '=', $id)
            ->first();

        if ($d == null) return \response()
            ->json([
                "statusCode" => 404,
                "message" => "Item not found!"
            ]);

        $this->dataPush([
            'id' => $id,
            'total' => $quantity
        ]);

        //return redirect('/')->with('success','Item successfuly added !');

        return \response()->json([
            'statusCode' => 201,
            "message" => "Item added!"
        ]);

    }

    public function CartAddActionFromProduct(Request $request,int $id)
    {
        $quantity =  $request->input('quantity',1);
        $d = DB::table('product')
            ->where('id', '=', $id)
            ->first();

        if ($d == null) return \response()
            ->json([
                "statusCode" => 404,
                "message" => "Item not found!"
            ]);

        $this->dataPush([
            'id' => $id,
            'total' => $quantity
        ]);

        return redirect('/')->with('success','Item successfuly added !');
    }

    public function DeleteCartAction(int $id)
    {
        $data = $this->data();
        $key = $data->search(function ($item) use ($id) {
            return $item['id'] == $id;
        });

        if ($key !== false) {
            // Remove the item from the cart
            $data->forget($key);
            // Update the session cart data
            Session::put('cart', $data);

            // Return a JSON response indicating success
            // return response()->json([
            //     'statusCode' => 200,
            //     "message" => "Item deleted from cart!"
            // ]);
            return redirect('/cart');
        }


    }
}
