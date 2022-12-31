<?php

namespace App\Http\Controllers;

use App\Models\orders;
use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    //
    public function index(){
//         $order = orders::paginate(10);
//         $order = orders::paginate(10);
        $sql="orders.*, users.first_name, users.last_name, ";
        $sql.="(case when user_category.category_id = '1' then users.referred_by else '' end) as distributor_id, order_items.qantity, products.price ";
//        DB::enableQueryLog();
        $order = DB::table('orders')
                    ->selectRaw($sql)
                    ->leftJoin('users','orders.purchaser_id','=','users.id')
                    ->leftJoin('order_items','orders.id','=','order_items.order_id')
                    ->leftJoin('user_category','users.referred_by','=','user_category.user_id')
                    ->leftJoin('products','order_items.product_id','=','products.id')
                    ->paginate(20);

//
        $orderItem = json_decode(json_encode($order->items()), true);
//        print_r($orderItem);
        $distributors = array_unique(array_column($orderItem, 'distributor_id'));


//        DB::enableQueryLog();
//
//        $sql="SELECT u.referred_by, uc.category_id, COUNT(u.referred_by)  from users u LEFT JOIN user_category uc ON uc.user_id = u.id where uc.category_id='1' AND u.referred_by != '' GROUP BY u.referred_by ";
//        $selectConditions = [
//            'category_id'       => '1',
//            'referred_by'         => ""
//        ];
        $distributerCount = DB::table('users')
                        ->selectRaw(" users.referred_by, COUNT(users.referred_by) as total_referred ")
                        ->whereIn('referred_by',$distributors)
                        ->groupBy('referred_by')
                        ->get();
        $distributerCount = json_decode(json_encode($distributerCount->toArray()), true);

        $distributerName = DB::table('users')
            ->selectRaw("id as referred_by, CONCAT(users.first_name,' ',users.last_name) as distributer_name")
            ->whereIn('id',$distributors)
            ->get();
        $distributerName = json_decode(json_encode($distributerName->toArray()), true);
        foreach ($distributerName as $distributerNames){
            $distributerNamesId[$distributerNames['referred_by']] = $distributerNames['distributer_name'];
        }
        foreach ($distributerCount as $distributerCounts){
            $distributorCountID[$distributerCounts['referred_by']] = [
                "distributor_name" => isset($distributerNamesId[$distributerCounts['referred_by']]) ? $distributerNamesId[$distributerCounts['referred_by']] : $distributerCounts['referred_by'] ,
                "total_referred" => $distributerCounts['total_referred'],
                "referred_percentage" => $distributerCounts['total_referred'] >= 5 && $distributerCounts['total_referred'] <= 10 ? 10 :($distributerCounts['total_referred'] >= 11 && $distributerCounts['total_referred'] <= 20 ? 15 :($distributerCounts['total_referred'] >= 21 && $distributerCounts['total_referred'] <= 30 ? 20 :($distributerCounts['total_referred'] >= 31 ? 30 : 5))) ,
            ];
        }
//        print_r($distributorCountID);
//        die();
//        $distributer = DB::select($sql);
//        dump($distributer);
//        dd(DB::getQueryLog());
//         dd($order);
        return view('home',compact('order','distributorCountID'));
    }
}
