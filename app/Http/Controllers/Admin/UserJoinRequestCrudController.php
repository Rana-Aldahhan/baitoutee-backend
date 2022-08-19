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
        CRUD::setEntityNameStrings(trans('adminPanel.entities.user_join_request'),trans('adminPanel.entities.user_join_requests'));
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
            $query->orWhere('id','LIKE', $searchTerm);}
        );
        CRUD::addColumn([
            'name'     => 'location_id',
            'label'    => trans('adminPanel.attributes.location'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->location->name;
            }
        ]); 
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('email')->label(trans('adminPanel.attributes.email'));
        CRUD::column('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::column('birth_date')->label(trans('adminPanel.attributes.birth_date'));
        CRUD::column('gender')->label(trans('adminPanel.attributes.gender'));
        CRUD::column('national_id')->label(trans('adminPanel.attributes.national_id'));
        CRUD::column('campus_card_id')->label(trans('adminPanel.attributes.campus_card_id'));
        CRUD::column('campus_unit_number')->label(trans('adminPanel.attributes.campus_unit_number'));
        CRUD::column('campus_card_expiry_date')->label(trans('adminPanel.attributes.campus_card_expiry_date'));
        CRUD::column('study_specialty')->label(trans('adminPanel.attributes.study_specialty'));
        CRUD::column('study_year')->label(trans('adminPanel.attributes.study_year'));
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
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserJoinRequestRequest::class);
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.attributes.location'),
            'type'      => 'select',
            'name'      => 'location_id',
            'entity'    => 'location',
            'attribute' => 'name',
            'model'     => "App\Models\Location",
            'pivot'     => false,
        ]); 
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));
        CRUD::field('email')->label(trans('adminPanel.attributes.email'));
        CRUD::field('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::field('birth_date')->label(trans('adminPanel.attributes.birth_date'));
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.gender'),
            'name'      => 'gender',
            'type'      => 'select_from_array',
            'options'   => ['m' => 'male', 'f' => 'female'],
            'allows_null' => false,
        ]); 
        CRUD::field('national_id')->label(trans('adminPanel.attributes.national_id'));
        CRUD::field('campus_card_id')->label(trans('adminPanel.attributes.campus_card_id'));
        CRUD::field('campus_unit_number')->label(trans('adminPanel.attributes.campus_unit_number'));
        CRUD::field('campus_card_expiry_date')->label(trans('adminPanel.attributes.campus_card_expiry_date'));
        CRUD::field('study_specialty')->label(trans('adminPanel.attributes.study_specialty'));
        CRUD::field('study_year')->label(trans('adminPanel.attributes.study_year'));
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
        $this->setupCreateOperation();
    }
}
