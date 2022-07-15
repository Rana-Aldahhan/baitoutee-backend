<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate; 

/**
 * Class ReportCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Report::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report');
        CRUD::setEntityNameStrings(trans('adminPanel.entities.report'), trans('adminPanel.entities.reports'));
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
        CRUD::addColumn([
            'name'     => 'sendable_id',
            'label'    =>  trans('adminPanel.attributes.sendable'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                if($entry->sendable_type=='App\Models\User')
                    $sendable='user';
                else if($entry->sendable_type=='App\Models\Chef')
                    $sendable='chef';
                else if($entry->sendable_type=='App\Models\Deliveryman')
                    $sendable='deliveryman';
                return "<a href='/admin/$sendable/$entry->sendable_id/show' >". $entry->sendable->name." </a> ";
            }
        ]); 
        CRUD::addColumn([
            'name'     => 'receivable_id',
            'label'    =>  trans('adminPanel.attributes.receivable'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                if($entry->receivable_type=='App\Models\User')
                    $receivable='user';
                else if($entry->receivable_type=='App\Models\Chef')
                    $receivable='chef';
                else if($entry->receivable_type=='App\Models\Deliveryman')
                    $receivable='deliveryman';
                return "<a href='/admin/$receivable/$entry->receivable_id/show' >". $entry->receivable->name." </a> ";
            }
        ]); 
        CRUD::column('reason')->label(trans('adminPanel.attributes.reason'));
        CRUD::addColumn([
            'name'     => 'order_id',
            'label'    =>  trans('adminPanel.attributes.order'),
            'type'     => 'custom_html',
            'value' => function($entry) {
                return "<a href='/admin/order/$entry->order_id/show' >". $entry->order_id." </a> ";
            }
        ]); 
        // CRUD::column('seen')->label(trans('adminPanel.attributes.seen'));
        CRUD::column('created_at')->label(trans('adminPanel.attributes.created_at'));
        if(request()->filter=='seen'){
            $this->crud->addClause('whereSeen', true);
        }else if(request()->filter=='unseen'){
            $this->crud->addClause('whereSeen', false);
        }
        $this->crud->addButtonFromView('top', 'filterReports', 'filterReports', 'end');
        $this->crud->addButtonFromView('line', 'markAsSeen', 'markAsSeen', 'beginning');
        $this->crud->removeButtons(['create','update']);

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
        CRUD::setValidation(ReportRequest::class);

        CRUD::field('id');
        CRUD::field('sendable_type');
        CRUD::field('sendable_id');
        CRUD::field('receivable_type');
        CRUD::field('receivable_id');
        CRUD::field('order_id');
        CRUD::field('reason');
        CRUD::field('seen');
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
    /**
     * update a report to seen
     */
    public function markAsSeen($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('review-reports');
        $report=Report::findOrFail($id);
        $report->seen=true;
        $report->save();
        return redirect('/admin/report');
    }
}
