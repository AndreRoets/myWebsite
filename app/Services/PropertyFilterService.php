<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PropertyFilterService
{
    /**
     * A whitelist of columns on the 'properties' table that are filterable.
     * To add a new filter, add the database column name and a user-friendly label here.
     */
    private array $filterableColumns = [
        'price' => 'Price',
        'suburb' => 'Suburb',
        'property_type' => 'Property Type',
        'special_type' => 'Special Category',
        'bedrooms' => 'Bedrooms',
        'bathrooms' => 'Bathrooms',
        'garages' => 'Garages',
        'parking' => 'Parking Spaces',
        'floor_size' => 'Min Floor Size (m²)',
        'erf_size' => 'Min Erf Size (m²)',
    ];

    /**
     * Defines static options for columns that should be rendered as a <select> dropdown.
     * 'special_type' is now handled dynamically.
     */
    private array $selectOptions = [
        'property_type' => ['House', 'Apartment', 'Unit', 'Townhouse'],
    ];

    /**
     * Generates an array of filter definitions for the frontend.
     * Caches the result to avoid hitting the database schema on every request.
     *
     * @return array
     */
    public function getFilterDefinitions(): array
    {
        return Cache::rememberForever('property_filters_config_v3', function () {
            
            // Dynamically get options for 'special_type' from the database
            $this->selectOptions['special_type'] = Property::select('special_type')
                ->whereNotNull('special_type')
                ->where('special_type', '!=', '')
                ->distinct()
                ->orderBy('special_type')
                ->pluck('special_type')
                ->all();

            $definitions = [];
            $table = 'properties';
            
            $schemaColumns = Schema::getColumnListing($table);

            foreach ($this->filterableColumns as $column => $label) {
                if (!in_array($column, $schemaColumns)) {
                    continue; 
                }

                // Skip special_type if no options were found
                if ($column === 'special_type' && empty($this->selectOptions['special_type'])) {
                    continue;
                }

                $type = Schema::getColumnType($table, $column);
                $definition = [
                    'name' => $column,
                    'label' => $label,
                    'type' => $this->resolveInputType($column, $type),
                    'options' => $this->selectOptions[$column] ?? [],
                ];
                
                if ($column === 'price') {
                    $definitions[] = array_merge($definition, ['name' => 'price_from', 'label' => 'Price From', 'type' => 'number']);
                    $definitions[] = array_merge($definition, ['name' => 'price_to', 'label' => 'Price To', 'type' => 'number']);
                } else {
                    $definitions[] = $definition;
                }
            }
            return $definitions;
        });
    }

    /**
     * Applies the dynamic filters from the request to the Eloquent query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function applyFilters(Builder $query, Request $request): Builder
    {
        $filterableParams = array_keys($this->filterableColumns);
        $filterableParams = array_merge($filterableParams, ['price_from', 'price_to']);

        foreach ($filterableParams as $param) {
            if ($request->filled($param)) {
                $value = $request->input($param);

                switch ($param) {
                    case 'price_from':
                    case 'floor_size':
                    case 'erf_size':
                    case 'garages':
                    case 'parking':
                        $query->where(str_replace('_from', '', $param), '>=', $value);
                        break;
                    case 'price_to':
                        $query->where('price', '<=', $value);
                        break;
                    case 'suburb':
                        $query->where('suburb', 'like', '%' . $value . '%');
                        break;
                    default:
                        $query->where($param, $value);
                        break;
                }
            }
        }

        return $query;
    }

    /**
     * Resolves the appropriate HTML input type based on column name and DB type.
     *
     * @param string $column
     * @param string $type
     * @return string
     */
    private function resolveInputType(string $column, string $type): string
    {
        if (array_key_exists($column, $this->selectOptions) && !empty($this->selectOptions[$column])) {
            return 'select';
        }

        return match ($type) {
            'integer', 'decimal', 'float', 'double' => 'number',
            default => 'text',
        };
    }
}
