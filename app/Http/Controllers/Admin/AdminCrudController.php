<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Admin;
use Illuminate\Support\Facades\Gate;

/**
 * Class AdminCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AdminCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Admin::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/admin');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.admin'), trans('adminPanel.entities.admins'));
        CRUD::setValidation(AdminRequest::class);
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
        Gate::authorize('add-admins');
        CRUD::setValidation(AdminRequest::class);
        CRUD::column('id')->searchLogic(function ($query, $column, $searchTerm) {
            $query->orWhere('id', $searchTerm);}
        );
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('email')->label(trans('adminPanel.attributes.email'));
        CRUD::addColumn([
            'name'     => 'role_id',
            'label'    => trans('adminPanel.attributes.role'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->role->name;
            }
        ]); 
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
        Gate::authorize('add-admins');
        CRUD::setValidation(AdminRequest::class);

        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('password');
        CRUD::addField([   // Checklist
            'label'     => 'Roles',
            'type'      => 'select',
            'name'      => 'role_id',
            'entity'    => 'role',
            'attribute' => 'name',
            'model'     => "App\Models\Role",
            'pivot'     => false,
            // 'number_of_columns' => 3,
        ]); 
        Admin::creating(function($entry) {
            $entry->password =bcrypt($entry->password) ;
        });
        
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
        Gate::authorize('add-admins');
        $this->setupCreateOperation();
        Admin::updating(function($entry) {
            $entry->password =bcrypt($entry->password) ;
        });
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();

    }
    
}
