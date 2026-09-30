<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Queries\SellerSearch;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Query string for /{state}/{city} (docs/03): ?radius=25|50|100&cat=&verified=1&sort=&zip=&per_page=&page=
 *
 * This is a public GET page, so bad input is normalised to defaults in prepareForValidation() rather than
 * bouncing the visitor with a 422/redirect: an unknown radius becomes 50, an unknown category is ignored,
 * a malformed ZIP is kept only as a string for the "couldn't find" notice. The rules then always pass.
 */
class CitySearchRequest extends FormRequest
{
    public const PER_PAGE = [10, 20, 50];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $radius = filter_var($this->query('radius'), FILTER_VALIDATE_INT);
        $cat = is_string($this->query('cat')) ? $this->query('cat') : null;
        $sort = is_string($this->query('sort')) ? $this->query('sort') : null;
        $perPage = filter_var($this->query('per_page'), FILTER_VALIDATE_INT);
        $zip = is_string($this->query('zip')) ? mb_substr(trim($this->query('zip')), 0, 10) : null;

        $this->merge([
            'radius' => in_array($radius, SellerSearch::RADII, true) ? $radius : SellerSearch::DEFAULT_RADIUS,
            'cat' => $cat && Category::where('slug', $cat)->exists() ? $cat : null,
            'verified' => filter_var($this->query('verified'), FILTER_VALIDATE_BOOLEAN),
            'sort' => in_array($sort, SellerSearch::SORTS, true) ? $sort : 'distance',
            'per_page' => in_array($perPage, self::PER_PAGE, true) ? $perPage : self::PER_PAGE[0],
            'zip' => $zip !== '' ? $zip : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'radius' => ['required', 'integer', 'in:'.implode(',', SellerSearch::RADII)],
            'cat' => ['nullable', 'string', 'exists:categories,slug'],
            'verified' => ['boolean'],
            'sort' => ['required', 'in:'.implode(',', SellerSearch::SORTS)],
            'per_page' => ['required', 'integer', 'in:'.implode(',', self::PER_PAGE)],
            'zip' => ['nullable', 'string', 'max:10'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
