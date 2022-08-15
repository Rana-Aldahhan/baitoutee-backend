<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.user'), trans('adminPanel.entities.users'));
        $this->crud->query = $this->crud->query->withTrashed();
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
        CRUD::addColumn([
            'name'     => 'location_id',
            'label'    => 'مكان السكن',
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
        CRUD::column('approved_at')->label(trans('adminPanel.attributes.approved_at'))->type('datetime');
        CRUD::column('deleted_at')->label(trans('adminPanel.attributes.deleted_at'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        $this->crud->addButtonFromView('line', 'block', 'block', 'beginning');
        $this->crud->removeButton('delete');
        $this->crud->removeButton('create');

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
        CRUD::setValidation(UserRequest::class);

        CRUD::field('id');
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
        CRUD::field('gender')->label(trans('adminPanel.attributes.gender'));
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
