<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyConfigRepository\CompanyConfigRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Models\ManipulationActivity;
use App\Models\CompanyConfig;
session_start();

class CompanyConfigController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $comRepo;
    
    public function __construct(CompanyConfigRepositoryInterface $comRepo)
    {
        $this->comRepo = $comRepo;
    }
    public function show_company_config(){
         $info = $this->comRepo->getCompany();
        return view('admin.ConfigWeb.companyconfig')->with(compact('info'));
    }

    public function edit_content_footer(Request $request){
        $result = $this->comRepo->InsertorUpdate($request->all());        
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}

