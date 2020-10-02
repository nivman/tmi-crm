<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    protected function isValid($request)
    {
        return $request->validate([
            'name'          => 'required',
            'email'         => 'required',
            'phone'         => 'required',
            'extra'         => 'nullable',
            'state'         => 'nullable',
            'footer'        => 'nullable',
            'country'       => 'nullable',
            'address'       => 'required',
            'template'      => 'required',
            'show_tax'      => 'nullable',
            'show_image'    => 'nullable',
            'show_discount' => 'nullable',
            'logo'          => 'sometimes|nullable|mimes:jpg,jpeg,gif,png|dimensions:min_width=80,max_width=601,min_height=59,max_height=161',
        ]);
    }

    public function show(Company $company)
    {
        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $v                 = $this->isValid($request);
        $cs                = getCS($v['country'], $v['state']);
        $v['state_name']   = $cs['state']->name;
        $v['country_name'] = $cs['country']->name;
        if ($request->has('logo')) {
            $name = 'logo.' . $request->logo->extension();
            // $name = 'logo.' . $request->logo->getClientOriginalExtension();
            $request->logo->move(public_path('/images'), $name);
            $v['logo'] = 'images/' . $name;
        }
        $company->update($v);
        return $company;
    }
}
