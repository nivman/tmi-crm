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
            $expenses = $category->expenses()->get()->toArray();
            $list = $this->entityList($category, $expenses);
            return response(['message' => 'הקטגוריה צורפה להוצאות ולא ניתן למחוק אותה.' . '<br> ' . $list], 422);
        } elseif ($category->incomes()->exists()) {
            $incomes = $category->incomes()->get()->toArray();
            $list = $this->entityList($category, $incomes);
            return response(['message' => 'קטגוריה צורפה לכמה הכנסות ולא ניתן למחוק אותה.' . '<br> ' . $list], 422);
        } elseif ($category->products()->exists()) {
            $products = $category->products()->get()->toArray();
            $list = $this->entityList($category, $products);
            return response(['message' => 'קטגוריה צורפה למוצרים ולא ניתן למחוק אותה'. '<br> ' . $list ], 422);
        }
        $category->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {

        if ($request->all) {
            return Category::select(['id', 'name', 'id as value'])->orderBy('name', 'asc')->get();
        }
        if ($request->type === 'App\Expenses') {
            return Category::where(['entity_name'=> $request->type])->select(['id', 'name', 'id as value'])->orderBy('name', 'asc')->get();
        }
        $categories = response()->json(Category::vueTable(Category::$columns));

        $entityConvert = Category::convertEntityName($categories->getData(), $categories->original['count']);

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

    /**
     * @param Category $category
     * @param $entity
     * @return string
     */
    public function entityList(Category $category, $entity): string
    {

        $mapEntities = array_map(function ($t) {
            return ['id' => $t['id'], 'title' => $t['title']];
        }, $entity);
        $mapEntities = array_column($mapEntities, 'id', 'title');
        return implode('<br>', array_map(function ($k, $v) {
            return " שם: $k  $v מזהה: ";
        }, array_keys($mapEntities), array_values($mapEntities)));

    }
}
