<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\BrandService;
use App\Traits\JsonResponseTrait;
use App\DataTables\BrandDataTable;

class BrandController extends Controller
{
    use JsonResponseTrait;

    public $brandService;

    public function __construct(BrandService $brandService){
        $this->brandService = $brandService;
    }

    public function index(BrandDataTable $dataTables){

        return $dataTables->render('admin.brands.list');

    }

    public function create(){

        return view("admin.brands.create");

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required|unique:brands",
            "status" => 'required|boolean',
        ]);

        if ($validator->passes()) {

            return $this->brandService->create($request->all());

        }else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function edit($brandId, Request $request){

        $brand = $this->brandService->find($brandId);

        if (empty($brand)) {
            return redirect()->route('brands.index');
        }
        return view('admin.brands.edit', compact('brand'));

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required",
            "status" => 'required|boolean',
        ]);

        if ($validator->passes()) {

            return $this->brandService->update($request->brand, $request->all());

        } else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function destroy(Request $request, $id){

        return $this->brandService->delete($id);
        
    }
}
