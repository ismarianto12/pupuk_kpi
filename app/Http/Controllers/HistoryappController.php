<?php

namespace App\Http\Controllers;

use App\Models\Historyapp;
use Illuminate\Http\Request;

class HistoryappController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Request $request)
    {

        $this->view = '';
        $this->route = '';
        $this->request = $request;

    }

    public function index()
    {
        $title = 'History Assigmnent';
        return view($this->view . 'history.assigment', [
            'title' => $title,

        ]);
    }

    public function assigment()
    {

        return 'Under Maintence';
    }
    public function peganjuan_kamus()
    {

        return 'Under Maintence';
    }
    public function chat()
    {

        return 'Under Maintence';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
     * @param  \App\Models\Historyapp  $historyapp
     * @return \Illuminate\Http\Response
     */
    public function show(Historyapp $historyapp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Historyapp  $historyapp
     * @return \Illuminate\Http\Response
     */
    public function edit(Historyapp $historyapp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Historyapp  $historyapp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Historyapp $historyapp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Historyapp  $historyapp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Historyapp $historyapp)
    {
        //
    }
}
