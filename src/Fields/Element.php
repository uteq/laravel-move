<?php

namespace Uteq\Move\Fields;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use Uteq\Move\Concerns\AuthorizedToSee;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Contracts\ElementInterface;
use Uteq\Move\Fields\Concerns\ShowsConditionally;

abstract class Element extends Component implements ElementInterface
{
    use AuthorizedToSee;
    use Metable;
    use Makeable;
    use ShowsConditionally;

    /**
     * The element's component.
     *
     * @var string
     */
    public string $component;

    /**
     * The assigned panel, if available.
     */
    public string $panel;

    /**
     * Determine if the element should be displayed for the given request.
     *
     * @param Request $request
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
}
