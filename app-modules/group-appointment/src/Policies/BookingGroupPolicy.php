<?php

namespace AdvisingApp\GroupAppointment\Policies;

use AdvisingApp\GroupAppointment\Models\BookingGroup;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class BookingGroupPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.view-any'],
            denyResponse: 'You do not have permissions to view booking groups.'
        );
    }

    public function view(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.*.view'],
            denyResponse: 'You do not have permissions to view this booking group.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.create'],
            denyResponse: 'You do not have permissions to create booking groups.'
        );
    }

    public function update(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.*.update'],
            denyResponse: 'You do not have permissions to update this booking group.'
        );
    }

    public function delete(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.*.delete'],
            denyResponse: 'You do not have permissions to delete this booking group.'
        );
    }

    public function restore(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.*.restore'],
            denyResponse: 'You do not have permissions to restore this booking group.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['booking_group.*.force-delete'],
            denyResponse: 'You do not have permissions to force delete this booking group.'
        );
    }
}
