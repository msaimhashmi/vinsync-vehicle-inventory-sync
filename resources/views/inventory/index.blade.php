<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Julio Tu Car Guy</title>
    <meta name="description" content="Browse our full inventory of new and used vehicles.">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

{{-- ══════════════════════════════════════════════
     TOP NAVIGATION
══════════════════════════════════════════════ --}}
<nav class="top-nav">
    <div class="nav-container">

        {{-- Mobile brand --}}
        <a href="/" class="nav-brand">Julio Tu Car Guy</a>

        {{-- Hamburger --}}
        <button class="nav-toggler" id="navToggler" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>

        {{-- Collapsible wrapper on mobile --}}
        <div class="nav-collapse-wrapper" id="navCollapse">
            <div class="nav-links" id="navLinks">
                <a href="#">Home</a>
                <a href="#">About Us</a>
                <a href="{{ route('inventory.index') }}" class="active">Inventory</a>
                <a href="#" class="has-caret">Financial Services</a>
                <a href="#">Contact Us</a>
            </div>
            <div class="nav-ctas" id="navCtas">
                <a href="#" class="btn-gradient" style="font-size:12px;padding:7px 16px;line-height:1.3;border-radius:6px;">
                    Apply<br>Now
                </a>
                <a href="#" class="btn-gradient">
                    <i class="bi bi-geo-alt-fill"></i>Visit<br>Us
                </a>
            </div>
        </div>

    </div>
</nav>

{{-- ══════════════════════════════════════════════
     PAGE BANNER
══════════════════════════════════════════════ --}}
<div class="page-banner">
    <h1>Inventory</h1>
</div>

{{-- ══════════════════════════════════════════════
     SEARCH BAR
══════════════════════════════════════════════ --}}
<div class="search-section">
    <div class="search-inner">
        <form method="GET" action="{{ route('inventory.index') }}" id="searchForm">
            {{-- Preserve non-search filters across search submits --}}
            @foreach(request()->except(['search','page']) as $k => $v)
                @if(is_array($v))
                    @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by make, model, year, VIN, stock..."
                       value="{{ request('search') }}">
                <button type="submit" class="btn-gradient btn-search-submit">Search</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MAIN LAYOUT
