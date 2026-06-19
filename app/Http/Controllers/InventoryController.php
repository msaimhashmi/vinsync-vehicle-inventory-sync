<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()
            ->search($request->input('search'))
            ->filterCondition($request->input('condition'))
            ->filterYear($request->input('year'))
            ->filterMake($request->input('make'))
            ->filterModel($request->input('model'))
            ->filterTrim($request->input('trim'))
            ->filterBodyStyle($request->input('body_style'))
            ->filterExteriorColor($request->input('exterior_color'))
            ->filterCylinders($request->input('cylinders'))
            ->filterEngine($request->input('engine_filter'))
            ->filterTransmission($request->input('transmission'))
            ->filterFeature($request->input('feature'))
            ->filterFuelType($request->input('fuel_type'))
            ->filterDrivetrain($request->input('drivetrain'))
            ->filterPriceMin($request->input('price_min'))
            ->filterPriceMax($request->input('price_max'))
            ->filterMileageMax($request->input('mileage_max'));

        $sort = $request->input('sort', 'year_desc');

        // COALESCE picks sale_price, falls back to msrp if sale_price is null.
        // ISNULL() returns 1 for null rows, 0 for non-null — sorting by it first
        // pushes vehicles with no price to the bottom. MySQL doesn't support
        // the standard "NULLS LAST" syntax that PostgreSQL uses.
        if ($sort === 'price_asc') {
            $query->orderByRaw('ISNULL(COALESCE(sale_price, msrp)), COALESCE(sale_price, msrp) ASC');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('ISNULL(COALESCE(sale_price, msrp)), COALESCE(sale_price, msrp) DESC');
        } elseif ($sort === 'year_asc') {
            $query->orderBy('year', 'asc');
        } elseif ($sort === 'mileage') {
            $query->orderBy('mileage', 'asc');
        } else {
            $query->orderBy('year', 'desc');
        }

        $vehicles = $query->paginate(24)->withQueryString();
        $options  = $this->dropdownOptions($request);
        $view     = in_array($request->input('view'), ['list', 'grid'])
            ? $request->input('view')
            : 'grid';

        return view('inventory.index', compact('vehicles', 'options', 'view'));
    }

    private function dropdownOptions(Request $request): array
    {
        $make  = $request->input('make');
        $model = $request->input('model');
        $trim  = $request->input('trim');

        // Scoped base queries — each level inherits the selections above it.
        // Makes is always unscoped so you can always switch makes.
        $byMake       = fn($q) => $make  ? $q->where('make', $make)   : $q;
        $byMakeModel  = fn($q) => $byMake($model ? $q->where('model', $model) : $q);
        $byAll        = fn($q) => $byMakeModel($trim ? $q->where('trim', $trim) : $q);

        $engineStrings = $byAll(Vehicle::whereNotNull('engine'))->pluck('engine');

        $engineSizes = $engineStrings
            ->map(fn($e) => preg_match('/(\d+\.\d+)\s*(?:L\b|Liter)/i', $e, $m) ? $m[1].'L' : null)
            ->filter()->unique()->sort()->values();

        $cylinders = $engineStrings
            ->map(function ($e) {
                if (preg_match('/[IVW]-(\d+)/i', $e, $m))      return (int) $m[1];
                if (preg_match('/\bV(\d+)\b/i', $e, $m))       return (int) $m[1];
                if (preg_match('/(\d+)-cylinder/i', $e, $m))   return (int) $m[1];
                if (preg_match('/(\d+)\s+cylinder/i', $e, $m)) return (int) $m[1];
                return null;
            })
            ->filter()->unique()->sort()->values();

        $featuresAll = $byAll(Vehicle::whereNotNull('features'))->pluck('features')
            ->flatMap(fn($f) => is_array($f) ? $f : [])
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(40);

        return [
            'years'         => $byMake(Vehicle::select('year')->distinct()->whereNotNull('year'))->orderBy('year', 'desc')->pluck('year'),
            'makes'         => Vehicle::select('make')->distinct()->whereNotNull('make')->orderBy('make')->pluck('make'),
            'models'        => $byMake(Vehicle::select('model')->distinct()->whereNotNull('model'))->orderBy('model')->pluck('model'),
            'trims'         => $byMakeModel(Vehicle::select('trim')->distinct()->whereNotNull('trim'))->orderBy('trim')->pluck('trim'),
            'body_styles'   => $byAll(Vehicle::select('body_style')->distinct()->whereNotNull('body_style'))->orderBy('body_style')->pluck('body_style'),
            'colors'        => $byAll(Vehicle::select('exterior_color')->distinct()->whereNotNull('exterior_color')->where('exterior_color', '!=', ''))->orderBy('exterior_color')->pluck('exterior_color'),
            'cylinders'     => $cylinders,
            'engine_sizes'  => $engineSizes,
            'transmissions' => $byAll(Vehicle::select('transmission')->distinct()->whereNotNull('transmission')->where('transmission', '!=', ''))->orderBy('transmission')->pluck('transmission'),
            'features_list' => $featuresAll,
            'fuel_types'    => $byAll(Vehicle::select('fuel_type')->distinct()->whereNotNull('fuel_type'))->orderBy('fuel_type')->pluck('fuel_type'),
            'drivetrains'   => $byAll(Vehicle::select('drivetrain')->distinct()->whereNotNull('drivetrain'))->orderBy('drivetrain')->pluck('drivetrain'),
            'total'         => Vehicle::count(),
        ];
    }
}
