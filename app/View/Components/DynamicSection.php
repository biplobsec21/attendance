<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DynamicSection extends Component
{
    public $title;
    public $description;
    public $options;
    public $type;
    public $fields;

    /**
     * Create a new component instance.
     */
    public function __construct($title, $description, $options, $type, $fields = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->options = $options;
        $this->type = $type;
        $this->fields = $fields;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.dynamic-section');
    }
}
