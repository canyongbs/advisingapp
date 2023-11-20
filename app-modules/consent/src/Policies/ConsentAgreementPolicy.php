<?php

namespace Assist\Consent\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Consent\Models\ConsentAgreement;

class ConsentAgreementPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'consent_agreement.view-any',
            denyResponse: 'You do not have permission to view consent agreements.'
        );
    }

    public function view(User $user, ConsentAgreement $agreement): Response
    {
        return $user->canOrElse(
            abilities: ['consent_agreement.*.view', "consent_agreement.{$agreement->id}.view"],
            denyResponse: 'You do not have permission to view this consent agreement.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Consent Agreements cannot be created.');
    }

    public function update(User $user, ConsentAgreement $agreement): Response
    {
        return $user->canOrElse(
            abilities: ['consent_agreement.*.update', "consent_agreement.{$agreement->id}.update"],
            denyResponse: 'You do not have permission to update this consent agreement.'
        );
    }

    public function delete(User $user, ConsentAgreement $agreement): Response
    {
        return Response::deny('Consent Agreements cannot be deleted.');
    }

    public function restore(User $user, ConsentAgreement $agreement): Response
    {
        return Response::deny('Consent Agreements cannot be restored.');
    }

    public function forceDelete(User $user, ConsentAgreement $agreement): Response
    {
        return Response::deny('Consent Agreements cannot be permanently deleted.');
    }
}
