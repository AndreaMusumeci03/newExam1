<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // <-- Aggiungi questo
use Illuminate\Validation\Rules\File;   // <-- Aggiungi questo

class ProfileController extends Controller
{
    /**
     * Mostra il form per modificare il profilo.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Aggiorna il profilo dell'utente.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'bio' => 'nullable|string|max:1000',
            
            // --- ECCO LA CORREZIONE ---
            'avatar' => [
                'nullable',
                'image', 
                'mimes:jpg,jpeg,png,webp', 
                'max:2048', 
            ],

        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => "L'email è obbligatoria.",
            'email.email' => "L'email deve essere valida.",
            'email.unique' => 'Questa email è già utilizzata da un altro utente.',
            'bio.max' => 'La biografia non può superare i 1000 caratteri.',
            'avatar.image' => "Il file deve essere un'immagine.",
            'avatar.mimes' => "L'immagine deve essere di tipo jpg, jpeg, png, o webp.",
            'avatar.max' => "L'immagine non può superare i 2MB.",
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $dataToUpdate['avatar_url'] = $path;
        }

        $user->update($dataToUpdate);

        return redirect()->route('profile.edit')->with('success', 'Profilo aggiornato con successo!');
    }
}