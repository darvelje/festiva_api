<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    //section New_Order
    public function newOrder(Request $request){

        try{
            DB::beginTransaction();

            $order = new Order();

            $order->user_id = $request->orderUserId;
            $length = count($request->order);
            for($i = 0; $i < $length; $i++)
            {
                $order->shop_id = $request->order[$i]['idShop'];
                $order->save();
                $idOrder = $order->id;
                $lengthProducts = count($request->order[$i]['products']);
                for($j = 0; $j < $lengthProducts; $j++)
                {
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $idOrder;
                    $orderProduct->shop_product_id = $request->order[$i]['products'][$j]['idProduct'];
                    $orderProduct->amount = $request->order[$i]['products'][$j]['amount'];
                    $orderProduct->save();
                }
            }
            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Order created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

}
