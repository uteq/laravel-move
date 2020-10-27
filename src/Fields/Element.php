<?php

namespace Uteq\Move\Fields;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use Uteq\Move\Concerns\AuthorizedToSee;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;

abstract class Element extends Component
{
    use Metable;
    use AuthorizedToSee;
    use Makeable;

    /**
     * The element's component.
     *
     * @var string
     */
    public string $component;

    /**
     * Indicates if the element is only shown on the detail screen.
     *
     * @var bool
     */
    public bool $onlyOnDetail = false;

    /**
     * Determine if the element should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return $this->authorizedToSee($request);
    }

    /**
     * Sets the component name for the element
     *
     * @param string $component
     */
    public function setComponent(string $component)
    {
        $this->component = $component;
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Specify that the element should only be shown on the detail view.
     *
     * @return $this
     */
    public function onlyOnDetail()
    {
        $this->onlyOnDetail = true;

        return $this;
    }
}
