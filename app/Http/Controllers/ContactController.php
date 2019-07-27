<?php

namespace App\Http\Controllers;

use App\Mail\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Sends contact message to my email address
     *
     * @param Request $request
     * @return void
     */
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:2|max:20',
            'email' => 'required|email',
            'subject' => 'required|min:2',
            'message' => 'required|min:5'
        ]);
        try {
            Mail::to('aleksa.j.1996@gmail.com')->queue(new ContactForm($data));
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            throw ValidationException::withMessages(['_general_error' => ['Mail cannot be sent now, please try again later!']]);
        }
    }
}
