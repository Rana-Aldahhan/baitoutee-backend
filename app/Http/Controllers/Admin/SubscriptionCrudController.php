<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubscriptionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Subscription::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.subscription'), trans('adminPanel.entities.subscriptions'));
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
            'name'     => 'chef_id',
            'label'    =>  trans('adminPanel.entities.chef'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/chef/$entry->chef_id/show' >". $entry->chef->name." </a> ";
            },
        ]); 
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('days_number')->label(trans('adminPanel.attributes.days_number'));
        CRUD::addColumn([
            'label'    =>  trans('adminPanel.entities.meals'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                $meals=$entry->meals;
                $details="";
                $meals->map(function($meal) use (&$details){
                    $details=$details."<a href='/admin/meal/$meal->id/show' >  (".$meal->pivot->day_number.")". $meal->name ." </a> <br>";
                });
                return $details;
            },
        ]);
        CRUD::column('meals_cost')->label(trans('adminPanel.attributes.meals_cost'));
        CRUD::column('meal_delivery_time')->label(trans('adminPanel.attributes.meal_delivery_time'))->type('time');
        CRUD::column('is_available')->label(trans('adminPanel.attributes.is_available'))->type('boolean');
        CRUD::column('starts_at')->label(trans('adminPanel.attributes.starts_at'))->type('datetime');        
        CRUD::column('max_subscribers')->label(trans('adminPanel.attributes.max_subscribers'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'))->type('datetime');
        $this->crud->removeButtons(['create']);

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
        Gate::authorize('manage-orders');

        CRUD::setValidation(SubscriptionRequest::class);
        CRUD::field('chef_id')->label(trans('adminPanel.entities.chef'));
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));
        CRUD::field('days_number')->label(trans('adminPanel.attributes.days_number'))->type('text');
        CRUD::field('meal_delivery_time')->label(trans('adminPanel.attributes.meal_delivery_time'));
        CRUD::field('is_available')->label(trans('adminPanel.attributes.is_available'));
        CRUD::field('starts_at')->label(trans('adminPanel.attributes.starts_at'));
        CRUD::field('max_subscribers')->label(trans('adminPanel.attributes.max_subscribers'));
        CRUD::field('meals_cost')->label(trans('adminPanel.attributes.meals_cost'));

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
