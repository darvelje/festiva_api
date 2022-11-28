<?php


namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewBusinessRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Shop;
use App\Models\ShopProduct;
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
    //section Get_Order
    public function getOrders(){

        $orders = Order::with('user', 'shop', 'currency', 'orderProducts', 'orderProducts.shopProduct', 'orderProducts.shopProduct.shopProductPhotos', 'userAddress' ,'userAddress.locality', 'userAddress.locality.municipality',  'userAddress.locality.municipality.province')->get();

        if($orders){
            foreach($orders as $order){

                $order->products = $order->orderProducts;
                $order->currency_code = $order->currency->code;

                foreach($order->products as $product){

                    $product->product_id = $product->shopProduct->id;
                    $product->name = $product->shopProduct->name;
                    foreach($product->shopProduct->shopProductPhotos as $photo){
                        $product->photo = $photo->path_photo;
                        break;
                    }

                    unset($product->id);
                    unset($product->order_id);
                    unset($product->shop_product_id);
                    unset($product->created_at);
                    unset($product->updated_at);
                    unset($product->shopProduct);


                }

                $order->deliver_address = $order->userAddress;

                unset($order->deliver_address->user_id);
                unset($order->deliver_address->created_at);
                unset($order->deliver_address->updated_at);

                $order->deliver_address->locality_name = $order->deliver_address->locality->name;

                $order->deliver_address->municipalitie_id = $order->deliver_address->locality->municipalitie_id;
                $order->deliver_address->municipalitie_name = $order->deliver_address->locality->municipality->name;
                $order->deliver_address->province_id = $order->deliver_address->locality->municipality->province_id;
                $order->deliver_address->province_name = $order->deliver_address->locality->municipality->province->name;

                unset($order->deliver_address->locality);

                unset($order->created_at);
                unset($order->updated_at);
                unset($order->user_id);
                unset($order->shop_id);

                unset($order->user->created_at);
                unset($order->user->updated_at);
                unset($order->user->email_verified_at);
                unset($order->user->password);
                unset($order->currency);

                unset($order->shop->created_at);
                unset($order->shop->updated_at);
                unset($order->shop->description);
                unset($order->shop->cover);
                unset($order->shop->avatar);
                unset($order->shop->facebook_link);
                unset($order->shop->instagram_link);
                unset($order->shop->twitter_link);
                unset($order->shop->wa_link);
                unset($order->shop->telegram_link);
                unset($order->shop->user_id);
                unset($order->shop->comission);

                unset($order->orderProducts);
                unset($order->userAddress);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Orders',
                    'orders' => $orders
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Orders',
                'orders' => $orders
            ]
        );
    }

    //section Get_Order_By_Status
    public function getOrdersByStatus(){

        $orders = Order::with('user', 'shop', 'currency', 'orderProducts', 'orderProducts.shopProduct', 'orderProducts.shopProduct.shopProductPhotos', 'userAddress' ,'userAddress.locality', 'userAddress.locality.municipality',  'userAddress.locality.municipality.province')->get();

        $pendingStatusTemp = [];
        $activeStatusTemp = [];
        $completeStatusTemp = [];
        if($orders){
            foreach($orders as $order){

                $order->products = $order->orderProducts;
                $order->currency_code = $order->currency->code;

                foreach($order->products as $product){

                    $product->product_id = $product->shopProduct->id;
                    $product->name = $product->shopProduct->name;
                    foreach($product->shopProduct->shopProductPhotos as $photo){
                        $product->photo = $photo->path_photo;
                        break;
                    }

                    unset($product->id);
                    unset($product->order_id);
                    unset($product->shop_product_id);
                    unset($product->created_at);
                    unset($product->updated_at);
                    unset($product->shopProduct);


                }

                $order->deliver_address = $order->userAddress;

                unset($order->deliver_address->user_id);
                unset($order->deliver_address->created_at);
                unset($order->deliver_address->updated_at);

                $order->deliver_address->locality_name = $order->deliver_address->locality->name;

                $order->deliver_address->municipalitie_id = $order->deliver_address->locality->municipalitie_id;
                $order->deliver_address->municipalitie_name = $order->deliver_address->locality->municipality->name;
                $order->deliver_address->province_id = $order->deliver_address->locality->municipality->province_id;
                $order->deliver_address->province_name = $order->deliver_address->locality->municipality->province->name;

                unset($order->deliver_address->locality);

                unset($order->created_at);
                unset($order->updated_at);
                unset($order->user_id);
                unset($order->shop_id);

                unset($order->user->created_at);
                unset($order->user->updated_at);
                unset($order->user->email_verified_at);
                unset($order->user->password);
                unset($order->currency);

                unset($order->shop->created_at);
                unset($order->shop->updated_at);
                unset($order->shop->description);
                unset($order->shop->cover);
                unset($order->shop->avatar);
                unset($order->shop->facebook_link);
                unset($order->shop->instagram_link);
                unset($order->shop->twitter_link);
                unset($order->shop->wa_link);
                unset($order->shop->telegram_link);
                unset($order->shop->user_id);
                unset($order->shop->comission);

                unset($order->orderProducts);
                unset($order->userAddress);

                if($order->status_payment === 1){
                    array_push($pendingStatusTemp, $order);
                }
                else if($order->status_payment === 2){
                    array_push($activeStatusTemp, $order);
                }
                else if($order->status_payment === 3){
                    array_push($completeStatusTemp, $order);
                }

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Orders by status',
                    'orders_pending' => $pendingStatusTemp,
                    'orders_active' => $activeStatusTemp,
                    'orders_completed' => $completeStatusTemp
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Orders',
                'orders' => $orders
            ]
        );
    }

    //section Get_OrderByUser
    public function getOrdersByUser(Request $request){

        $userDb = $request->user();

        $user = User::with('orders', 'orders.currency', 'orders.orderProducts', 'orders.orderProducts.shopProduct', 'orders.orderProducts.shopProduct.shopProductPhotos', 'orders.userAddress' ,'orders.userAddress.locality', 'orders.userAddress.locality.municipality',  'orders.userAddress.locality.municipality.province')->whereId($userDb->id)->first();

        if($user){

            $orders = $user->orders;

            foreach($orders as $order){
                unset($order->shop_id);
                unset($order->user_id);
                unset($order->created_at);
                unset($order->updated_at);
                unset($order->user_address_id);

                $order->currency_code = $order->currency->code;

                $order->products = $order->orderProducts;

                foreach($order->products as $product){

                    $product->product_id = $product->shopProduct->id;
                    $product->name = $product->shopProduct->name;

                    foreach($product->shopProduct->shopProductPhotos as $photo){
                        $product->photo = $photo->path_photo;
                        break;
                    }

                    unset($product->id);
                    unset($product->order_id);
                    unset($product->shop_product_id);
                    unset($product->created_at);
                    unset($product->updated_at);
                    unset($product->shopProduct);

                }

                unset($order->orderProducts);
                unset($order->currency);

                $order->deliver_address = $order->userAddress;

                unset($order->deliver_address->user_id);
                unset($order->deliver_address->created_at);
                unset($order->deliver_address->updated_at);

                $order->deliver_address->locality_name = $order->deliver_address->locality->name;

                $order->deliver_address->municipalitie_id = $order->deliver_address->locality->municipalitie_id;
                $order->deliver_address->municipalitie_name = $order->deliver_address->locality->municipality->name;
                $order->deliver_address->province_id = $order->deliver_address->locality->municipality->province_id;
                $order->deliver_address->province_name = $order->deliver_address->locality->municipality->province->name;

                unset($order->deliver_address->locality);
                unset($order->userAddress);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Order',
                    'order' => $orders
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'There are no order for that user'
            ]
        );


    }

    //section Get_OrderByBusinessSlug
    public function getOrdersByBusinessSlug(Request $request){

        $shop = Shop::with('orders', 'orders.orderProducts', 'orders.orderProducts.shopProduct', 'orders.userAddress' ,'orders.userAddress.locality', 'orders.userAddress.locality.municipality',  'orders.userAddress.locality.municipality.province')->whereSlug($request->businessSlug)->first();

        if($shop){
            $orders = $shop->orders;

            foreach($orders as $order){
                unset($order->shop_id);
                unset($order->user_id);
                unset($order->created_at);
                unset($order->updated_at);
                unset($order->user_address_id);


                $order->products = $order->orderProducts;

                foreach($order->products as $product){

                    $product->product_id = $product->shopProduct->id;
                    $product->name = $product->shopProduct->name;

                    unset($product->id);
                    unset($product->order_id);
                    unset($product->shop_product_id);
                    unset($product->created_at);
                    unset($product->updated_at);
                    unset($product->shopProduct);

                }

                unset($order->orderProducts);

                $order->deliver_address = $order->userAddress;

                unset($order->deliver_address->user_id);
                unset($order->deliver_address->created_at);
                unset($order->deliver_address->updated_at);

                $order->deliver_address->locality_name = $order->deliver_address->locality->name;

                $order->deliver_address->municipalitie_id = $order->deliver_address->locality->municipalitie_id;
                $order->deliver_address->municipalitie_name = $order->deliver_address->locality->municipality->name;
                $order->deliver_address->province_id = $order->deliver_address->locality->municipality->province_id;
                $order->deliver_address->province_name = $order->deliver_address->locality->municipality->province->name;

                unset($order->deliver_address->locality);
                unset($order->userAddress);

            }

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Order',
                    'order' => $orders
                ]
            );
        }

        return response()->json(
            [
                'code' => 'error',
                'message' => 'There are no order for that business'
            ]
        );


    }

    //section Get_OrderById
    public function getOrderById(Request $request){

        $order = Order::with('user', 'shop', 'orderProducts', 'orderProducts.shopProduct', 'userAddress' ,'userAddress.locality', 'userAddress.locality.municipality',  'userAddress.locality.municipality.province')->whereId($request->orderId)->first();

        if($order){
            $order->products = $order->orderProducts;

            foreach($order->products as $product){

                $product->product_id = $product->shopProduct->id;
                $product->name = $product->shopProduct->name;

                unset($product->id);
                unset($product->order_id);
                unset($product->shop_product_id);
                unset($product->created_at);
                unset($product->updated_at);
                unset($product->shopProduct);

            }

            $order->deliver_address = $order->userAddress;

            unset($order->deliver_address->user_id);
            unset($order->deliver_address->created_at);
            unset($order->deliver_address->updated_at);

            $order->deliver_address->locality_name = $order->deliver_address->locality->name;

            $order->deliver_address->municipalitie_id = $order->deliver_address->locality->municipalitie_id;
            $order->deliver_address->municipalitie_name = $order->deliver_address->locality->municipality->name;
            $order->deliver_address->province_id = $order->deliver_address->locality->municipality->province_id;
            $order->deliver_address->province_name = $order->deliver_address->locality->municipality->province->name;

            unset($order->deliver_address->locality);

            unset($order->created_at);
            unset($order->updated_at);
            unset($order->user_id);
            unset($order->shop_id);

            unset($order->user->created_at);
            unset($order->user->updated_at);
            unset($order->user->email_verified_at);
            unset($order->user->password);

            unset($order->shop->created_at);
            unset($order->shop->updated_at);
            unset($order->shop->description);
            unset($order->shop->cover);
            unset($order->shop->avatar);
            unset($order->shop->facebook_link);
            unset($order->shop->instagram_link);
            unset($order->shop->twitter_link);
            unset($order->shop->wa_link);
            unset($order->shop->telegram_link);
            unset($order->shop->user_id);
            unset($order->shop->comission);

            unset($order->orderProducts);
            unset($order->userAddress);


            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Order',
                    'order' => $order
                ]
            );
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Order not found'
            ]
        );

    }

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

    // section Delete_Order
    public function deleteOrder(Request $request){
        try {
            DB::beginTransaction();

            $result = Order::whereId($request->orderId)->delete();

            DB::commit();

            if($result){
                return response()->json(
                    [
                        'code' => 'ok',
                        'message' => 'Order deleted successfully'
                    ]
                );
            }

            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Order not found'
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
