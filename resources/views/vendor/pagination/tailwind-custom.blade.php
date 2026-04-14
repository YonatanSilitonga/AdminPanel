@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center space-x-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 cursor-default rounded-xl">
                Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 leading-5 rounded-xl hover:text-sidebar focus:outline-none transition ease-in-out duration-150">
                Previous
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span aria-disabled="true">
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 cursor-default rounded-xl">{{ $element }}</span>
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page">
                            <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-sidebar border border-sidebar cursor-default rounded-xl shadow-lg shadow-sidebar/20">{{ $page }}</span>
                        </span>
                    @else
                        <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 leading-5 rounded-xl hover:text-sidebar focus:outline-none transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 leading-5 rounded-xl hover:text-sidebar focus:outline-none transition ease-in-out duration-150">
                Next
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 cursor-default rounded-xl">
                Next
            </span>
        @endif
    </nav>
@endif
