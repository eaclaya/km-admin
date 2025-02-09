<?php

namespace App\Livewire\Datatables;

use App\Models\CloningControl;
use App\Models\Main\Account;
use App\Models\Main\Brand;
use App\Models\Main\Product;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProductsTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Product::orderBy('id', 'DESC')
            ->leftJoin('brands', 'brands.brand_id', '=', 'products.brand_id')
            ->leftJoin('accounts', 'accounts.id', '=', 'products.account_id')
            ->select([
                'products.id', 'products.public_id', 'products.product_key',
                'products.notes', 'products.category_id', 'products.cost',
                'products.price', 'products.wholesale_price', 'products.special_price',
                'products.club_price', 'products.normal_price', 'products.price_one',
                'products.price_two', 'products.price_three', 'products.qty',
                'products.relation_qty', 'products.warehouse_name', 'products.related',
                'products.relation_id', 'products.location', 'products.picture',
                'products.deleted_at', 'products.invoice_date',
                'products.account_id', 'brands.name as brand_name', 'accounts.name as account_name'
            ]);
    }
    public function configure(): void
    {
        $this->setSearchDebounce(1000);
        $this->setShouldRetrieveTotalItemCountDisabled();
        $this->setPaginationMethod('simple');
        $this->setPerPageVisibilityDisabled();
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        $columns = [
            Column::make("Codigo", "product_key")
                ->searchable(),
            Column::make("Description", "notes")
                ->searchable(),
            Column::make("Tienda", "account_id")
                ->format(function(string $value, $row) {
                    return $row->account_name;
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(10);
                    return $query->orWhereIn('products.account_id', $accounts->pluck('id'));
                }),
            Column::make("Ubicacion", "location")
                ->searchable(),
            Column::make("Equivalencia", "relation_id")
                ->searchable(),
            Column::make("Marca", "brand_id")
                ->format(function(string $value, $row) {
                    return isset($row->brand_name) ? $row->brand_name : '';
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $brands = Brand::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('brand_id')->take(10);
                    return $query->orWhereIn('products.account_id', $brands->pluck('brand_id'));
                }),
            Column::make("Precio", "price")
                ->searchable(),
            Column::make("Precio Bodega", "wholesale_price"),
            Column::make("Precio Especial", "special_price"),
            Column::make("Precio Club", "club_price"),
            Column::make("Precio Normal", "normal_price"),
            Column::make("Cantidad", "qty"),
            Column::make("Foto", "picture"),
        ];
        return $columns;
    }
}
