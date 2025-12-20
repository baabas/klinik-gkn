<?php

namespace App\Http\Controllers;

use App\Models\MasterWhatsappValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterWhatsappValidatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validators = MasterWhatsappValidator::orderBy('created_at', 'desc')->get();
        return view('master-whatsapp-validator.index', compact('validators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-whatsapp-validator.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_validator' => 'required|string|max:100',
                'nomor_wa' => 'required|string|max:20|unique:master_whatsapp_validators,nomor_wa',
                'keterangan' => 'nullable|string',
            ], [
                'nama_validator.required' => 'Nama validator harus diisi',
                'nomor_wa.required' => 'Nomor WhatsApp harus diisi',
                'nomor_wa.unique' => 'Nomor WhatsApp sudah terdaftar',
            ]);

            MasterWhatsappValidator::create([
                'nama_validator' => $validated['nama_validator'],
                'nomor_wa' => $validated['nomor_wa'],
                'keterangan' => $validated['keterangan'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect()->route('master-whatsapp-validator.index')
                ->with('success', 'WhatsApp Validator berhasil ditambahkan');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating WhatsApp Validator: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan WhatsApp Validator: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $validator = MasterWhatsappValidator::findOrFail($id);
        return view('master-whatsapp-validator.show', compact('validator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $validator = MasterWhatsappValidator::findOrFail($id);
        return view('master-whatsapp-validator.edit', compact('validator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'nama_validator' => 'required|string|max:100',
                'nomor_wa' => 'required|string|max:20|unique:master_whatsapp_validators,nomor_wa,' . $id,
                'keterangan' => 'nullable|string',
            ], [
                'nama_validator.required' => 'Nama validator harus diisi',
                'nomor_wa.required' => 'Nomor WhatsApp harus diisi',
                'nomor_wa.unique' => 'Nomor WhatsApp sudah terdaftar',
            ]);

            $whatsappValidator = MasterWhatsappValidator::findOrFail($id);
            $whatsappValidator->update([
                'nama_validator' => $validated['nama_validator'],
                'nomor_wa' => $validated['nomor_wa'],
                'keterangan' => $validated['keterangan'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect()->route('master-whatsapp-validator.index')
                ->with('success', 'WhatsApp Validator berhasil diupdate');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating WhatsApp Validator: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengupdate WhatsApp Validator: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $validator = MasterWhatsappValidator::findOrFail($id);
        $validator->delete();

        return redirect()->route('master-whatsapp-validator.index')
            ->with('success', 'WhatsApp Validator berhasil dihapus');
    }
}
