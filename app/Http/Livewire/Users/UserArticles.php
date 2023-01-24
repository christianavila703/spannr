<?php

namespace App\Http\Livewire\Users;

use App\Models\Article;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserArticles extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public User $user;
    public $perPage = 9;

    public function gotoPage($page)
    {
        $this->setPage($page);
        $this->emit('gotoTop');
    }

    public function nextPage()
    {
        $this->setPage($this->page + 1);
        $this->emit('gotoTop');
    }

    public function previousPage()
    {
        $this->setPage(max($this->page - 1, 1));
        $this->emit('gotoTop');
    }

    public function render()
    {
        return view('livewire.users.user-articles', [
            'articles' => Article::orderByDesc('date')
                ->where('user_id', $this->user->id)
                ->paginate($this->perPage)
        ]);
    }
}
