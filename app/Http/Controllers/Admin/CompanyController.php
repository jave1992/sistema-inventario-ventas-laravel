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
        Gate::authorize('read-companies');

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
        Gate::authorize('create-companies');

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
        Gate::authorize('update-companies');

        $company = Company::where('id', $company)->where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'razon_social' => 'nullable|string|min:5',
            'ruc' => [
                'nullable',
                'string',
                'regex:/^(10|20)\d{9}$/',
                new UniqueRucRule($company->id),
            ],
            'direccion' => 'nullable|string|min:5',
            'sol_user' => 'nullable|string|min:5',
            'sol_pass' => 'nullable|string|min:5',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'production' => 'nullable|boolean',
        ]);

        $company->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'La compañía se ha actualizado correctamente'
        ]);

        return redirect()->route('admin.companies.edit', $company);
    }

    public function destroy($company)
    {
        Gate::authorize('delete-companies');

        $company = Company::where('id', $company)->where('user_id', Auth::id())->firstOrFail();

        $company->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'La compañía se ha eliminado correctamente'
        ]);

        return redirect()->route('admin.companies.index');
    }

    public function logo(Request $request, Company $company)
    {
        Gate::authorize('update-companies');

        $request->validate([
            'logo' => 'nullable|image'
        ]);

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

        $request->validate([
            'cert' => 'nullable|file|mimes:pem,txt'
        ]);

        $certificado = $company->certs()->create([
            'path' => Storage::put('/certs', $request->file('file')),
            'size' => $request->file('file')->getSize()
        ]);

        return response()->json([
            'id' => $certificado->id,
            'path' => $certificado->path
        ]);
    }

    public function destroyLogo(Company $company, $logo)
    {
        Gate::authorize('update-companies');

        $image = $company->images()->where('id', $logo)->firstOrFail();

        Storage::delete($image->path);

        $image->delete();

        return response()->json([
            'message' => 'Logo eliminado correctamente'
        ]);
    }

    public function destroyCert(Company $company, $cert)
    {
        Gate::authorize('update-companies');

        $certificado = $company->certs()->where('id', $cert)->firstOrFail();

        Storage::delete($certificado->path);

        $certificado->delete();

        return response()->json([
            'message' => 'Certificado eliminado correctamente'
        ]);
    }
}
