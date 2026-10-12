<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $statuses = Status::withCount('orders')
            ->latest()
            ->paginate(10);

        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $status = new Status();
        return view('statuses.create', compact('status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StatusRequest $request): RedirectResponse
    {
        $status = Status::create($request->validated());

        return redirect()->route('statuses.index')
            ->with('success', "Durum '{$status->name}' başarıyla oluşturuldu.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status): View
    {
        $status->load(['orders.customer']);

        return view('statuses.show', compact('status'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status): View
    {
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StatusRequest $request, Status $status): RedirectResponse
    {
        $status->update($request->validated());

        return redirect()->route('statuses.index')
            ->with('success', "Durum '{$status->name}' başarıyla güncellendi.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status): RedirectResponse
    {
        $name = $status->name;
        $status->delete();

        return redirect()->route('statuses.index')
            ->with('success', "Durum '{$name}' başarıyla silindi.");
    }
}
