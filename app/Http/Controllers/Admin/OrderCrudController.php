<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Order::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/order');
        CRUD::setEntityNameStrings('order', 'orders');
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
        // CRUD::column('user_id');
        // CRUD::column('chef_id');
        // CRUD::column('delivery_id');
        // CRUD::column('subscription_id');
        CRUD::column('selected_delivery_time');
        CRUD::column('notes');
        CRUD::column('status');
        CRUD::column('total_cost');
        CRUD::column('meals_cost');
        CRUD::column('profit');
        CRUD::column('accepted_at');
        CRUD::column('prepared_at');
        CRUD::column('paid_to_chef');
        CRUD::column('paid_to_accountant');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        //$this->crud->addButtonFromView('line', 'approveOrReject', 'approveOrReject', 'beginning');

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
        CRUD::setValidation(OrderRequest::class);

        CRUD::field('id');
        CRUD::field('user_id');
        CRUD::field('chef_id');
        CRUD::field('delivery_id');
        CRUD::field('subscription_id');
        CRUD::field('selected_delivery_time');
        CRUD::field('notes');
        CRUD::field('status');
        CRUD::field('total_cost');
        CRUD::field('meals_cost');
        CRUD::field('profit');
        CRUD::field('accepted_at');
        CRUD::field('prepared_at');
        CRUD::field('paid_to_chef');
        CRUD::field('paid_to_accountant');
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
