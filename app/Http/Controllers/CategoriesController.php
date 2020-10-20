<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    protected function isValid($request)
    {
        return $request->validate([
            'name' => 'required',
            'entity_name' => 'nullable'
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->expenses()->exists()) {
            return response(['message' => 'Category has been attached to some expenses and can not be deleted.'], 422);
        } elseif ($category->incomes()->exists()) {
            return response(['message' => 'Category has been attached to some incomes and can not be deleted.'], 422);
        } elseif ($category->products()->exists()) {
            return response(['message' => 'Category has been attached to some products and can not be deleted.'], 422);
        }
        $category->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        if ($request->all) {
            return Category::select(['id', 'name', 'id as value'])->orderBy('name', 'asc')->get();
        }
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied!');
        }
        $categories = response()->json(Category::vueTable(Category::$columns));
        $entityConvert = Category::convertEntityName($categories->getData());
        return response($entityConvert);
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function store(Request $request)
    {
        $v = $this->isValid($request);
        return Category::create($v);
    }

    public function update(Request $request, Category $category)
    {
        $v = $this->isValid($request);
        $category->update($v);
        return $category;
    }
}
