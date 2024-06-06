<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCustomerData;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    // public function store(Request $request){
    //     $request->validate([
    //         'import_csv' => 'required|mimes:csv|max:40000',
    //     ]);
    //     //read csv file and skip data
    //     $file = $request->file('import_csv');
    //     $handle = fopen($file->path(), 'r');

    //     //skip the header row
    //     fgetcsv($handle);

    //     set_time_limit(0);
        
    //     $chunksize = 3000;
    //     while(!feof($handle))
    //     {
    //         $chunkdata = [];

    //         for($i = 0; $i<$chunksize; $i++)
    //         {
    //             $data = fgetcsv($handle);
    //             if($data === false)
    //             {
    //                 break;
    //             }
    //             $chunkdata[] = $data; 
    //         }

    //         $this->getchunkdata($chunkdata);
    //     }
    //     fclose($handle);

    //     return redirect()->route('employee.create')->with('success', 'Data has been added successfully.');
    // }
    // public function getchunkdata($chunkdata)
    // {
    //     foreach($chunkdata as $column){
    //         $customer_id = $column[1];
    //         $firstname = $column[2];
    //         $lastname = $column[3];
    //         $company = $column[4];
    //         $city = $column[5];
    //         $country = $column[6];
    //         $phone_first = $column[7];
    //         $phone_second = $column[8];
    //         $email = $column[9];
    //         $subscription_date = $column[10];
    //         $website = $column[11];

    //         //create new employee
    //         $employee = new Customer();
    //         $employee->customer_id = $customer_id;
    //         $employee->fname = $firstname;
    //         $employee->lname = $lastname;
    //         $employee->company = $company;
    //         $employee->city = $city;
    //         $employee->country = $country;
    //         $employee->phone_first = $phone_first;
    //         $employee->phone_second = $phone_second;
    //         $employee->email = $email;
    //         $employee->subscription_date = $subscription_date;
    //         $employee->website = $website;

    //         $employee->save();
    //     }
    // }


    // useing queue
    public function store(Request $request)
    {
        $request->validate([
            'import_csv' => 'required|mimes:csv,txt|max:40000',
        ]);
    
        $file = $request->file('import_csv');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
    
        fgetcsv($handle);
    
        set_time_limit(0);
    
        $chunksize = 5000;
        $chunkdata = [];
        $chunkCount = 0;
    
        while (!feof($handle)) {
            for ($i = 0; $i < $chunksize; $i++) {
                $data = fgetcsv($handle);
                if ($data === false) {
                    break;
                }
                $chunkdata[] = $data;
            }
    
            if (!empty($chunkdata)) {
                ImportCustomerData::dispatch($chunkdata);
                Log::info('Dispatched a chunk', ['chunkNumber' => ++$chunkCount, 'chunkSize' => count($chunkdata)]);
                $chunkdata = [];
            }
        }
    
        fclose($handle);
    
        Log::info('CSV import completed successfully.');
    
        return redirect()->route('home')->with('success', 'Data has been added successfully.');
    }
}

