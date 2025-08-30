<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProfileStepNav extends Component
{
    public $steps;

    public function __construct($steps = [])
    {
        $this->steps = $steps;
    }

    public function render()
    {
        return view('mpm.components.profile-step-nav');
    }
}
