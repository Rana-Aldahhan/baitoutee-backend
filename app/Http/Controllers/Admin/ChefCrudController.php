<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ChefRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class ChefCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChefCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Chef::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/chef');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.chef'), trans('adminPanel.entities.chefs'));
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
            $query->orWhere('id','LIKE', $searchTerm);}
        );
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('email')->label(trans('adminPanel.attributes.email'));
        CRUD::column('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::column('is_available')->label(trans('adminPanel.attributes.is_available'))->type('boolean');
        CRUD::column('balance')->label(trans('adminPanel.attributes.balance'));
        CRUD::addColumn([
            'name'     => 'location_id',
            'label'    =>  trans('adminPanel.attributes.location'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->location->name;
            }
        ]); 
        CRUD::column('profile_picture')->prefix(url(''))->type('image')->label(trans('adminPanel.attributes.profile_picture'));
        CRUD::column('gender')->label(trans('adminPanel.attributes.gender'));
        CRUD::column('delivery_starts_at')->label(trans('adminPanel.attributes.delivery_starts_at'));
        CRUD::column('delivery_ends_at')->label(trans('adminPanel.attributes.delivery_ends_at'));
        CRUD::column('max_meals_per_day')->label(trans('adminPanel.attributes.max_meals_per_day'));
       // CRUD::column('certificate')->label(trans('adminPanel.attributes.certificate'))->type('custom_html')->value('<a href='.asset($this->entry->certificate).'>'.trans('adminPanel.attributes.certificate').' </a>');
        CRUD::addColumn([
            'name'     => 'certificate',
            'label'    =>  trans('adminPanel.attributes.certificate'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                if($entry->certificate!=null)
                return'<a href='.asset($entry->certificate).'>'.trans('adminPanel.attributes.certificate').' </a>';
            }
        ]); 
        CRUD::column('birth_date')->label(trans('adminPanel.attributes.birth_date'))->type('date');
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
        \Auth::shouldUse('backpack');
        Gate::authorize('edit-users');
        CRUD::setValidation(ChefRequest::class);
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.chef_join_request'),
            'type'      => 'select',
            'name'      => 'chef_join_request_id',
            'entity'    => 'chefJoinRequest',
            'attribute' => 'id',
            'model'     => "App\Models\ChefJoinRequest",
            'pivot'     => false,
        ]); 
        CRUD::field('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));
        CRUD::field('email')->label(trans('adminPanel.attributes.email'));
        CRUD::field('birth_date')->label(trans('adminPanel.attributes.birth_date'));
        CRUD::field('gender')->label(trans('adminPanel.attributes.gender'))->type('enum');
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.attributes.gender'),
            'name'      => 'gender',
            'type'      => 'select_from_array',
            'options'   => ['m' => 'male', 'f' => 'female'],
            'allows_null' => false,
        ]); 
       
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.attributes.location'),
            'type'      => 'select',
            'name'      => 'location_id',
            'entity'    => 'location',
            'attribute' => 'name',
            'model'     => "App\Models\Location",
            'pivot'     => false,
        ]); 
        CRUD::field('delivery_starts_at')->label(trans('adminPanel.attributes.delivery_starts_at'));
        CRUD::field('delivery_ends_at')->label(trans('adminPanel.attributes.delivery_ends_at'));
        CRUD::field('max_meals_per_day')->label(trans('adminPanel.attributes.max_meals_per_day'))->type('text');
        CRUD::field('is_available')->label(trans('adminPanel.attributes.is_available'));
        CRUD::field('balance')->label(trans('adminPanel.attributes.balance'));

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
