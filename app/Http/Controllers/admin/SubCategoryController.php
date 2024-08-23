<?php

namespace App\Http\Controllers\admin;

use App\DataTables\SubcategoryDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponseTrait;
use App\Services\SubcategoryService;
use App\Services\CategoryService;

class SubCategoryController extends Controller
{
    use JsonResponseTrait;

    public $subCategoryService;
    public $categoryService;

    public function __construct(

        SubcategoryService $subCategoryService,
        CategoryService $categoryService

        ){

        $this->subCategoryService = $subCategoryService;
        $this->categoryService = $categoryService;

    }

    public function index(SubcategoryDataTable $dataTable){

        return $dataTable->render("admin.subcategory.list");

    }

    public function create()
    {
        $categories = $this->categoryService->getByOrder();
        return view("admin.subcategory.create", compact("categories"));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required|unique:sub_categories",
            "category_id" => 'required',
            "status" => 'required|boolean',
        ]);

        if ($validator->passes()) {
           return $this->subCategoryService->create($request->all());

        }else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function edit($categoryId, Request $request)
    {
        $subcategory = $this->subCategoryService->find($categoryId);
        $categories = $this->categoryService->getByOrder();
        if (empty($subcategory)) {
            return redirect()->route('sub-categories.index');
        }
        return view('admin.subcategory.edit', compact('subcategory','categories'));
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required",
            "status" => 'required|boolean',
            "category_id" => 'required',
        ]);

        if ($validator->passes()) {
            return $this->subCategoryService->update($request->category, $request->all());
        } else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function destroy(Request $request, $id)
    {
        return $this->subCategoryService->delete($id);
    }
}

