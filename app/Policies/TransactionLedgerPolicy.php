<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TransactionLedger;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionLedgerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TransactionLedger');
    }

    public function view(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('View:TransactionLedger');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TransactionLedger');
    }

    public function update(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('Update:TransactionLedger');
    }

    public function delete(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('Delete:TransactionLedger');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TransactionLedger');
    }

    public function restore(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('Restore:TransactionLedger');
    }

    public function forceDelete(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('ForceDelete:TransactionLedger');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TransactionLedger');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TransactionLedger');
    }

    public function replicate(AuthUser $authUser, TransactionLedger $transactionLedger): bool
    {
        return $authUser->can('Replicate:TransactionLedger');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TransactionLedger');
    }

}