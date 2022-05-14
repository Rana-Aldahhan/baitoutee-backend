<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ChefJoinRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ChefJoinRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChefJoinRequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ChefJoinRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/chef-join-request');
        CRUD::setEntityNameStrings('chef join request', 'chef join requests');
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
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('phone_number');
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('birth_date');
        CRUD::column('gender');
        //CRUD::column('location_id');
        CRUD::column('delivery_starts_at');
        CRUD::column('delivery_ends_at');
        CRUD::column('max_meals_per_day');
        CRUD::column('profile_picture');
        CRUD::column('certificate');
        CRUD::column('approved');

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
        CRUD::setValidation(ChefJoinRequestRequest::class);

        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('phone_number');
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('birth_date');
        CRUD::field('gender');
        CRUD::field('location_id');
        CRUD::field('delivery_starts_at');
        CRUD::field('delivery_ends_at');
        CRUD::field('max_meals_per_day');
        CRUD::field('profile_picture');
        CRUD::field('certificate');
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
        $this->setupCreateOperation();
    }
}
