@props(['name'])
<svg {{ $attributes->class(['portal-icon']) }} width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	@switch($name)
		@case('arrow')
			<path d="M4 12h15m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			@break
		@case('sword')
			<path d="m12 2 3 5v9H9V7l3-5Zm0 5v9M6 16h12m-6 0v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			<circle cx="12" cy="21" r="1.5" stroke="currentColor" stroke-width="1.5" />
			@break
		@case('shield')
			<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Zm0 5v8m-4-4h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			@break
		@case('compass')
			<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
			<path d="m16 8-2 6-6 2 2-6 6-2ZM12 1v2m0 18v2M1 12h2m18 0h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			@break
		@case('quill')
			<path d="M4 21 16 9M6 18l1-8 8-7h6v6l-7 8-8 1Zm5-9 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			@break
		@case('crown')
			<path d="m3 6 5 4 4-6 4 6 5-4-2 12H5L3 6Zm3 15h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			@break
		@case('vk')
			<path d="M3 6h3c.3 4 1.8 6 3 7V6h3v5c1.4-.2 3-2.4 4-5h3c-.7 2.5-2 4.6-3.5 6 1.5 1 3.2 3 4.5 6h-3.5c-1-2-2.8-4-4.5-4v4h-1C5.5 18 3.2 12 3 6Z" fill="currentColor" />
			@break
	@endswitch
</svg>
