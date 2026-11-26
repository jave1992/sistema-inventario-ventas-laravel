<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'general');

        $tabs = [
            'general',
            'security',
            'appearance',
            'backup',
        ];

        if (!in_array($tab, $tabs, true)) {
            abort(404);
        }

        $settings = Setting::pluck('value', 'key');

        return view('admin.settings.index', compact('tab', 'settings'));
    }

    public function updateSecurity(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        if (!Hash::check($data['current_password'], $request->user()->password)) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])
                ->withInput();
        }

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function updateAppearance(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|in:light,dark,system',
            'primary_color' => 'required|in:blue,indigo,emerald,violet,rose',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Apariencia actualizada.');
    }


    public function backup()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Iniciando respaldo desde navegador', [
                'php_version' => PHP_VERSION,
                'php_ini' => php_ini_loaded_file(),
                'sapi' => PHP_SAPI,
                'user' => get_current_user(),
                'working_directory' => getcwd(),
                'mysqldump' => shell_exec('where mysqldump 2>&1'),
            ]);

            $exitCode = Artisan::call('backup:run -vvv');
            $output = Artisan::output();

            \Illuminate\Support\Facades\Log::info('Resultado del respaldo desde navegador', [
                'exit_code' => $exitCode,
                'output' => $output,
            ]);

            if ($exitCode !== 0) {
                return back()->with('error', 'El respaldo falló. Revisa el registro de Laravel.');
            }

            return back()->with('success', 'Respaldo generado correctamente.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error en respaldo desde navegador', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error al generar respaldo: ' . $e->getMessage());
        }
    }
}
