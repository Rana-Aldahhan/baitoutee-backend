<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserJoinRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserJoinRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserJoinRequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\UserJoinRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user-join-request');
        CRUD::setEntityNameStrings('user join request', 'user join requests');
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
        //CRUD::column('location_id');
        //CRUD::column('user_id');
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('email_verified_at');
        CRUD::column('phone_number');
        CRUD::column('birth_date');
        CRUD::column('gender');
        CRUD::column('national_id');
        CRUD::column('campus_card_id');
        CRUD::column('campus_unit_number');
        CRUD::column('campus_card_expiry_date');
        CRUD::column('study_specialty');
        CRUD::column('study_year');
        CRUD::column('approved');
        CRUD::column('deleted_at');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        $this->crud->addButtonFromView('line', 'approve', 'approve', 'beginning');

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
        CRUD::setValidation(UserJoinRequestRequest::class);

        CRUD::field('id');
        CRUD::field('location_id');
        CRUD::field('user_id');
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('email_verified_at');
        CRUD::field('phone_number');
        CRUD::field('birth_date');
        CRUD::field('gender');
        CRUD::field('national_id');
        CRUD::field('campus_card_id');
        CRUD::field('campus_unit_number');
        CRUD::field('campus_card_expiry_date');
        CRUD::field('study_specialty');
        CRUD::field('study_year');
        CRUD::field('approved');
        CRUD::field('deleted_at');
        CRUD::field('created_at');
        CRUD::field('updated_at');

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
