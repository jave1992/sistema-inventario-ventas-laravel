<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Rules\UniqueRucRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())->get();

        return response()->json($companies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string',
            'ruc' => [
                'required',
                'string',
                'regex:/^(10|20)\d{9}$/',
                new UniqueRucRule(),
            ],
            'direccion' => 'required|string',
            'logo' => 'nullable|image',
            'sol_user' => 'required|string',
            'sol_pass' => 'required|string',
            'cert' => 'required|file|mimes:pem,txt',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        $data['cert_path'] = $request->file('cert')->store('certs');
        $data['user_id'] = Auth::id();

        $company = Company::create($data);

        return response()->json(['message' => 'Compañía creada exitosamente', 'company' => $company], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($company)
    {
        $company = Company::where('ruc', $company)->where('user_id', Auth::id())->firstOrFail();

        return response()->json($company, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $company)
    {
        $company = Company::where('ruc', $company)->where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'razon_social' => 'nullable|string|min:5',
            'ruc' => [
                'nullable',
                'string',
                'regex:/^(10|20)\d{9}$/',
                new UniqueRucRule($company->id),
            ],
            'direccion' => 'nullable|string|min:5',
            'logo' => 'nullable|image',
            'sol_user' => 'nullable|string|min:5',
            'sol_pass' => 'nullable|string|min:5',
            'cert' => 'nullable|file|mimes:pem,txt',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        if ($request->hasFile('cert')) {
            $data['cert_path'] = $request->file('cert')->store('certs');
        }

        $company->update($data);

        return response()->json(['message' => 'Compañía actualizada exitosamente', 'company' => $company], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($company)
    {
        $company = Company::where('ruc', $company)->where('user_id', Auth::id())->firstOrFail();

        $company->delete();

        return response()->json(['message' => 'Compañía eliminada exitosamente'], 200);
    }
}
