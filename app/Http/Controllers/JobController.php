<?php

namespace App\Http\Controllers;

// use App\Services\EmployeeManagement\Applicant;
use Illuminate\Http\Request;
use Validator;
use App\Models\Applicant; 

class JobController extends Controller
{
    protected $applicant;

    public function __construct(Applicant $applicant)
    {
        $this->applicant = $applicant;
    }

    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:applicants|max:255',
            'email' => 'required|unique:applicants|email',
            'position' => 'required',
         ]);

         if ($validator->fails()) {
            return apiResponse('Validation Error', $validator->errors()->all(), 422);

        }else{

            if ($request->file('avatar')) {
                $image = $request->file('avatar');
                $destinationPath = 'img/applicants';
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
            }

            $profileImage = $profileImage??Null;
            $applicant = Applicant::Create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address ?? null,
                'position' => $request->position,
            ]);
            $applicant->avatar = $profileImage;
            $applicant->save();

            return apiResponse('data', $applicant, 200);
        }
    }
}
