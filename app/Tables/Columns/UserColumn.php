<?php

namespace App\Tables\Columns;

use App\Models\User;
use Arr;
use Filament\Tables\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserColumn extends Column
{
    protected string $view = 'tables.columns.user-column';
    protected bool|Closure $shouldRedirect = true;

    public function getState(): mixed
    {
        $record = $this->getRecord();
        $user = $record instanceof User ? $record : $record->user ?? null;

        return $user ? [
            'id' => $user->id,
            'avatar' => $user->avatar(),
            'username' => $user->username,
            'email' => $user->email,
        ] : null;
    }

    public function applySearchConstraint(EloquentBuilder $query, string $search, bool &$isFirst): EloquentBuilder
    {
        if (!$query->getModel() instanceof User) {
            $this->searchQuery = function ($query, $search) use ($isFirst) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            };
        } else {
            $this->searchQuery = function ($query, $search) use ($isFirst) {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            };
        }

        return parent::applySearchConstraint($query, $search, $isFirst);
    }

    public function applyEagerLoading(EloquentBuilder|Relation $query): EloquentBuilder|Relation
    {
        if (!($query->getModel() instanceof User)) {
            return $query->with('user');
        }

        return $query;
    }

    public function redirect(bool|Closure $condition = true): static
    {
        $this->shouldRedirect = $condition;
        return $this;
    }

    public function shouldRedirect(): bool
    {
        return (bool)$this->evaluate($this->shouldRedirect);
    }
}
