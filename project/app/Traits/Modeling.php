<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Modeling
{
  public function __construct()
  {
    // $this->perPage = request()->per_page ?? generalSetting()->paginate_count;
  }

  protected function getStatusBadgeAttribute()
  {
    if ($this->status == 1) {
      return getBadge('Active', 'success');
    }

    if ($this->status == 0) {
      return getBadge('Inactive', 'danger');
    }
  }

  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  public function scopeInactive($query)
  {
    return $query->where('status', 0);
  }

  public function scopeSorting(Builder $query)
  {
    if (request()->sort_by) $query->orderBy(request()->sort_by ?? 'id', request()->sort_type ?? 'asc');
  }

  public function scopeSearching(Builder $query, array $fields = [])
  {
   

    $search = trim((string) request()->get('search', ''));

    if ($search === '' || empty($fields)) {
      return $query;
    }

    return $query->where(function (Builder $q) use ($fields, $search) {
      foreach ($fields as $field) {
        if (str_contains($field, ':')) {
          [$relation, $relatedField] = explode(':', $field, 2);

          $q->orWhereHas($relation, function (Builder $rq) use ($relatedField, $search) {
            $rq->where($relatedField, 'LIKE', "%{$search}%");
          });
        } else {
          $q->orWhere($field, 'LIKE', "%{$search}%");
        }
      }
    });
  }
}
