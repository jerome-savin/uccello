<?php

namespace Sardoj\Uccello\Http\Controllers;

use Illuminate\Http\Request;
use Sardoj\Uccello\Domain;
use Sardoj\Uccello\Module;
use Debugbar;

class ApiController extends Controller
{
    const ITEMS_PER_PAGE = 20;

    /**
     * Display a listing of the resource. The result is formated differently if it is a classic query or one requested by datatable.
     * @param Domain $domain
     * @param Module $module
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Domain $domain, Module $module, Request $request)
    {
        if ($request->get('datatable')) {
            // Get data formated for Datatable
            $result = $this->getResultForDatatable($domain, $module, $request);
        } else {
            // Get entity model class
            $entityClass = $module->entity_class;

            // Paginate results
            $result = $entityClass::paginate(self::ITEMS_PER_PAGE) ?? [];
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function getResultForDatatable(Domain $domain, Module $module, Request $request)
    {
        $draw = (int) $request->get('draw');
        $start = (int) $request->get('start');
        $length = (int) $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');        

        // Get entity model class
        $entityClass = $module->entity_class;

        // If the class exists, make the query
        if (class_exists($entityClass)) {
            // Count all results
            $total = $entityClass::count();

            // Paginate results
            $query = $entityClass::skip($start)->take($length);

            // Order results
            foreach ($order as $orderInfo) {
                $columnIndex = (int) $orderInfo["column"];
                $column = $columns[$columnIndex];
                $query = $query->orderBy($column["data"], $orderInfo["dir"]);
            }

            // Make the query
            $data = $query->get();
        
        } else {
            $data = [];
            $total = 0;
        }

        return [
            "data" => $data,
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
        ];
    }
}