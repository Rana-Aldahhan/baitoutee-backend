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
        CRUD::setEntityNameStrings(trans('adminPanel.entities.price_change_request'),trans('adminPanel.entities.price_change_requests'));
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
        CRUD::addColumn([
            'name'     => 'meal_id_id',
            'label'    =>  trans('adminPanel.entities.meal'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/meal/$entry->meal_id/show' >". $entry->meal->name." </a> ";
            }
        ]); 
        CRUD::addColumn([
            'name'     => 'chef_id',
            'label'    =>  trans('adminPanel.entities.chef'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/chef/".$entry->meal->chef_id."/show' >". $entry->meal->chef->name." </a> ";
            }
        ]); 
        CRUD::addColumn([
            'name'     => 'old_price',
            'label'    => trans('adminPanel.attributes.old_price'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->meal->price;
            }
        ]); 
        CRUD::column('new_price')->label(trans('adminPanel.attributes.new_price'));
        CRUD::column('reason')->label(trans('adminPanel.attributes.reason'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        CRUD::column('approved')->label(trans('adminPanel.attributes.approved'))->type('boolean');
        $this->crud->addButtonFromView('line', 'approveOrReject', 'approveOrReject', 'beginning');
        //$this->crud->removeButtons(['create','update']);
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
        CRUD::field('meal_id')->label(trans('adminPanel.entities.meal'));
        CRUD::field('new_price')->label(trans('adminPanel.attributes.new_price'));
        CRUD::field('reason')->label(trans('adminPanel.attributes.reason'));

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }
       /**
     * Define what happens when the show operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->setupListOperation();
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
