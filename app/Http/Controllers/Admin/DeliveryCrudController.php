<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DeliveryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeliveryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DeliveryCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Delivery::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/delivery');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.delivery'), trans('adminPanel.entities.deliveries'));
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
            'name'     => 'deliveryman_id',
            'label'    =>  trans('adminPanel.entities.deliveryman'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/deliveryman/$entry->deliveryman_id/show' >". $entry->deliveryman->name." </a> ";
            },
        ]); 
        CRUD::addColumn([
            'label'    =>  trans('adminPanel.entities.orders'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                $orders=$entry->orders;
                $details="";
                $orders->map(function($order) use (&$details){
                    $details=$details."<a href='/admin/order/$order->id/show' > #". $order->id." </a> <br>";
                });
                return $details;
            },
        ]);
        CRUD::column('picked_at')->label(trans('adminPanel.attributes.picked_at'))->type('datetime');
        CRUD::column('delivered_at')->label(trans('adminPanel.attributes.delivered_at'))->type('datetime');
        CRUD::column('cost')->label(trans('adminPanel.attributes.cost'));
        CRUD::column('deliveryman_cost_share')->label(trans('adminPanel.attributes.deliveryman_cost_share'));
        CRUD::column('paid_to_deliveryman')->label(trans('adminPanel.attributes.paid_to_deliveryman'))->type('boolean');
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'))->type('datetime');
        

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
        CRUD::setValidation(DeliveryRequest::class);

        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');

        CRUD::field('deliveryman_id')->label(trans('adminPanel.entities.deliveryman'));
        CRUD::field('cost')->label(trans('adminPanel.attributes.cost'));
        CRUD::field('deliveryman_cost_share')->label(trans('adminPanel.attributes.deliveryman_cost_share'));

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
        CRUD::field('picked_at')->label(trans('adminPanel.attributes.picked_at'));
        CRUD::field('delivered_at')->label(trans('adminPanel.attributes.delivered_at'));
        CRUD::field('paid_to_deliveryman')->label(trans('adminPanel.attributes.paid_to_deliveryman'));
    }
}
