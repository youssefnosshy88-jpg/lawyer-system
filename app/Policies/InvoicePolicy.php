<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InvoicePolicy extends BasePolicy
{
    protected string $resource = 'invoices';

    public function view(User $user, Model $model): bool
    {
        if ($user->client_id !== null && (int) $model->client_id === (int) $user->client_id) {
            return true;
        }

        return parent::view($user, $model);
    }
}
