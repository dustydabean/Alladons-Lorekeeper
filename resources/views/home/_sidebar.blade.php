<ul>
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Inventory</div>
        <div class="sidebar-item"><a href="{{ url('characters') }}" class="{{ set_active('characters') }}">My Alladons</a></div>
        <!--<div class="sidebar-item"><a href="{{ url('pets') }}" class="{{ set_active('pets*') }}">My Pets</a></div>-->
        <div class="sidebar-item"><a href="{{ url('breeding-permissions') }}" class="{{ set_active('breeding-permissions') }}">Breeding Permissions</a></div>
        <div class="sidebar-item"><a href="{{ url('inventory') }}" class="{{ set_active('inventory*') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ url('bank') }}" class="{{ set_active('bank*') }}">Bank</a></div>
        <div class="sidebar-item"><a href="{{ url('collection') }}" class="{{ set_active('collection*') }}">Collections</a></div>
        <div class="sidebar-item"><a href="{{ url('friends') }}" class="{{ set_active('friends') }}">My Friends</a></div>
        <div class="sidebar-item"><a href="{{ url('friends/requests') }}" class="{{ set_active('friends/requests') }}">My Friend Requests</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Activity</div>
        <div class="sidebar-item"><a href="{{ url('characters/transfers/incoming') }}" class="{{ set_active('characters/transfers*') }}">Character Transfers</a></div>
        <div class="sidebar-item"><a href="{{ url('submissions') }}" class="{{ set_active('submissions*') }}">Prompt Submissions</a></div>
        <div class="sidebar-item"><a href="{{ url('trades/open') }}" class="{{ set_active('trades/open*') }}">Trades</a></div>
        <div class="sidebar-item"><a href="{{ url('claims') }}" class="{{ set_active('claims*') }}">Claims</a></div>
        <!--<div class="sidebar-item"><a href="{{ url('characters/pairings') }}" class="{{ set_active('characters/pairings') }}">Character Pairings</a></div>-->
        <div class="sidebar-item"><a href="{{ url('comments/liked') }}" class="{{ set_active('comments/liked*') }}">Liked Comments</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Crafting</div>
        <div class="sidebar-item"><a href="{{ url('crafting') }}" class="{{ set_active('crafting') }}">My Recipes</a></div>
        <div class="sidebar-item"><a href="{{ url('world/recipes') }}" class="{{ set_active('world/recipes') }}">All Recipes</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Reports</div>
        <div class="sidebar-item"><a href="{{ url('reports') }}" class="{{ set_active('reports*') }}">Reports</a></div>
    </li>
</ul>
