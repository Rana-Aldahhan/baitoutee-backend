<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MealRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MealCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Meal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/meal');
        CRUD::setEntityNameStrings('وجبة', 'وجبات');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('price');
        CRUD::addColumn([
            'name'     => 'chef_id',
            'label'    => 'chef',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->chef->name;
            }
        ]); 
        CRUD::column('ingredients');
        CRUD::addColumn([
            'name'     => 'category_id',
            'label'    => 'category',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->category->name;
            }
        ]); 
        CRUD::column('expected_preparation_time');
        CRUD::column('discount_percentage');
        CRUD::column('image');
        CRUD::column('max_meals_per_day');
        CRUD::column('is_available');
        CRUD::column('approved');
        CRUD::column('rating');
        CRUD::column('rates_count');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        $this->crud->addButtonFromView('line', 'approveOrReject', 'approveOrReject', 'beginning');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MealRequest::class);

        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('chef_id');
        CRUD::field('category_id');
        CRUD::field('image');
        CRUD::field('name');
        CRUD::field('price');
        CRUD::field('max_meals_per_day');
        CRUD::field('is_available');
        CRUD::field('expected_preparation_time');
        CRUD::field('discount_percentage');
        CRUD::field('ingredients');
        CRUD::field('approved');
        CRUD::field('rating');
        CRUD::field('rates_count');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
