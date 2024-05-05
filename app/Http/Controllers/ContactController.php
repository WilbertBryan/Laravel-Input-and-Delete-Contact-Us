<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Str;

class ContactController extends Controller
{
    private function data()
    {
        if (!Cookie::has('contact'))
        {
            return [];
        }

        // Terima as JSON
        $data = Cookie::get('contact');
        $data = \json_decode($data);
        return $data;
    }

    public function View()
    {
        return \view('contact');
    }

    
   

public function ActionContact(Request $request)
{
    $data = $this->data();

    // Determine the next ID based on the count of existing data
    $nextId = count($data) + 1;

    $newData = [
        "id" => $nextId, 
        "name" => $request->input('name'),
        "email" => $request->input('email'),
        "phone" => $request->input('phone'),
        "message" => $request->input('message'),
    ];

    // Add new data to existing data
    $data[] = $newData;

    // Encode the combined data to JSON
    $jsonData = json_encode($data);

    // Update the cookie with the modified JSON data
    $cookie = Cookie::make("contact", $jsonData, 60*24*30);
    Cookie::queue($cookie);

    // Return the view
    return view('contact');
}


    public function ContactList(Request $request)
    {
        dd($request->cookie('contact'));
    }
    

    public function ContactData(Request $request)
    {
        // Retrieve JSON data from the cookie
        $jsonData = $request->cookie('contact');

        // Decode the JSON data into an array
        $data = json_decode($jsonData, false);

        // Pass the decoded data to the view
        return view("data",[
            "contacts" => $data
        ]);
    }
    public function deleteContact(Request $request)
    {
        $contactIdToDelete = $request->input('contactIdToDelete');
    
        // Retrieve the existing data from the cookie
        $jsonData = $request->cookie('contact');
        $data = json_decode($jsonData, true);
    
        // Find the index of the contact to delete
        $indexToDelete = null;
        foreach ($data as $index => $contact) {
            if ($contact['id'] == $contactIdToDelete) {
                $indexToDelete = $index;
                break;
            }
        }
    
        // If the contact was found, remove it from the array
        if ($indexToDelete !== null) {
            unset($data[$indexToDelete]);
            $data = array_values($data); 
        }
    
        // Encode the modified data back to JSON
        $updatedJsonData = json_encode($data);
    
        // Update the cookie with the modified JSON data
        $cookie = Cookie::make("contact", $updatedJsonData, 60*24*30);
        Cookie::queue($cookie);
    
        return redirect()->back();
    }
    
}
