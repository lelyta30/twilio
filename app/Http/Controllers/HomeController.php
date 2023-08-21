<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class HomeController extends Controller
{
   
    public function index()
    {
        $users = Contact::all();
        return view('welcome', ['users' => $users]);
    }
    
    public function store(Request $request)
    {
        \Validator::make($request->all(), [
            'phone' => 'required|unique:contacts|numeric'
        ])->validate();

        $contact = new Contact;
        $contact->phone = $request->phone;
        $contact->save();

        $this->sendMessage('Contact registered successfully!!', $request->phone);
        return back()->with(['success' => "{$request->phone} registered"]);
    }
   
    public function sendCustomMessage(Request $request)
    {
        \Validator::make($request->all(), [
            'contact' => 'required|array',
            'body' => 'required',
        ])->validate();
        $recipients = $request->contact;
     
        foreach ($recipients as $recipient) {
            $this->sendMessage($request->body, $recipient);
        }
        return back()->with(['success' => "Message on its way to recipients!"]);
    }
   
    private function sendMessage($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }
} 