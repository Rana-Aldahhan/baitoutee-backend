<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DeliverymanJoinRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DeliverymanJoinRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DeliverymanJoinRequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\DeliverymanJoinRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deliveryman-join-request');
        CRUD::setEntityNameStrings('deliveryman join request', 'deliveryman join requests');
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
        CRUD::column('phone_number');
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('birth_date');
        CRUD::column('gender');
        CRUD::column('transportation_type');
        CRUD::column('work_days');
        CRUD::column('work_hours_from');
        CRUD::column('work_hours_to');
        CRUD::column('approved');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        $this->crud->addButtonFromView('line', 'approveUser', 'approveUser', 'beginning');

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
        CRUD::setValidation(DeliverymanJoinRequestRequest::class);

        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('phone_number');
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('birth_date');
        CRUD::field('gender');
        CRUD::field('transportation_type');
        CRUD::field('work_days');
        CRUD::field('work_hours_from');
        CRUD::field('work_hours_to');
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
