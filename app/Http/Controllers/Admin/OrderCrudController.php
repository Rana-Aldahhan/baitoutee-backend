<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

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
        CRUD::setEntityNameStrings(trans('adminPanel.entities.order'), trans('adminPanel.entities.orders'));
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
            'name'     => 'user_id',
            'label'    =>  trans('adminPanel.entities.user'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/user/$entry->user_id/show' >". $entry->user->name." </a> ";
            }
        ]); 
        CRUD::addColumn([
            'name'     => 'chef_id',
            'label'    =>  trans('adminPanel.entities.chef'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/chef/$entry->chef_id/show' >". $entry->chef->name." </a> ";
            }
        ]); 
        CRUD::addColumn([
            'label'    =>  trans('adminPanel.attributes.order_details'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                $meals=$entry->meals;
                $details="";
                $meals->map(function($meal) use (&$details){
                    $details=$details."<a href='/admin/meal/$meal->id/show' >". $meal->name." </a> (".$meal->pivot->quantity.")<br>";
                });
                return $details;
            },
        ]); 
        CRUD::column('selected_delivery_time')->label(trans('adminPanel.attributes.selected_delivery_time'))->type('datetime');
        CRUD::column('status')->label(trans('adminPanel.attributes.status'));
        CRUD::column('notes')->label(trans('adminPanel.attributes.notes'));
        CRUD::column('total_cost')->label(trans('adminPanel.attributes.total_cost'));
        CRUD::column('meals_cost')->label(trans('adminPanel.attributes.meals_cost'));
        CRUD::column('profit')->label(trans('adminPanel.attributes.profit'));
        CRUD::column('payment_method')->label(trans('adminPanel.attributes.payment_method'));
        CRUD::addColumn([
            'name'     => 'delivery_id',
            'label'    =>  trans('adminPanel.entities.delivery'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/delivery/$entry->delivery_id/show' >". $entry->delivery_id." </a> ";
            },
        ]); 
        CRUD::addColumn([
            'name'     => 'subscription_id',
            'label'    =>  trans('adminPanel.entities.subscription'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/subscription/$entry->subscription_id/show' >". $entry->subscription_id." </a> ";
            },
        ]); 
        CRUD::column('accepted_at')->label(trans('adminPanel.attributes.accepted_at'))->type('datetime');
        CRUD::column('prepared_at')->label(trans('adminPanel.attributes.prepared_at'));
        CRUD::column('paid_to_chef')->label(trans('adminPanel.attributes.paid_to_chef'))->type('boolean');
        CRUD::column('paid_to_accountant')->label(trans('adminPanel.attributes.paid_to_accountant'))->type('boolean');
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        if(request()->status!=null){
            $this->crud->addClause('whereStatus', request()->status);
        }
        $this->crud->addButtonFromView('top', 'filterOrderStatus', 'filterOrderStatus', 'end');
        $this->crud->addButtonFromView('line', 'cancelOrder', 'cancelOrder', 'end');
        $this->crud->addButtonFromView('line', 'approveRejectOrders', 'approveRejectOrders', 'beginning');
        $this->crud->addButtonFromView('line', 'reassignOrder', 'reassignOrder', 'end');
        $this->crud->removeButtons(['create','delete']);

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
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');

        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.user'),
            'type'      => 'select',
            'name'      => 'user_id',
            'entity'    => 'user',
            'attribute' => 'name',
            'model'     => "App\Models\User",
            'pivot'     => false,
        ]); 
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.chef'),
            'type'      => 'select',
            'name'      => 'chef_id',
            'entity'    => 'chef',
            'attribute' => 'name',
            'model'     => "App\Models\Chef",
            'pivot'     => false,
        ]); 
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.subscription'),
            'type'      => 'select',
            'name'      => 'subscription_id',
            'entity'    => 'subscription',
            'attribute' => 'name',
            'model'     => "App\Models\Subscription",
            'pivot'     => false,
        ]); 
        // CRUD::addField([   // Checklist
        //     'label'     => trans('adminPanel.entities.meals'),
        //     'type'      => 'select_multiple',
        //     'name'      => 'meals', 
        //     // optional
        //     'entity'    => 'meals',
        //     'model'     => "App\Models\Meal", 
        //     'attribute' => 'name',
        //     'pivot'     => true, 
        // ]); 
        // CRUD::field('meals')->subfields([
        //     ['name' => 'quantity', 'type' => 'text'],
        //     ['name' => 'notes']
        // ]);
        CRUD::field('selected_delivery_time')->label(trans('adminPanel.attributes.selected_delivery_time'));
        CRUD::field('notes')->label(trans('adminPanel.attributes.notes'));
        CRUD::field('total_cost')->label(trans('adminPanel.attributes.total_cost'));
        CRUD::field('meals_cost')->label(trans('adminPanel.attributes.meals_cost'));
        CRUD::field('profit')->label(trans('adminPanel.attributes.profit'));
        CRUD::addField([   // Checklist
            'label'     => trans('adminPanel.entities.delivery'),
            'type'      => 'select',
            'name'      => 'delivery_id',
            'entity'    => 'delivery',
            'attribute' => 'id',
            'model'     => "App\Models\Delivery",
            'pivot'     => false,
        ]); 


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
        CRUD::field('status')->label(trans('adminPanel.attributes.status'))->type('enum');
    }
}
