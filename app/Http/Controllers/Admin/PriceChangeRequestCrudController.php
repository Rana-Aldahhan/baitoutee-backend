<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PriceChangeRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class PriceChangeRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PriceChangeRequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\PriceChangeRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/price-change-request');
        CRUD::setEntityNameStrings('طلب تغيير سعر', 'طلبات تغيير السعر');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-meal-prices');
        //CRUD::column('id');
        CRUD::column('meal_id');
        CRUD::addColumn([
            'name'     => 'chef_name',
            'label'    => 'Chef name',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->meal->chef->name;
            }
        ]); 
        CRUD::addColumn([
            'name'     => 'old_price',
            'label'    => 'Old price',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->meal->price;
            }
        ]); 
        CRUD::column('new_price');
        CRUD::column('reason');
        CRUD::column('approved');
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
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-meal-prices');
        CRUD::setValidation(PriceChangeRequestRequest::class);

        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('meal_id');
        CRUD::field('new_price');
        CRUD::field('reason');
        CRUD::field('approved');

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
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-meal-prices');
        $this->setupCreateOperation();
    }
}
