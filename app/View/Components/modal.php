<?php

namespace App\View\Components;

use Illuminate\View\Component;

class modal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $title;
    public $modalTitle;
    public $id;
    public $method;
    public $action;
    public $enctype;
    public function __construct($title, $modalTitle, $id, $enctype = "", $method = "", $action = "")
    {
        //
        $this->id = $id;
        $this->title = $title;
        $this->modalTitle = $modalTitle;
        $this->enctype = $enctype;
        $this->method = $method;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
