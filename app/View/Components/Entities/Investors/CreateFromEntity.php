<?php

namespace App\View\Components\Entities\Investors;

use Illuminate\View\Component;

class CreateFromEntity extends Component
{
    public string $entityType;
    public int $entityId;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $entityType, int $entityId)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.entities.investors.create-from-entity');
    }
}
