<ul>
    <li class="sidebar-header"><a href="{{ url('world') }}" class="card-link">Information</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Characters</div>
        <div class="sidebar-item"><a href="{{ url('world/species') }}" class="{{ set_active('world/species*') }}">Species</a></div>
        <div class="sidebar-item"><a href="{{ url('world/subtypes') }}" class="{{ set_active('world/subtypes*') }}">Species Content</a></div>
        <div class="sidebar-item"><a href="{{ url('world/character-categories') }}" class="{{ set_active('world/character-categories*') }}">Species Categories</a></div>
        <div class="sidebar-item"><a href="{{ url('world/character-pedigrees') }}" class="{{ set_active('world/character-pedigrees*') }}">Character Pedigrees</a></div>
        <!--<div class="sidebar-item"><a href="{{ url('world/character-generations') }}" class="{{ set_active('world/character-generations*') }}">Character Generations</a></div>-->
    </li>
    <li class="sidebar-section">
    <div class="sidebar-section-header">Mutations</div>
        @if (config('lorekeeper.extensions.visual_trait_index.enable_universal_index'))
            <div class="sidebar-item"><a href="{{ url('world/universaltraits') }}" class="{{ set_active('world/universaltraits*') }}">Mutation Index</a></div>
        @endif
        <div class="sidebar-item"><a href="{{ url('world/trait-categories') }}" class="{{ set_active('world/trait-categories*') }}">Mutation Categories</a></div>
        <div class="sidebar-item"><a href="{{ url('world/rarities') }}" class="{{ set_active('world/rarities*') }}">Mutation Points</a></div>
        <div class="sidebar-item"><a href="{{ url('world/traits') }}" class="{{ set_active('world/traits*') }}">All Mutations</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Items</div>
        <div class="sidebar-item"><a href="{{ url('world/item-categories') }}" class="{{ set_active('world/item-categories*') }}">Item Categories</a></div>
        <div class="sidebar-item"><a href="{{ url('world/items') }}" class="{{ set_active('world/items*') }}">All Items</a></div>
        <div class="sidebar-item"><a href="{{ url('world/currencies') }}" class="{{ set_active('world/currencies*') }}">Currencies</a></div>
        <!--<div class="sidebar-item"><a href="{{ url('world/pets') }}" class="{{ set_active('world/pets*') }}">Pets</a></div>-->
        <div class="sidebar-item"><a href="{{ url('world/collections') }}" class="{{ set_active('world/collections*') }}">Collections</a></div>
        <div class="sidebar-item"><a href="{{ url('world/collection-categories') }}" class="{{ set_active('world/collection-categories*') }}">Collection Categories</a></div>
    </li>
</ul>
