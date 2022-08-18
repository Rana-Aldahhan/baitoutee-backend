<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DeliverymanRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeliverymanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DeliverymanCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Deliveryman::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deliveryman');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.deliveryman'), trans('adminPanel.entities.deliverymen'));
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
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('email')->label(trans('adminPanel.attributes.email'));
        CRUD::column('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::column('is_available')->label(trans('adminPanel.attributes.is_available'))->type('boolean');
        CRUD::column('transportation_type')->label(trans('adminPanel.attributes.transportation_type'));
        CRUD::column('balance')->label(trans('adminPanel.attributes.balance'));
        CRUD::column('total_collected_order_costs')->label(trans('adminPanel.attributes.total_collected_order_costs'));
        CRUD::column('work_days')->label(trans('adminPanel.attributes.work_days'));
        CRUD::column('work_hours_from')->label(trans('adminPanel.attributes.work_hours_from'));
        CRUD::column('work_hours_to')->label(trans('adminPanel.attributes.work_hours_to'));
        CRUD::column('gender')->label(trans('adminPanel.attributes.gender'));
        CRUD::column('birth_date')->label(trans('adminPanel.attributes.birth_date'))->type('date');
        CRUD::column('approved_at')->label(trans('adminPanel.attributes.approved_at'))->type('datetime');
        CRUD::column('deleted_at')->label(trans('adminPanel.attributes.deleted_at'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        if(request()->available==1){
            $this->crud->addClause('whereIsAvailable', true);
        }else if(request()->available==0 && request()->available!=null){
            $this->crud->addClause('whereIsAvailable', false);
        }
        $this->crud->addButtonFromView('top', 'filterDeliverymen', 'filterDeliverymen', 'end');
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
        CRUD::setValidation(DeliverymanRequest::class);
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.deliveryman_join_request'),
            'type'      => 'select',
            'name'      => 'deliveryman_join_request_id',
            'entity'    => 'deliverymanJoinRequest',
            'attribute' => 'id',
            'model'     => "App\Models\DeliverymanJoinRequest",
            'pivot'     => false,
        ]); 
        CRUD::field('phone_number')->label(trans('adminPanel.attributes.phone_number'));
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));
        CRUD::field('email')->label(trans('adminPanel.attributes.email'));
        CRUD::field('birth_date')->label(trans('adminPanel.attributes.birth_date'));
        CRUD::field('gender')->label(trans('adminPanel.attributes.gender'))->type('enum');
        CRUD::field('transportation_type')->label(trans('adminPanel.attributes.transportation_type'))->type('enum');
        CRUD::field('work_days')->label(trans('adminPanel.attributes.work_days'));
        CRUD::field('work_hours_from')->label(trans('adminPanel.attributes.work_hours_from'));
        CRUD::field('work_hours_to')->label(trans('adminPanel.attributes.work_hours_to'));
        CRUD::field('is_available')->label(trans('adminPanel.attributes.is_available'));
        CRUD::field('balance')->label(trans('adminPanel.attributes.balance'));
        CRUD::field('total_collected_order_costs')->label(trans('adminPanel.attributes.total_collected_order_costs'));
        CRUD::field('current_longitude')->label(trans('adminPanel.attributes.current_longitude'));
        CRUD::field('current_latitude')->label(trans('adminPanel.attributes.current_latitude'));


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
