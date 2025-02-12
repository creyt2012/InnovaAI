<!-- Add this to your admin navigation -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link href="{{ route('admin.plugins.index') }}" :active="request()->routeIs('admin.plugins.*')">
        {{ __('Plugins') }}
    </x-nav-link>
</div> 