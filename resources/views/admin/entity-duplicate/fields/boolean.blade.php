<input class="form-control" type="text" value="{{ ($entity->{$name} === 1 || (boolean)$entity->{$name} === true) ? 'Yes (true)' : 'No (false)' }}" readonly/>