══════════════════════════════════════════════ --}}
<div class="inventory-wrapper">

    {{-- Mobile: toggle filter sidebar --}}
    <button class="btn-gradient filter-toggle-btn" id="filterToggle">
        <i class="bi bi-funnel-fill me-2"></i> Filter Vehicles
        <i class="bi bi-chevron-down ms-auto" id="filterChevron"></i>
    </button>

    <div class="inventory-body">

        {{-- ══ LEFT SIDEBAR ══ --}}
        <aside class="filter-sidebar" id="filterSidebar">
            <form method="GET" action="{{ route('inventory.index') }}" id="filterForm">
                {{-- Preserve sort & view & search across filter changes --}}
                @foreach(['sort','view','search'] as $pk)
                    @if(request($pk))
                        <input type="hidden" name="{{ $pk }}" value="{{ request($pk) }}">
                    @endif
                @endforeach

                {{-- Year --}}
                <div class="filter-group">
                    <label>Year</label>
                    <select name="year" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Year</option>
                        @foreach($options['years'] as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Make --}}
                <div class="filter-group">
                    <label>Make</label>
                    <select name="make" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Make</option>
                        @foreach($options['makes'] as $m)
                            <option value="{{ $m }}" {{ request('make') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Model --}}
                <div class="filter-group">
                    <label>Model</label>
                    <select name="model" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Model</option>
                        @foreach($options['models'] as $m)
                            <option value="{{ $m }}" {{ request('model') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Trim --}}
                <div class="filter-group">
                    <label>Trim</label>
                    <select name="trim" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Trim</option>
                        @foreach($options['trims'] as $t)
                            <option value="{{ $t }}" {{ request('trim') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Max Price --}}
                <div class="filter-group">
                    <label>Max Price</label>
                    <select name="price_max" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Price</option>
                        @foreach([15000,20000,25000,30000,35000,40000,50000,60000,75000,100000] as $p)
                            <option value="{{ $p }}" {{ request('price_max') == $p ? 'selected' : '' }}>
                                ${{ number_format($p) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Max Mileage --}}
                <div class="filter-group">
                    <label>Max Mileage</label>
                    <select name="mileage_max" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Mileage</option>
                        @foreach([5000,10000,15000,25000,50000,75000,100000,150000] as $mi)
                            <option value="{{ $mi }}" {{ request('mileage_max') == $mi ? 'selected' : '' }}>
                                {{ number_format($mi) }} mi
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Body Style --}}
                <div class="filter-group">
                    <label>Body Style</label>
                    <select name="body_style" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Body Style</option>
                        @foreach($options['body_styles'] as $b)
                            <option value="{{ $b }}" {{ request('body_style') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Exterior Color --}}
                <div class="filter-group">
                    <label>Color</label>
                    <select name="exterior_color" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Color</option>
                        @foreach($options['colors'] as $c)
                            <option value="{{ $c }}" {{ request('exterior_color') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cylinders --}}
                @if($options['cylinders']->isNotEmpty())
                <div class="filter-group">
                    <label>Cylinders</label>
                    <select name="cylinders" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Cylinders</option>
                        @foreach($options['cylinders'] as $cyl)
                            <option value="{{ $cyl }}" {{ request('cylinders') == $cyl ? 'selected' : '' }}>
                                {{ $cyl }}-Cylinder
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Engine Size --}}
                @if($options['engine_sizes']->isNotEmpty())
                <div class="filter-group">
                    <label>Engine Size</label>
                    <select name="engine_filter" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Engine Size</option>
                        @foreach($options['engine_sizes'] as $es)
                            <option value="{{ $es }}" {{ request('engine_filter') === $es ? 'selected' : '' }}>{{ $es }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Transmission --}}
                <div class="filter-group">
                    <label>Transmission</label>
                    <select name="transmission" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Transmission</option>
                        @foreach($options['transmissions'] as $t)
                            <option value="{{ $t }}" {{ request('transmission') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Features --}}
                @if($options['features_list']->isNotEmpty())
                <div class="filter-group">
                    <label>Features</label>
                    <select name="feature" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Feature</option>
                        @foreach($options['features_list'] as $feat)
                            <option value="{{ $feat }}" {{ request('feature') === $feat ? 'selected' : '' }}>{{ $feat }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Fuel Type --}}
                <div class="filter-group">
                    <label>Fuel Type</label>
                    <select name="fuel_type" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Fuel Type</option>
                        @foreach($options['fuel_types'] as $f)
                            <option value="{{ $f }}" {{ request('fuel_type') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Drivetrain --}}
                <div class="filter-group">
                    <label>Drivetrain</label>
                    <select name="drivetrain" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Drivetrain</option>
                        @foreach($options['drivetrains'] as $d)
                            <option value="{{ $d }}" {{ request('drivetrain') === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Condition --}}
                <div class="filter-group">
                    <label>Vehicle Condition</label>
                    <select name="condition" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Any Condition</option>
                        <option value="new"  {{ request('condition') === 'new'  ? 'selected' : '' }}>New</option>
                        <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Used</option>
                    </select>
                </div>

                {{-- Reset --}}
                <a href="{{ route('inventory.index') }}" class="btn-gradient btn-reset-filters">
                    Reset All Filters
                </a>
            </form>
        </aside>

        {{-- ══ RIGHT CONTENT ══ --}}
        <div class="inventory-content">

            {{-- Active filter chips --}}
            @php
                $chips = array_filter([
                    'condition'      => request('condition') ? ucfirst(request('condition')) : null,
                    'year'           => request('year'),
                    'make'           => request('make'),
                    'model'          => request('model'),
                    'trim'           => request('trim'),
                    'body_style'     => request('body_style') ?: null,
                    'exterior_color' => request('exterior_color'),
                    'cylinders'      => request('cylinders') ? request('cylinders').'-Cylinder' : null,
                    'engine_filter'  => request('engine_filter'),
                    'transmission'   => request('transmission'),
                    'feature'        => request('feature'),
                    'fuel_type'      => request('fuel_type'),
                    'drivetrain'     => request('drivetrain'),
                    'price_max'      => request('price_max') ? '$'.number_format(request('price_max')).' max' : null,
                    'mileage_max'    => request('mileage_max') ? number_format(request('mileage_max')).' mi max' : null,
                    'search'         => request('search') ? '"'.request('search').'"' : null,
                ]);
            @endphp
            @if(count($chips))
                <div class="active-chips">
                    <span class="chip-label">Active:</span>
                    @foreach($chips as $key => $label)
                        <a href="{{ route('inventory.index', request()->except([$key,'page'])) }}"
                           class="filter-chip">
                            {{ $label }}
                            <i class="bi bi-x"></i>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- ── TOOLBAR ── --}}
            <div class="results-toolbar">
                <div class="count-text">
                    @if($vehicles->total() > 0)
                        Showing <strong>{{ $vehicles->firstItem() }}–{{ $vehicles->lastItem() }}</strong>
                        of <strong>{{ $vehicles->total() }}</strong> vehicles
                    @else
                        No vehicles found
                    @endif
                </div>

                <div class="toolbar-right">

                    {{-- Grid / List toggle --}}
                    <div class="view-toggle" role="group" aria-label="View mode">
                        <a href="{{ route('inventory.index', array_merge(request()->except(['view','page']), ['view'=>'grid'])) }}"
                           class="view-btn {{ $view === 'grid' ? 'active' : '' }}"
                           title="Grid view">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </a>
                        <a href="{{ route('inventory.index', array_merge(request()->except(['view','page']), ['view'=>'list'])) }}"
                           class="view-btn {{ $view === 'list' ? 'active' : '' }}"
                           title="List view">
                            <i class="bi bi-list-ul"></i>
                        </a>
                    </div>

                    {{-- Sort --}}
                    <form method="GET" action="{{ route('inventory.index') }}" class="sort-form" id="sortForm">
                        @foreach(request()->except(['sort','page']) as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <label>Sort by:</label>
                        <select name="sort" onchange="document.getElementById('sortForm').submit()">
                            <option value="year_desc"  {{ request('sort','year_desc') === 'year_desc'  ? 'selected':'' }}>Newly Listed</option>
                            <option value="year_asc"   {{ request('sort') === 'year_asc'   ? 'selected':'' }}>Oldest First</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected':'' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected':'' }}>Price: High to Low</option>
                            <option value="mileage"    {{ request('sort') === 'mileage'    ? 'selected':'' }}>Lowest Mileage</option>
                        </select>
                    </form>

                </div>
            </div>
            {{-- /toolbar --}}

            {{-- ════════════════════════════════════
                 VEHICLE LISTING
            ════════════════════════════════════ --}}
            @php
                /* Set $quickView = true to enable the Quick View modal feature */
                $quickView = true;

                /* Strip HTML tags + decode entities from API data fields */
                $ct = fn($s) => $s ? html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
            @endphp
            @if($vehicles->isEmpty())

                <div class="empty-state">
                    <i class="bi bi-car-front"></i>
                    <h5>No vehicles found</h5>
                    <p>Try widening your search or resetting the filters.</p>
                    <a href="{{ route('inventory.index') }}" class="btn-gradient" style="padding:10px 24px;font-size:14px;border-radius:5px;margin-top:14px;display:inline-flex;">
                        Clear All Filters
                    </a>
                </div>

            @elseif($view === 'list')

                {{-- ───── LIST VIEW ───── --}}
                <div class="vehicles-list">
                    @foreach($vehicles as $vehicle)
                    @if($quickView)
                    @php
                        $vData = [
                            'title'          => $vehicle->year.' '.$vehicle->make.' '.$vehicle->model.($vehicle->trim ? ' — '.$vehicle->trim : ''),
                            'condition'      => $vehicle->condition,
                            'price'          => $vehicle->display_price ? '$'.number_format($vehicle->display_price) : null,
                            'vin'            => $vehicle->vin,
                            'stock_number'   => $vehicle->stock_number,
                            'year'           => $vehicle->year,
                            'make'           => $vehicle->make,
                            'model'          => $vehicle->model,
                            'trim'           => $vehicle->trim,
                            'body_style'     => $vehicle->body_style,
                            'mileage'        => $vehicle->mileage !== null ? number_format($vehicle->mileage).' mi' : null,
                            'engine'         => $ct($vehicle->engine),
                            'transmission'   => $ct($vehicle->transmission),
                            'drivetrain'     => $vehicle->drivetrain,
                            'fuel_type'      => $vehicle->fuel_type,
                            'mpg'            => $vehicle->mpg_city ? $vehicle->mpg_city.'/'.$vehicle->mpg_hwy.' MPG' : null,
                            'exterior_color' => $vehicle->exterior_color,
                            'interior_color' => $vehicle->interior_color,
                            'dealer_name'    => $vehicle->dealer_name,
                            'dealer_city'    => $vehicle->dealer_city,
                            'dealer_state'   => $vehicle->dealer_state,
                            'features'       => $vehicle->features ?? [],
                            'images'         => $vehicle->images ?? [],
                            'primary_image'  => $vehicle->primary_image,
                            'detail_url'     => $vehicle->detail_url,
                        ];
                    @endphp
                    @endif
                        <div class="v-row">

                            {{-- Thumbnail --}}
                            <div class="row-img-wrap">
                                <img src="{{ $vehicle->primary_image }}"
                                     alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                                     loading="lazy">
                                <span class="cond-pill {{ $vehicle->condition === 'used' ? 'used' : '' }}">
                                    {{ strtoupper($vehicle->condition) }}
                                </span>
                                @if($quickView)
                                <button type="button" class="quick-view-overlay" data-vehicle='{{ json_encode($vData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) }}'>
                                    <i class="bi bi-eye-fill"></i> Quick View
                                </button>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="row-body">
                                <div class="v-title">
                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                    @if($vehicle->trim) &mdash; {{ $vehicle->trim }} @endif
                                </div>
                                <div class="v-meta">
                                    @if($vehicle->mileage !== null)
                                        {{ number_format($vehicle->mileage) }} miles
                                    @endif
                                    @if($vehicle->stock_number)
                                        <span class="bull">Stock #{{ $vehicle->stock_number }}</span>
                                    @endif
                                    @if($vehicle->vin)
                                        <span class="bull">VIN {{ $vehicle->vin }}</span>
                                    @endif
                                </div>
                                <div class="row-specs">
                                    @if($vehicle->engine)
                                        <span><i class="bi bi-cpu"></i>{{ $ct($vehicle->engine) }}</span>
                                    @endif
                                    @if($vehicle->transmission)
                                        <span><i class="bi bi-gear"></i>{{ $ct($vehicle->transmission) }}</span>
                                    @endif
                                    @if($vehicle->drivetrain)
                                        <span><i class="bi bi-arrows-move"></i>{{ $vehicle->drivetrain }}</span>
                                    @endif
                                    @if($vehicle->exterior_color)
                                        <span><i class="bi bi-palette"></i>{{ $vehicle->exterior_color }}</span>
                                    @endif
                                    @if($vehicle->mpg_city)
                                        <span><i class="bi bi-speedometer"></i>{{ $vehicle->mpg_city }}/{{ $vehicle->mpg_hwy }} MPG</span>
                                    @endif
                                    @if(!$quickView)
                                        @if($vehicle->fuel_type)
                                            <span><i class="bi bi-fuel-pump"></i>{{ $vehicle->fuel_type }}</span>
                                        @endif
                                        @if($vehicle->interior_color)
                                            <span><i class="bi bi-palette2"></i>Int: {{ $vehicle->interior_color }}</span>
                                        @endif
                                        @if($vehicle->body_style)
                                            <span><i class="bi bi-car-front"></i>{{ $vehicle->body_style }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Price + buttons --}}
                            <div class="row-price-col">
                                <div class="v-price">
                                    @if($vehicle->display_price)
                                        ${{ number_format($vehicle->display_price) }}
                                    @else
                                        <span style="font-size:14px;color:#888;">Call for Price</span>
                                    @endif
                                </div>
                                <div class="v-price-note">Plus taxes, tags &amp; fees</div>
                                <div class="row-btns">
                                    @if($quickView)
                                    <button type="button" class="btn-outline-blue" style="padding: 6px 12px;" data-vehicle='{{ json_encode($vData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) }}'>
                                        View Details
                                    </button>
{{--                                        <button type="button" class="btn-quick-view-sm" data-vehicle='{{ json_encode($vData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) }}'>--}}
{{--                                            <i class="bi bi-eye"></i> View Details--}}
{{--                                        </button>--}}
                                    @endif
{{--                                    <a href="{{ $vehicle->detail_url ?? '#' }}"--}}
{{--                                       @if($vehicle->detail_url) target="_blank" rel="noopener" @endif--}}
{{--                                       class="btn-outline-blue">View Details</a>--}}
                                    <a href="#" class="btn-gradient">Apply Now</a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

            @else

                {{-- ───── GRID VIEW (default) ───── --}}
                <div class="vehicles-grid">
                    @foreach($vehicles as $vehicle)
                    @if($quickView)
                    @php
                        $vData = [
                            'title'          => $vehicle->year.' '.$vehicle->make.' '.$vehicle->model.($vehicle->trim ? ' — '.$vehicle->trim : ''),
                            'condition'      => $vehicle->condition,
                            'price'          => $vehicle->display_price ? '$'.number_format($vehicle->display_price) : null,
                            'vin'            => $vehicle->vin,
                            'stock_number'   => $vehicle->stock_number,
                            'year'           => $vehicle->year,
                            'make'           => $vehicle->make,
                            'model'          => $vehicle->model,
                            'trim'           => $vehicle->trim,
                            'body_style'     => $vehicle->body_style,
                            'mileage'        => $vehicle->mileage !== null ? number_format($vehicle->mileage).' mi' : null,
                            'engine'         => $ct($vehicle->engine),
                            'transmission'   => $ct($vehicle->transmission),
                            'drivetrain'     => $vehicle->drivetrain,
                            'fuel_type'      => $vehicle->fuel_type,
                            'mpg'            => $vehicle->mpg_city ? $vehicle->mpg_city.'/'.$vehicle->mpg_hwy.' MPG' : null,
                            'exterior_color' => $vehicle->exterior_color,
                            'interior_color' => $vehicle->interior_color,
                            'dealer_name'    => $vehicle->dealer_name,
                            'dealer_city'    => $vehicle->dealer_city,
                            'dealer_state'   => $vehicle->dealer_state,
                            'features'       => $vehicle->features ?? [],
                            'images'         => $vehicle->images ?? [],
                            'primary_image'  => $vehicle->primary_image,
                            'detail_url'     => $vehicle->detail_url,
                        ];
                    @endphp
                    @endif
                        <div class="v-card">

                            {{-- Image --}}
                            <div class="img-wrap">
                                <img src="{{ $vehicle->primary_image }}"
                                     alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                                     loading="lazy">
                                <span class="cond-pill {{ $vehicle->condition === 'used' ? 'used' : '' }}">
                                    {{ strtoupper($vehicle->condition) }}
                                </span>
                                @if($quickView)
                                <button type="button" class="quick-view-overlay" data-vehicle='{{ json_encode($vData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) }}'>
                                    <i class="bi bi-eye-fill"></i> Quick View
                                </button>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="card-body-wrap">
                                <div class="v-title">
                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                    @if($vehicle->trim) <span class="v-trim">&mdash; {{ $vehicle->trim }}</span> @endif
                                </div>
                                <div class="v-meta">
                                    @if($vehicle->mileage !== null)
                                        {{ number_format($vehicle->mileage) }} mi
                                    @endif
                                    @if($vehicle->stock_number)
                                        <span class="bull">Stock #{{ $vehicle->stock_number }}</span>
                                    @endif
                                    @if(!$quickView && $vehicle->vin)
                                        <span class="bull">VIN {{ $vehicle->vin }}</span>
                                    @endif
                                </div>
                                <div class="card-specs">
                                    @if($vehicle->engine)
                                        <span><i class="bi bi-cpu"></i>{{ $ct($vehicle->engine) }}</span>
                                    @endif
                                    @if($vehicle->transmission)
                                        <span><i class="bi bi-gear"></i>{{ $ct($vehicle->transmission) }}</span>
                                    @endif
                                    @if($vehicle->drivetrain)
                                        <span><i class="bi bi-arrows-move"></i>{{ $vehicle->drivetrain }}</span>
                                    @endif
                                    @if($vehicle->exterior_color)
                                        <span><i class="bi bi-palette"></i>{{ $vehicle->exterior_color }}</span>
                                    @endif
                                    @if(!$quickView)
                                        @if($vehicle->fuel_type)
                                            <span><i class="bi bi-fuel-pump"></i>{{ $vehicle->fuel_type }}</span>
                                        @endif
                                        @if($vehicle->mpg_city)
                                            <span><i class="bi bi-speedometer"></i>{{ $vehicle->mpg_city }}/{{ $vehicle->mpg_hwy }} MPG</span>
                                        @endif
                                        @if($vehicle->interior_color)
                                            <span><i class="bi bi-palette2"></i>Int: {{ $vehicle->interior_color }}</span>
                                        @endif
                                        @if($vehicle->body_style)
                                            <span><i class="bi bi-car-front"></i>{{ $vehicle->body_style }}</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="v-price">
                                    @if($vehicle->display_price)
                                        ${{ number_format($vehicle->display_price) }}
                                    @else
                                        <span style="font-size:13px;color:#888;font-weight:600;">Call for Price</span>
                                    @endif
                                </div>
                                <div class="v-price-note">Plus taxes, tags &amp; fees</div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="card-actions">
                                @if($quickView)
                                <button type="button" class="btn-outline-blue" data-vehicle='{{ json_encode($vData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) }}'>
                                    View Details
                                </button>
                                @endif
{{--                                <a href="{{ $vehicle->detail_url ?? '#' }}"--}}
{{--                                   @if($vehicle->detail_url) target="_blank" rel="noopener" @endif--}}
{{--                                   class="btn-outline-blue">View Details</a>--}}
                                <a href="#" class="btn-gradient">Apply Now</a>
                            </div>

                        </div>
                    @endforeach
                </div>

            @endif
            {{-- /listing --}}

            {{-- Pagination --}}
            @if($vehicles->hasPages())
                <div class="pagination-wrap">
                    {{ $vehicles->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>{{-- /inventory-content --}}
    </div>{{-- /inventory-body --}}
</div>{{-- /inventory-wrapper --}}

@if($quickView)
{{-- ══════════════════════════════════════════════
     IMAGE ZOOM LIGHTBOX
══════════════════════════════════════════════ --}}
<div id="qvZoomOverlay">
    <button id="qvZoomClose" aria-label="Close zoom">&times;</button>
    <img id="qvZoomImg" src="" alt="">
</div>

{{-- ══════════════════════════════════════════════
     QUICK VIEW MODAL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="qvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content qv-modal-content">

            <div class="modal-header qv-header">
                <div class="qv-header-left">
                    <span class="cond-pill qv-cond-pill" id="qvCondPill"></span>
                    <h5 class="modal-title qv-title" id="qvModalLabel"></h5>
                    <div class="qv-price-line">
                        <span class="qv-price" id="qvPrice"></span>
                        <span class="qv-price-note">Plus taxes, tags &amp; fees</span>
                    </div>
                </div>
                <button type="button" class="btn-close qv-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body qv-body">
                <div class="qv-layout">

                    {{-- Left: image gallery --}}
                    <div class="qv-gallery">
                        <div class="qv-main-img-wrap">
                            <img id="qvMainImg" src="" alt="" class="qv-main-img">
                            <button class="qv-gallery-nav qv-prev" id="qvPrev" aria-label="Previous image">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="qv-gallery-nav qv-next" id="qvNext" aria-label="Next image">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <span class="qv-img-counter" id="qvImgCounter"></span>
                        </div>
                        <div class="qv-thumbs" id="qvThumbs"></div>
                    </div>

                    {{-- Right: specs + features --}}
                    <div class="qv-details">

                        <div class="qv-section-title">Vehicle Details</div>
                        <div class="qv-specs-grid" id="qvSpecsGrid"></div>

                        <div class="qv-section-title qv-features-title" id="qvFeaturesTitle" style="display:none">Features &amp; Options</div>
                        <div class="qv-features-wrap" id="qvFeatures"></div>

                    </div>
                </div>
            </div>

            <div class="modal-footer qv-footer">
                <a href="#" class="btn-gradient" id="qvApply" style="padding:10px 22px;font-size:14px;border-radius:6px;">
                    Apply Now
                </a>
{{--                <a href="#" class="btn-outline-blue qv-view-details" id="qvViewDetails" target="_blank" rel="noopener">--}}
{{--                    View Full Details <i class="bi bi-box-arrow-up-right ms-1" style="font-size:11px;"></i>--}}
{{--                </a>--}}
            </div>

        </div>
    </div>
</div>
@endif {{-- $quickView --}}

{{-- ══════════════════════════════════════════════
     FOOTER CTA STRIP
══════════════════════════════════════════════ --}}
<div class="footer-cta">
    <div class="cta-text">Have a question? Feel free to ask</div>
    <div class="cta-icons">
        <a href="tel:" title="Call us"><i class="bi bi-telephone-fill"></i></a>
        <a href="#"   title="Location"><i class="bi bi-geo-alt-fill"></i></a>
        <a href="#"   title="Email"><i class="bi bi-envelope-fill"></i></a>
        <a href="#"   title="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#"   title="Instagram"><i class="bi bi-instagram"></i></a>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     SITE FOOTER
══════════════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">

            <div class="col-12 col-md-4 col-lg-3">
                <p class="footer-brand-text">
                    At Julio Tu Car Guy, we believe buying a car shouldn't be
                    complicated, overpriced, or stressful.
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-2 offset-lg-1">
                <h6>Vehicles</h6>
                <ul>
                    <li><a href="{{ route('inventory.index', ['body_style' => 'SUV']) }}">SUVs</a></li>
                    <li><a href="{{ route('inventory.index', ['body_style' => 'Truck']) }}">Trucks</a></li>
                    <li><a href="{{ route('inventory.index', ['body_style' => 'Crossover']) }}">Crossovers</a></li>
                    <li><a href="{{ route('inventory.index', ['fuel_type' => 'Hybrid']) }}">Hybrids</a></li>
                    <li><a href="{{ route('inventory.index', ['fuel_type' => 'Electric']) }}">Electrified Vehicles</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6>Helpful Links</h6>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li><a href="#">Financial Services</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6>Help</h6>
                <ul>
                    <li><a href="#">How To Buy A Car?</a></li>
                    <li><a href="#">FAQ's</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms &amp; Conditions</a></li>
                    <li><a href="#">Legal Disclaimer</a></li>
                </ul>
            </div>

        </div>
    </div>
</footer>
<div class="footer-bar">
    &copy; {{ date('Y') }} Julio Tu Car Guy &mdash;
    Inventory synced automatically every 12 hours. All prices subject to change without notice.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    /* ── Mobile nav toggle ── */
    document.getElementById('navToggler').addEventListener('click', function () {
        document.getElementById('navCollapse').classList.toggle('open');
    });

    /* ── Mobile filter sidebar toggle ── */
    document.getElementById('filterToggle').addEventListener('click', function () {
        const sidebar  = document.getElementById('filterSidebar');
        const chevron  = document.getElementById('filterChevron');
        const isOpen   = sidebar.classList.toggle('open');
        chevron.className = isOpen ? 'bi bi-chevron-up ms-auto' : 'bi bi-chevron-down ms-auto';
    });

    /* ── On desktop, always show sidebar (remove mobile-open class conflicts) ── */
    function handleResize() {
        const sidebar = document.getElementById('filterSidebar');
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('open');
        }
    }
    window.addEventListener('resize', handleResize);

    @if($quickView)
    /* ── Quick View Modal ── */
    (function () {
        var bsModal   = null;
        var images    = [];
        var curImg    = 0;

        function getEl(id) { return document.getElementById(id); }

        function getModal() {
            if (!bsModal) bsModal = new bootstrap.Modal(getEl('quickViewModal'));
            return bsModal;
        }

        function setImage(idx) {
            if (!images.length) return;
            curImg = (idx + images.length) % images.length;
            var mainImg = getEl('qvMainImg');
            if (mainImg) mainImg.src = images[curImg];
            var counter = getEl('qvImgCounter');
            if (counter) counter.textContent = images.length > 1 ? (curImg + 1) + ' / ' + images.length : '';
            var thumbs = getEl('qvThumbs');
            if (thumbs) thumbs.querySelectorAll('.qv-thumb').forEach(function (t, i) {
                t.classList.toggle('active', i === curImg);
            });
        }

        /* ── Zoom lightbox ── */
        var zoomOverlay = document.getElementById('qvZoomOverlay');
        var zoomImg     = document.getElementById('qvZoomImg');
        function openZoom(src) {
            zoomImg.src = src;
            zoomOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeZoom() {
            zoomOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        zoomOverlay.addEventListener('click', closeZoom);
        document.getElementById('qvZoomClose').addEventListener('click', function(e) { e.stopPropagation(); closeZoom(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeZoom(); });

        /* Clicking the main modal image opens zoom */
        document.getElementById('qvMainImg').addEventListener('click', function() {
            if (this.src) openZoom(this.src);
        });

        document.addEventListener('click', function (e) {
            /* Gallery prev/next inside modal */
            if (e.target.closest('#qvPrev')) { e.stopPropagation(); setImage(curImg - 1); return; }
            if (e.target.closest('#qvNext')) { e.stopPropagation(); setImage(curImg + 1); return; }
            if (e.target.closest('.qv-thumb')) return;

            var btn = e.target.closest('[data-vehicle]');
            if (!btn) return;

            var raw = btn.getAttribute('data-vehicle');
            if (!raw) return;

            var v;
            try { v = JSON.parse(raw); }
            catch (err) { console.error('QuickView parse error:', err); return; }

            /* Header */
            var el;
            if ((el = getEl('qvModalLabel')))  el.textContent = v.title || '';
            if ((el = getEl('qvCondPill')))  {
                el.textContent = v.condition ? v.condition.toUpperCase() : '';
                el.className   = 'cond-pill qv-cond-pill' + (v.condition === 'used' ? ' used' : '');
            }
            if ((el = getEl('qvPrice')))    el.textContent = v.price || 'Call for Price';

            /* Detail link */
            if ((el = getEl('qvViewDetails'))) {
                if (v.detail_url) { el.href = v.detail_url; el.style.display = ''; }
                else              { el.style.display = 'none'; }
            }

            /* Images */
            images = (v.images && v.images.length) ? v.images : (v.primary_image ? [v.primary_image] : []);
            curImg  = 0;

            var prev = getEl('qvPrev'), next = getEl('qvNext');
            if (prev) prev.style.display = images.length > 1 ? '' : 'none';
            if (next) next.style.display = images.length > 1 ? '' : 'none';

            var thumbsEl = getEl('qvThumbs');
            if (thumbsEl) {
                thumbsEl.innerHTML = '';
                if (images.length > 1) {
                    images.forEach(function (src, i) {
                        var t = document.createElement('img');
                        t.src = src;
                        t.className = 'qv-thumb' + (i === 0 ? ' active' : '');
                        t.setAttribute('data-idx', i);
                        thumbsEl.appendChild(t);
                    });
                    thumbsEl.onclick = function (ev) {
                        var th = ev.target.closest('.qv-thumb');
                        if (th) setImage(parseInt(th.getAttribute('data-idx'), 10));
                    };
                }
            }
            setImage(0);

            /* Specs */
            var specs = [
                { icon: 'bi-hash',         label: 'Stock #',      val: v.stock_number },
                { icon: 'bi-upc',          label: 'VIN',          val: v.vin },
                { icon: 'bi-calendar3',    label: 'Year',         val: v.year },
                { icon: 'bi-car-front',    label: 'Make / Model', val: (v.make && v.model) ? v.make + ' ' + v.model : null },
                { icon: 'bi-tag',          label: 'Trim',         val: v.trim },
                { icon: 'bi-grid',         label: 'Body Style',   val: v.body_style },
                { icon: 'bi-speedometer2', label: 'Mileage',      val: v.mileage },
                { icon: 'bi-cpu',          label: 'Engine',       val: v.engine },
                { icon: 'bi-gear',         label: 'Transmission', val: v.transmission },
                { icon: 'bi-arrows-move',  label: 'Drivetrain',   val: v.drivetrain },
                { icon: 'bi-fuel-pump',    label: 'Fuel Type',    val: v.fuel_type },
                { icon: 'bi-speedometer',  label: 'MPG City/Hwy', val: v.mpg },
                { icon: 'bi-palette',      label: 'Ext. Color',   val: v.exterior_color },
                { icon: 'bi-palette2',     label: 'Int. Color',   val: v.interior_color },
                { icon: 'bi-building',     label: 'Dealer',       val: v.dealer_name },
                { icon: 'bi-geo-alt',      label: 'Location',     val: (v.dealer_city && v.dealer_state) ? v.dealer_city + ', ' + v.dealer_state : (v.dealer_city || v.dealer_state || null) },
            ];
            var grid = getEl('qvSpecsGrid');
            if (grid) grid.innerHTML = specs.filter(function (s) { return s.val; }).map(function (s) {
                return '<div class="qv-spec-item">'
                     + '<span class="qv-spec-icon"><i class="bi ' + s.icon + '"></i></span>'
                     + '<div><span class="qv-spec-label">' + s.label + '</span>'
                     + '<span class="qv-spec-val">' + String(s.val) + '</span></div>'
                     + '</div>';
            }).join('');

            /* Features */
            var feats   = Array.isArray(v.features) ? v.features : [];
            var featWrap = getEl('qvFeatures');
            var featHead = getEl('qvFeaturesTitle');
            if (feats.length) {
                if (featHead) featHead.style.display = '';
                if (featWrap) featWrap.innerHTML = feats.map(function (f) {
                    return '<span class="qv-feat-badge">' + String(f) + '</span>';
                }).join('');
            } else {
                if (featHead) featHead.style.display = 'none';
                if (featWrap) featWrap.innerHTML = '';
            }

            getModal().show();
        });
    })();
    @endif {{-- $quickView --}}
</script>
</body>
</html>
