<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Query;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;


/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchStringQuerySpec extends ObjectBehavior
{

    function let(Request $request, ParameterBag $bag)
    {
        $bag->get('q')->willReturn('searchTerm');
        $bag->get('search_param')->willReturn('all');
        $bag->get('filters')->willReturn(array('test'));

        $request->query = $bag;

        $this->beConstructedWith($bag, true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Query\SearchStringQuery');
    }

    public function it_has_a_search_term()
    {
        $this->getSearchTerm()->shouldReturn('search term');
    }

    public function it_has_a_search_param()
    {
        $this->getSearchParam()->shouldReturn('all');
    }

    public function it_has_some_applied_filters()
    {
        $this->getAppliedFilters()->shouldReturn(array('test'));
    }

    public function it_should_take_in_mind_the_dropdown_filter()
    {
        $this->isDropdownFilterEnabled()->shouldReturn(true);
    }

} 