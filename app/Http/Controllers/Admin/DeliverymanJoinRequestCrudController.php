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
        CRUD::setEntityNameStrings(trans('adminPanel.entities.deliveryman_join_request'),trans('adminPanel.entities.deliveryman_join_requests'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id')->searchLogic(function ($query, $column, $searchTerm) {
            $query->orWhere('id', $searchTerm);}
        );
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('email')->label(trans('adminPanel.attributes.email'));
        CRUD::column('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::column('transportation_type')->label(trans('adminPanel.attributes.transportation_type'));
        CRUD::column('work_days')->label(trans('adminPanel.attributes.work_days'));
        CRUD::column('work_hours_from')->label(trans('adminPanel.attributes.work_hours_from'));
        CRUD::column('work_hours_to')->label(trans('adminPanel.attributes.work_hours_to'));
        CRUD::column('gender')->label(trans('adminPanel.attributes.gender'));
        CRUD::column('birth_date')->label(trans('adminPanel.attributes.birth_date'))->type('date');
        CRUD::column('approved')->label(trans('adminPanel.attributes.approved_at'))->type('boolean');
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        $this->crud->addButtonFromView('line', 'approveOrReject', 'approveOrReject', 'beginning');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
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
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DeliverymanJoinRequestRequest::class);
        CRUD::field('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));;
        CRUD::field('email')->label(trans('adminPanel.attributes.email'));
        CRUD::field('birth_date')->label(trans('adminPanel.attributes.birth_date'));
        CRUD::field('gender')->label(trans('adminPanel.attributes.gender'))->type('enum');
        CRUD::field('transportation_type')->label(trans('adminPanel.attributes.transportation_type'))->type('enum');
        CRUD::field('work_days')->label(trans('adminPanel.attributes.work_days'));
        CRUD::field('work_hours_from')->label(trans('adminPanel.attributes.work_hours_from'));
        CRUD::field('work_hours_to')->label(trans('adminPanel.attributes.work_hours_to'));

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
