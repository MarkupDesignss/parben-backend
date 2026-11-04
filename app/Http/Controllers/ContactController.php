<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    // Show all contacts
    public function index()
    {
        $contacts = Contact::latest()->paginate(10);
        return view('admin.contacts', compact('contacts'));
    }

    // Show single contact
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contacts.show', compact('contact'));
    }

    // Delete contact
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully.');
    }

  public function bulkDelete(Request $request)
    {
        try {
            // Get IDs from request
            $ids = $request->input('ids');
            
            // If it's a string, try to decode it
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
                // If json_decode fails, try to parse as comma-separated
                if (!is_array($ids) && strpos($ids, ',') !== false) {
                    $ids = explode(',', $ids);
                }
            }
            
            // If it's still not an array, return error
            if (!is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid IDs format'
                ], 400);
            }
            
            // Clean and validate IDs
            $ids = array_filter($ids, function($id) {
                return is_numeric($id) && $id > 0;
            });
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid IDs provided'
                ], 400);
            }
            
            // Delete the contacts
            $deleted = Contact::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} contact(s)",
                'deleted' => $deleted
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting contacts: ' . $e->getMessage()
            ], 500);
        }
    }
}
