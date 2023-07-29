<?php

namespace App\Traits;

use App\Models\Lookup;
use Illuminate\Support\Facades\Log;

trait HelpersTrait
{
    /**
     * Extracts query parameters from a given query string.
     *
     * @param string $query The query string to extract parameters from.
     * @return false|array An associative array containing the extracted parameters in [name => value] format.
     */
    public function extractQueryParams($query)
    {
        $params = []; // Initialize an empty array to store the extracted parameters.
        $pairs = explode(',', $query); // Split the query into individual key-value pairs using commas.

        if (count($pairs) < 1) return false;
        // Loop through each key-value pair.
        foreach ($pairs as $pair) {
            try {
                list($key, $value) = explode('=', $pair); // Split the pair into key and value using the '=' sign.
            } catch (\Exception $e) {
                Log::info('invalid_query_params', ["string" => $pair]);
                continue;
            }
            $params[$key] = $value; // Add the key-value pair to the $params array.
        }

        return $params; // Return the array containing the extracted parameters.
    }

    /**
     * Check if all query names are present in the list of available columns.
     *
     * This function validates the query names provided in the given query string
     * against the list of available columns. It ensures that all query names are
     * valid and present in the available columns. If any invalid query names are found,
     * the function returns a JSON error response with the list of invalid columns and the
     * list of available columns.
     *
     * @param array $params The query string to extract parameters from.
     * @param array $availableColumns An array containing the list of available column names.
     * @param string? $action Name of query action
     * @return array|\Illuminate\Http\JsonResponse If all query names are valid, it returns true.
     * If there are invalid query names, it returns a JSON error response with the list of invalid
     * columns and the list of available columns.
     */
    public function checkQueryNames($params, $availableColumns, $action = null)
    {
        $invalidColumns = array_diff(array_keys($params), $availableColumns);

        if (!empty($invalidColumns)) {
            $response = [
                'error' => 'Invalid columns in ' . ($action ?? 'query'),
                'invalid_columns' => $invalidColumns,
                'available_columns' => $availableColumns,
            ];

            return response()->json($response, 400);
        }

        // All query names are in available columns
        return $params;
    }
}
