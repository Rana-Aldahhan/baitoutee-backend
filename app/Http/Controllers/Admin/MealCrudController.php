<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MealRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MealCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Meal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/meal');
        CRUD::setEntityNameStrings( trans('adminPanel.entities.meal'), trans('adminPanel.entities.meals'));
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
        CRUD::column('name')->label(trans('adminPanel.attributes.name'));
        CRUD::column('price')->label(trans('adminPanel.attributes.price'));
        CRUD::addColumn([
            'name'     => 'chef_id',
            'label'    =>  trans('adminPanel.entities.chef'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/chef/$entry->chef_id/show' >". $entry->chef->name." </a> ";
            }
        ]); 
        CRUD::column('ingredients')->label(trans('adminPanel.attributes.ingredients'));
        CRUD::addColumn([
            'name'     => 'category_id',
            'label'    => trans('adminPanel.entities.category'),
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->category->name;
            }
        ]); 
        CRUD::column('expected_preparation_time')->label(trans('adminPanel.attributes.expected_preparation_time'));
        CRUD::column('discount_percentage')->label(trans('adminPanel.attributes.discount_percentage'));
        CRUD::column('image')->prefix(url(''))->type('image')->label(trans('adminPanel.attributes.image'));
        CRUD::column('max_meals_per_day')->label(trans('adminPanel.attributes.max_meals_per_day'));
        CRUD::column('is_available')->label(trans('adminPanel.attributes.is_available'));
        CRUD::column('approved')->label(trans('adminPanel.attributes.approved'));
        CRUD::column('rating')->label(trans('adminPanel.attributes.rating'));
        CRUD::column('rates_count')->label(trans('adminPanel.attributes.rates_count'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        CRUD::column('updated_at')->label(trans('adminPanel.attributes.updated_at'));
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
        CRUD::setValidation(MealRequest::class);

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
            'label'     => trans('adminPanel.entities.category'),
            'type'      => 'select',
            'name'      => 'category_id',
            'entity'    => 'category',
            'attribute' => 'name',
            'model'     => "App\Models\Category",
            'pivot'     => false,
        ]); 
        CRUD::field('name')->label(trans('adminPanel.attributes.name'));
        CRUD::field('price')->label(trans('adminPanel.attributes.price'));
        CRUD::field('max_meals_per_day')->label(trans('adminPanel.attributes.max_meals_per_day'))->type('text');
        CRUD::field('is_available')->label(trans('adminPanel.attributes.is_available'));
        CRUD::field('expected_preparation_time')->label(trans('adminPanel.attributes.expected_preparation_time'))->type('text');
        CRUD::field('discount_percentage')->label(trans('adminPanel.attributes.discount_percentage'))->type('text');
        CRUD::field('ingredients')->label(trans('adminPanel.attributes.ingredients'));

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
