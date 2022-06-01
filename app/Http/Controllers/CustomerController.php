<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function show($id): View|string
    {
        $customer = Customer::where('id', $id)->with('address')->get()->firstOrFail();
    }

    public function filter(Request $request): View
    {
        $filters = [
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email'),
            'isBlocked' => $request->get('isBlocked')
        ];

        $Blocked = $filters['isBlocked'] == "true";

        $customersToShow = Customer::where('phone', 'like', "%{$filters['phone']}%")
            ->where('email', 'like', "%{$filters['email']}%")
            ->where('blocked', $Blocked)
            ->where(DB::raw("concat(\"firstName\", ' ',\"lastName\")"), 'like', "%{$filters['name']}%")
            ->paginate(10);


        $customersToShow->appends($request->except('page'));

        return view('customers')->with('customers', $customersToShow);
    }
}
