<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with('table')->get();
        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tables = Table::where('status', TableStatus::Available)->get();
        return view('admin.reservations.create', compact('tables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $reservationData = $request->validated();

        $reservation = Reservation::create($reservationData);

        return to_route('admin.reservations.index')
            ->with('success', "{$reservation->table->name} for guest $reservation->first_name $reservation->last_name has been successfully reserved.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $tables = Table::where('status', TableStatus::Available)->get();

        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $reservationData = $request->validated();

        $reservation->update($reservationData);

        return to_route('admin.reservations.index')->with('success', 'Reservation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return to_route('admin.reservations.index')->with('danger', 'Reservation deleted successfully');
    }
}
