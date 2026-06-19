<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Inventory | Classic Auto Group</title>
    <meta name="description" content="Browse our full inventory of new and used vehicles. Search, filter, and find your perfect car today.">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:   #1a3a5c;
            --accent:    #e8a020;
            --bg-light:  #f4f6f9;
            --card-radius: 10px;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ── Header ── */
        .site-header {
            background: var(--primary);
            padding: 14px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .site-header .brand {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-decoration: none;
        }
        .site-header .brand span { color: var(--accent); }

        /* ── Hero strip ── */
        .inventory-hero {
            background: linear-gradient(135deg, var(--primary) 0%, #2c5282 100%);
            color: #fff;
            padding: 36px 0 28px;
        }
        .inventory-hero h1 { font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 700; }
        .inventory-hero p  { opacity: .8; margin: 0; }

        /* ── Search bar ── */
        .search-bar .form-control:focus { box-shadow: 0 0 0 3px rgba(232,160,32,.35); border-color: var(--accent); }
        .btn-search { background: var(--accent); border: none; color: #fff; font-weight: 600; }
        .btn-search:hover { background: #cf8e18; color: #fff; }

        /* ── Filter panel ── */
        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            padding: 18px 20px;
        }
        .filter-card select { font-size: .85rem; }
        .filter-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: #6c757d; margin-bottom: 4px; }

        /* ── Toolbar ── */
        .toolbar {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            padding: 10px 16px;
        }
        .results-count { font-size: .9rem; color: #6c757d; }
        .view-btn { border: 1px solid #dee2e6; background: #fff; color: #6c757d; padding: 6px 12px; }
        .view-btn.active, .view-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Vehicle Card (grid) ── */
        .vehicle-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .vehicle-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.14); transform: translateY(-2px); }
        .vehicle-card .card-img-wrap {
            position: relative;
            padding-top: 62%;
            overflow: hidden;
            background: #e9ecef;
        }
        .vehicle-card .card-img-wrap img {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .3s;
        }
        .vehicle-card:hover .card-img-wrap img { transform: scale(1.04); }
        .vehicle-card .condition-badge {
            position: absolute; top: 10px; left: 10px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .badge-new  { background: #d1fae5; color: #065f46; }
        .badge-used { background: #dbeafe; color: #1e40af; }

        .vehicle-card .card-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; }
        .vehicle-card .vehicle-title { font-size: .95rem; font-weight: 700; color: #1a202c; line-height: 1.3; margin-bottom: 2px; }
        .vehicle-card .vehicle-trim  { font-size: .8rem; color: #6c757d; margin-bottom: 10px; }
        .vehicle-card .vehicle-price { font-size: 1.2rem; font-weight: 800; color: var(--primary); }
        .vehicle-card .msrp-label   { font-size: .72rem; color: #6c757d; }
        .vehicle-card .specs-row    { font-size: .78rem; color: #555; margin-top: 10px; }
        .vehicle-card .specs-row i  { color: #aaa; margin-right: 3px; }
        .vehicle-card .btn-details  {
            margin-top: auto; padding-top: 12px;
            display: block; text-align: center;
            background: var(--primary); color: #fff;
            border-radius: 6px; padding: 8px;
            text-decoration: none; font-weight: 600; font-size: .85rem;
            transition: background .2s;
        }
        .vehicle-card .btn-details:hover { background: #2c5282; }

        /* ── List row ── */
        .vehicle-row {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            overflow: hidden;
            margin-bottom: 12px;
            display: flex;
            align-items: stretch;
        }
        .vehicle-row .row-img {
            width: 200px; min-width: 200px;
            object-fit: cover;
            display: block;
        }
        @media (max-width: 576px) {
            .vehicle-row { flex-direction: column; }
            .vehicle-row .row-img { width: 100%; min-width: unset; height: 180px; }
        }
        .vehicle-row .row-body { padding: 14px 18px; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .vehicle-row .row-price { text-align: right; padding: 14px 18px; display: flex; flex-direction: column; justify-content: center; align-items: flex-end; }
        @media (max-width: 768px) { .vehicle-row .row-price { display: none; } }

        /* ── Pagination ── */
        .pagination .page-link { color: var(--primary); }
        .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

        /* ── Empty state ── */
        .empty-state { padding: 60px 20px; text-align: center; background: #fff; border-radius: var(--card-radius); }
        .empty-state i { font-size: 3rem; color: #dee2e6; }

        /* ── Active filters chips ── */
        .filter-chip {
            display: inline-flex; align-items: center; gap: 5px;
            background: #e8f0fe; color: var(--primary);
            border-radius: 20px; padding: 3px 10px;
            font-size: .78rem; font-weight: 600;
            text-decoration: none;
        }
        .filter-chip:hover { background: #d0e2ff; color: var(--primary); }
    </style>
</head>
<body>

{{-- ──────────────────────────────── HEADER ──────────────────────────────── --}}
<header class="site-header">
    <div class="container">
        <a href="{{ route('inventory.index') }}" class="brand">
            Classic <span>Auto Group</span>
        </a>
    </div>
</header>

{{-- ──────────────────────────────── HERO ──────────────────────────────── --}}
<div class="inventory-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <h1><i class="bi bi-collection me-2"></i>Vehicle Inventory</h1>
                <p>{{ number_format($options['total']) }} vehicles available &mdash; updated every 12 hours</p>
            </div>
            <div class="col-md-6">
                {{-- Search bar --}}
                <form method="GET" action="{{ route('inventory.index') }}" class="search-bar">
                    @foreach(request()->except(['search','page']) as $key => $val)
                        @if(is_array($val))
                            @foreach($val as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg"
                               placeholder="Search by VIN, stock #, make, model, trim…"
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-search btn-lg px-4">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ──────────────────────────────── MAIN CONTENT ──────────────────────────────── --}}
<div class="container py-4">

    {{-- ── FILTER PANEL ── --}}
    <div class="filter-card mb-3">
        <form method="GET" action="{{ route('inventory.index') }}" id="filterForm">
            <input type="hidden" name="view" value="{{ $view }}">
            <input type="hidden" name="sort" value="{{ request('sort','year_desc') }}">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="row g-2 align-items-end">

                {{-- Condition --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Condition</div>
                    <select name="condition" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        <option value="new"  {{ request('condition') === 'new'  ? 'selected' : '' }}>New</option>
                        <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Used</option>
                    </select>
                </div>

                {{-- Year --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Year</div>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['years'] as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Make --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Make</div>
                    <select name="make" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['makes'] as $m)
                            <option value="{{ $m }}" {{ request('make') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Model --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Model</div>
                    <select name="model" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['models'] as $m)
                            <option value="{{ $m }}" {{ request('model') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Body Style --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Body Style</div>
                    <select name="body_style" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['body_styles'] as $b)
                            <option value="{{ $b }}" {{ request('body_style') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Fuel Type --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Fuel Type</div>
                    <select name="fuel_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['fuel_types'] as $f)
                            <option value="{{ $f }}" {{ request('fuel_type') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Transmission --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Transmission</div>
                    <select name="transmission" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['transmissions'] as $t)
                            <option value="{{ $t }}" {{ request('transmission') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Drivetrain --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Drivetrain</div>
                    <select name="drivetrain" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['drivetrains'] as $d)
                            <option value="{{ $d }}" {{ request('drivetrain') === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Exterior Color --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Ext. Color</div>
                    <select name="exterior_color" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['colors'] as $c)
                            <option value="{{ $c }}" {{ request('exterior_color') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Trim --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Trim</div>
                    <select name="trim" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any</option>
                        @foreach($options['trims'] as $t)
                            <option value="{{ $t }}" {{ request('trim') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Price Range --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Min Price ($)</div>
                    <input type="number" name="price_min" class="form-control form-select-sm"
                           placeholder="0" min="0" step="500"
                           value="{{ request('price_min') }}"
                           onchange="this.form.submit()">
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Max Price ($)</div>
                    <input type="number" name="price_max" class="form-control form-select-sm"
                           placeholder="Any" min="0" step="500"
                           value="{{ request('price_max') }}"
                           onchange="this.form.submit()">
                </div>

                {{-- Max Mileage --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="filter-label">Max Mileage</div>
                    <input type="number" name="mileage_max" class="form-control form-select-sm"
                           placeholder="Any" min="0" step="1000"
                           value="{{ request('mileage_max') }}"
                           onchange="this.form.submit()">
                </div>

                {{-- Clear all --}}
                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-end">
                    <a href="{{ route('inventory.index', array_filter(['search'=>request('search'),'view'=>$view])) }}"
                       class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                </div>

            </div>{{-- /row --}}
        </form>
    </div>{{-- /filter-card --}}

    {{-- ── ACTIVE FILTER CHIPS ── --}}
    @php
        $activeFilters = array_filter([
            'condition'      => request('condition'),
            'year'           => request('year'),
            'make'           => request('make'),
            'model'          => request('model'),
            'trim'           => request('trim'),
            'body_style'     => request('body_style'),
            'fuel_type'      => request('fuel_type'),
            'transmission'   => request('transmission'),
            'drivetrain'     => request('drivetrain'),
            'exterior_color' => request('exterior_color'),
            'price_min'      => request('price_min') ? '$'.number_format(request('price_min')).' min' : null,
            'price_max'      => request('price_max') ? '$'.number_format(request('price_max')).' max' : null,
            'mileage_max'    => request('mileage_max') ? number_format(request('mileage_max')).' mi max' : null,
        ]);
    @endphp
    @if(count($activeFilters))
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            <small class="text-muted fw-semibold">Active:</small>
            @foreach($activeFilters as $key => $label)
                @php
                    $removeParams = request()->except([$key, 'page']);
                @endphp
                <a href="{{ route('inventory.index', $removeParams) }}" class="filter-chip">
                    {{ ucwords(str_replace('_',' ',$key)) }}: {{ $label }}
                    <i class="bi bi-x"></i>
                </a>
            @endforeach
        </div>
    @endif

    {{-- ── TOOLBAR: count + sort + view toggle ── --}}
    <div class="toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="results-count">
            Showing <strong>{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</strong>
            of <strong>{{ $vehicles->total() }}</strong> vehicles
            @if(request('search'))
                for "<em>{{ request('search') }}</em>"
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Sort --}}
            <form method="GET" action="{{ route('inventory.index') }}" class="d-flex align-items-center gap-1">
                @foreach(request()->except(['sort','page']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                    <option value="year_desc"  {{ request('sort','year_desc') === 'year_desc'  ? 'selected':'' }}>Newest First</option>
                    <option value="year_asc"   {{ request('sort') === 'year_asc'   ? 'selected':'' }}>Oldest First</option>
                    <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected':'' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected':'' }}>Price: High to Low</option>
                    <option value="mileage"    {{ request('sort') === 'mileage'    ? 'selected':'' }}>Lowest Mileage</option>
                </select>
            </form>

            {{-- View toggle --}}
            <div class="btn-group btn-group-sm">
                <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['view' => 'grid'])) }}"
                   class="view-btn {{ $view === 'grid' ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap"></i>
                </a>
                <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['view' => 'list'])) }}"
                   class="view-btn {{ $view === 'list' ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ── VEHICLE LISTING ── --}}
    @if($vehicles->isEmpty())
        <div class="empty-state">
            <i class="bi bi-car-front mb-3 d-block"></i>
            <h4 class="text-muted">No vehicles found</h4>
            <p class="text-muted mb-3">Try adjusting your search or filters.</p>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary">Clear all filters</a>
        </div>
    @else

        {{-- ────── GRID VIEW ────── --}}
        @if($view === 'grid')
            <div class="row g-3">
                @foreach($vehicles as $vehicle)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="vehicle-card">
                            <div class="card-img-wrap">
                                <img src="{{ $vehicle->primary_image }}"
                                     alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                                     loading="lazy">
                                <span class="condition-badge {{ $vehicle->condition === 'new' ? 'badge-new' : 'badge-used' }}">
                                    {{ ucfirst($vehicle->condition) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="vehicle-title">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</div>
                                <div class="vehicle-trim">{{ $vehicle->trim }}</div>

                                <div class="vehicle-price">
                                    @if($vehicle->display_price)
                                        ${{ number_format($vehicle->display_price) }}
                                    @else
                                        <span class="text-muted fs-6">Call for Price</span>
                                    @endif
                                </div>
                                @if($vehicle->sale_price && $vehicle->msrp && $vehicle->msrp > $vehicle->sale_price)
                                    <div class="msrp-label">MSRP ${{ number_format($vehicle->msrp) }}</div>
                                @endif

                                <div class="specs-row row g-0 mt-2">
                                    <div class="col-6">
                                        <i class="bi bi-speedometer2"></i>
                                        {{ $vehicle->condition === 'new' ? 'New' : number_format($vehicle->mileage).' mi' }}
                                    </div>
                                    <div class="col-6">
                                        <i class="bi bi-gear"></i>
                                        {{ Str::before($vehicle->transmission ?? '—', ' ') ?: '—' }}
                                    </div>
                                    <div class="col-6 mt-1">
                                        <i class="bi bi-fuel-pump"></i>
                                        {{ $vehicle->mpg_city ? "{$vehicle->mpg_city}/{$vehicle->mpg_hwy} MPG" : '—' }}
                                    </div>
                                    <div class="col-6 mt-1">
                                        <i class="bi bi-palette"></i>
                                        {{ Str::words($vehicle->exterior_color ?? '—', 2, '') }}
                                    </div>
                                </div>

                                @if($vehicle->detail_url)
                                    <a href="{{ $vehicle->detail_url }}" target="_blank" rel="noopener" class="btn-details mt-3">
                                        View Details <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        {{-- ────── LIST VIEW ────── --}}
        @else
            @foreach($vehicles as $vehicle)
                <div class="vehicle-row">
                    <img src="{{ $vehicle->primary_image }}"
                         alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}"
                         class="row-img"
                         loading="lazy">

                    <div class="row-body">
                        <div class="d-flex align-items-start gap-2 mb-1">
                            <span class="badge {{ $vehicle->condition === 'new' ? 'badge-new' : 'badge-used' }} condition-badge" style="position:relative;top:0;left:0;">
                                {{ ucfirst($vehicle->condition) }}
                            </span>
                            <strong>{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->trim }}</strong>
                        </div>
                        <div class="text-muted small mb-2">
                            Stock #{{ $vehicle->stock_number }}
                            &bull; VIN: {{ $vehicle->vin }}
                        </div>
                        <div class="d-flex flex-wrap gap-3 small text-secondary">
                            <span><i class="bi bi-speedometer2 me-1"></i>
                                {{ $vehicle->condition === 'new' ? 'New' : number_format($vehicle->mileage).' mi' }}
                            </span>
                            @if($vehicle->engine)
                                <span><i class="bi bi-cpu me-1"></i>{{ $vehicle->engine }}</span>
                            @endif
                            @if($vehicle->transmission)
                                <span><i class="bi bi-gear me-1"></i>{{ $vehicle->transmission }}</span>
                            @endif
                            @if($vehicle->exterior_color)
                                <span><i class="bi bi-palette me-1"></i>{{ $vehicle->exterior_color }}</span>
                            @endif
                            @if($vehicle->mpg_city)
                                <span><i class="bi bi-fuel-pump me-1"></i>{{ $vehicle->mpg_city }}/{{ $vehicle->mpg_hwy }} MPG</span>
                            @endif
                        </div>
                        @if($vehicle->detail_url)
                            <a href="{{ $vehicle->detail_url }}" target="_blank" rel="noopener"
                               class="btn btn-sm mt-2 d-inline-block"
                               style="background:var(--primary);color:#fff;font-weight:600;">
                                View Details <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </div>

                    <div class="row-price">
                        <div class="vehicle-price" style="font-size:1.3rem;font-weight:800;color:var(--primary);">
                            @if($vehicle->display_price)
                                ${{ number_format($vehicle->display_price) }}
                            @else
                                <span class="text-muted fs-6">Call</span>
                            @endif
                        </div>
                        @if($vehicle->sale_price && $vehicle->msrp && $vehicle->msrp > $vehicle->sale_price)
                            <div class="text-muted small">MSRP ${{ number_format($vehicle->msrp) }}</div>
                        @endif
                        @if($vehicle->window_sticker_url)
                            <a href="{{ $vehicle->window_sticker_url }}" target="_blank"
                               class="btn btn-outline-secondary btn-sm mt-2">
                                <i class="bi bi-file-text me-1"></i>Sticker
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

    @endif {{-- end if vehicles not empty --}}

    {{-- ── PAGINATION ── --}}
    @if($vehicles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $vehicles->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>{{-- /container --}}

{{-- ── FOOTER ── --}}
<footer class="py-4 mt-4" style="background:#1a3a5c;color:rgba(255,255,255,.7);">
    <div class="container text-center">
        <small>
            &copy; {{ date('Y') }} Classic Auto Group &mdash; Inventory updated automatically every 12 hours.
            All prices and availability subject to change without notice.
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmrAYP3aFrZHaZl5HFnJbZdNg6uFzu4r1pKP"
        crossorigin="anonymous"></script>
</body>
</html>
