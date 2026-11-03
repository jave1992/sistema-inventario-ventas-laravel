<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Rules\UniqueRucRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())->get();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        Gate::authorize('create-companies');

        return view('admin.companies.create');
    }

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
            'sol_user' => 'required|string',
            'sol_pass' => 'required|string',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();

        $company = Company::create($data);
        
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'La compañía se ha creado correctamente'
        ]);

        return view('admin.companies.edit', compact('company'));
    }

    public function edit(Company $company)
    {
        Gate::authorize('update-companies');

        return view('admin.companies.edit', compact('company'));
    }

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

    public function destroy($company)
    {
        $company = Company::where('ruc', $company)->where('user_id', Auth::id())->firstOrFail();

        $company->delete();

        return response()->json(['message' => 'Compañía eliminada exitosamente'], 200);
    }

    public function logo(Request $request, Company $company)
    {
        Gate::authorize('update-companies');

        $image = $company->images()->create([
            'path' => Storage::put('/logos', $request->file('file')),
            'size' => $request->file('file')->getSize()
        ]);

        return response()->json([
            'id' => $image->id,
            'path' => $image->path
        ]);
    }

    public function cert(Request $request, Company $company)
    {
        Gate::authorize('update-companies');

        $cert = $company->certs()->create([
            'path' => Storage::put('/certs', $request->file('file')),
            'size' => $request->file('file')->getSize()
        ]);

        return response()->json([
            'id' => $cert->id,
            'path' => $cert->path
        ]);
    }
}
