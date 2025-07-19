<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phonebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhonebookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Phonebook::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Group filter
        if ($request->filled('group') && $request->group !== 'all') {
            $query->byGroup($request->group);
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $contacts = $query->orderBy('name')->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $contact = Phonebook::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil ditambahkan',
            'data' => $contact
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Phonebook $phonebook)
    {
        return response()->json([
            'success' => true,
            'data' => $phonebook
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phonebook $phonebook)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phonebook->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diperbarui',
            'data' => $phonebook
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phonebook $phonebook)
    {
        $phonebook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil dihapus'
        ]);
    }

    /**
     * Get all groups
     */
    public function groups()
    {
        $groups = Phonebook::distinct()->pluck('group')->filter()->sort()->values();
        
        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Search contacts
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $contacts = Phonebook::search($search)
            ->active()
            ->limit(10)
            ->get(['id', 'name', 'phone_number', 'group']);
        
        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }
}
