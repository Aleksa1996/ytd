<?php

namespace App\Http\Controllers;

use App\Mail\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Exceptions\GeneralException;

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
            'message' => 'required|min:10|max:250'
        ]);

        try {
            Mail::to('aleksa.j.1996@gmail.com')->queue(new ContactForm($data));
            return response()->json(['message' => 'Message successfully sent!'], 200);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            throw new GeneralException('Mail cannot be sent now, please try again later!');
        }
    }
}
